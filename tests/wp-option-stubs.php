<?php

// Global array to mimic WordPress option storage during tests.
if (!isset($GLOBALS['_wp_option_store'])) {
    $GLOBALS['_wp_option_store'] = [];
}

if (!function_exists('get_option')) {
    function get_option($name, $default = false) {
        return array_key_exists($name, $GLOBALS['_wp_option_store']) ? $GLOBALS['_wp_option_store'][$name] : $default;
    }
}

if (!function_exists('update_option')) {
    function update_option($name, $value) {
        $GLOBALS['_wp_option_store'][$name] = $value;
        return true;
    }
}

?>
