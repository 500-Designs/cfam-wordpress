<?php
/**
 * Will route page to the correct template.
 * Templates will take priority and fall down the hierarchy wp tree
 *
 */

if (is_front_page()) {
    get_template_part('resources/views/templates/homepage/index');
} elseif (is_page()) {
    switch (get_page_template_slug()) {
        case 'resources/views/templates/homepage/index.php' :
            get_template_part('resources/views/templates/homepage/index');
            break;
        default;
        get_template_part('resources/views/templates/general/default');
        break;
    }
} elseif (is_single()) {
    switch (get_post_type()) {
        // case 'SAMPLEPOST':
        //     get_template_part('resources/views/templates/SAMPLEPOST/detail');
        //     break;
        default:
        get_template_part('resources/views/templates/general/default');
        break;
    }
} elseif (is_404()) {
    get_template_part('resources/views/templates/404/index');
}
