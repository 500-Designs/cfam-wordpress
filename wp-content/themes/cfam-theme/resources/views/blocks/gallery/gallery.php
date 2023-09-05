<?php
acf_register_block_type([
    'name'            => 'gallery',
    'title'           => __('Gallery', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback'    => 'block_render_callback_gallery',
    'category'        => 'section',
    // 'icon'            => 'video-alt3',
    'keywords'        => ['gallery', 'section'],

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
function block_render_callback_gallery($block, $content = '', $is_preview = false)
{
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    global $paged;
    if (!isset($paged) || !$paged) {
        $paged = 1;
    }
    // $context = Timber::get_context();
    $args = array(
        'post_type' => 'investment',
        'posts_per_page' => 4,
        'paged' => $paged
        // Gest post by "graphic" cataegory
        // 'meta_query' => array(
        //     array(
        //         'key' => 'project_category',
        //         'value' => 'graphic',
        //         'compare' => 'LIKE'
        //     )
        // ),
        // Order by post date
        // 'orderby' => array(
        //     'title' => 'ASC'
        // )
    );
    query_posts($args);
    $context['types'] = Timber::get_terms([
        'taxonomy' => 'investmenttype',
    ]);
    $context['investments'] = Timber::get_posts();
    $context['pagination'] = Timber::get_pagination();
    // ================================================
    // Render the block.
    Timber::render(['./gallery.twig'], $context);
}