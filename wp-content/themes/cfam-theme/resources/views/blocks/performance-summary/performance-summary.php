<?php
acf_register_block_type([
    'name'            => 'performance-summary',
    'title'           => __('Performance Summary', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback' => 'block_render_callback_performance_summary',
    'category'        => 'section',
    // 'icon'         => 'video-alt3',
    'keywords'        => ['list', 'summary'],

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
function block_render_callback_performance_summary($block, $content = '', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;


    // START: get latest daily file
    $dailyFiles = preg_grep('~^Cantor_Daily_Website_.*\.csv$~', scandir(ABSPATH . "DailySourceFile/"));

    $fileDates = [];
    foreach ($dailyFiles as $file) {
        $parsed = end((explode('Cantor_Daily_Website_', $file, 2)));
        $parsed = strtok($parsed, '.csv');
        array_push($fileDates, $parsed);
    }
    sort($fileDates);
    $latestDailyFile = ABSPATH . "DailySourceFile/Cantor_Daily_Website_" . end($fileDates) . ".csv";
    // END: get latest daily file

    script_console_log("Latest daily file: " . str_replace('DailySourceFile/', '', $latestDailyFile));
    $daily_data = array_map('str_getcsv', file($latestDailyFile));
    $row1_daily = $daily_data[1];

    // START: get latest monthly file
    $monthlyFiles = preg_grep('~^CANTORSIFMONTHLY_.*\.csv$~', scandir(ABSPATH . "DailySourceFile/"));
    $fileDates = [];
    foreach ($monthlyFiles as $file) {
        $parsed = end((explode('CANTORSIFMONTHLY_', $file, 2)));
        $parsed = strtok($parsed, '.csv');
        array_push($fileDates, $parsed);
    }
    sort($fileDates);
    $latestMonthlyFile = ABSPATH . "DailySourceFile/CANTORSIFMONTHLY_" . end($fileDates) . ".csv";
    // END: get latest monthly file

    $latest_monthly_csv = array_map('str_getcsv', file($latestMonthlyFile));
    script_console_log("Latest monthly file: " . str_replace('DailySourceFile/', '', $latestMonthlyFile));
    $row1_monthly = $latest_monthly_csv[1];

    $context['temp_array'] = $fileDates;

    $monthlyFileKeys = array(
        "Report Date" => array_search('Report Date', $latest_monthly_csv[0]),
        "Inception_Date" => array_search('Inception_Date', $latest_monthly_csv[0]),
        "R_Fd_ITD_Cum_NoLoad" => array_search('R_Fd_ITD_Cum_NoLoad', $latest_monthly_csv[0]),
        "R_Fd_ITD_Cum_Load" => array_search('R_Fd_ITD_Cum_Load', $latest_monthly_csv[0])
    );
    php_array_to_js($row1_daily[0], "dailyReportDate");

    // At NAV - Annualized Return Since Inception
    $R_Fd_ITD_Cum_NoLoad = $row1_monthly[$monthlyFileKeys['R_Fd_ITD_Cum_NoLoad']];
    $report_date = $row1_monthly[$monthlyFileKeys['Report Date']];
    $inception_date = $row1_monthly[$monthlyFileKeys['Inception_Date']];

    $check_date = new DateTime($report_date);
    $referenceDate = new DateTime('2023-06-01');

    if ($check_date >= $referenceDate) {
        $context['aYearActive'] = true;
        $arsiApplicable = true;
    } else {
        $context['aYearActive'] = false;
        $arsiApplicable = false;
        script_console_log("Latest monthly report date is earlier than June 2023, hence ANNUALIZED RETURN SINCE INCEPTION is set to N/A");
    }

    // Final COMPUTED VALUES to pass to front-end content
    /*
    ANNUALIZED RETURN SINCE INCEPTION: Right now this is manually set to show “N/A” 
    as the Ultimus team advised we cannot show this data until the fund has been active for one year. 
    If the fund report date on Latest monthly file has value of June 2023 then value using the calculations below would replace “N/A”
    */
    script_console_log("Performance Summary items source: Latest monthly & daily file");

    // Get daily data per fund
    $dailyData = array_map('str_getcsv', file($latestDailyFile));
    $funds = array();
    $header = $dailyData[0]; // Get the header row
    $dataRows = array_slice($dailyData, 1); // Get the data rows
    foreach ($dataRows as $row) {
        $record = array_combine($header, $row); // Combine header and row into an associative array
        $fundNumber = $record['fund_number'];
        if (!isset($funds[$fundNumber])) {
            $funds[$fundNumber] = array();
        }
        $funds[$fundNumber]['NAV'] = $record['NAV'];
        $funds[$fundNumber]['Annualized_Distribution_Yield'] = $record['Annualized_Distribution_Yield'];
    }
    $psDailyData = array();
    foreach ($funds as $fundNumber => $fundData) {
        $fundKey = 'f_' . $fundNumber;
        $psDailyData[$fundKey] = $fundData;
    }
    php_array_to_js($psDailyData, "psDailyData");

    // Get monthly data per fund
    $monthlyData = array_map('str_getcsv', file($latestMonthlyFile));
    // Extract the header row
    $header = array_shift($monthlyData);
    // Get the column indices based on column names
    $columns = array_flip($header);
    // Initialize the result array
    $psMonthlyData = array();
    // Process each row of the CSV data
    foreach ($monthlyData as $row) {
        // Extract the required column values
        $report_date = $row[$columns['Report Date']];
        $class_id = $row[$columns['Class ID']];
        $fund_no = $row[$columns['Fund No']];
        $R_Fd_ITD_Cum_NoLoad = $row[$columns['R_Fd_ITD_Cum_NoLoad']];
        $R_Fd_ITD_Cum_Load = $row[$columns['R_Fd_ITD_Cum_Load']];
        $inception_date = $row[$columns['Inception_Date']];

        // Generate the array key based on fund number and load type
        $array_key = 'f_' . $fund_no . (($class_id == 1) ? '_load' : '');

        // Calculate the ITD and ARSI values based on load type
        if ($class_id == 1) {
            $psMonthlyData[$array_key]['itd'] = $R_Fd_ITD_Cum_Load;
            $psMonthlyData[$array_key]['arsi'] = get_has_load_arsi($R_Fd_ITD_Cum_Load, $inception_date, $report_date);
        } else {
            $psMonthlyData[$array_key]['itd'] = $R_Fd_ITD_Cum_NoLoad;
            $psMonthlyData[$array_key]['arsi'] = get_no_load_arsi($R_Fd_ITD_Cum_NoLoad, $inception_date, $report_date);
        }

        // Create the non-load type key if it doesn't exist
        if (!isset($psMonthlyData['f_' . $fund_no])) {
            $psMonthlyData['f_' . $fund_no] = array(
                'itd' => $R_Fd_ITD_Cum_NoLoad,
                'arsi' => get_no_load_arsi($R_Fd_ITD_Cum_NoLoad, $inception_date, $report_date)
            );
        }
    }

    php_array_to_js($context['fields']['summary_items']['annualized_distribution_rate_nav']['default_adr'], "psDefaultADR");
    php_array_to_js($context['fields']['summary_items']['annualized_distribution_rate_nav']['default_adr_other'], "psDefaultADROther");
    php_array_to_js($context['fields']['summary_items']['annualized_distribution_rate_nav']['force_default_adr'], "forceDefaultAdr");
    php_array_to_js($psMonthlyData, "psMonthlyData");
    php_array_to_js($arsiApplicable, "arsiApplicable");

    // Render the block.
    Timber::render(['./performance-summary.twig'], $context);
}

function isCurrentYear($string) {
    // $text = end((explode('Cantor_Daily_Website_', $string, 2)));
    // return 'testing';
    // return strtok( $text, '.csv' );
    if (str_contains($string, "2023")) {
        return $string;
    }
}

function dateDifference($date_1, $date_2, $differenceFormat = '%r%a') {
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);

    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($differenceFormat);
}

function get_row_for_CAfix($data) {
    // Loop through each row in the data array
    foreach ($data as $row) {
        // If the nasdaq_symbol value is "CAFIX", return the row array
        if ($row[3] == "CAFIX") {
            return $row; // return the entire row
        }
    }
    // If no row with nasdaq_symbol = "CAFIX" was found, return null
    return null;
}

function validateDate($date, $format = 'Y-m-d') {
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

function get_no_load_arsi($R_Fd_ITD_Cum_NoLoad, $inception_date, $report_date) {
    // Check if $R_Fd_ITD_Cum_NoLoad is a valid numeric value
    if (!is_numeric($R_Fd_ITD_Cum_NoLoad)) {
        return "Error: Invalid value for R_Fd_ITD_Cum_NoLoad.";
    }
    // // Check if $inception_date and $report_date are valid date formats
    // if (!validateDate($inception_date) || !validateDate($report_date)) {
    //     return "Error: Invalid date format.";
    // }

    $arsi = 1 + $R_Fd_ITD_Cum_NoLoad / 100;
    $days = dateDifference($inception_date, $report_date);
    $arsi = pow($arsi, ($days / 365)) - 1;
    $arsi = $arsi * 100;
    $arsi = number_format($arsi, 2, '.', '');
    return $arsi;
}

function get_has_load_arsi($R_Fd_ITD_Cum_Load, $inception_date, $report_date) {
    $arsi = 1 + $R_Fd_ITD_Cum_Load / 100;
    $arsi = pow($arsi, (365 / dateDifference($inception_date, $report_date))) - 1;
    $arsi = $arsi * 100;
    $arsi = number_format($arsi, 2, '.', '');
    return $arsi;
}
