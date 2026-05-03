{{--
  Brndle comments partial.

  Loaded from `comments.php` at the theme root. Renders the comment list
  via the Brndle\\Comments\\Walker, the standard comment_form() (with
  Tailwind-friendly field templates), and a status banner when comments
  are closed but the post still has comments.

  Filters:
    brndle/disable_comments_styling — short-circuits rendering and
        re-uses the WordPress default markup. Set in `comments.php`.
--}}

@php
  use Brndle\Comments\Walker as BrndleCommentWalker;

  $count = (int) get_comments_number();
  $hasComments = have_comments();
  $isClosed = ! comments_open() && $count !== 0 && post_type_supports(get_post_type(), 'comments');
  $loginRequired = get_option('comment_registration') && ! is_user_logged_in();

  $fieldClasses = 'block w-full rounded-md border border-border-subtle bg-surface-primary px-3 py-2 text-text-primary placeholder:text-text-tertiary focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-colors';
  $labelClasses = 'block text-sm font-medium text-text-secondary mb-1';

  $commentFields = [
    'author' => '<p class="comment-form-author"><label for="author" class="' . esc_attr($labelClasses) . '">' . esc_html__('Name', 'brndle') . ' <span class="text-accent">*</span></label><input id="author" name="author" type="text" class="' . esc_attr($fieldClasses) . '" required /></p>',
    'email' => '<p class="comment-form-email"><label for="email" class="' . esc_attr($labelClasses) . '">' . esc_html__('Email', 'brndle') . ' <span class="text-accent">*</span></label><input id="email" name="email" type="email" class="' . esc_attr($fieldClasses) . '" required /></p>',
    'url' => '<p class="comment-form-url"><label for="url" class="' . esc_attr($labelClasses) . '">' . esc_html__('Website', 'brndle') . '</label><input id="url" name="url" type="url" class="' . esc_attr($fieldClasses) . '" /></p>',
  ];

  $formArgs = [
    'fields' => $commentFields,
    'class_form' => 'brndle-comment-form space-y-4',
    'comment_field' => '<p class="comment-form-comment"><label for="comment" class="' . esc_attr($labelClasses) . '">' . esc_html__('Comment', 'brndle') . ' <span class="text-accent">*</span></label><textarea id="comment" name="comment" rows="5" class="' . esc_attr($fieldClasses) . '" required></textarea></p>',
    'class_submit' => 'inline-flex items-center justify-center rounded-md bg-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-accent-strong focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors cursor-pointer',
    'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
    'title_reply' => '<span class="text-xl font-bold text-text-primary">' . esc_html__('Leave a comment', 'brndle') . '</span>',
    'title_reply_to' => '<span class="text-xl font-bold text-text-primary">' . esc_html__('Reply to %s', 'brndle') . '</span>',
    'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title mb-4">',
    'title_reply_after' => '</h3>',
    'cancel_reply_link' => esc_html__('Cancel reply', 'brndle'),
    'must_log_in' => '<p class="text-sm text-text-secondary">' . sprintf(
      /* translators: %s: login URL */
      wp_kses(__('You must be <a href="%s" class="text-accent hover:text-accent-strong underline">logged in</a> to post a comment.', 'brndle'), ['a' => ['href' => true, 'class' => true]]),
      esc_url(wp_login_url(apply_filters('the_permalink', get_permalink())))
    ) . '</p>',
    'logged_in_as' => '<p class="logged-in-as text-sm text-text-tertiary mb-2">' . sprintf(
      /* translators: 1: profile URL, 2: display name, 3: logout URL */
      wp_kses(__('Logged in as <a href="%1$s" class="text-accent hover:underline">%2$s</a>. <a href="%3$s" class="hover:underline">Log out?</a>', 'brndle'), ['a' => ['href' => true, 'class' => true]]),
      esc_url(get_edit_user_link()),
      esc_html(wp_get_current_user()->display_name),
      esc_url(wp_logout_url(apply_filters('the_permalink', get_permalink())))
    ) . '</p>',
    'comment_notes_before' => '<p class="comment-notes text-sm text-text-tertiary mb-3">' . esc_html__('Your email address will not be published. Required fields are marked *', 'brndle') . '</p>',
    'comment_notes_after' => '',
  ];
@endphp

<section id="comments" class="brndle-comments mt-12 pt-10 border-t border-border-subtle">
  @if($hasComments)
    <h2 class="text-2xl font-bold text-text-primary mb-6">
      {{-- translators: %s: number of comments --}}
      {{ sprintf(_n('%s Comment', '%s Comments', $count, 'brndle'), number_format_i18n($count)) }}
    </h2>

    <ol class="comment-list space-y-4 mb-10">
      @php(wp_list_comments(['walker' => new BrndleCommentWalker(), 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 48]))
    </ol>

    @if(get_comment_pages_count() > 1 && get_option('page_comments'))
      <nav class="brndle-comments__nav flex items-center justify-between gap-4 mb-10 text-sm">
        <div class="text-text-tertiary">
          @php($prev = get_previous_comments_link(__('Older comments', 'brndle')))
          @if($prev) {!! $prev !!} @endif
        </div>
        <div class="text-text-tertiary">
          @php($next = get_next_comments_link(__('Newer comments', 'brndle')))
          @if($next) {!! $next !!} @endif
        </div>
      </nav>
    @endif
  @endif

  @if($isClosed)
    <p class="brndle-comments__closed rounded-md border border-border-subtle bg-surface-secondary px-4 py-3 text-sm text-text-tertiary">
      {{ __('Comments are closed.', 'brndle') }}
    </p>
  @endif

  @if(comments_open())
    <div class="brndle-comments__form rounded-lg border border-border-subtle bg-surface-primary p-6">
      @php(comment_form($formArgs))
    </div>
  @endif
</section>
