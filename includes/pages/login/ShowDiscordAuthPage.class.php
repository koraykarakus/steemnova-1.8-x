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

class ShowDiscordAuthPage extends AbstractLoginPage
{
    public static $require_module = MODULE_AUTH_GOOGLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function callBack()
    {
        global $LNG;

        $code = HTTP::_GP('code', '');

        if (!isset($code))
        {
            $this->printMessage('code not found !');
        }

        $sql = "SELECT client_id, client_secret, callback_url, redirect_url  
        FROM %%DISCORD_AUTH%%
        LIMIT 1";

        $auth_info = Database::get()->selectSingle($sql, []);

        if (!$auth_info
            || empty($auth_info['client_id'])
            || empty($auth_info['client_secret'])
            || empty($auth_info['callback_url']))
        {
            $this->printMessage('Function not available yet.');
        }

        $data = [
            'client_id'     => $auth_info['client_id'],
            'client_secret' => $auth_info['client_secret'],
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => html_entity_decode($auth_info['callback_url']),
            //'scope'         => 'identify email',
        ];

        // cURL send post
        $token_url = "https://discord.com/api/oauth2/token";
        $ch = curl_init($token_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            ['Content-Type: application/x-www-form-urlencoded']
        );

        $response = curl_exec($ch);
        $token_data = json_decode($response, true);

        if (!isset($token_data['access_token']))
        {
            exit('Token not found:');
        }

        $access_token = $token_data['access_token'];

        $user_url = "https://discord.com/api/users/@me";

        $ch = curl_init($user_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
        ]);

        $user_response = curl_exec($ch);

        $user_data = json_decode($user_response, true);

        if (!isset($user_data['email']))
        {
            $this->printMessage('email info not found');
        }

        $email = $user_data['email'];

        if (curl_errno($ch))
        {
            echo 'cURL error: ' . curl_error($ch);
        }

        $db = Database::get();
        $sql = "SELECT * FROM %%USERS%% WHERE email = :email;";

        $user = $db->selectSingle($sql, [
            ':email' => $email,
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
                $email,
                $language,
                null,
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
        $sql = "SELECT client_id, client_secret, redirect_url, callback_url 
        FROM %%DISCORD_AUTH%%
        LIMIT 1";

        $auth_info = Database::get()->selectSingle($sql, []);

        if (!$auth_info
            || empty($auth_info['client_id'])
            || empty($auth_info['client_secret'])
            || empty($auth_info['redirect_url'])
            || empty($auth_info['callback_url']))
        {
            $this->printMessage('Function not available yet.');
        }

        HTTP::redirectTo(html_entity_decode($auth_info['redirect_url']), true);
    }

}
