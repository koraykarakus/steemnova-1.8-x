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

class ShowLostPasswordPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $uni_sel = $this->getUniverseSelector();

        $this->assign([
            'universeSelect' => $uni_sel,
        ]);

        $this->display('page.lostPassword.default.tpl');
    }

    public function newPassword(): void
    {
        global $LNG;
        $uid = HTTP::_GP('u', 0);
        $val_key = HTTP::_GP('k', '');

        $db = Database::get();

        $sql = "SELECT COUNT(*) as state FROM %%LOSTPASSWORD%% 
        WHERE user_id = :user_id AND `key` = :validation_key AND `time` > :time AND has_changed = 0;";

        $is_valid = $db->selectSingle($sql, [
            ':user_id'        => $uid,
            ':validation_key' => $val_key,
            ':time'           => (TIMESTAMP - 1800),
        ], 'state');

        if (empty($is_valid))
        {
            $this->printMessage($LNG['passwordValidInValid'], [[
                'label' => $LNG['passwordBack'],
                'url'   => 'index.php',
            ]]);
        }

        $new_pass = uniqid();

        $sql = "SELECT username, email_2 as mail, universe FROM %%USERS%% WHERE id = :userID;";

        $user_data = $db->selectSingle($sql, [
            ':userID' => $uid,
        ]);

        $config = Config::get($user_data['universe']);

        $mail_raw = $LNG->getTemplate('email_lost_password_changed');
        $mail_content = str_replace([
            '{USERNAME}',
            '{GAMENAME}',
            '{GAMEMAIL}',
            '{PASSWORD}',
        ], [
            $user_data['username'],
            $config->game_name.' - '.$config->uni_name,
            $config->smtp_sendmail,
            $new_pass,
        ], $mail_raw);

        $sql = "UPDATE %%USERS%% SET password = :newPassword WHERE id = :userID;";

        $db->update($sql, [
            ':userID'      => $uid,
            ':newPassword' => PlayerUtil::cryptPassword($new_pass),
        ]);

        if (!empty($config->smtp_host))
        {
            require 'includes/classes/Mail.class.php';
            $subject = sprintf($LNG['passwordChangedMailTitle'], $config->game_name);
            Mail::send($user_data['mail'], $user_data['username'], $subject, $mail_content);
        }
        else
        {
            $this->printMessage(nl2br($mail_content), [[
                'label' => $LNG['passwordNext'],
                'url'   => 'index.php',
            ]]);
        }

        $sql = "UPDATE %%LOSTPASSWORD%% SET has_changed = 1 
        WHERE user_id = :user_id AND `key` = :validation_key;";
        $db->update($sql, [
            ':user_id'        => $uid,
            ':validation_key' => $val_key,
        ]);

        $this->printMessage($LNG['passwordChangedMailSend'], [[
            'label' => $LNG['passwordNext'],
            'url'   => 'index.php',
        ]]);
    }

    public function send(): void
    {
        global $LNG;
        $user_name = HTTP::_GP('username', '', UTF8_SUPPORT);
        $mail = HTTP::_GP('mail', '', true);

        $error_arr = [];

        if (empty($user_name))
        {
            $error_arr[] = $LNG['passwordUsernameEmpty'];
        }

        if (empty($mail))
        {
            $error_arr[] = $LNG['passwordErrorMailEmpty'];
        }

        $config = Config::get();

        if ($config->google_recaptcha_active == 1)
        {
            require('includes/libs/reCAPTCHA/autoload.php');

            $recaptcha = new \ReCaptcha\ReCaptcha($config->google_recaptcha_private_key);
            $resp = $recaptcha->verify(HTTP::_GP('g-recaptcha-response', ''), Session::getClientIp());
            if (!$resp->isSuccess())
            {
                $error_arr[] = $LNG['registerErrorCaptcha'];
            }
        }

        if (!empty($error_arr))
        {
            $message = implode("<br>\r\n", $error_arr);
            $this->printMessage($message, [[
                'label' => $LNG['passwordBack'],
                'url'   => 'index.php?page=lostPassword',
            ]]);
        }

        $db = Database::get();

        $sql = "SELECT id FROM %%USERS%% 
        WHERE universe = :universe AND username = :username AND email_2 = :mail;";

        $uid = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':username' => $user_name,
            ':mail'     => $mail,
        ], 'id');

        if (empty($uid))
        {
            $this->printMessage($LNG['passwordErrorUnknown'], [[
                'label' => $LNG['passwordBack'],
                'url'   => 'index.php?page=lostPassword',
            ]]);
        }

        $sql = "SELECT COUNT(*) as state FROM %%LOSTPASSWORD%% 
        WHERE user_id = :user_id AND time > :time AND has_changed = 0;";

        $has_changed = $db->selectSingle($sql, [
            ':user_id' => $uid,
            ':time'    => (TIMESTAMP - 86400),
        ], 'state');

        if (!empty($has_changed))
        {
            $this->printMessage($LNG['passwordErrorOnePerDay'], [[
                'label' => $LNG['passwordBack'],
                'url'   => 'index.php?page=lostPassword',
            ]]);
        }

        $val_key = md5(uniqid());

        $mail_raw = $LNG->getTemplate('email_lost_password_validation');

        $mail_content = str_replace([
            '{USERNAME}',
            '{GAMENAME}',
            '{VALIDURL}',
        ], [
            $user_name,
            $config->game_name.' - '.$config->uni_name,
            HTTP_PATH.'index.php?page=lostPassword&mode=newPassword&u='.$userID.'&k='.$val_key,
        ], $mail_raw);

        if (!empty($config->smtp_host))
        {
            require 'includes/classes/Mail.class.php';
            $subject = sprintf($LNG['passwordValidMailTitle'], $config->game_name);
            Mail::send($mail, $user_name, $subject, $mail_content);
        }
        else
        {
            $validurl = HTTP_PATH .
            'index.php?page=lostPassword&mode=newPassword&u='.$uid.'&k=' .
            $val_key;

            echo '<meta http-equiv="refresh" content="0; url='.$validurl.'"/>';
        }

        $sql = "INSERT INTO %%LOSTPASSWORD%% 
        SET user_id = :user_id, `key` = :validation_key, `time` = :timestamp, from_ip = :remote_addr;";
        $db->insert($sql, [
            ':user_id'        => $uid,
            ':timestamp'      => TIMESTAMP,
            ':validation_key' => $val_key,
            ':remote_addr'    => Session::getClientIp(),
        ]);

        $this->printMessage($LNG['passwordValidMailSend'], [[
            'label' => $LNG['passwordNext'],
            'url'   => 'index.php',
        ]]);
    }
}
