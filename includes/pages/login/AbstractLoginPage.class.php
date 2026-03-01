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

abstract class AbstractLoginPage
{
    /**
     * reference of the template object
     * @var template
     */
    protected $tpl_obj = null;
    protected $window;
    public $default_window = 'normal';

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

    // TODO : this should only generate, not set cookie
    protected function generateCSRFToken(): string
    {

        //generate token
        $csrf_token = md5(uniqid(mt_rand(), true));

        //write in to session

        HTTP::sendCookie('csrfToken', $csrf_token, TIMESTAMP + 3600);

        return  $csrf_token;
    }

    protected function getUniverseSelector(): array
    {
        $uni_sel = [];
        foreach (Universe::availableUniverses() as $c_uni_id)
        {
            $uni_sel[$c_uni_id] = Config::get($c_uni_id)->uni_name;
        }

        return $uni_sel;
    }

    protected function initTemplate(): void
    {
        if (isset($this->tpl_obj))
        {
            return;
        }

        $this->tpl_obj = new template();
        list($tpl_dir) = $this->tpl_obj->getTemplateDir();
        $this->tpl_obj->setTemplateDir($tpl_dir.'login/');
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
        $config = Config::get();

        $this->tpl_obj->assign_vars([
            'recaptchaEnable'        => $config->capaktiv,
            'recaptchaPublicKey'     => $config->cappublic,
            'use_recaptcha_on_login' => $config->use_recaptcha_on_login,
            'gameName'               => $config->game_name,
            'mailEnable'             => $config->mail_active,
            'reg_close'              => $config->reg_closed,
            'referralEnable'         => $config->ref_active,
            'analyticsEnable'        => $config->ga_active,
            'analyticsUID'           => $config->ga_key,
            'lang'                   => $LNG->getLanguage(),
            'UNI'                    => Universe::current(),
            'VERSION'                => $config->VERSION,
            'REV'                    => substr($config->VERSION, -4),
            'languages'              => Language::getAllowedLangs(false),
            'loginInfo'              => sprintf($LNG['loginInfo'], '<a href="index.php?page=rules">'.$LNG['menu_rules'].'</a>'),
            'universeSelect'         => $this->getUniverseSelector(),
            'page'                   => HTTP::_GP('page', ''),
        ]);
    }

    protected function printMessage($msg, $redirect_btns = null, $redirect = null, $full = true): void
    {
        $this->assign([
            'message'         => $msg,
            'redirectButtons' => $redirect_btns,
        ]);

        if (isset($redirect))
        {
            $this->tpl_obj->gotoside($redirect[0], $redirect[1]);
        }

        if (!$full)
        {
            $this->setWindow('popup');
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

    // TODO : unused maybe strip
    protected function redirectPost($url, $post_fields): void
    {
        $this->assign([
            'url'        => $url,
            'postFields' => $post_fields,
        ]);

        $this->display('info_redirect_post.tpl');
    }
}
