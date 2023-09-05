<?php
acf_register_block_type([
    'name'            => 'page-hero-section',
    'title'           => __('Page Hero Section', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback'	=> 'block_render_callback_page_hero',
    'category'        => 'layout',
    'icon'            => 'superhero',
    'keywords'        => ['hero', 'section'],

    // The following disables the "preview" display inside Wordpress.
    // Useful for certain blocks that might be a trouble to style for administrators.
    'mode'            => 'edit',
    'supports'        => array(
        'mode'        => false,
        // This line only allows multiple instances of the block per page.
        // Change this to `false` to limit this to 1 block per page.
        'multiple'    => false,
    ),
]);
/**
 *  This is the callback that displays the block.
 *
 * @param   array  $block      The block settings and attributes.
 * @param   string $content    The block content (empty string).
 * @param   bool   $is_preview True during AJAX preview.
*/
function block_render_callback_page_hero($block, $content='', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // You can pass a query to the template like this
    $args = [
        'post_type'      => 'posts',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ];
    $context['posts'] = Timber::get_posts($args);

    // Render the block.
    Timber::render(['./example.twig'], $context);
}
