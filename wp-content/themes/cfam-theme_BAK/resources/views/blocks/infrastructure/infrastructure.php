<?php
acf_register_block_type([
    'name'            => 'infrastructure',
    'title'           => __('Infrastructure', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback'	=> 'block_render_callback_infrastructure',
    'category'        => 'section',
    // 'icon'            => 'video-alt3',
    'keywords'        => ['info', 'section'],

    // The following disables the "preview" display inside Wordpress.
    // Useful for certain blocks that might be a trouble to style for administrators.
    'mode'            => 'preview',
    'supports'        => array(
        // 'mode'        => false,
        // This line only allows multiple instances of the block per page.
        // Change this to `false` to limit this to 1 block per page.
        'multiple'    => true,
        'jsx'         => true
    ),
]);
/**
 *  This is the callback that displays the block.
 *
 * @param   array  $block      The block settings and attributes.
 * @param   string $content    The block content (empty string).
 * @param   bool   $is_preview True during AJAX preview.
*/
function block_render_callback_infrastructure($block, $content='', $is_preview = false) {

    $context = Timber::get_context();
    $context['title'] = 'Title here';

    $investment_posts = get_posts(
        [
            'post_type'      => 'investment',
            'posts_per_page' => 3,
            'fields'         => 'ids',
        ]
    );

    $terms_args = [
        'hide_empty' => true,
        'object_ids' => $investment_posts,
    ];

    $context['investments'] = Timber::get_posts();
    $context['types'] = Timber::get_terms('type', $terms_args);
    // $context['technologies'] = Timber::get_terms('technology', $terms_args);

    Timber::render('./infrastructure.twig', $context);

}