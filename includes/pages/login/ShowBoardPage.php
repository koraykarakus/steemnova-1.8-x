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

class ShowBoardPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;
        $board_url = Config::get()->forum_url;
        if (filter_var($board_url, FILTER_VALIDATE_URL))
        {
            HTTP::sendHeader('Location', $board_url);
        }
        else
        {
            $this->printMessage($LNG['bad_forum_url']);
        }
    }
}
