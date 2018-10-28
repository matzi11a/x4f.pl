<?php

/**
 * @param bool $validPage
 * @return bool|mixed|string
 */
function get_friendly_url_name($validPage = true) {
    if ($validPage) {
        $friendlyName = $_SERVER['REQUEST_URI'];
        $friendlyName = preg_replace('/\?.*/', '', $friendlyName);
        $friendlyName = trim($friendlyName, '/');
        $friendlyName = str_replace(array('-', '/'), array(' ', ' - '), $friendlyName);
        $friendlyName = ucwords($friendlyName);

        if (empty($friendlyName)) {
            return "Home Page";
        }

        return $friendlyName;
    }

    return false;
}

/**
 * @param string $text
 * @return mixed|string
 */
function create_slug($text) {
    setlocale(LC_ALL, 'en_US.utf8');
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $slug = trim($slug);
    $slug = preg_replace('/[^a-z0-9 -]/i', '', $slug);
    $slug = str_replace(' ', '-', $slug);
    $slug = strtolower($slug);

    return $slug;
}

/**
 * @param string $text
 * @param int $count
 * @return mixed|string
 */
function chop_text($text, $count) {
    if (strlen($text) > $count) {
        if (str_replace(' ', '', $text) == $text) {
            return substr($text, 0, $count).'&hellip;';
        } else {
            $count -= 1;
            return preg_replace("/^(.{0,$count}[^\s])\b.*$/", '$1&hellip;', $text);
        }
    }
    return $text;
}

/**
 * @param string $date
 * @param string $timezone
 * @param bool $showTime
 * @return bool|null|string
 */
function format_date($date = null, $timezone = 'Europe/London', $showTime = true) {
    date_default_timezone_set('Europe/London');
    $format = $showTime ? 'jS M Y h:ia' : 'jS M Y';
    $date = !is_null($date) ? date($format, strtotime($date)) : date($format);
    date_default_timezone_set('UTC');

    return $date;
}

/**
 * @param string $userLogin
 * @param string $userNiceName
 * @return mixed
 */
function get_preferred_user_display($userLogin, $userNiceName) {
    return trim($userNiceName) != '' ? $userNiceName : $userLogin;
}
