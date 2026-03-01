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

// Create google API console

require_once('./includes/enums/Register.class.php');
require_once('./includes/enums/Login.class.php');

class ShowGoogleAuthPage extends AbstractLoginPage
{
    public static $require_module = MODULE_AUTH_GOOGLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function callBack()
    {
        global $LNG;

        $sql = "SELECT client_id, client_secret, callback_url 
        FROM %%GOOGLE_AUTH%%
        LIMIT 1";

        $auth_info = Database::get()->selectSingle($sql, []);

        if (!$auth_info
            || empty($auth_info['client_id'])
            || empty($auth_info['client_secret'])
            || empty($auth_info['callback_url']))
        {
            $this->printMessage('Function not available yet.');
        }

        $client = new Google_Client();
        $client->setClientId(html_entity_decode($auth_info['client_id']));
        $client->setClientSecret(html_entity_decode($auth_info['client_secret']));
        $client->setRedirectUri(html_entity_decode($auth_info['callback_url']));

        $code = HTTP::_GP('code', '');
        if (empty($code))
        {
            $this->printMessage("error_1");
        }

        // Access token
        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error']))
        {
            $this->printMessage('error_2');
        }

        $client->setAccessToken($token['access_token']);

        // user info
        $oauth2 = new \Google_Service_Oauth2($client);
        $user_info = $oauth2->userinfo->get();

        if (empty($user_info->email))
        {
            $this->printMessage("No mail");
        }

        $db = Database::get();
        $sql = "SELECT * FROM %%USERS%% WHERE email = :email;";

        $user = $db->selectSingle($sql, [
            ':email' => $user_info->email,
        ]);

        if ($user === false)
        {
            $language = $LNG->getUserAgentLanguage();
            if (!in_array($language, Language::getAllowedLangs(true)))
            {
                $language = 'en';
            }
            // Register
            $service_obj = new LoginService();

            $result = $service_obj->register(
                null,
                null,
                $user_info->email,
                $language,
                null,
                $_COOKIE['csrfToken'] ?? '',
                null,
                null,
                0,
                true
            );

            if ($result == Register::register_success_no_verify)
            {
                HTTP::redirectTo($service_obj->verify_url);
            }
            // mail verification is required
            elseif ($result == Register::register_success_verify_with_mail)
            {
                $this->printMessage($LNG['reg_suc_1']);
            }
            elseif ($result == Register::register_success_verify_with_mail_existing)
            {
                $this->printMessage($LNG['reg_suc_2']);
            }

        }
        // Login
        else
        {
            $service_obj = new LoginService();
            $external_auth = true;
            $result = $service_obj->Login(
                $user['email'],
                $user['password'],
                $_COOKIE['csrfToken'] ?? '',
                null,
                null,
                null,
                $user['universe'],
                $external_auth
            );

            if ($result == Login::success)
            {
                $session = Session::create();
                $session->userId = (int) $service_obj->login_id;
                $session->adminAccess = 0;
                $session->save();
                HTTP::redirectTo('game.php');
            }

        }
    }

    public function show(): void
    {
        $sql = "SELECT client_id, client_secret, callback_url 
        FROM %%GOOGLE_AUTH%%
        LIMIT 1";

        $auth_info = Database::get()->selectSingle($sql, []);

        if (!$auth_info
            || empty($auth_info['client_id'])
            || empty($auth_info['client_secret'])
            || empty($auth_info['callback_url']))
        {
            $this->printMessage('Function not available yet.');
        }

        $client = new Google_Client();
        $client->setClientId(html_entity_decode($auth_info['client_id']));
        $client->setClientSecret(html_entity_decode($auth_info['client_secret']));
        $client->setRedirectUri(html_entity_decode($auth_info['callback_url']));
        $client->addScope('email');

        HTTP::redirectTo($client->createAuthUrl(), true);
    }

}
