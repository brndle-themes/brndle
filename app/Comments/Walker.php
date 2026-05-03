<?php

namespace Brndle\Comments;

use Walker_Comment;
use WP_Comment;

/**
 * Brndle comment walker.
 *
 * Renders an `<li>` per comment with Brndle design tokens. Avatar uses
 * the local-avatar pipeline (LocalAvatar overrides `get_avatar`), so we
 * pull through `get_avatar` directly and trust the upstream filter.
 *
 * Markup is additive — keeps WP-default classes (`comment`, `byuser`,
 * `bypostauthor`, `comment-author-{slug}`, `depth-{n}`) so plugins like
 * subscribe-to-comments / Akismet badges keep working.
 */
class Walker extends Walker_Comment
{
    /**
     * @param WP_Comment           $comment
     * @param int                  $depth
     * @param array<string, mixed> $args
     */
    protected function html5_comment($comment, $depth, $args): void // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    {
        $tag = ('div' === $args['style']) ? 'div' : 'li';
        $depthClass = 'brndle-comment depth-' . (int) $depth;
        $isAuthor = (int) $comment->user_id === (int) get_post_field('post_author', $comment->comment_post_ID) && (int) $comment->user_id !== 0;

        $classes = array_filter([
            'brndle-comment',
            'rounded-lg',
            'border',
            'border-border-subtle',
            'bg-surface-primary',
            'p-5',
            'transition-colors',
            $isAuthor ? 'border-accent/40 bg-accent/5' : null,
        ]);

        ?>
        <<?php echo esc_attr($tag); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($depthClass, $comment); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">
                <header class="brndle-comment__meta flex items-start gap-3 mb-3">
                    <div class="brndle-comment__avatar shrink-0">
                        <?php echo get_avatar($comment, $args['avatar_size'] ?? 48, '', '', ['class' => 'rounded-full']); ?>
                    </div>
                    <div class="brndle-comment__byline min-w-0 flex-1">
                        <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                            <?php /* translators: %s: comment author */ ?>
                            <span class="font-semibold text-text-primary truncate">
                                <?php printf(wp_kses_post(__('%s', 'brndle')), get_comment_author_link($comment)); ?>
                            </span>
                            <?php if ($isAuthor) : ?>
                                <span class="brndle-comment__badge inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium uppercase tracking-wide bg-accent/10 text-accent">
                                    <?php esc_html_e('Author', 'brndle'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>" class="text-xs text-text-tertiary hover:text-accent transition-colors">
                            <time datetime="<?php comment_time('c'); ?>">
                                <?php
                                printf(
                                    /* translators: 1: comment date, 2: comment time */
                                    esc_html__('%1$s at %2$s', 'brndle'),
                                    esc_html(get_comment_date('', $comment)),
                                    esc_html(get_comment_time())
                                );
                                ?>
                            </time>
                        </a>
                    </div>
                </header>

                <?php if ('0' === $comment->comment_approved) : ?>
                    <p class="brndle-comment__moderation text-sm text-text-tertiary italic mb-3">
                        <?php esc_html_e('Your comment is awaiting moderation.', 'brndle'); ?>
                    </p>
                <?php endif; ?>

                <div class="brndle-comment__body prose prose-sm max-w-none text-text-secondary">
                    <?php comment_text(); ?>
                </div>

                <footer class="brndle-comment__actions mt-3 flex items-center gap-3 text-xs">
                    <?php
                    comment_reply_link(array_merge($args, [
                        'add_below' => 'div-comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'before'    => '<span class="brndle-comment__reply text-accent hover:text-accent-strong font-medium">',
                        'after'     => '</span>',
                    ]));
                    ?>
                    <?php edit_comment_link(__('Edit', 'brndle'), '<span class="brndle-comment__edit text-text-tertiary hover:text-accent">', '</span>'); ?>
                </footer>
            </article>
        <?php
    }
}
