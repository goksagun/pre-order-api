<?php

namespace App\Utils;

class StringHelper
{
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     * @return string
     */
    public static function random($length = 16): string
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ('' !== $needle && substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }
}