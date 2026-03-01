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

// TODO: %%BANNED%% table don't have user_id column so ban system works with username instead of id,
// add user_id for %%BANNED%% table, and rework ban system

/**
 *
 */
class ShowBannedPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG, $USER;

        $db = Database::get();

        $order_type = HTTP::_GP('order', 'id');

        if (!in_array($order_type, ['id', 'username']))
        {
            $this->printMessage('Wrong order type !', $this->createButtonBack());
            return;
        }

        $where_type = HTTP::_GP('view', '');

        $where_text = "";
        if ($where_type == "banned")
        {
            $where_text = " AND `bana` = '1' ";
        }

        $sql = "SELECT `username`, `id`, `bana` FROM %%USERS%%
		WHERE `id` != 1 AND `authlevel` <= :authlevel AND `universe` = :universe " .
        $where_text . " ORDER BY " . $order_type . " ASC;";

        $user_list = $db->select($sql, [
            ':authlevel' => $USER['authlevel'],
            ':universe'  => Universe::getEmulated(),
        ]);

        $user_select = ['List' => '', 'ListBan' => ''];

        foreach ($user_list as $c_user)
        {
            $user_select['List'] .= '<option value="'.$c_user['id'].'">' .
            $c_user['username'] .
            '&nbsp;&nbsp;(ID:&nbsp;' .
            $c_user['id'] .')' .
            (($c_user['bana'] == '1') ? $LNG['bo_characters_suus'] : '') .
            '</option>';
        }

        $order_2 = (HTTP::_GP('order2', '') == 'id') ? "id" : "username";

        $sql = "SELECT `username`,`id` FROM %%USERS%% 
        WHERE `bana` = '1' AND `universe` = :universe ORDER BY " . $order_2 . " ASC;";

        $banned_users = $db->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        foreach ($banned_users as $c_user)
        {
            $user_select['ListBan'] .= '<option value="' .
            $c_user['username'] .
            '">' .
            $c_user['username'] .
            '&nbsp;&nbsp;(ID:&nbsp;' .
            $c_user['id'] .
            ')</option>';
        }

        $this->tpl_obj->loadscript('./scripts/game/filterlist.js');

        $name = HTTP::_GP('ban_name', '', true);

        $sql = "SELECT b.theme, b.longer, u.id, u.urlaubs_modus, u.banaday 
        FROM %%USERS%% as u
		LEFT JOIN %%BANNED%% as b ON u.`username` = b.`who` 
        WHERE u.`username` = :Name AND u.`universe` = :universe;";

        $BANUSER = $db->selectSingle($sql, [
            ':Name'     => $name,
            ':universe' => Universe::getEmulated(),
        ]);

        $this->assign([
            'UserSelect' => $user_select,
            'usercount'  => count($user_list),
            'bancount'   => count($banned_users),
        ]);

        $this->display('page.banned.default.tpl');
    }

    public function unbanUser(): void
    {
        global $LNG;

        $name = HTTP::_GP('unban_name', '', true);

        $sql = "UPDATE %%USERS%% SET bana = '0', banaday = '0'
						WHERE username = :name AND `universe` = :universe;";

        $db = Database::get();

        $db->update($sql, [
            ':name'     => $name,
            ':universe' => Universe::getEmulated(),
        ]);

        $sql = "DELETE FROM %%BANNED%% WHERE who = :name AND `universe` = :universe";

        $db->delete($sql, [
            ':name'     => $name,
            ':universe' => Universe::getEmulated(),
        ]);

        $this->printMessage($LNG['bo_the_player2'] . $name . $LNG['bo_unbanned'], $this->createButtonBack());
    }

    public function banUser(): void
    {
        global $USER, $LNG;

        $Name = HTTP::_GP('ban_name', '', true);
        $reas = HTTP::_GP('ban_reason', '', true);
        $days = HTTP::_GP('days', 0);
        $hour = HTTP::_GP('hour', 0);
        $mins = HTTP::_GP('mins', 0);
        $secs = HTTP::_GP('secs', 0);
        $admin = $USER['username'];
        $mail = $USER['email'];
        $ban_time = $days * 86400 + $hour * 3600 + $mins * 60 + $secs;
        $ban_perma = (HTTP::_GP('ban_permanently', '') == "on") ? 1 : 0;
        $target_user_id = HTTP::_GP('target_id', 0);

        $db = Database::get();

        $sql = "SELECT u.username,u.urlaubs_modus,u.banaday,u.id as user_id,b.* FROM %%USERS%% as u
		LEFT JOIN %%BANNED%% as b ON b.id = u.id
		WHERE u.id = :target_user_id AND u.universe = :universe;";

        $target_user_info = $db->selectSingle($sql, [
            ':target_user_id' => $target_user_id,
            ':universe'       => Universe::getEmulated(),
        ]);

        if ($target_user_info['longer'] > TIMESTAMP)
        {
            $ban_time += ($target_user_info['longer'] - TIMESTAMP);
        }

        $banned_until = ($ban_time + TIMESTAMP) < TIMESTAMP ? TIMESTAMP : TIMESTAMP + $ban_time;
        if ($ban_perma)
        {
            $banned_until = 2147483647;
        }

        if ($target_user_info['banaday'] > TIMESTAMP)
        {
            $sql = "UPDATE %%BANNED%% SET ";
            $sql .= "`who` = :name, ";
            $sql .= "`theme` = :reas, ";
            $sql .= "`time` = :action_time, ";
            $sql .= "`longer` = :banned_until, ";
            $sql .= "`author` = :admin, ";
            $sql .= "`email` = :mail ";
            $sql .= "WHERE `who` = :name AND `universe` = :universe;";

            $db->update($sql, [
                ':name'         => $target_user_info['username'],
                ':reas'         => $reas,
                ':action_time'  => TIMESTAMP,
                ':banned_until' => $banned_until,
                ':admin'        => $admin,
                ':mail'         => $mail,
                ':universe'     => Universe::getEmulated(),
            ]);

        }
        else
        {
            $sql = "INSERT INTO %%BANNED%% SET ";
            $sql .= "`who` = :name, ";
            $sql .= "`theme` = :reas, ";
            $sql .= "`time` = :action_time, ";
            $sql .= "`longer` = :banned_until, ";
            $sql .= "`author` = :admin, ";
            $sql .= "`universe` = :universe, ";
            $sql .= "`email` = :mail;";

            $db->insert($sql, [
                ':name'         => $target_user_info['username'],
                ':reas'         => $reas,
                ':action_time'  => TIMESTAMP,
                ':banned_until' => $banned_until,
                ':admin'        => $admin,
                ':universe'     => Universe::getEmulated(),
                ':mail'         => $mail,
            ]);

        }

        $sql = "UPDATE %%USERS%% SET 
        `bana` = '1', 
        `banaday` = :banned_until, 
        urlaubs_modus = :urlaubs_modus
		WHERE `username` = :Name 
        AND `universe` = :universe;";

        $db->update($sql, [
            ':banned_until'  => $banned_until,
            ':urlaubs_modus' => isset($_POST['vacat']) ? '1' : '0',
            ':Name'          => $target_user_info['username'],
            ':universe'      => Universe::getEmulated(),
        ]);

        $this->printMessage($LNG['bo_the_player'].$target_user_info['username'].$LNG['bo_banned']);

    }

    public function userDetail(): void
    {
        global $LNG;

        $target_user_id = HTTP::_GP('target_id', 0);

        $db = Database::get();

        $sql = "SELECT u.username,u.urlaubs_modus,u.banaday,u.id as user_id,b.* 
        FROM %%USERS%% as u
		LEFT JOIN %%BANNED%% as b ON b.id = u.id
		WHERE u.id = :target_user_id AND u.universe = :universe;";

        $target_user_info = $db->selectSingle($sql, [
            ':target_user_id' => $target_user_id,
            ':universe'       => Universe::getEmulated(),
        ]);

        if (!$target_user_info)
        {
            $this->printMessage('user could not be found !', $this->createButtonBack());
        }

        if ($target_user_info['banaday'] <= TIMESTAMP)
        {
            $title = $LNG['bo_bbb_title_1'];
            $changedate = $LNG['bo_bbb_title_2'];
            $changedate_advert = '';
            $reas = '';
            $timesus = '';
        }
        else
        {
            $title = $LNG['bo_bbb_title_3'];
            $changedate = $LNG['bo_bbb_title_6'];
            $changedate_advert = '<td class="c" width="18px"><img src="./styles/resource/images/admin/i.gif" class="tooltip" data-tooltip-content="'.$LNG['bo_bbb_title_4'].'"></td>';

            $reas = $target_user_info['theme'];
            $timesus =
                "<tr>
					<th>".$LNG['bo_bbb_title_5']."</th>
					<th height=25 colspan=2>".date($LNG['php_tdformat'], $target_user_info['longer'])."</th>
				</tr>";
        }

        $vacation = ($target_user_info['urlaubs_modus'] == 1) ? true : false;

        $this->assign([
            'target_id'         => $target_user_info['user_id'],
            'name'              => $target_user_info['username'],
            'bantitle'          => $title,
            'changedate'        => $changedate,
            'reas'              => $reas,
            'changedate_advert' => $changedate_advert,
            'timesus'           => $timesus,
            'vacation'          => $vacation,
        ]);

        $this->display('page.banned.detail.tpl');
    }

}
