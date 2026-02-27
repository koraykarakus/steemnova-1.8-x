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

class ShowErrorPage extends AbstractGamePage
{
    public static $require_module = 0;

    protected $disable_eco_system = true;

    public function __construct()
    {
        parent::__construct();
        $this->initTemplate();
    }

    public static function printError($msg, $is_full = true, $redirect = null): void
    {
        $page_obj = new self();
        $page_obj->printMessage($msg, $is_full, $redirect);
    }

    public function show(): void
    {

    }
}
