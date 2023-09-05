<?php

use Timber\Timber;

add_filter('timber/twig', 'add_to_twig');

function add_to_twig($twig) {
    $twig->addFunction(new \Twig\TwigFunction('get_filtered_posts', 'get_filtered_posts'));
    $twig->addFunction(new \Twig\TwigFunction('get_image_html', 'get_image_html'));
    $twig->addFunction(new \Twig\TwigFunction('safe_text', 'safe_text'));
    $twig->addFunction(new \Twig\TwigFunction('b64_encode', 'b64_encode'));

    return $twig;
}

/**
 * You can create PHP functions here to pass to Twig.
 * You can use them directly in a Twig template like this: {{ get_filtered_posts(123, 'news') }}
 */
function get_filtered_posts($id, $type) {
    $args = [
        'post_type'      => $type,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'post__not_in'   => array($id),

    ];

    return $get_filtered_posts = Timber::get_posts($args);
}


function get_image_html($id, $size = 'full', $class) {
    return wp_get_attachment_image($id, $size, '', ['class' => $class]);
}

function b64_encode($text) {
    return base64_encode($text);
}

function script_console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

function php_array_to_js($array, $name = 'phpData') {
    $js_code = "<!-- php_array_to_js " . $name . " -->";
    $js_code .= '<script>var ' . $name . ' = ' . json_encode($array) . ';</script>';
    echo $js_code;
}

add_action("wp_head", "php_array_to_js", 0);

function getDayMonthYear($dateString) {
    $date = DateTime::createFromFormat('m/d/y', $dateString);
    if (!$date) {
        $date = DateTime::createFromFormat('m/d/Y', $dateString);
    }
    if (!$date) {
        return false;
    }
    return array(
        'day' => $date->format('d'),
        'month' => $date->format('m'),
        'year' => $date->format('Y'),
    );
}

function isCSVFileTabDelimited($fileUrl) {
    $file = fopen($fileUrl, 'r');
    $firstLine = fgets($file);
    $tabCount = substr_count($firstLine, "\t");
    $commaCount = substr_count($firstLine, ",");
    fclose($file);

    if ($tabCount > $commaCount) {
        return true;
    } else {
        return false;
    }
}

function parse_csv($file, $delimiter = ",") {
    $rows = [];
    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $rows[] = $data;
        }
        fclose($handle);
    }
    return $rows;
}


function filterByFundNo($dataArray, $filterValue) {
    $filteredArray = array();
    $headers = $dataArray[0];

    foreach ($dataArray as $index => $row) {
        if ($index === 0) continue; // Skip the headers row

        $fundNoIndex = array_search('Fund No', $headers);
        if ($row[$fundNoIndex] === $filterValue) {
            $filteredArray[] = $row;
        }
    }

    return $filteredArray;
}


function filterByColumnName($dataArray, $columnName, $filterValue) {
    $filteredArray = array();
    $headers = $dataArray[0];

    foreach ($dataArray as $index => $row) {
        if ($index === 0) continue; // Skip the headers row

        $fundNoIndex = array_search($columnName, $headers);
        if ($row[$fundNoIndex] === $filterValue) {
            $filteredArray[] = $row;
        }
    }

    return $filteredArray;
}
