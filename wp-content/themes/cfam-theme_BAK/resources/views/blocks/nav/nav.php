<?php
acf_register_block_type([
    'name'            => 'nav',
    'title'           => __('NAV', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback'    => 'block_render_callback_nav',
    'category'        => 'section',
    // 'icon'            => 'video-alt3',
    'keywords'        => ['nav', 'section'],

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
function block_render_callback_nav($block, $content = '', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    $context['post'] = Timber::query_post();

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    if (function_exists('yoast_breadcrumb')) {
    $context['breadcrumb'] = yoast_breadcrumb('', '', false);
} else {
    $context['breadcrumb'] = 'Breadcrumb function not available';
}

    $context['subject_image'] = wp_get_attachment_image($context['fields']['subject_image']['ID'], 'full', '', ['class' => 'img-fluid']);

    #Check for the most recent file
    $today = date("Ymd", strtotime("-0 days")); #Fetch the current date
    $filename = "DailySourceFile/Cantor_Daily_Website_" . $today . ".csv";

    #Loop Through
    #Get the most recent filename using the current date
    #then if that file doesnt exists 
    #check the other files by getting the file with the previous date
    #Continue to loop and deduct days until you find the most recent file that exists
    if (!is_admin()) {
        $file_exists = false;
        $day_count = 0;
        while ($file_exists == false) {
            if (file_exists($filename)) {
                $file_exists = true;
            } else {
                $day_count++;
                $today = date("Ymd", strtotime("-" . $day_count . " days"));
                $filename = "DailySourceFile/Cantor_Daily_Website_" . $today . ".csv";
            }
        }

        # Get latest Daily file csv data
        $csv_data = array_map('str_getcsv', file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        // Remove the headers row and store the rest of the data in an array of rows with labels
        $headers = array_shift($csv_data);
        $rows = array();
        foreach ($csv_data as $row) {
            $rows[] = array_combine($headers, $row);
        }

        function search_csv($rows, $column_name, $search_value) {
            // Find the first row containing the given value of the specified column
            foreach ($rows as $row) {
                if ($row[$column_name] == $search_value) {
                    return $row;
                }
            }
            return null; // If no row is found containing the given value of the specified column
        }

        # get 2 new classes
        $classIRow = search_csv($rows, "nasdaq_symbol", "CFIIX");
        $classCRow = search_csv($rows, "nasdaq_symbol", "CFCIX");
        $context["class_I"] = $classIRow;
        $context["class_C"] = $classCRow;

        # Store Daily Source File csv data
        $extracted_csv_data = array_map('str_getcsv', file($filename));
        $extracted_csv_header = array_shift($extracted_csv_data);
        $extracted_csv_data = $extracted_csv_data[0];


        $index = 0;
        foreach ($extracted_csv_header as $header) {

            $header = strtolower($header);

            switch ($header) {

                case "nav":
                    $context["csv"][$header] = number_format((float)$extracted_csv_data[$index], 2, '.', '');
                    break;

                case "nav_change":
                    $context["csv"][$header] = number_format((float)$extracted_csv_data[$index], 2, '.', '');
                    break;

                case "percent_change_1d":
                    $context["csv"][$header] = number_format((float)$extracted_csv_data[$index], 2, '.', '');
                    break;

                case "ytd_return_noload":
                    $context["csv"][$header] = number_format((float)$extracted_csv_data[$index], 2, '.', '');
                    break;

                case "ytd_return_load":
                    $context["csv"][$header] = number_format((float)$extracted_csv_data[$index], 2, '.', '');
                    break;

                case "distribution_factor":
                    $context["csv"][$header] = number_format((float)$extracted_csv_data[$index], 2, '.', '') * $context["nav"];
                    $context["csv"][$header] = number_format((float)$context["csv"][$header], 2, '.', '');
                    break;

                case "load_flag":
                    $context["csv"][$header] = strtolower($extracted_csv_data[$index]);
                    break;


                default:
                    $context["csv"][$header] = $extracted_csv_data[$index];
            }
            $index++;
        }
    }



    // Render the block.
    Timber::render(['./nav.twig'], $context);
}
