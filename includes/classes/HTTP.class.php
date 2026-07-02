<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 * @link https://github.com/jkroepke/2Moons
 */

class HTTP
{
    public static function redirectTo(string $URL, bool $external = false): void
    {
        if ($external)
        {
            self::sendHeader('Location', $URL);
        }
        else
        {
            self::sendHeader('Location', HTTP_PATH.$URL);
        }
        exit;
    }

    public static function sendHeader(string $header, string $value = '')
    {
        if (!empty($value))
        {
            $header .= ": " . $value;
        }

        header($header);
    }

    public static function redirectToUniverse(int $universe): void
    {
        $uni_str = (string) $universe;
        HTTP::redirectTo(
            PROTOCOL . HTTP_HOST . HTTP_BASE . "uni" . $uni_str . "/" . HTTP_FILE,
            true
        );
    }

    public static function sendCookie(
        string $name,
        string $value = "",
        int $to_time = 0
    ): void {
        setcookie($name, $value, $to_time);
    }

    public static function _GP(
        string $name,
        mixed $default,
        bool $multibyte = false,
        bool $highnum = false
    ): mixed {
        if (!isset($_REQUEST[$name]))
        {
            return $default;
        }

        if (is_float($default)
            || $highnum)
        {
            return (float) $_REQUEST[$name];
        }

        if (is_int($default))
        {
            return (int) $_REQUEST[$name];
        }

        if (is_string($default))
        {
            return self::_quote($_REQUEST[$name], $multibyte);
        }

        if (is_array($default)
            && is_array($_REQUEST[$name]))
        {
            return self::_quoteArray($_REQUEST[$name], $multibyte, !empty($default) && $default[0] === 0);
        }

        return $default;
    }

    private static function _quoteArray(
        array $var,
        bool $multibyte,
        bool $only_numbers = false
    ): array {
        $data = [];
        foreach ($var as $key => $value)
        {
            if (is_array($value))
            {
                $data[$key] = self::_quoteArray($value, $multibyte);
            }
            elseif ($only_numbers)
            {
                $data[$key] = (int) $value;
            }
            else
            {
                $data[$key] = self::_quote($value, $multibyte);
            }
        }

        return $data;
    }

    private static function _quote(string $var, bool $multibyte): string
    {
        $var = str_replace(["\r\n", "\r", "\0"], ["\n", "\n", ''], $var);
        $var = htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        $var = trim($var);

        if ($multibyte)
        {
            if (!preg_match('/^./u', $var))
            {
                $var = '';
            }
        }
        else
        {
            $var = preg_replace('/[\x80-\xFF]/', '?', $var); // no multibyte, allow only ASCII (0-127)
        }

        return $var;
    }
}
