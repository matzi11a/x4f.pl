<?php
/**
 * HttpRequest - make HTTP requests with curl
 *
 * @package HttpRequest
 * @author Ed Eliot
 **/
class HttpRequest {
    /**
     * Instance of class - used to implement Singleton design pattern
     *
     * @var object
     **/
    static protected $instance = null;

    /**
     * Default CURL request options
     *
     * @var array
     **/
    protected $requestOptions = array(
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_TIMEOUT => 10, // 10 seconds
        CURLOPT_CONNECTTIMEOUT => 5, // 5 seconds
        CURLOPT_DNS_CACHE_TIMEOUT => 86400 // 1 day
    );

    /**
     * Get an instance of the class - used to implement the singleton design patter
     *
     * @return object
     * @author Ed Eliot
     **/
    public static function get_instance() {
        if (is_null(self::$instance)) {
            return new HttpRequest();
        }

        return self::$instance;
    }

    /**
     * Make an HTTP GET request
     *
     * @param string $url URL to request
     * @return string
     * @author Ed Eliot
     **/
    public function get_http($url) {
        // TODO: Implement caching with memcached
        $curl = curl_init($url);

        curl_setopt_array($curl, $this->requestOptions);

        if (!($result = curl_exec($curl)) && curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            $result = false;
        }

        curl_close($curl);

        return $result;
    }
}
