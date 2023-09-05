<?php
$context = Timber::context();
$context['posts'] = new Timber\PostQuery();
$context['post'] = Timber::query_post();

// Store field values.
$context['fields'] = get_fields();

$context['blocks'] = do_blocks($context['post']->post_content);

Timber::render(['./index.twig'], $context);