<?php
acf_register_block_type([
    'name'            => 'performance-metrics',
    'title'           => __('Performance Metrics', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback' => 'block_render_callback_performance_metrics',
    'category'        => 'section',
    // 'icon'         => 'video-alt3',
    'keywords'        => ['chart', 'tables', 'metric'],

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
function block_render_callback_performance_metrics($block, $content='', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    # Parse Monthly CSVs
        // Get the most recent monthly csv file based on date suffix regardless of current date
    $monthlyFiles = preg_grep('~^CANTORSIFMONTHLY_.*\.csv$~', scandir(ABSPATH."DailySourceFile/"));

    $fileDates = [];
    foreach ($monthlyFiles as $file) {
        $parsed = end((explode('CANTORSIFMONTHLY_', $file, 2)));
        $parsed = strtok( $parsed, '.csv' );
        array_push($fileDates, $parsed);
    }
    sort($fileDates);

    $latestMonthlyData = array_map('str_getcsv', file(ABSPATH."DailySourceFile/CANTORSIFMONTHLY_".end($fileDates).".csv"));
    
    $monthlyFileKeys = array(
        "Report Date" => array_search('Report Date', $latestMonthlyData[0]),
        "Inception_Date" => array_search('Inception_Date', $latestMonthlyData[0]),
        "Index Name" => array_search('Index Name', $latestMonthlyData[0]),
        
        "R_Fd_ITD_Cum_NoLoad" => array_search('R_Fd_ITD_Cum_NoLoad', $latestMonthlyData[0]),
        "R_Fd_ITD_Cum_Load" => array_search('R_Fd_ITD_Cum_Load', $latestMonthlyData[0]),

        "R_Fd_1_Month_NoLoad" => array_search('R_Fd_1_Month_NoLoad', $latestMonthlyData[0]),
        "R_Fd_3_Month_NoLoad" => array_search('R_Fd_3_Month_NoLoad', $latestMonthlyData[0]),
        "R_Fd_6_Month_NoLoad" => array_search('R_Fd_6_Month_NoLoad', $latestMonthlyData[0]),
        "R_Fd_1Yr_NoLoad" => array_search('R_Fd_1Yr_NoLoad', $latestMonthlyData[0]),
        "R_Fd_ITD_Cum_NoLoad" => array_search('R_Fd_ITD_Cum_NoLoad', $latestMonthlyData[0]),
        
        "R_Fd_1_Month_Load" => array_search('R_Fd_1_Month_Load', $latestMonthlyData[0]),
        "R_Fd_3_Month_Load" => array_search('R_Fd_3_Month_Load', $latestMonthlyData[0]),
        "R_Fd_6_Month_Load" => array_search('R_Fd_6_Month_Load', $latestMonthlyData[0]),
        "R_Fd_1Yr_Load" => array_search('R_Fd_1Yr_Load', $latestMonthlyData[0]),
        "R_Fd_ITD_Cum_Load" => array_search('R_Fd_ITD_Cum_Load', $latestMonthlyData[0]),

        "R_IDX_1_Month" => array_search('R_IDX_1_Month', $latestMonthlyData[0]),
        "R_IDX_3_Month" => array_search('R_IDX_3_Month', $latestMonthlyData[0]),
        "R_IDX_6_Month" => array_search('R_IDX_6_Month', $latestMonthlyData[0]),
        "R_IDX_1YR" => array_search('R_IDX_1YR', $latestMonthlyData[0]),
        "R_IDX_ITD_CUM" => array_search('R_IDX_ITD_CUM', $latestMonthlyData[0]),
    );

    script_console_log("Performance and Metrics graph & Table source: Latest monthly file" . end($fileDates));

    $pmData = array(
        "keys" => $monthlyFileKeys,
        "report_date" => $latestMonthlyData[1][$monthlyFileKeys["Report Date"]],
        "pm_data" => $latestMonthlyData
    );

    php_array_to_js($pmData, 'performanceData');

    // Render the block.
    Timber::render(['./performance-metrics.twig'], $context);
}
