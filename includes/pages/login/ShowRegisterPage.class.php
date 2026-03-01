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

class ShowRegisterPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
        $this->setWindow('light');
    }

    public function show(): void
    {
        global $LNG, $config;
        $referral_data = ['id' => 0, 'name' => ''];
        $account_name = "";
        $referral_id = HTTP::_GP('referralID', 0);

        if ($config->ref_active == 1
            && !empty($referral_id))
        {
            $db = Database::get();

            $sql = "SELECT username FROM %%USERS%% WHERE id = :referralID AND universe = :universe;";
            $referral_account_name = $db->selectSingle($sql, [
                ':referralID' => $referral_id,
                ':universe'   => Universe::current(),
            ], 'username');

            if (!empty($referral_account_name))
            {
                $referral_data = ['id' => $referral_id, 'name' => $referral_account_name];
            }
        }

        $this->assign([
            'use_recaptcha_on_register' => $config->use_recaptcha_on_register,
            'referral_data'             => $referral_data,
            'account_name'              => $account_name,
            'register_password_desc'    => sprintf($LNG['registerPasswordDesc'], 6),
            'register_rules_desc'       => sprintf($LNG['registerRulesDesc'], '<a href="index.php?page=rules">'.$LNG['menu_rules'].'</a>'),
            'csrf_token'                => $this->generateCSRFToken(),
        ]);

        $this->display('page.register.default.tpl');
    }

    public function send(): void
    {
        $user_name = HTTP::_GP('userName', '', UTF8_SUPPORT);
        $password = HTTP::_GP('password', '', true);
        $email = HTTP::_GP('email', '', true);
        $language = HTTP::_GP('language', '');
        $rules_checked = HTTP::_GP('rules', 0);
        $csrf_token = HTTP::_GP('csrfToken', '', true);
        $user_secret_question_id = HTTP::_GP('secretQuestion', 0);
        $user_secret_question_answer = HTTP::_GP('secretQuestionAnswer', '', true);
        $referral_id = HTTP::_GP('referralID', 0);
        $external_auth = false;

        $service_obj = new LoginService();
        
        $result = $service_obj->register(
            $user_name,
            $password,
            $email,
            $language,
            $rules_checked,
            $csrf_token,
            $user_secret_question_id,
            $user_secret_question_answer,
            $referral_id,
            $external_auth
        );

        $data = $service_obj->getJsonDataByResult($result);

        $this->sendJSON($data); 
    }
}
