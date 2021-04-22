<?php


namespace App\Utils;

class JSONParser
{
    public static function parseToString(array $json): string {
        return json_encode($json, true);
    }

    public static function parseToJson(string $string): array {
        return json_decode($string, true);
    }
}
