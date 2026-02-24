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

class ShowLoginPage extends AbstractLoginPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function generateRememberMeToken($universe): array
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        return [
            'selector'  => $selector,
            'validator' => $validator,
            'full'      => $universe . ':' . $selector . ':' . $validator,
        ];
    }

    public function validate(): void
    {
        global $config, $LNG;

        $db = Database::get();

        $email = HTTP::_GP('userEmail', '', true);
        $password = HTTP::_GP('password', '', true);

        $csrf_token = HTTP::_GP('csrfToken', '', true);
        $remember_me = HTTP::_GP('remember_me', 'false');

        $token_val = HTTP::_GP('rememberedTokenValidator', '');
        $token_sel = HTTP::_GP('rememberedTokenSelector', '');
        $mem_email = HTTP::_GP('rememberedEmail', '');

        $universe = HTTP::_GP('universe', Universe::current());

        $error = [];

        if (isset($_COOKIE['csrfToken'])
            && $_COOKIE['csrfToken'] != $csrf_token)
        {
            $error[] = "csrf attack";
        }

        if (empty($email))
        {
            $error[] = $LNG['login_error_1'];
        }

        if (empty($password))
        {
            $error[] = $LNG['login_error_2'];
        }

        if (!empty($password)
            && !empty($email))
        {
            $sql = "SELECT id, password FROM %%USERS%% WHERE email = :email AND universe = :universe;";

            $login_data = $db->selectSingle($sql, [
                ':email'    => $email,
                ':universe' => $universe,
            ]);

            if (!$login_data)
            {
                $error[] = $LNG['login_error_3'];
            }

        }

        //verify with password
        if (empty($token_val)
            || empty($token_sel)
            || $mem_email != $email
            || $password != 'password')
        {
            if (isset($login_data['password']))
            {
                if (!password_verify($password, $login_data['password']))
                {
                    $error[] = $LNG['login_error_5'];
                }
            }

        }
        //verify with token
        //if user type a random password
        else
        {
            $sql = "SELECT * FROM %%REMEMBER_ME%% WHERE selector = :selector;";

            $token_full = $db->selectSingle($sql, [
                ':selector' => $token_sel,
            ]);

            if (!$token_full)
            {
                $error[] = $LNG['login_error_3'];
            }

            if (isset($token_full['hashed_validator']))
            {
                if (!password_verify($token_val, $token_full['hashed_validator']))
                {
                    $error[] = $LNG['login_error_3'];
                }

                $sql = "SELECT email FROM %%USERS%% WHERE id = :userId;";

                $email_check = $db->selectSingle($sql, [
                    ':userId' => $token_full['user_id'],
                ], 'email');

                if (empty($email_check))
                {
                    $error[] = $LNG['login_error_1'];
                }

                if ($email_check != $email)
                {
                    $error[] = $LNG['login_error_3'];
                }

            }

        }

        if ($config->capaktiv === '1'
            && $config->use_recaptcha_on_login)
        {
            require('includes/libs/reCAPTCHA/src/autoload.php');

            $recaptcha = new \ReCaptcha\ReCaptcha($config->capprivate);
            $resp = $recaptcha->verify(HTTP::_GP('g_recaptcha_response', ''), Session::getClientIp());
            if (!$resp->isSuccess())
            {
                $error[] = $LNG['login_error_4'];
            }
        }

        if (empty($error))
        {
            $session = Session::create();
            $session->userId = (int) $login_data['id'];
            $session->adminAccess = 0;
            $session->save();

            if ($remember_me == 'true')
            {
                $token = $this->generateRememberMeToken($universe);

                //set a cookie
                HTTP::sendCookie('remember_me', $token['full'], TIMESTAMP + 60 * 60 * 24 * 30);

                //delete old remember me data

                $sql = "DELETE FROM %%REMEMBER_ME%% WHERE `user_id` = :userId;";

                $db->delete($sql, [
                    ':userId' => (int) $login_data['id'],
                ]);

                //insert new remember data,

                $sql = "INSERT INTO %%REMEMBER_ME%% (`selector`,`hashed_validator`, `expiration_date`, `user_id`,`universe`)
				VALUES (:selector,:hashed_validator,:expiration_date,:user_id,:universe);";

                $db->insert($sql, [
                    ':selector'         => $token['selector'],
                    ':hashed_validator' => password_hash($token['validator'], PASSWORD_DEFAULT),
                    ':expiration_date'  => TIMESTAMP + 60 * 60 * 24 * 30, //30 days
                    ':user_id'          => (int) $login_data['id'],
                    ':universe'         => $universe,
                ]);

            }

            if ($remember_me == "false")
            {
                $sql = "DELETE FROM %%REMEMBER_ME%% WHERE user_id = :userId;";
                $db->delete($sql, [
                    ':userId' => (int) $login_data['id'],
                ]);
            }

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
