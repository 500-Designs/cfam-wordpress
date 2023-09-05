<?php
acf_register_block_type([
    'name'            => 'historical-performance',
    'title'           => __('Historical Performance', 'lucera-bootstrap-backend'),
    'description'     => __('', 'lucera-bootstrap-backend'),
    'render_callback' => 'block_render_callback_historical_performance',
    'category'        => 'section',
    // 'icon'         => 'video-alt3',
    'keywords'        => ['tables'],

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
function block_render_callback_historical_performance($block, $content = '', $is_preview = false) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    # Parse Daily CSV files


    // get the last day for each month of daily files 
    // print_r(is_dir("DailySourceFile"));

    $dailyFiles = preg_grep('~^Cantor_Daily_Website_.*\.csv$~', scandir(ABSPATH . "DailySourceFile/"));

    $fileDates = [];
    foreach ($dailyFiles as $file) {
        $parsed = end((explode('Cantor_Daily_Website_', $file, 2)));
        $parsed = strtok($parsed, '.csv');
        array_push($fileDates, $parsed);
    }
    sort($fileDates);

    $yearMonths = [];
    foreach ($fileDates as $date) {
        $dateValue = strtotime($date);
        $year = date("Y", $dateValue) . "";
        $month = date("m", $dateValue) . "";
        $yearMonths[$year . $month] = [];
    }

    $lastDaysOfMonths = [];
    foreach ($fileDates as $date) {
        $dateValue = strtotime($date);
        $year = date("Y", $dateValue) . "";
        $month = date("m", $dateValue) . "";
        $yearMonth = $year . $month;
        array_push($yearMonths[$yearMonth], $date);
    }
    foreach ($yearMonths as $yearMonth) {
        sort($yearMonth);
        array_push($lastDaysOfMonths, end($yearMonth));
    }

    $NavBarChartFileSources = [];
    $xAxis = [];
    $yAxisLeft = [];
    $yAxisRight = [];

    $xAxisC = [];
    $yAxisLeftC = [];
    $yAxisRightC = [];

    $xAxisI = [];
    $yAxisLeftI = [];
    $yAxisRightI = [];


    function processFundData($fundData, $dateData, $date) {

        $keys = array(
            "fund_number" => array_search('fund_number', $dateData[0]),
            "date" => array_search('date', $dateData[0]),
            "NAV" => array_search('NAV', $dateData[0]),
            "Annualized_Distribution_Yield" => array_search('Annualized_Distribution_Yield', $dateData[0]),
        );

        $fundData = $fundData[0];


        $parsedDate = getDayMonthYear($fundData[$keys["date"]]);
        $day = strval(intval($parsedDate['day']));
        $month = strval(intval($parsedDate['month']));
        $year = $parsedDate['year'];
        $formattedDate = $month . "/" . $day . "/" . $year;

        
        $nav_per_share = preg_replace('/[^A-Za-z0-9. -]/', '', $fundData[$keys["NAV"]]);

        // echo $date . " | " . $formattedDate . " | " . $nav_per_share . " | ";
        // echo $fundData[$keys["fund_number"]];
        // echo "<hr/>";

        if ($keys["Annualized_Distribution_Yield"]) {
            $gady_per_share = intval($fundData[$keys["Annualized_Distribution_Yield"]]);
        } else {
            $gady_per_share = 0;
        }

        return [$formattedDate, $nav_per_share, $gady_per_share];

    };

    foreach ($lastDaysOfMonths as $key => $date) {
        $dateData = array_map('str_getcsv', file(ABSPATH . "DailySourceFile/Cantor_Daily_Website_" . $date . ".csv"));

        $fund_A_row = filterByColumnName($dateData, 'fund_number', '59003');
        $fund_C_row = filterByColumnName($dateData, 'fund_number', '59005');
        $fund_I_row = filterByColumnName($dateData, 'fund_number', '59001');

        if (!empty($fund_A_row)) {
            $data = processFundData($fund_A_row, $dateData, $date);
            array_push($xAxis, $data[0]);
            array_push($yAxisLeft, $data[1]);
            array_push($yAxisRight, $data[2]);
        }
        if (!empty($fund_C_row)) {
            processFundData($fund_C_row, $dateData, $date);
            array_push($xAxisC, $data[0]);
            array_push($yAxisLeftC, $data[1]);
            array_push($yAxisRightC, $data[2]);
        }
        if (!empty($fund_I_row)) {
            processFundData($fund_I_row, $dateData, $date);
            array_push($xAxisI, $data[0]);
            array_push($yAxisLeftI, $data[1]);
            array_push($yAxisRightI, $data[2]);
        }

        array_push($NavBarChartFileSources, "Cantor_Daily_Website_" . $date . ".csv");
    }

    php_array_to_js([$xAxis, $yAxisLeft, $yAxisRight, $NavBarChartFileSources], "ndhData");
    php_array_to_js([$xAxisC, $yAxisLeftC, $yAxisRightC], "ndhDataC");
    php_array_to_js([$xAxisI, $yAxisLeftI, $yAxisRightI], "ndhDataI");
    
    # Parse Monthly CSV files

    $monthlyFiles = preg_grep('~^CANTORSIFMONTHLY_.*\.csv$~', scandir(ABSPATH . "DailySourceFile/"));

    $fileSources = [];
    $yearlyDataAtNav = [];
    $yearlyDataAtOffer = [];
    $yearlyDataC = [];
    $yearlyDataI = [];
    $month = [];
    $fileDates = [];
    $yearMonths = [];
    $latestYearUploaded = 0;
    $additionalYearToShow = 0;


    foreach ($monthlyFiles as $file) {
        $parsed = end((explode('CANTORSIFMONTHLY_', $file, 2)));
        $parsed = strtok($parsed, '.csv');
        array_push($fileDates, $parsed);
    }
    sort($fileDates);

    foreach ($fileDates as $date) {
        $dateValue = strtotime($date);
        $year = date("Y", $dateValue) . "";
        $month = date("m", $dateValue) . "";
        $yearMonths[$year . $month] = [];
        if ($latestYearUploaded < $year) {
            $latestYearUploaded = $year;
        }
        // add another year if current year has value for December
        if ($latestYearUploaded == $year && $month == '12') {
            $additionalYearToShow = $latestYearUploaded + 1;
            $yearlyDataAtNav[$additionalYearToShow] = [];
            $yearlyDataAtOffer[$additionalYearToShow] = [];
        }
    }



    foreach ($fileDates as $key => $date) {
        $fileData = array_map('str_getcsv', file(ABSPATH . "DailySourceFile/CANTORSIFMONTHLY_" . $date . ".csv"));
        array_push($fileSources, "CANTORSIFMONTHLY_" . $date . ".csv");

        $keys = array(
            "Report Date" => array_search('Report Date', $fileData[0]),
            "Inception_Date" => array_search('Inception_Date', $fileData[0]),
            "Index Name" => array_search('Index Name', $fileData[0]),

            "R_Fd_1_Month_NoLoad" => array_search('R_Fd_1_Month_NoLoad', $fileData[0]),
            "R_Fd_3_Month_NoLoad" => array_search('R_Fd_3_Month_NoLoad', $fileData[0]),
            "R_Fd_CYTD_NoLoad" => array_search('R_Fd_CYTD_NoLoad', $fileData[0]),

            "R_Fd_1_Month_Load" => array_search('R_Fd_1_Month_Load', $fileData[0]),
            "R_Fd_3_Month_Load" => array_search('R_Fd_3_Month_Load', $fileData[0]),
            "R_Fd_CYTD_Load" => array_search('R_Fd_CYTD_Load', $fileData[0]),

        );

        $reportDate = getDayMonthYear($fileData[1][$keys["Report Date"]]);
        $month = $reportDate['month'];
        $year = $reportDate['year'];

        $fund_A_row = filterByFundNo($fileData, '59003');
        $fund_C_row = filterByFundNo($fileData, '59005');
        $fund_I_row = filterByFundNo($fileData, '59001');

        $R_Fd_1_Month_NoLoad = $fund_A_row[0][$keys["R_Fd_1_Month_NoLoad"]];
        $R_Fd_3_Month_NoLoad = $fund_A_row[0][$keys["R_Fd_3_Month_NoLoad"]];
        $R_Fd_CYTD_NoLoad = $fund_A_row[0][$keys["R_Fd_CYTD_NoLoad"]];

        $R_Fd_1_Month_Load = $fund_A_row[0][$keys["R_Fd_1_Month_Load"]];
        $R_Fd_3_Month_Load = $fund_A_row[0][$keys["R_Fd_3_Month_Load"]];
        $R_Fd_CYTD_Load = $fund_A_row[0][$keys["R_Fd_CYTD_Load"]];

        $R_Fd_1_Month_C = $fund_C_row[0][$keys["R_Fd_1_Month_Load"]];
        $R_Fd_3_Month_C = $fund_C_row[0][$keys["R_Fd_3_Month_Load"]];
        $R_Fd_CYTD_C = $fund_C_row[0][$keys["R_Fd_CYTD_Load"]];

        $R_Fd_1_Month_I = $fund_I_row[0][$keys["R_Fd_1_Month_Load"]];
        $R_Fd_3_Month_I = $fund_I_row[0][$keys["R_Fd_3_Month_Load"]];
        $R_Fd_CYTD_I = $fund_I_row[0][$keys["R_Fd_CYTD_Load"]];

        $yearlyDataAtNav[$year][$month] = $R_Fd_1_Month_NoLoad;
        $yearlyDataAtOffer[$year][$month] = $R_Fd_1_Month_Load;
        $yearlyDataC[$year][$month] = $R_Fd_1_Month_C;
        $yearlyDataI[$year][$month] = $R_Fd_1_Month_I;

        $yearlyDataAtNav[$year]["ytd"] = $R_Fd_CYTD_NoLoad;
        $yearlyDataAtOffer[$year]["ytd"] = $R_Fd_CYTD_Load;
        $yearlyDataC[$year]["ytd"] = $R_Fd_CYTD_C;
        $yearlyDataI[$year]["ytd"] = $R_Fd_CYTD_I;


        if ($month == "03") {
            $yearlyDataAtNav[$year]["q1"] = $R_Fd_3_Month_NoLoad;
            $yearlyDataAtOffer[$year]["q1"] = $R_Fd_3_Month_Load;
            $yearlyDataC[$year]["q1"] = $R_Fd_3_Month_C;
            $yearlyDataI[$year]["q1"] = $R_Fd_3_Month_I;
        }
        if ($month == "06") {
            $yearlyDataAtNav[$year]["q2"] = $R_Fd_3_Month_NoLoad;
            $yearlyDataAtOffer[$year]["q2"] = $R_Fd_3_Month_Load;
            $yearlyDataC[$year]["q2"] = $R_Fd_3_Month_C;
            $yearlyDataI[$year]["q2"] = $R_Fd_3_Month_I;
        }
        if ($month == "09") {
            $yearlyDataAtNav[$year]["q3"] = $R_Fd_3_Month_NoLoad;
            $yearlyDataAtOffer[$year]["q3"] = $R_Fd_3_Month_Load;
            $yearlyDataC[$year]["q3"] = $R_Fd_3_Month_C;
            $yearlyDataI[$year]["q3"] = $R_Fd_3_Month_I;
        }
        if ($month == "12") {
            $yearlyDataAtNav[$year]["q4"] = $R_Fd_3_Month_NoLoad;
            $yearlyDataAtOffer[$year]["q4"] = $R_Fd_3_Month_Load;
            $yearlyDataC[$year]["q4"] = $R_Fd_3_Month_C;
            $yearlyDataI[$year]["q4"] = $R_Fd_3_Month_I;
        }
    }

    script_console_log("Monthly Total Returns by Share Class source files: ");
    script_console_log($fileSources);

    php_array_to_js([$yearlyDataAtNav, $yearlyDataAtOffer, $yearlyDataC, $yearlyDataI], "shareClassTableData");

    // Initialize an array to store years
    // Render the block.
    Timber::render(['./historical-performance.twig'], $context);
}


function filterRowsByFundNo($fileData, $fundNo) {
    $filteredRows = array_filter($fileData, function ($row) use ($fundNo) {
        return $row['Fund No'] === $fundNo;
    });

    return array_values($filteredRows);
}
