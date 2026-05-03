<?php

/**
 * Comments template entry point.
 *
 * Loaded by `comments_template()`. Sites can opt out of Brndle's styled
 * comments by returning `true` from the `brndle/disable_comments_styling`
 * filter — control reverts to WordPress default markup in that case.
 */

if (post_password_required()) {
    return;
}

if (apply_filters('brndle/disable_comments_styling', false)) {
    require ABSPATH . WPINC . '/theme-compat/comments.php';
    return;
}

echo \Roots\view('partials.components.comments')->render();
