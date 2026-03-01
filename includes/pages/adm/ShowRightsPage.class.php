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

// TODO: REWORK rights and listing

/**
 *
 */
class ShowRightsPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;

        $type = HTTP::_GP('type', '');

        switch ($type)
        {
            case 'adm':
                $sql_where = "AND `authlevel` = '".AUTH_ADM."'";
                break;
            case 'ope':
                $sql_where = "AND `authlevel` = '".AUTH_OPS."'";
                break;
            case 'mod':
                $sql_where = "AND `authlevel` = '".AUTH_MOD."'";
                break;
            case 'pla':
                $sql_where = "AND `authlevel` = '".AUTH_USR."'";
                break;
            default:
                $sql_where = "";
                break;
        }

        $this->tpl_obj->loadscript('./scripts/game/filterlist.js');

        $sql = "SELECT `id`, `username`, `authlevel` 
        FROM %%USERS%% WHERE `universe` = :universe ".$sql_where.";";

        $users = Database::get()->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        $user_list = "";
        foreach ($users as $c_user)
        {
            $user_list .= '<option value="' .
            $c_user['id'] .
            '">' .
            $c_user['username'] .
            '&nbsp;&nbsp;(' .
            $LNG['rank_' .
            $c_user['authlevel']] .
            ')</option>';
        }

        $this->assign([
            'Selector' => [0 => $LNG['rank_0'], 1 => $LNG['rank_1'], 2 => $LNG['rank_2'], 3 => $LNG['rank_3']],
            'UserList' => $user_list,
            'sid'      => session_id(),
        ]);

        $this->display('page.rights.default.tpl');
    }

    public function rights(): void
    {
        global $USER, $LNG;

        $db = Database::get();

        $id = HTTP::_GP('id_1', 0);

        if ($USER['id'] != ROOT_USER
            && $id == ROOT_USER)
        {
            $this->printMessage($LNG['ad_authlevel_error_3'], '?page=rights&mode=rights&sid='.session_id());
        }

        if (!isset($_POST['rights']))
        {
            $_POST['rights'] = [];
        }

        if ($_POST['action'] == 'send')
        {
            $sql = "UPDATE %%USERS%% 
            SET rights = :rights 
            WHERE id = :id";

            $db->update($sql, [
                ':rights' => serialize(array_map('intval', $_POST['rights'])),
                ':id'     => (int)$id,
            ]);
        }

        $sql = "SELECT rights FROM %%USERS%% WHERE id = :userId;";

        $rights = $db->selectSingle($sql, [
            ':userId' => $id,
        ]);

        if (($rights['rights'] = unserialize($rights['rights'])) === false)
        {
            $rights['rights'] = [];
        }

        $files = array_map('prepare', array_diff(scandir('includes/pages/adm/'), ['.', '..', '.svn', 'index.html', '.htaccess', 'ShowIndexPage.php', 'ShowOverviewPage.php', 'ShowMenuPage.php', 'ShowTopnavPage.php']));

        $this->assign([
            'Files'              => $files,
            'Rights'             => $rights['rights'],
            'id'                 => $id,
            'yesorno'            => [1 => $LNG['one_is_yes_1'], 0 => $LNG['one_is_yes_0']],
            'ad_authlevel_title' => $LNG['ad_authlevel_title'],
            'button_submit'      => $LNG['button_submit'],
            'sid'                => session_id(),
        ]);

        $this->display('ModerrationRightsPostPage.tpl');
    }

    public function users(): void
    {
        global $LNG;

        $this->tpl_obj->loadscript('./scripts/game/filterlist.js');

        $type = HTTP::_GP('type', '');

        switch ($type)
        {
            case 'adm':
                $sql_where = "AND `authlevel` = '".AUTH_ADM."'";
                break;
            case 'ope':
                $sql_where = "AND `authlevel` = '".AUTH_OPS."'";
                break;
            case 'mod':
                $sql_where = "AND `authlevel` = '".AUTH_MOD."'";
                break;
            case 'pla':
                $sql_where = "AND `authlevel` = '".AUTH_USR."'";
                break;
            default:
                $sql_where = "";
                break;
        }

        $sql = "SELECT id, username, authlevel 
        FROM %%USERS%% 
        WHERE universe = :universe " . $sql_where;

        $users = Database::get()->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        $user_list = "";
        foreach ($users as $List)
        {
            $user_list .= '<option value="'.(int)$List['id'].'">'
                .htmlspecialchars($List['username'], ENT_QUOTES, 'UTF-8')
                .'&nbsp;&nbsp;('
                .$LNG['rank_'.$List['authlevel']]
                .')</option>';
        }

        $this->assign([
            'Selector'               => [0 => $LNG['rank_0'], 1 => $LNG['rank_1'], 2 => $LNG['rank_2'], 3 => $LNG['rank_3']],
            'UserList'               => $user_list,
            'ad_authlevel_title'     => $LNG['ad_authlevel_title'],
            'bo_select_title'        => $LNG['bo_select_title'],
            'button_submit'          => $LNG['button_submit'],
            'button_deselect'        => $LNG['button_deselect'],
            'button_filter'          => $LNG['button_filter'],
            'ad_authlevel_insert_id' => $LNG['ad_authlevel_insert_id'],
            'ad_authlevel_auth'      => $LNG['ad_authlevel_auth'],
            'ad_authlevel_aa'        => $LNG['ad_authlevel_aa'],
            'ad_authlevel_oo'        => $LNG['ad_authlevel_oo'],
            'ad_authlevel_mm'        => $LNG['ad_authlevel_mm'],
            'ad_authlevel_jj'        => $LNG['ad_authlevel_jj'],
            'ad_authlevel_tt'        => $LNG['ad_authlevel_tt'],
            'sid'                    => session_id(),
        ]);

        $this->display('page.search.users.tpl');

    }

    public function usersSend(): void
    {
        global $USER, $LNG;

        $id = HTTP::_GP('id_1', 0);

        if ($id == 0)
        {
            $id = HTTP::_GP('id_2', 0);
        }

        if ($USER['id'] != ROOT_USER && $id == ROOT_USER)
        {
            $this->printMessage($LNG['ad_authlevel_error_3']);
        }

        $db = Database::get();

        $sql = "UPDATE %%USERS%% SET `authlevel` = :authlevel WHERE `id` = :userId;";

        $db->update($sql, [
            ':authlevel' => HTTP::_GP('authlevel', 0),
            ':userId'    => $id,
        ]);

        $this->printMessage($LNG['ad_authlevel_succes']);

    }

}
