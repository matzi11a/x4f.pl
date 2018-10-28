<?php

/**
 * Class X4FPLInput
 */
class X4FPLInput {
    /**
     * @param string $key
     * @param string $default
     * @param bool $stripTags
     * @return string
     */
    public static function get($key, $default = '', $stripTags = true) {
        $value = isset($_GET[$key]) ? $_GET[$key] : $default;
        return $stripTags ? strip_tags($value) : $value;
    }

    /**
     * @param string $key
     * @param string $default
     * @param bool $stripTags
     * @return string
     */
    public static function post($key, $default = '', $stripTags = true) {
        $value = isset($_POST[$key]) ? $_POST[$key] : $default;
        return $stripTags ? strip_tags($value) : $value;
    }

    /**
     * @param string $key
     * @param string $default
     * @param bool $stripTags
     * @return string
     */
    public static function request($key, $default = '', $stripTags = true) {
        $value = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
        return $stripTags ? strip_tags($value) : $value;
    }

    /**
     * @param string $key
     * @param string $default
     * @param bool $stripTags
     * @return string
     */
    public static function session($key, $default = '', $stripTags = true) {
        $value = isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        return $stripTags ? strip_tags($value) : $value;
    }

    /**
     * @param string $key
     * @param string $default
     * @param bool $stripTags
     * @return string
     */
    public static function cookie($key, $default = '', $stripTags = true) {
        $value = isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
        return $stripTags ? strip_tags($value) : $value;
    }

    /**
     * @param string $key
     * @param string $default
     * @param bool $stripTags
     * @return string
     */
    public static function server($key, $default = '', $stripTags = true) {
        $value = isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
        return $stripTags ? strip_tags($value) : $value;
    }
}
