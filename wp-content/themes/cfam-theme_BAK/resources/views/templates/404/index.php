<?php
$context = Timber::context();
$previous = 'javascript:history.go(-1)';
if (isset($_SERVER['HTTP_REFERER'])) {
    $previous = $_SERVER['HTTP_REFERER'];
}
$context['previous'] = $previous;
Timber::render(['./index.twig'], $context);
