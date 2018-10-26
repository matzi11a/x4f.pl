<?php
/**
 * Class Functions
 */
class Functions {
    /**
     * @param string $text
     * @return mixed|string
     */
    static public function create_slug($text) {
        setlocale(LC_ALL, 'en_US.utf8');
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $slug = trim($slug);
        $slug = preg_replace('/[^a-z0-9 -]/i', '', $slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = strtolower($slug);

        return $slug;
    }

    /**
     * @return string
     */
    static public function get_memory_used() {
        $size = memory_get_usage(true);
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
    }
}
