<?php
acf_register_block_type([
    'name'            => 'line-charts',
    'title'           => __('Line Charts', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback' => 'block_render_callback_line_charts',
    'category'        => 'section',
    // 'icon'            => 'video-alt3',
    'keywords'        => ['charts', 'line'],

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
function block_render_callback_line_charts($block, $content = '', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    # Parse Growth10K files
    $growthFiles = preg_grep('~^Cantor_Growth10k_Website_.*\.csv$~', scandir(ABSPATH."DailySourceFile/"));
    $latestFileDate = '';
    $growthFileData = [];
    foreach ($growthFiles as $file) {
        $fileUrl = ABSPATH."DailySourceFile/" . $file;
        $hasData = false;
        $date = end((explode('Cantor_Growth10k_Website_', $file, 2)));
        $date = strtok($date, '.csv');
        $fileData = array_map('str_getcsv', file($fileUrl));
        $hasData = is_array($fileData[1]);
        if (($date > $latestFileDate) && $hasData) {
            $latestFileDate = $date;
            if (isCSVFileTabDelimited($fileUrl)) {
                $growthFileData = parse_csv($fileUrl, "\t");
            } else {
                $growthFileData = parse_csv($fileUrl, ",");
            }
        }
    }
    script_console_log("Hypothetical Growth Chart Source File: Cantor_Growth10k_Website_".$latestFileDate.".csv");

    $growthFileKeys = array(
        "End Dt" => array_search('End Dt', $growthFileData[0]),
        "Investment Value No Load" => array_search('Investment Value No Load', $growthFileData[0]),
        "Investment Value Load" => array_search('Investment Value Load', $growthFileData[0]),
        "Portfolio Number" => array_search('Portfolio Number', $growthFileData[0]),
        "Fund Name" => array_search('Fund Name', $growthFileData[0])
    );

    $hpg_x = [];
    $hpg_y_no_load = [];
    $hpg_y_load = [];
    $hpgI_x = [];
    $hpgI_y = [];
    $hpgC_x = [];
    $hpgC_y = [];

    foreach ($growthFileData as $row) {
        if ($row[$growthFileKeys['Fund Name']] == "59003") {
            $hpg_x[] = date("m/d/Y", strtotime($row[$growthFileKeys['End Dt']]));
            $hpg_y_no_load[] = $row[$growthFileKeys['Investment Value No Load']];
            $hpg_y_load[] = $row[$growthFileKeys['Investment Value Load']];
        }
        if ($row[$growthFileKeys['Fund Name']] == "59001") {
            $hpgI_x[] = date("m/d/Y", strtotime($row[$growthFileKeys['End Dt']]));
            $hpgI_y[] = $row[$growthFileKeys['Investment Value No Load']];
        }
        if ($row[$growthFileKeys['Fund Name']] == "59005") {
            $hpgC_x[] = date("m/d/Y", strtotime($row[$growthFileKeys['End Dt']]));
            $hpgC_y[] = $row[$growthFileKeys['Investment Value No Load']];
        }
    }

    php_array_to_js([$hpg_x, $hpg_y_no_load, $hpg_y_load,], "hpgData");
    php_array_to_js([$hpgI_x, $hpgI_y], "hpgDataI");
    php_array_to_js([$hpgC_x, $hpgC_y], "hpgDataC");

    // Render the block.
    Timber::render(['./line-charts.twig'], $context);
}
