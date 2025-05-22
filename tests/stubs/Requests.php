<?php
class Requests {
    public static $requests = [];
    public static $responses = [];

    public static function addResponse($body) {
        self::$responses[] = (object)['body' => $body];
    }

    public static function post($url, $headers = [], $data = [], $options = []) {
        self::$requests[] = [
            'url' => $url,
            'headers' => $headers,
            'data' => $data,
            'options' => $options,
        ];
        if (self::$responses) {
            return array_shift(self::$responses);
        }
        return (object)['body' => ''];
    }

    public static function reset() {
        self::$requests = [];
        self::$responses = [];
    }
}
