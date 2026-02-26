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
class ShowSendMessagesPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;

        $send_modes = $LNG['ma_modes'];

        if (Config::get()->mail_active == 0)
        {
            unset($send_modes[1]);
            unset($send_modes[2]);
        }

        $this->assign([
            'langSelector' => array_merge(['' => $LNG['ma_all']], $LNG->getAllowedLangs(false)),
            'modes'        => $send_modes,
        ]);

        $this->display('page.sendmessages.default.tpl');

    }

    public function send(): void
    {
        global $USER, $LNG;

        $db = Database::get();

        switch ($USER['authlevel'])
        {
            case AUTH_MOD:
                $class = 'mod';
                break;
            case AUTH_OPS:
                $class = 'ops';
                break;
            case AUTH_ADM:
                $class = 'admin';
                break;
            default:
                $class = '';
                break;
        }

        $subject = HTTP::_GP('subject', '', true);
        $message = HTTP::_GP('text', '', true);
        $mode = HTTP::_GP('type', 0);
        $lang = HTTP::_GP('globalmessagelang', '');

        if (!empty($message)
            && !empty($subject))
        {
            if ($mode == 0
                || $mode == 2)
            {
                $from = '<span class="'.$class.'">' .
                $LNG['user_level_'.$USER['authlevel']] . ' ' .
                $USER['username'].'</span>';

                $pm_subject = '<span class="'.$class.'">'.$subject.'</span>';
                $pm_msg = '<span class="'.$class.'">'.BBCode::parse($message).'</span>';

                if (!empty($lang))
                {
                    $sql = "SELECT `id`, `username` FROM %%USERS%%
					WHERE `universe` = :universe ";
                    $sql .= " AND `lang` = :lang;";

                    $users = $db->select($sql, [
                        ':universe' => Universe::getEmulated(),
                        ':lang'     => $lang,
                    ]);

                }
                else
                {
                    $sql = "SELECT `id`, `username` FROM %%USERS%%
					WHERE `universe` = :universe ";

                    $users = $db->select($sql, [
                        ':universe' => Universe::getEmulated(),
                    ]);
                }

                foreach ($users as $c_user)
                {
                    $sendMessage = str_replace('{USERNAME}', $c_user['username'], $pm_msg);
                    PlayerUtil::sendMessage(
                        $c_user['id'],
                        $USER['id'],
                        $from,
                        50,
                        $pm_subject,
                        $sendMessage,
                        TIMESTAMP,
                        null,
                        1,
                        Universe::getEmulated()
                    );
                }
            }

            if ($mode == 1
                || $mode == 2)
            {
                require 'includes/classes/Mail.class.php';
                $userList = [];

                if (!empty($Lang))
                {
                    $sql = "SELECT `email`, `username` FROM %%USERS%%
					WHERE `universe` = :universe ";
                    $sql .= " AND `lang` = :lang;";

                    $users = $db->select($sql, [
                        ':universe' => Universe::getEmulated(),
                        ':lang'     => $lang,
                    ]);

                }
                else
                {

                    $sql = "SELECT `email`, `username` FROM %%USERS%%
					WHERE `universe` = :universe ";

                    $users = $db->select($sql, [
                        ':universe' => Universe::getEmulated(),
                    ]);

                }

                foreach ($users as $c_user)
                {
                    $userList[$UserData['email']] = [
                        'username' => $c_user['username'],
                        'body'     => BBCode::parse(str_replace(
                            '{USERNAME}',
                            $c_user['username'],
                            $message
                        )),
                    ];
                }

                Mail::multiSend($userList, strip_tags($subject));
            }
            $this->printMessage($LNG['ma_message_sended']);
        }
        else
        {
            $this->printMessage($LNG['ma_subject_needed']);
        }

    }

}
