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
class ShowQuickEditorPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
        $this->setWindow('light');
    }

    public function player(): void
    {
        global $USER, $LNG, $reslist, $resource;

        $action = HTTP::_GP('action', '');
        $target_id = HTTP::_GP('id', 0);

        $db = Database::get();

        $data_id_arr = array_merge($reslist['tech'], $reslist['officier']);
        $specify_items_pq = "";

        foreach ($data_id_arr as $c_id)
        {
            $specify_items_pq .= "`".$resource[$c_id]."`,";
        }

        $sql = "SELECT ".$specify_items_pq." `username`, `authlevel`, 
        `galaxy`, `system`, `planet`, `id_planet`, 
        `darkmatter`, `authattack`, `authlevel`, `urlaubs_modus` 
        FROM %%USERS%% WHERE `id` = :target_id;";

        $user_data = $db->selectSingle($sql, [
            ':target_id' => $target_id,
        ]);

        $change_pw = $USER['id'] == ROOT_USER
        || ($target_id != ROOT_USER && $USER['authlevel'] > $user_data['authlevel']);

        $sql = "SELECT `name` FROM %%PLANETS%% 
        WHERE `id` = :planetId AND `universe` = :universe;";

        $planet_info = $db->selectSingle($sql, [
            ':planetId' => $user_data['id_planet'],
            ':universe' => Universe::getEmulated(),
        ]);

        $tech = $officier = [];

        foreach ($reslist['tech'] as $ID)
        {
            $tech[] = [
                'type'  => $resource[$ID],
                'name'  => $LNG['tech'][$ID],
                'count' => pretty_number($user_data[$resource[$ID]]),
                'input' => $user_data[$resource[$ID]],
            ];
        }

        foreach ($reslist['officier'] as $ID)
        {
            $officier[] = [
                'type'  => $resource[$ID],
                'name'  => $LNG['tech'][$ID],
                'count' => pretty_number($user_data[$resource[$ID]]),
                'input' => $user_data[$resource[$ID]],
            ];
        }

        $sql = "SELECT COUNT(*) FROM %%MULTI%% WHERE userID = :target_id;";
        $multi = $db->selectSingle($sql, [
            ':target_id' => $target_id,
        ]);

        var_dump($user_data['urlaubs_modus']);

        $this->assign([
            'tech'          => $tech,
            'officier'      => $officier,
            'targetID'      => $target_id,
            'planetid'      => $user_data['id_planet'],
            'planetname'    => $planet_info['name'],
            'name'          => $user_data['username'],
            'galaxy'        => $user_data['galaxy'],
            'system'        => $user_data['system'],
            'planet'        => $user_data['planet'],
            'authlevel'     => $user_data['authlevel'],
            'authattack'    => $user_data['authattack'],
            'vacation_mode' => $user_data['urlaubs_modus'],
            'multi'         => $multi,
            'ChangePW'      => $change_pw,
            'yesorno'       => [1 => $LNG['one_is_yes_1'], 0 => $LNG['one_is_yes_0']],
            'darkmatter'    => floatToString($user_data['darkmatter']),
            'darkmatter_c'  => pretty_number($user_data['darkmatter']),
        ]);

        $this->display('page.quickeditor.user.tpl');
    }

    public function playerSend(): void
    {
        global $USER, $LNG, $reslist, $resource;

        $data_id_arr = array_merge($reslist['tech'], $reslist['officier']);

        $specify_items_pq = "";
        foreach ($data_id_arr as $c_id)
        {
            $specify_items_pq .= "`" . $resource[$c_id] . "`,";
        }

        $sql = "SELECT " . $specify_items_pq . " `username`, 
        `authlevel`, `galaxy`, `system`, `planet`, 
        `id_planet`, `darkmatter`, `authattack`, 
        `authlevel` FROM %%USERS%% WHERE `id` = :userId;";

        $db = Database::get();
        $target_id = HTTP::_GP('id', 0);
        $user_data = $db->selectSingle($sql, [
            ':userId' => $target_id,
        ]);

        $change_pw = $USER['id'] == ROOT_USER
        || ($target_id != ROOT_USER && $USER['authlevel'] > $user_data['authlevel']);

        $sql = "UPDATE %%USERS%% SET ";

        foreach ($data_id_arr as $c_id)
        {
            $sql .= "`".$resource[$c_id]."` = ".min(abs(HTTP::_GP($resource[$c_id], 0)), 255).", ";
        }

        $sql .= "`darkmatter` = :darkmatter, ";
        $sql .= "`username` = :username, ";
        $sql .= "`authattack` = :authattack, ";
        $sql .= "`urlaubs_modus` = :vacation_mode ";
        $sql .= "WHERE `id` = :userId AND `universe` = :universe;";

        $db->update($sql, [
            ':darkmatter'    => max(HTTP::_GP('darkmatter', 0), 0),
            ':username'      => HTTP::_GP('name', '', UTF8_SUPPORT),
            ':authattack'    => ($user_data['authlevel'] != AUTH_USR && HTTP::_GP('authattack', '') == 'on' ? $user_data['authlevel'] : 0),
            ':vacation_mode' => HTTP::_GP('vacation_mode', '') == 'on' ? 1 : 0,
            ':userId'        => $target_id,
            ':universe'      => Universe::getEmulated(),
        ]);

        if (!empty($_POST['password'])
            && $change_pw)
        {
            $sql = "UPDATE %%USERS%% SET password = :password WHERE id = :userId;";

            $db->update($sql, [
                ':userId'   => $target_id,
                ':password' => PlayerUtil::cryptPassword(HTTP::_GP('password', '', true)),
            ]);
        }

        $old = [];
        $new = [];
        $multi = HTTP::_GP('multi', 0);

        foreach ($data_id_arr as $c_id)
        {
            $old[$c_id] = $user_data[$resource[$c_id]];
            $new[$c_id] = abs(HTTP::_GP($resource[$c_id], 0));
        }

        $old[921] = $user_data[$resource[921]];
        $new[921] = abs(HTTP::_GP($resource[921], 0));

        $old['username'] = $user_data['username'];
        $new['username'] = $GLOBALS['DATABASE']->sql_escape(HTTP::_GP('name', '', UTF8_SUPPORT));
        $old['authattack'] = $user_data['authattack'];
        $new['authattack'] = ($user_data['authlevel'] != AUTH_USR && HTTP::_GP('authattack', '') == 'on' ? $user_data['authlevel'] : 0);

        $sql = "SELECT COUNT(*) FROM %%MULTI%% WHERE userID = :target_id;";

        $old['multi'] = $db->selectSingle($sql, [
            ':target_id' => $target_id,
        ]);

        $new['authattack'] = $multi;

        if ($old['multi'] != $multi)
        {
            if ($multi == 0)
            {
                $sql = "DELETE FROM %%MULTI%% WHERE userID = :target_id;";
                $db->delete($sql, [
                    ':target_id' => $target_id,
                ]);
            }
            elseif ($multi == 1)
            {
                $sql = "INSERT INTO %%MULTI%% SET userID = :target_id;";
                $db->insert($sql, [
                    ':target_id' => $target_id,
                ]);
            }
        }

        $log = new Log(1);
        $log->target = $target_id;
        $log->old = $old;
        $log->new = $new;
        $log->save();

        $this->printMessage(sprintf($LNG['qe_edit_player_sucess'], $user_data['username'], $target_id));
    }

    public function planetSend(): void
    {
        global $reslist, $resource, $LNG;

        $db = Database::get();

        $id = HTTP::_GP('id', 0);

        $specify_items_pq = "";
        $data_ids = array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']);

        foreach ($data_ids as $c_id)
        {
            $specify_items_pq .= "`".$resource[$c_id]."`,";
        }

        $sql = "SELECT " . $specify_items_pq .
        " `name`, `id_owner`, `planet_type`, `galaxy`, `system`, 
        `planet`, `destruyed`, `diameter`, `field_current`, 
        `field_max`, `temp_min`, `temp_max`, `metal`, `crystal`, 
        `deuterium` FROM %%PLANETS%% WHERE `id` = :planet_id;";

        $planet_data = $db->selectSingle($sql, [
            ':planet_id' => $id,
        ]);

        if (!$planet_data)
        {
            return;
        }

        $sql = "UPDATE %PLANETS%% SET ";
        $Fields = $planet_data['field_current'];

        foreach ($data_ids as $c_id)
        {
            $level = min(max(0, round(HTTP::_GP($resource[$c_id], 0.0))), (in_array($c_id, $reslist['build']) ? 255 : 18446744073709551615));

            if (in_array($c_id, $reslist['allow'][$planet_data['planet_type']]))
            {
                $Fields += $level - $planet_data[$resource[$c_id]];
            }

            $sql .= "`".$resource[$c_id]."` = ".$level.", ";
        }

        $sql .= "`metal` = :metal, ";
        $sql .= "`crystal` = :crystal, ";
        $sql .= "`deuterium` = :deuterium, ";
        $sql .= "`field_current` = :field_current, ";
        $sql .= "`field_max` = :field_max, ";
        $sql .= "`name` = :name, ";
        $sql .= "`eco_hash` = '' ";
        $sql .= "WHERE `id` = :id' AND `universe` = :universe;";

        $db->update($sql, [
            ':metal'         => max(0, round(HTTP::_GP('metal', 0.0))),
            ':crystal'       => max(0, round(HTTP::_GP('crystal', 0.0))),
            ':deuterium'     => max(0, round(HTTP::_GP('deuterium', 0.0))),
            ':field_current' => $Fields,
            ':field_max'     => HTTP::_GP('field_max', 0),
            ':name'          => HTTP::_GP('name', ''),
            ':id'            => $id,
            ':universe'      => Universe::getEmulated(),
        ]);

        $old = [];
        $new = [];

        foreach (array_merge($data_ids, $reslist['resstype'][1]) as $c_id)
        {
            $old[$c_id] = $planet_data[$resource[$c_id]];
            $new[$c_id] = max(0, round(HTTP::_GP($resource[$c_id], 0.0)));
        }

        $old['field_max'] = $planet_data['field_max'];
        $new['field_max'] = HTTP::_GP('field_max', 0);

        $log = new Log(2);
        $log->target = $id;
        $log->old = $old;
        $log->new = $new;
        $log->save();

        $this->printMessage(sprintf(
            $LNG['qe_edit_planet_sucess'],
            $planet_data['name'],
            $planet_data['galaxy'],
            $planet_data['system'],
            $planet_data['planet']
        ));

    }

    public function planet(): void
    {
        global $LNG, $reslist, $resource;

        $action = HTTP::_GP('action', '');
        $id = HTTP::_GP('id', 0);

        $data_ids = array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']);
        $specify_items_pq = "";

        foreach ($data_ids as $c_id)
        {
            $specify_items_pq .= "`".$resource[$c_id]."`,";
        }

        $db = Database::get();

        $sql = "SELECT " . $specify_items_pq . " `name`, `id_owner`, `planet_type`, 
        `galaxy`, `system`, `planet`, `destruyed`, `diameter`, 
        `field_current`, `field_max`, `temp_min`, `temp_max`, 
        `metal`, `crystal`, `deuterium` FROM %%PLANETS%% WHERE `id` = :id;";

        $planet_data = $db->selectSingle($sql, [
            ':id' => $id,
        ]);

        $sql = "SELECT `username` FROM %%USERS%% 
        WHERE `id` = :user_id AND `universe` = '".Universe::getEmulated()."';";

        $UserInfo = $db->selectSingle($sql, [
            ':user_id' => $planet_data['id_owner'],
        ]);

        $build = $defense = $fleet = [];

        foreach ($reslist['allow'][$planet_data['planet_type']] as $ID)
        {
            $build[] = [
                'type'  => $resource[$ID],
                'name'  => $LNG['tech'][$ID],
                'count' => pretty_number($planet_data[$resource[$ID]]),
                'input' => $planet_data[$resource[$ID]],
            ];
        }

        foreach ($reslist['fleet'] as $c_id)
        {
            $fleet[] = [
                'type'  => $resource[$c_id],
                'name'  => $LNG['tech'][$c_id],
                'count' => pretty_number($planet_data[$resource[$c_id]]),
                'input' => $planet_data[$resource[$c_id]],
            ];
        }

        foreach ($reslist['defense'] as $c_id)
        {
            $defense[] = [
                'type'  => $resource[$c_id],
                'name'  => $LNG['tech'][$c_id],
                'count' => pretty_number($planet_data[$resource[$c_id]]),
                'input' => $planet_data[$resource[$c_id]],
            ];
        }

        $this->assign([
            'build'       => $build,
            'fleet'       => $fleet,
            'defense'     => $defense,
            'planetId'    => $id,
            'ownerid'     => $planet_data['id_owner'],
            'ownername'   => $UserInfo['username'],
            'name'        => $planet_data['name'],
            'galaxy'      => $planet_data['galaxy'],
            'system'      => $planet_data['system'],
            'planet'      => $planet_data['planet'],
            'field_min'   => $planet_data['field_current'],
            'field_max'   => $planet_data['field_max'],
            'temp_min'    => $planet_data['temp_min'],
            'temp_max'    => $planet_data['temp_max'],
            'metal'       => floatToString($planet_data['metal']),
            'crystal'     => floatToString($planet_data['crystal']),
            'deuterium'   => floatToString($planet_data['deuterium']),
            'metal_c'     => pretty_number($planet_data['metal']),
            'crystal_c'   => pretty_number($planet_data['crystal']),
            'deuterium_c' => pretty_number($planet_data['deuterium']),
        ]);

        $this->display('page.quickeditor.planet.tpl');
    }

}
