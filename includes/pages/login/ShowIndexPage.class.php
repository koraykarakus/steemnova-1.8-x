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

class ShowIndexPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
        $this->setWindow('light');
    }

    public function parseRememberMeToken($token): array
    {
        $parts = explode(':', $token);

        if ($parts && count($parts) == 3)
        {
            return [$parts[0], $parts[1], $parts[2]];
        }
        return [];
    }

    public function show(): void
    {
        global $LNG, $config;

        $referral_id = HTTP::_GP('ref', 0);

        if (!empty($referral_id))
        {
            $this->redirectTo('index.php?page=register&referralID='.$referral_id);
        }

        $code = HTTP::_GP('code', 0);
        $login_code = false;
        if (isset($LNG['login_error_'.$code]))
        {
            $login_code = $LNG['login_error_'.$code];
        }

        $mem_email = $mem_pass = $mem_token_valid = $mem_token_sel = "";

        $mem_uni_id = Universe::current();

        if (isset($_COOKIE['remember_me']))
        {

            $token_parsed = $this->parseRememberMeToken($_COOKIE['remember_me']);

            if (!empty($token_parsed))
            {
                $sql = "SELECT * FROM %%REMEMBER_ME%% WHERE selector = :selector;";

                $token_db = Database::get()->selectSingle($sql, [
                    ':selector' => $token_parsed[1],
                ]);

                if (isset($token_db['hashed_validator'])
                    && isset($token_db['user_id'])
                    && isset($token_parsed[0])
                    && isset($token_parsed[1])
                    && isset($token_parsed[2]))
                {

                    if (password_verify($token_parsed[2], $token_db['hashed_validator']))
                    {

                        $sql = "SELECT email FROM %%USERS%% WHERE id = :userId;";

                        $mem_email = Database::get()->selectSingle($sql, [
                            ':userId' => $token_db['user_id'],
                        ], 'email');

                        $mem_pass = true;

                        $mem_uni_id = $token_parsed[0];
                        $mem_token_sel = $token_parsed[1];
                        $mem_token_valid = $token_parsed[2];
                    }
                }

            }

        }

        $this->assign([
            'code'                   => $login_code,
            'use_recaptcha_on_login' => $config->use_recaptcha_on_login,
            'csrf_token'             => $this->generateCSRFToken(),
            'mem_email'              => $mem_email,
            'mem_pass'               => $mem_pass,
            'mem_token_valid'        => $mem_token_valid,
            'mem_token_sel'          => $mem_token_sel,
            'mem_uni_id'             => $mem_uni_id,
        ]);

        $this->display('page.index.default.tpl');
    }
}
