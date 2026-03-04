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

class Universe
{
    private static $current_universe = null;
    private static $emulated_universe = null;
    private static $universe_array = [];

    /**
     * Find current universe id using cookies, get parameter or session keys.
     *
     * @return int
     */

    private static function getCurrentUniverse()
    {
        if (MODE === 'INSTALL')
        {
            // Installer are always in the first universe.
            return ROOT_UNI;
        }

        $universe = null;
        $universe_count = count(self::getAvailableUniverses());
        if ($universe_count != 1)
        {
            if (MODE == 'LOGIN')
            {
                if (isset($_COOKIE['uni']))
                {
                    $universe = (int) $_COOKIE['uni'];
                }

                if (isset($_REQUEST['uni']))
                {
                    $universe = (int) $_REQUEST['uni'];
                }
            }
            elseif (MODE == 'ADMIN' && isset($_SESSION['admin_uni']))
            {
                $universe = (int) $_SESSION['admin_uni'];
            }

            if (is_null($universe))
            {
                if (UNIS_WILDCAST)
                {
                    $temp = explode('.', $_SERVER['HTTP_HOST']);
                    $temp = substr($temp[0], 3);
                    if (is_numeric($temp))
                    {
                        $universe = $temp;
                    }
                    else
                    {
                        $universe = ROOT_UNI;
                    }
                }
                else
                {
                    if (isset($_SERVER['REDIRECT_UNI']))
                    {
                        // Apache - faster then preg_match
                        $universe = $_SERVER["REDIRECT_UNI"];
                    }
                    elseif (isset($_SERVER['REDIRECT_REDIRECT_UNI']))
                    {
                        // Patch for www.top-hoster.de - Hoster
                        $universe = $_SERVER["REDIRECT_REDIRECT_UNI"];
                    }
                    elseif (preg_match('!/uni([0-9]+)/!', HTTP_PATH, $match))
                    {
                        if (isset($match[1]))
                        {
                            $universe = $match[1];
                        }
                    }
                    else
                    {
                        $universe = ROOT_UNI;
                    }
                }

                if (!isset($universe) || !self::exists($universe))
                {
                    HTTP::redirectToUniverse(ROOT_UNI);
                }
            }
        }
        else
        {
            if (HTTP_ROOT != HTTP_BASE)
            {
                HTTP::redirectTo(PROTOCOL.HTTP_HOST.HTTP_BASE.HTTP_FILE, true);
            }

            $universe = ROOT_UNI;
        }

        return $universe;
    }

    /**
     * Return the current universe id.
     *
     * @return int
     */

    public static function current()
    {
        if (is_null(self::$current_universe))
        {
            self::$current_universe = self::getCurrentUniverse();
        }

        return self::$current_universe;
    }

    /**
     * User by Config class
     * adds config row inside universe array
     */

    public static function add($universe)
    {
        self::$universe_array[] = $universe;
    }

    public static function getEmulated()
    {
        if (is_null(self::$emulated_universe))
        {
            $session = Session::load();
            if (isset($session->emulatedUniverse))
            {
                self::setEmulated($session->emulatedUniverse);
            }
            else
            {
                self::setEmulated(self::current());
            }
        }

        return self::$emulated_universe;
    }

    public static function setEmulated($universe_id)
    {
        if (!self::exists($universe_id))
        {
            throw new Exception('Unknown universe ID: '.$universe_id);
        }

        $session = Session::load();
        $session->emulatedUniverse = $universe_id;
        $session->save();

        self::$emulated_universe = $universe_id;

        return true;
    }

    /**
     * Return an array of all universe ids
     *
     * @return array
     */

    public static function getAvailableUniverses(): array
    {
        return self::$universe_array;
    }

    /**
     * Find current universe id using cookies, get parameter or session keys.
     *
     * @param int universe id
     *
     * @return int
     */

    public static function exists($universe_id)
    {
        return in_array($universe_id, self::getAvailableUniverses());
    }
}
