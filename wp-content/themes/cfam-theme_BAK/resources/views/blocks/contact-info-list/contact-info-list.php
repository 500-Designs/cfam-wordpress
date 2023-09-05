<?php
acf_register_block_type([
    'name'            => 'contact-info-list',
    'title'           => __('Contact Info List', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback' => 'block_render_callback_contact_info_list',
    'category'        => 'section',
    // 'icon'            => 'video-alt3',
    'keywords'        => ['contact', 'media', 'list'],

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
function block_render_callback_contact_info_list($block, $content='', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // Render the block.
    Timber::render(['./contact-info-list.twig'], $context);
}
