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

class ShowBuddyListPage extends AbstractGamePage
{
    public static $require_module = MODULE_BUDDYLIST;

    public function __construct()
    {
        parent::__construct();
    }

    public function request(): void
    {
        global $USER, $LNG;

        $this->initTemplate();
        $this->setWindow('popup');

        $id = HTTP::_GP('id', 0);

        if ($id == $USER['id'])
        {
            $this->printMessage($LNG['bu_cannot_request_yourself']);
        }

        $db = Database::get();

        $sql = "SELECT COUNT(*) as count FROM %%BUDDY%% WHERE (sender = :userID AND owner = :friendID) OR (owner = :userID AND sender = :friendID);";
        $exists = $db->selectSingle($sql, [
            ':userID'   => $USER['id'],
            ':friendID' => $id,
        ], 'count');

        if ($exists != 0)
        {
            $this->printMessage($LNG['bu_request_exists']);
        }

        $sql = "SELECT username, galaxy, system, planet FROM %%USERS%% WHERE id = :friendID;";
        $user_data = $db->selectSingle($sql, [
            ':friendID' => $id,
        ]);

        $this->assign([
            'username' => $user_data['username'],
            'galaxy'   => $user_data['galaxy'],
            'system'   => $user_data['system'],
            'planet'   => $user_data['planet'],
            'id'       => $id,
        ]);

        $this->display('page.buddyList.request.tpl');
    }

    public function send(): void
    {
        global $USER, $LNG;

        $this->initTemplate();
        $this->setWindow('popup');
        $this->tpl_obj->execscript('window.setTimeout(parent.$.fancybox.close, 2000);');

        $id = HTTP::_GP('id', 0);
        $text = HTTP::_GP('text', '', UTF8_SUPPORT);

        if ($id == $USER['id'])
        {
            $this->printMessage($LNG['bu_cannot_request_yourself']);
        }

        $db = Database::get();

        $sql = "SELECT COUNT(*) as count FROM %%BUDDY%% WHERE (sender = :userID AND owner = :friendID) OR (owner = :userID AND sender = :friendID);";
        $exists = $db->selectSingle($sql, [
            ':userID'   => $USER['id'],
            ':friendID' => $id,
        ], 'count');

        if ($exists != 0)
        {
            $this->printMessage($LNG['bu_request_exists']);
        }

        $sql = "INSERT INTO %%BUDDY%% SET sender = :userID,	owner = :friendID, universe = :universe;";
        $db->insert($sql, [
            ':userID'   => $USER['id'],
            ':friendID' => $id,
            ':universe' => Universe::current(),
        ]);

        $buddy_id = $db->lastInsertId();

        $sql = "INSERT INTO %%BUDDY_REQUEST%% SET id = :buddyID, text = :text;";
        $db->insert($sql, [
            ':buddyID' => $buddy_id,
            ':text'    => $text,
        ]);

        $sql = "SELECT username, lang FROM %%USERS%% WHERE id = :friendID;";
        $row = $db->selectSingle($sql, [
            ':friendID' => $id,
        ]);

        $friend_lang = $LNG;

        if ($USER['lang'] != $row['lang'])
        {
            $friend_lang = new Language($row['lang']);
            $friend_lang->includeData(['INGAME']);
        }

        PlayerUtil::sendMessage(
            $id,
            $USER['id'],
            $USER['username'],
            4,
            $friend_lang['bu_new_request_title'],
            sprintf(
                $friend_lang['bu_new_request_body'],
                $row['username'],
                $USER['username']
            ),
            TIMESTAMP
        );

        $this->printMessage($LNG['bu_request_send']);
    }

