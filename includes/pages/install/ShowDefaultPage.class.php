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

class ShowDefaultPage extends AbstractInstallPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
        $this->initTemplate();
    }

    public function show(): void
    {
        global $LNG;
        $this->assign([
            'intro_text'    => $LNG['intro_text'],
            'intro_welcome' => $LNG['intro_welcome'],
            'intro_install' => $LNG['intro_install'],
        ]);
        $this->display('ins_intro.tpl');
    }
}
