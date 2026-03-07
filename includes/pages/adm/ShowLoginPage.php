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

/**
 *
 */
class ShowLoginPage extends AbstractAdminPage
{
    public function __construct()
    {
        global $USER;

        if ($USER['authlevel'] == AUTH_USR)
        {
            throw new Exception("Permission error!");
        }

        parent::__construct();
        $this->setWindow('login');
    }

    public function show(): void
    {
        global $USER, $config;

        $session = Session::create();
        if ($session->adminAccess == 1)
        {
            $this->redirectTo('admin.php?page=overview');
        }

        $this->assign([
            'bodyclass'                    => 'standalone',
            'username'                     => $USER['username'],
            'recaptchaEnable'              => $config->google_recaptcha_active,
            'use_recaptcha_on_admin_login' => $config->use_recaptcha_on_admin_login,
            'recaptchaPublicKey'           => $config->google_recaptcha_public_key,
        ]);

        $this->display('page.login.default.tpl');

    }

    public function validate(): void
    {
        global $USER, $LNG, $config;

        $error = [];

        $entered_password = HTTP::_GP('password', '', true);

        if (!password_verify($entered_password, $USER['password']))
        {
            $error[] = $LNG['adm_bad_password'];
        }

        if ($USER['authlevel'] != AUTH_ADM
            && $USER['authlevel'] != AUTH_MOD
            && $USER['authlevel'] != AUTH_OPS)
        {
            $error[] = $LNG['adm_bad_password'];
        }

        if ($config->google_recaptcha_active
            && $config->use_recaptcha_on_admin_login)
        {
            require('includes/libs/reCAPTCHA/src/autoload.php');

            $recaptcha = new \ReCaptcha\ReCaptcha($config->google_recaptcha_private_key);
            $resp = $recaptcha->verify(HTTP::_GP('g_recaptcha_response', ''), Session::getClientIp());
            if (!$resp->isSuccess())
            {
                $error[] = $LNG['adm_login_recaptcha_false'];
            }
        }

        if (empty($error))
        {
            $session = Session::create();
            $session->adminAccess = 1;

            $data = [];
            $data['status'] = "redirect";
            $this->sendJSON($data);
        }
        else
        {
            $error['status'] = "fail";
            $this->sendJSON($error);
        }

    }

}
