<?php

namespace Brndle\Providers;

use Brndle\Settings\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use WP_REST_Request;
use WP_REST_Response;

class FormServiceProvider
{
    public function boot(): void
    {
        add_action('after_switch_theme', [$this, 'createTable']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);

        // Create table on first load if missing.
        if (get_option('brndle_submissions_db_version') !== '1.0') {
            $this->createTable();
        }
    }

    /**
     * Create the custom submissions table.
     */
    public function createTable(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'brndle_submissions';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL DEFAULT '',
            form_data longtext NOT NULL,
            source_url varchar(2083) NOT NULL DEFAULT '',
            ip_hash varchar(64) NOT NULL DEFAULT '',
            mailchimp_status varchar(20) NOT NULL DEFAULT 'skipped',
            webhook_status varchar(20) NOT NULL DEFAULT 'skipped',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY email (email),
            KEY created_at (created_at)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        update_option('brndle_submissions_db_version', '1.0');
    }

    /**
     * Get the table name.
     */
    private static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'brndle_submissions';
    }

    public function registerRestRoutes(): void
    {
        $ns = 'brndle/v1';

        register_rest_route($ns, '/forms/submit', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleSubmit'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route($ns, '/forms/submissions', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleListSubmissions'],
            'permission_callback' => [$this, 'checkManageOptions'],
        ]);

        register_rest_route($ns, '/forms/submissions/export', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleExportCsv'],
            'permission_callback' => [$this, 'checkManageOptions'],
        ]);

        register_rest_route($ns, '/forms/submissions/(?P<id>\d+)', [
            'methods'             => 'DELETE',
            'callback'            => [$this, 'handleDeleteSubmission'],
            'permission_callback' => [$this, 'checkManageOptions'],
        ]);

        register_rest_route($ns, '/forms/mailchimp/lists', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleMailchimpLists'],
            'permission_callback' => [$this, 'checkManageOptions'],
        ]);
    }

    public function checkManageOptions(): bool
    {
        return current_user_can('manage_options');
    }

    /**
     * Handle public form submission.
     */
    public function handleSubmit(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;

        $params = $request->get_json_params();

        if (! is_array($params) || empty($params)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid data.'], 400);
        }

        // Verify nonce.
        $nonce = $params['_brndle_nonce'] ?? '';
        if (! wp_verify_nonce($nonce, 'brndle_form_submit')) {
            return new WP_REST_Response(['success' => false, 'message' => 'Security check failed.'], 403);
        }

        // Honeypot check.
        if (! empty($params['_brndle_hp'])) {
            return new WP_REST_Response(['success' => true, 'message' => 'Thank you!'], 200);
        }

        // Rate limiting: 5 per minute per IP.
        $ipHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $rateKey = 'brndle_form_limit_' . substr($ipHash, 0, 12);
        $count = (int) get_transient($rateKey);

        if ($count >= 5) {
            return new WP_REST_Response(['success' => false, 'message' => 'Too many submissions. Please try again later.'], 429);
        }

        set_transient($rateKey, $count + 1, 60);

        // Strip internal fields, sanitize the rest.
        $internalKeys = ['_brndle_nonce', '_brndle_hp', '_source_url', '_mailchimp_list'];
        $sourceUrl = sanitize_url($params['_source_url'] ?? '');
        $mailchimpList = sanitize_text_field($params['_mailchimp_list'] ?? '');
        $formData = [];
        $email = '';

        foreach ($params as $key => $value) {
            if (in_array($key, $internalKeys, true)) {
                continue;
            }

            $cleanKey = sanitize_text_field($key);
            $cleanValue = sanitize_text_field($value);
            $formData[$cleanKey] = $cleanValue;

            if (is_email($cleanValue) && empty($email)) {
                $email = $cleanValue;
            }
        }

        if (empty($formData)) {
            return new WP_REST_Response(['success' => false, 'message' => 'No form data received.'], 400);
        }

        // Store submission in custom table.
        $submissionId = 0;
        if (Settings::get('form_store_submissions', true)) {
            $wpdb->insert(self::table(), [
                'email'       => $email,
                'form_data'   => wp_json_encode($formData),
                'source_url'  => $sourceUrl,
                'ip_hash'     => $ipHash,
                'created_at'  => current_time('mysql'),
            ], ['%s', '%s', '%s', '%s', '%s']);

            $submissionId = $wpdb->insert_id;
        }

        // Email notification.
        if (Settings::get('form_email_notifications', true)) {
            $notifyEmail = Settings::get('form_notification_email', '');

            if (empty($notifyEmail)) {
                $notifyEmail = get_option('admin_email');
            }

            if (! empty($notifyEmail)) {
                $this->sendNotification($notifyEmail, $formData, $sourceUrl);
            }
        }

        // Mailchimp sync.
        $mcStatus = 'skipped';
        if (! empty($email)) {
            $apiKey = Settings::get('mailchimp_api_key', '');
            $listId = $mailchimpList ?: Settings::get('mailchimp_list_id', '');

            if (! empty($apiKey) && ! empty($listId)) {
                $mergeFields = [];
                foreach ($formData as $k => $v) {
                    if ($v !== $email) {
                        $tag = strtoupper(substr(preg_replace('/[^a-z0-9]/', '', strtolower($k)), 0, 10));
                        if (! empty($tag)) {
                            $mergeFields[$tag] = $v;
                        }
                    }
                }

                $mcResult = $this->syncToMailchimp($apiKey, $listId, $email, $mergeFields);
                $mcStatus = $mcResult['success'] ? 'synced' : 'failed';
            }
        }

        // Webhook.
        $webhookUrl = Settings::get('form_webhook_url', '');
        $whStatus = 'skipped';

        if (! empty($webhookUrl)) {
            $whResult = $this->forwardToWebhook($webhookUrl, [
                'fields'     => $formData,
                'email'      => $email,
                'source_url' => $sourceUrl,
                'submitted'  => current_time('c'),
                'site'       => get_bloginfo('name'),
            ]);
            $whStatus = $whResult['success'] ? 'sent' : 'failed';
        }

        // Update integration statuses.
        if ($submissionId) {
            $wpdb->update(self::table(), [
                'mailchimp_status' => $mcStatus,
                'webhook_status'   => $whStatus,
            ], ['id' => $submissionId], ['%s', '%s'], ['%d']);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => __('Thank you! Your submission has been received.', 'brndle'),
        ], 200);
    }

    /**
     * List submissions (admin).
     */
    public function handleListSubmissions(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;

        $page = max(1, (int) $request->get_param('page'));
        $perPage = max(1, min(100, (int) ($request->get_param('per_page') ?: 20)));
        $search = sanitize_text_field($request->get_param('search') ?? '');
        $offset = ($page - 1) * $perPage;
        $table = self::table();

        $where = '';
        $whereArgs = [];

        if (! empty($search)) {
            $where = 'WHERE email LIKE %s OR form_data LIKE %s';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $whereArgs = [$like, $like];
        }

        $total = (int) $wpdb->get_var(
            $where
                ? $wpdb->prepare("SELECT COUNT(*) FROM {$table} {$where}", ...$whereArgs)
                : "SELECT COUNT(*) FROM {$table}"
        );

        $rows = $where
            ? $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                ...array_merge($whereArgs, [$perPage, $offset])
            ))
            : $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $perPage,
                $offset
            ));

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id'        => (int) $row->id,
                'email'     => $row->email,
                'fields'    => json_decode($row->form_data, true) ?: [],
                'source'    => $row->source_url,
                'mailchimp' => $row->mailchimp_status,
                'webhook'   => $row->webhook_status,
                'date'      => wp_date('M j, Y g:i A', strtotime($row->created_at)),
            ];
        }

        return new WP_REST_Response([
            'items'    => $items,
            'total'    => $total,
            'pages'    => (int) ceil($total / $perPage),
            'page'     => $page,
            'per_page' => $perPage,
        ], 200);
    }

    /**
     * Export submissions as CSV (admin).
     */
    public function handleExportCsv(): void
    {
        global $wpdb;

        $table = self::table();
        $rows = $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC");

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=brndle-submissions-' . wp_date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');

        // Collect all unique field keys.
        $allKeys = [];
        $parsed = [];

        foreach ($rows as $row) {
            $data = json_decode($row->form_data, true) ?: [];
            $allKeys = array_merge($allKeys, array_keys($data));
            $parsed[] = ['row' => $row, 'fields' => $data];
        }

        $allKeys = array_unique($allKeys);

        fputcsv($output, array_merge(['Date', 'Email', 'Source URL', 'Mailchimp', 'Webhook'], $allKeys));

        foreach ($parsed as $item) {
            $csvRow = [
                $item['row']->created_at,
                $item['row']->email,
                $item['row']->source_url,
                $item['row']->mailchimp_status,
                $item['row']->webhook_status,
            ];

            foreach ($allKeys as $key) {
                $csvRow[] = $item['fields'][$key] ?? '';
            }

            fputcsv($output, $csvRow);
        }

        fclose($output);
        exit;
    }

    /**
     * Delete a submission (admin).
     */
    public function handleDeleteSubmission(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;

        $id = (int) $request->get_param('id');
        $deleted = $wpdb->delete(self::table(), ['id' => $id], ['%d']);

        if (! $deleted) {
            return new WP_REST_Response(['success' => false, 'message' => 'Not found.'], 404);
        }

        return new WP_REST_Response(['success' => true], 200);
    }

    /**
     * Fetch Mailchimp lists (admin).
     */
    public function handleMailchimpLists(): WP_REST_Response
    {
        $apiKey = Settings::get('mailchimp_api_key', '');

        if (empty($apiKey)) {
            return new WP_REST_Response(['lists' => [], 'message' => 'No API key configured.'], 200);
        }

        $dc = substr($apiKey, strrpos($apiKey, '-') + 1);

        if (empty($dc)) {
            return new WP_REST_Response(['lists' => [], 'message' => 'Invalid API key format.'], 400);
        }

        try {
            $client = new Client(['timeout' => 10]);
            $response = $client->get("https://{$dc}.api.mailchimp.com/3.0/lists?count=100", [
                'auth' => ['anystring', $apiKey],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $lists = [];

            foreach ($body['lists'] ?? [] as $list) {
                $lists[] = [
                    'id'           => $list['id'],
                    'name'         => $list['name'],
                    'member_count' => $list['stats']['member_count'] ?? 0,
                ];
            }

            return new WP_REST_Response(['lists' => $lists], 200);
        } catch (RequestException $e) {
            $msg = $e->hasResponse()
                ? json_decode($e->getResponse()->getBody()->getContents(), true)['detail'] ?? 'API error.'
                : 'Connection failed.';

            return new WP_REST_Response(['lists' => [], 'message' => $msg], 200);
        }
    }

    private function syncToMailchimp(string $apiKey, string $listId, string $email, array $mergeFields): array
    {
        $dc = substr($apiKey, strrpos($apiKey, '-') + 1);
        $hash = md5(strtolower(trim($email)));

        try {
            $client = new Client(['timeout' => 10]);
            $client->put("https://{$dc}.api.mailchimp.com/3.0/lists/{$listId}/members/{$hash}", [
                'auth' => ['anystring', $apiKey],
                'json' => [
                    'email_address' => $email,
                    'status_if_new' => 'subscribed',
                    'merge_fields'  => $mergeFields ?: (object) [],
                ],
            ]);

            return ['success' => true];
        } catch (RequestException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function forwardToWebhook(string $url, array $data): array
    {
        try {
            $client = new Client(['timeout' => 5]);
            $client->post($url, [
                'json'    => $data,
                'headers' => ['User-Agent' => 'Brndle-Theme/' . wp_get_theme()->get('Version')],
            ]);

            return ['success' => true];
        } catch (RequestException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function sendNotification(string $to, array $formData, string $sourceUrl): void
    {
        $siteName = get_bloginfo('name');
        $subject = sprintf(__('[%s] New Form Submission', 'brndle'), $siteName);

        $body = '<div style="font-family:sans-serif;max-width:600px;margin:0 auto">';
        $body .= '<h2 style="color:#333">' . esc_html__('New Form Submission', 'brndle') . '</h2>';
        $body .= '<table style="width:100%;border-collapse:collapse">';

        foreach ($formData as $key => $value) {
            $body .= '<tr>';
            $body .= '<td style="padding:8px 12px;border-bottom:1px solid #eee;font-weight:600;color:#555">' . esc_html($key) . '</td>';
            $body .= '<td style="padding:8px 12px;border-bottom:1px solid #eee">' . esc_html($value) . '</td>';
            $body .= '</tr>';
        }

        $body .= '</table>';

        if (! empty($sourceUrl)) {
            $body .= '<p style="margin-top:16px;color:#888;font-size:13px">' . esc_html__('Submitted from:', 'brndle') . ' ' . esc_url($sourceUrl) . '</p>';
        }

        $body .= '</div>';

        wp_mail($to, $subject, $body, ['Content-Type: text/html; charset=UTF-8']);
    }
}
