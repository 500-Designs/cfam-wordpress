<?php
$context = Timber::context();
$context['posts'] = new Timber\PostQuery();
$context['post'] = Timber::query_post();

$context['blocks'] = do_blocks($context['post']->post_content);
if (post_password_required($post->ID)) {
    // Timber::render('single-password.twig', $context);
} else {
    Timber::render(['page-' . $post->post_name . '.twig', './index.twig'], $context);
}
