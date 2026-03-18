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

abstract class AbstractReportPage
{
    /**
     * reference of the template object
     * @var template
     */
    protected $tplObj;

    public function __construct()
    {
        $this->initTemplate();
    }

    public function initTemplate(): void
    {
        if (isset($this->tplObj))
        {
            return;
        }

        $this->tplObj = new template();
        list($tpl_dir) = $this->tplObj->getTemplateDir();

        $this->tplObj->setTemplateDir($tpl_dir . "/game");
    }

    protected function printMessage($msg, $redirect_buttons = null, $redirect = null, $full = true): void
    {
        $this->assign([
            'message'         => $msg,
            'redirectButtons' => $redirect_buttons,
        ]);

        if (isset($redirect))
        {
            $this->tplObj->gotoside($redirect[0], $redirect[1]);
        }

        $this->display('error.default.tpl');
    }

    protected function assign($array, $not_cache = true): void
    {
        $this->tplObj->assign_vars($array, $not_cache);
    }

    protected function display($file): void
    {
        global $LNG;

        $this->assign([
            'lang' => $LNG->getLanguage(),
        ]);

        $this->assign([
            'dpath' => './styles/theme/',
            'LNG'   => $LNG,
        ], false);

        $this->tplObj->display('extends:layout.battlereport.tpl|' . $file);
        exit;
    }

    protected function redirectTo($url): void
    {
        HTTP::redirectTo($url);
        exit;
    }
}
