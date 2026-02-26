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

abstract class AbstractAdminPage
{
    /**
     * reference of the template object
     * @var template
     */
    protected $tplObj;

    /** @var string $window */
    protected $window;

    protected function __construct()
    {
        if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__)))
        {
            throw new Exception("Permission error!");
        }

        if (!AJAX_REQUEST)
        {
            $this->setWindow('full');
            $this->initTemplate();
        }
        else
        {
            $this->setWindow('ajax');
        }
    }

    protected function initTemplate(): void
    {
        if (isset($this->tplObj))
        {
            return;
        }

        $this->tplObj = new template();
        list($tplDir) = $this->tplObj->getTemplateDir();

        $this->tplObj->setTemplateDir($tplDir. '/adm');
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

    protected function getPageData(): void
    {
        global $USER;

        $universe_select = [];
        foreach (Universe::availableUniverses() as $uni_id)
        {
            $config = Config::get($uni_id);
            $universe_select[$uni_id] = sprintf('%s (ID: %d)', $config->uni_name, $uni_id);
        }

        $sql = "SELECT COUNT(*) as count FROM %%TICKETS%% 
        WHERE universe = :universe AND status = 0;";

        $number_tickets = Database::get()->selectSingle($sql, [
            ':universe' => Universe::getEmulated(),
        ], 'count');

        $this->assign([
            'title'         => 'pageTitle',
            'authlevel'     => $USER['authlevel'],
            'AvailableUnis' => $universe_select,
            'UNI'           => Universe::getEmulated(),
            'sid'           => session_id(),
            'id'            => $USER['id'],
            'supportticks'  => $number_tickets,
            'currentPage'   => HTTP::_GP('page', ''),
            'search'        => HTTP::_GP('search', ''),
        ]);
    }

    protected function createButtonBack(): array
    {
        global $LNG;
        return $btn[] = [
            'url'   => $_SERVER['HTTP_REFERER'] ?? '',
            'label' => $LNG['uvs_back'] ?? '',
        ];
    }

    protected function printMessage($msg, $btns = null, $redirect = null, $full = true): void
    {

        $this->assign([
            'message'         => $msg,
            'redirectButtons' => $btns,
        ]);

        if (isset($redirect))
        {
            $this->tplObj->gotoside($redirect[0], $redirect[1]);
        }

        if (!$full)
        {
            $this->setWindow('popup');
        }

        $this->display('error.default.tpl');
    }

    protected function assign($array, $no_cache = true): void
    {
        $this->tplObj->assign_vars($array, $no_cache);
    }

    protected function display($file): void
    {
        global $LNG;

        if ($this->getWindow() !== 'ajax')
        {
            $this->getPageData();
        }

        $this->assign([
            'lang'       => $LNG->getLanguage(),
            'scripts'    => $this->tplObj->jsscript,
            'execscript' => implode("\n", $this->tplObj->script),
            'basepath'   => PROTOCOL.HTTP_HOST.HTTP_BASE,
            'bodyclass'  => $this->getWindow(),
        ]);

        $this->assign([
            'LNG' => $LNG,
        ], false);

        $this->tplObj->display('extends:layout.'.$this->getWindow().'.tpl|'.$file);
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
