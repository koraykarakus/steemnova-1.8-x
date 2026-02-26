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

class ShowDisclaimerPage extends AbstractLoginPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $config = Config::get();
        $this->assign([
            'disclaimer_address' => makebr($config->disclamerAddress),
            'disclaimer_phone'   => $config->disclamerPhone,
            'disclaimer_mail'    => $config->disclamerMail,
            'disclaimer_notice'  => $config->disclamerNotice,
        ]);

        $this->display('page.disclaimer.default.tpl');
    }
}
