<?php
acf_register_block_type([
    'name'            => 'post-grid',
    'title'           => __('Post Grid', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback'	=> 'block_render_callback_post_grid',
    'category'        => 'section',
    // 'icon'            => 'video-alt3',
    'keywords'        => ['post', 'grid', 'section'],

    // The following disables the "preview" display inside Wordpress.
    // Useful for certain blocks that might be a trouble to style for administrators.
    'mode'            => 'edit',
    'supports'        => array(
        'mode'        => false,
        // This line only allows multiple instances of the block per page.
        // Change this to `false` to limit this to 1 block per page.
        'multiple'    => true,
    ),
]);
/**
 *  This is the callback that displays the block.
 *
 * @param   array  $block      The block settings and attributes.
 * @param   string $content    The block content (empty string).
 * @param   bool   $is_preview True during AJAX preview.
*/
function block_render_callback_post_grid($block, $content='', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // ================================================
    /** Get Featured Post & Unfeatured Posts
     * post_type        => "name of post type you want to query"
     * post_per_page    => "number of results you want to get on the query"
     * cat              => "id of the post's category you want to include on the query"
     * category_not_in  => "id of the post's category you dont want to include on the query"
    */
    
    #Get "Featured" category id
    $featured_post = get_category_by_slug('featured'); 
    $featured_id = $featured_post->term_id;

    $posts_featured = Timber::get_posts([ 'post_type' => 'post', 'posts_per_page' => 1, 'cat' => $featured_id ]);
    $posts = Timber::get_posts(['post_type' => 'post', 'posts_per_page' => 10, 'category__not_in' => array($featured_id) ]);

    $context['posts'] = array_merge( $posts_featured,$posts );

    // ================================================

    // Render the block.
    Timber::render(['./post-grid.twig'], $context);
}