    public function delete(): void
    {
        global $USER, $LNG;

        $id = HTTP::_GP('id', 0);
        $db = Database::get();

        $sql = "SELECT COUNT(*) as count FROM %%BUDDY%% WHERE id = :id AND (sender = :userID OR owner = :userID);";
        $is_allowed = $db->selectSingle($sql, [
            ':id'     => $id,
            ':userID' => $USER['id'],
        ], 'count');

        if ($is_allowed)
        {
            $sql = "SELECT COUNT(*) as count FROM %%BUDDY_REQUEST%% WHERE :id;";
            $is_request = $db->selectSingle($sql, [
                ':id' => $id,
            ], 'count');

            if ($is_request)
            {
                $sql = "SELECT u.username, u.id, u.lang FROM %%BUDDY%% b INNER JOIN %%USERS%% u ON u.id = IF(b.sender = :userID,b.owner,b.sender) WHERE b.id = :id;";
                $request_data = $db->selectSingle($sql, [
                    ':id'    => $id,
                    'userID' => $USER['id'],
                ]);

                $enemy_lang = $LNG;

                if ($USER['lang'] != $request_data['lang'])
                {
                    $enemy_lang = new Language($request_data['lang']);
                    $enemy_lang->includeData(['INGAME']);
                }

                PlayerUtil::sendMessage(
                    $request_data['id'],
                    $USER['id'],
                    $USER['username'],
                    4,
                    $enemy_lang['bu_rejected_request_title'],
                    sprintf(
                        $enemy_lang['bu_rejected_request_body'],
                        $request_data['username'],
                        $USER['username']
                    ),
                    TIMESTAMP
                );

            }

            $sql = "DELETE b.*, r.* FROM %%BUDDY%% b 
            LEFT JOIN %%BUDDY_REQUEST%% r USING (id) WHERE b.id = :id;";

            $db->delete($sql, [
                ':id' => $id,
            ]);
        }
        $this->redirectTo("game.php?page=buddyList");
    }

    public function accept(): void
    {
        global $USER, $LNG;

        $id = HTTP::_GP('id', 0);
        $db = Database::get();

        $sql = "DELETE FROM %%BUDDY_REQUEST%% WHERE id = :id;";
        $db->delete($sql, [
            ':id' => $id,
        ]);

        $sql = "SELECT sender, u.username, u.lang FROM %%BUDDY%% b INNER JOIN %%USERS%% u ON sender = u.id WHERE b.id = :id;";
        $sender = $db->selectSingle($sql, [
            ':id' => $id,
        ]);

        $Friend_LNG = $LNG;

        if ($USER['lang'] != $sender['lang'])
        {
            $Friend_LNG = new Language($sender['lang']);
            $Friend_LNG->includeData(['INGAME']);
        }

        PlayerUtil::sendMessage($sender['sender'], $USER['id'], $USER['username'], 4, $Friend_LNG['bu_accepted_request_title'], sprintf($Friend_LNG['bu_accepted_request_body'], $sender['username'], $USER['username']), TIMESTAMP);

        $this->redirectTo("game.php?page=buddyList");
    }

    public function show(): void
    {
        global $USER;

        $db = Database::get();
        $sql = "SELECT a.sender, a.id as buddyid, b.id, b.username, b.onlinetime, b.galaxy, b.system, b.planet, b.ally_id, c.ally_name, d.text
		FROM (%%BUDDY%% as a, %%USERS%% as b) LEFT JOIN %%ALLIANCE%% as c ON c.id = b.ally_id LEFT JOIN %%BUDDY_REQUEST%% as d ON a.id = d.id
		WHERE (a.sender = ".$USER['id']." AND a.owner = b.id) OR (a.owner = :userID AND a.sender = b.id);";

        $buddy_list_data = $db->select($sql, [
            'userID' => $USER['id'],
        ]);

        $my_request_list = [];
        $other_request_list = [];
        $my_buddy_list = [];

        foreach ($buddy_list_data as $c_buddy_data)
        {
            if (isset($c_buddy_data['text']))
            {
                if ($c_buddy_data['sender'] == $USER['id'])
                {
                    $my_request_list[$c_buddy_data['buddyid']] = $c_buddy_data;
                }
                else
                {
                    $other_request_list[$c_buddy_data['buddyid']] = $c_buddy_data;
                }
            }
            else
            {
                $c_buddy_data['onlinetime'] = floor((TIMESTAMP - $c_buddy_data['onlinetime']) / 60);
                $my_buddy_list[$c_buddy_data['buddyid']] = $c_buddy_data;
            }
        }

        $this->assign([
            'myBuddyList'      => $my_buddy_list,
            'myRequestList'    => $my_request_list,
            'otherRequestList' => $other_request_list,
        ]);

        $this->display('page.buddyList.default.tpl');
    }
}
