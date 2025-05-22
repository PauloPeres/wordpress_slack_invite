<?php
$GLOBALS['wp_options'] = [];

function get_option($name) {
    return isset($GLOBALS['wp_options'][$name]) ? $GLOBALS['wp_options'][$name] : false;
}

function update_option($name, $value) {
    $GLOBALS['wp_options'][$name] = $value;
    return true;
}
