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

class ShowVertifyPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    private function _activeUser(): array
    {
        global $LNG;

        $validation_id = HTTP::_GP('i', 0);
        $validation_key = HTTP::_GP('k', '');

        $db = Database::get();

        $sql = "SELECT * FROM %%USERS_VALID%%
		WHERE validationID	= :validation_id
		AND validationKey	= :validation_key
		AND universe		= :universe;";

        $user_data = $db->selectSingle($sql, [
            ':validation_id'  => $validation_id,
            ':validation_key' => $validation_key,
            ':universe'       => Universe::current(),
        ]);

        if (empty($user_data))
        {
            $this->printMessage($LNG['vertifyNoUserFound']);
        }

        $config = Config::get();

        $sql = "DELETE FROM %%USERS_VALID%% WHERE validationID = :validation_id;";
        $db->delete($sql, [
            ':validation_id' => $validation_id,
        ]);

        list($userID, $planetID) = PlayerUtil::createPlayer(
            $user_data['universe'],
            $user_data['userName'],
            $user_data['password'],
            $user_data['email'],
            $user_data['language'],
            null,
            null,
            null,
            null,
            0,
            null,
            $user_data['user_secret_question_id'],
            $user_data['user_secret_question_answer']
        );

        if ($config->mail_active == 1)
        {
            require('includes/classes/Mail.class.php');
            $mail_subject = sprintf(
                $LNG['registerMailCompleteTitle'],
                $config->game_name,
                Universe::current()
            );
            $mail_raw = $LNG->getTemplate('email_reg_done');
            $mail_content = str_replace([
                '{USERNAME}',
                '{GAMENAME}',
                '{GAMEMAIL}',
            ], [
                $user_data['userName'],
                $config->game_name.' - '.$config->uni_name,
                $config->smtp_sendmail,
            ], $mail_raw);

            try
            {
                Mail::send($user_data['email'], $user_data['userName'], $mail_subject, $mail_content);
            }
            catch (Exception $e)
            {
                // This mail is wayne.
            }
        }

        if (!empty($user_data['referralID']))
        {
            $sql = "UPDATE %%USERS%% SET
			`ref_id`	= :referralId,
			`ref_bonus`	= 1
			WHERE
			`id`		= :userID;";

            $db->update($sql, [
                ':referralId' => $user_data['referralID'],
                ':userID'     => $userID,
            ]);
        }

        if (!empty($user_data['externalAuthUID']))
        {
            $sql = "INSERT INTO %%USERS_AUTH%% SET
			`id`		= :userID,
			`account`	= :externalAuthUID,
			`mode`		= :externalAuthMethod;";
            $db->insert($sql, [
                ':userID'             => $userID,
                ':externalAuthUID'    => $user_data['externalAuthUID'],
                ':externalAuthMethod' => $user_data['externalAuthMethod'],
            ]);
        }

        $sender_name = $LNG['registerWelcomePMSenderName'];
        $subject = $LNG['registerWelcomePMSubject'];
        $message = sprintf($LNG['registerWelcomePMText'], $config->game_name, $user_data['universe']);

        PlayerUtil::sendMessage($userID, 1, $sender_name, 1, $subject, $message, TIMESTAMP);

        return [
            'userID'   => $userID,
            'userName' => $user_data['userName'],
            'planetID' => $planetID,
        ];
    }

    public function show(): void
    {
        $user_data = $this->_activeUser();

        $session = Session::create();
        $session->userId = (int) $user_data['userID'];
        $session->adminAccess = 0;
        $session->save();

        HTTP::redirectTo('game.php');
    }

    public function json(): void
    {
        global $LNG;
        $user_data = $this->_activeUser();
        $this->sendJSON(sprintf($LNG['vertifyAdminMessage'], $user_data['userName']));
    }
}
