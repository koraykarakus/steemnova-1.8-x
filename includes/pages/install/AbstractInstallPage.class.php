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

abstract class AbstractInstallPage
{
    /**
     * reference of the template object
     * @var template
     */
    protected $tpl_obj = null;
    protected $window;
    public $default_window = 'full';

    protected function __construct()
    {
        if (!AJAX_REQUEST)
        {
            $this->setWindow($this->default_window);
            $this->initTemplate();
        }
        else
        {
            $this->setWindow('ajax');
        }
    }

    protected function initTemplate(): void
    {
        if (isset($this->tpl_obj))
        {
            return;
        }

        $this->tpl_obj = new template();
        list($tpl_dir) = $this->tpl_obj->getTemplateDir();
        $this->tpl_obj->setTemplateDir($tpl_dir.'install/');
    }

    protected function setWindow($window): void
    {
        $this->window = $window;
    }

    protected function getWindow(): string
    {
        return $this->window;
    }

    protected function getQueryString(): string
    {
        $query_string = [];
        $page = HTTP::_GP('page', '');

        if (!empty($page))
        {
            $query_string['page'] = $page;
        }

        $mode = HTTP::_GP('mode', '');
        if (!empty($mode))
        {
            $query_string['mode'] = $mode;
        }

        return http_build_query($query_string);
    }

    // TODO: this is not getter, rename
    protected function getPageData(): void
    {
        global $LNG;

        $this->tpl_obj->assign_vars([
            'lang'       => $LNG->getLanguage(),
            'Selector'   => $LNG->getAllowedLangs(false),
            'title'      => $LNG['title_install'] . ' &bull; 2Moons',
            'header'     => $LNG['menu_install'],
            'canUpgrade' => file_exists('includes/config.php') && filesize('includes/config.php') !== 0,
        ]);
    }

    protected function printMessage($msg, $redirect_btns = null, $redirect = null, $full = true): void
    {
        $this->assign([
            'msg'           => $msg,
            'redirect_btns' => $redirect_btns,
        ]);

        if (isset($redirect))
        {
            $this->tpl_obj->gotoside($redirect[0], $redirect[1]);
        }

        $this->display('error.default.tpl');
    }

    protected function assign($array, $nocache = true): void
    {
        $this->tpl_obj->assign_vars($array, $nocache);
    }

    protected function display($file): void
    {
        global $LNG;

        if ($this->getWindow() !== 'ajax')
        {
            $this->getPageData();
        }

        if (UNIS_WILDCAST)
        {
            $host_parts = explode('.', HTTP_HOST);
            if (preg_match('/uni[0-9]+/', $host_parts[0]))
            {
                array_shift($host_parts);
            }
            $host = implode('.', $host_parts);
            $base_path = PROTOCOL.$host.HTTP_BASE;
        }
        else
        {
            $base_path = PROTOCOL.HTTP_HOST.HTTP_BASE;
        }

        $this->assign([
            'lang'            => $LNG->getLanguage(),
            'bodyclass'       => $this->getWindow(),
            'basepath'        => $base_path,
            'isMultiUniverse' => count(Universe::availableUniverses()) > 1,
            'unisWildcast'    => UNIS_WILDCAST,
        ]);

        $this->assign([
            'LNG' => $LNG,
        ], false);

        $this->tpl_obj->display('extends:layout.'.$this->getWindow().'.tpl|'.$file);
        exit;
    }

    protected function sendJSON($data): void
    {
        echo json_encode($data);
        exit;
    }

    protected function redirectTo($url): void
    {
        HTTP::redirectTo($url);
        exit;
    }
}
