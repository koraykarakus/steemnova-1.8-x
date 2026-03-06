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

# Actions not logged: Planet-Edit, Alliance-Edit

/**
 *
 */
class ShowAccountsPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $this->display('page.accounts.default.tpl');
    }

    public function resources(): void
    {
        $this->display('page.accounts.resources.tpl');
    }

    public function resourcesSend(): void
    {
        global $LNG;

        $id = HTTP::_GP('id', 0);
        $metal = max(0, round(HTTP::_GP('metal', 0.0)));
        $crystal = max(0, round(HTTP::_GP('cristal', 0.0)));
        $deut = max(0, round(HTTP::_GP('deut', 0.0)));
        $type = HTTP::_GP('type', 'add');

        $planet_select_type = '';
        $galaxy = HTTP::_GP('galaxy', 0);
        $system = HTTP::_GP('system', 0);
        $planet = HTTP::_GP('planet', 0);
        $planet_type = HTTP::_GP('planet_type', 0);

        if ($metal == 0
            && $crystal == 0
            && $deut == 0)
        {
            $this->printMessage('All resources are equal to zero !', $this->createButtonBack());
        }

        if (!in_array($type, ['add', 'delete']))
        {
            $this->printMessage('type is wrong !', $this->createButtonBack());
        }

        if ($id == 0
            && $galaxy == 0
            && $system == 0
            && $planet == 0)
        {
            $this->printMessage('Planet id or coordinate is not entered !', $this->createButtonBack());
        }

        $db = Database::get();

        ($id != 0) ? $planet_select_type = 'id' : $planet_select_type = 'coordinate';

        if ($planet_select_type == 'id')
        {
            $sql = "SELECT `metal`,`crystal`,`deuterium`,`universe` 
            FROM %%PLANETS%% 
            WHERE `id` = :id;";

            $before = $db->selectSingle($sql, [
                ':id' => $id,
            ]);
        }
        else
        {
            $sql = "SELECT `metal`,`crystal`,`deuterium`,`universe` 
            FROM %%PLANETS%% 
            WHERE `galaxy` = :galaxy AND `system` = :system 
            AND `planet` = :planet AND planet_type = :planet_type;";

            $before = $db->selectSingle($sql, [
                ':galaxy'      => $galaxy,
                ':system'      => $system,
                ':planet'      => $planet,
                ':planet_type' => $planet_type,
            ]);
        }

        if (!$before)
        {
            $this->printMessage('planet could not be found !', $this->createButtonBack());
        }

        if ($type == "add")
        {

            if ($planet_select_type == 'id')
            {
                $sql = "UPDATE %%PLANETS%% SET `metal` = `metal` + :metal,
					 `crystal` = `crystal` + :crystal,
					 `deuterium` = `deuterium` + :deut WHERE `id` = :id AND `universe` = :universe;";

                $db->update($sql, [
                    ':metal'    => $metal,
                    ':crystal'  => $crystal,
                    ':deut'     => $deut,
                    ':id'       => $id,
                    ':universe' => Universe::getEmulated(),
                ]);
            }
            else
            {
                $sql = "UPDATE %%PLANETS%% SET 
                `metal` = `metal` + :metal,
				`crystal` = `crystal` + :crystal,
                `deuterium` = `deuterium` + :deut 
                WHERE galaxy = :galaxy AND system = :system 
                AND planet = :planet AND planet_type = :planet_type 
                AND `universe` = :universe;";

                $db->update($sql, [
                    ':metal'       => $metal,
                    ':crystal'     => $crystal,
                    ':deut'        => $deut,
                    ':galaxy'      => $galaxy,
                    ':system'      => $system,
                    ':planet'      => $planet,
                    ':planet_type' => $planet_type,
                    ':universe'    => Universe::getEmulated(),
                ]);
            }

            $after = [
                'metal'     => ($before['metal'] + $metal),
                'crystal'   => ($before['crystal'] + $crystal),
                'deuterium' => ($before['deuterium'] + $deut),
            ];

        }
        elseif ($type == "delete")
        {

            if ($planet_select_type == 'id')
            {
                $sql = "UPDATE %%PLANETS%% SET `metal` = GREATEST(0, `metal` - :metal),
					`crystal` = GREATEST(0, `crystal` - :crystal), `deuterium` = GREATEST(0, `deuterium` - :deut)
					WHERE `id` = :id AND `universe` = :universe;";

                $db->update($sql, [
                    ':metal'    => $metal,
                    ':crystal'  => $crystal,
                    ':deut'     => $deut,
                    ':id'       => $id,
                    ':universe' => Universe::getEmulated(),
                ]);
            }
            else
            {
                $sql = "UPDATE %%PLANETS%% SET 
                `metal` = GREATEST(0, `metal` - :metal),
                `crystal` = GREATEST(0, `crystal` - :crystal), 
                `deuterium` = GREATEST(0, `deuterium` - :deut)
                WHERE `galaxy` = :galaxy AND system = :system 
                AND planet = :planet AND planet_type = :planet_type 
                AND `universe` = :universe;";

                $db->update($sql, [
                    ':metal'       => $metal,
                    ':crystal'     => $crystal,
                    ':deut'        => $deut,
                    ':galaxy'      => $galaxy,
                    ':system'      => $system,
                    ':planet'      => $planet,
                    ':planet_type' => $planet_type,
                    ':universe'    => Universe::getEmulated(),
                ]);
            }

            $after = [
                'metal'     => ($before['metal'] - $metal),
                'crystal'   => ($before['crystal'] - $crystal),
                'deuterium' => ($before['deuterium'] - $deut),
            ];

        }

        $log = new Log(2);
        $log->target = $id;
        $log->universe = $before['universe'];
        $log->old = $before;
        $log->new = $after;
        $log->save();

        if ($type == "add")
        {
            $this->printMessage($LNG['ad_add_res_sucess'], $this->createButtonBack());
        }
        elseif ($type == "delete")
        {
            $this->printMessage($LNG['ad_delete_res_sucess'], $this->createButtonBack());
        }

        $this->display('page.accounts.resources.tpl');
    }

    public function darkmatterSend(): void
    {
        global $LNG;

        $user_id = HTTP::_GP('user_id', 0);
        $dark = HTTP::_GP('dark', 0);
        $type = HTTP::_GP('type', 'add');

        if ($user_id == 0)
        {
            $this->printMessage('user id is not entered !', $this->createButtonBack());
        }

        if ($dark == 0)
        {
            $this->printMessage('Amount of dark matter is not set !', $this->createButtonBack());
        }

        if (!in_array($type, ['add', 'delete']))
        {
            $this->printMessage('type is wrong !', $this->createButtonBack());
        }

        $db = Database::get();

        $sql = "SELECT `darkmatter`,`universe` FROM %%USERS%% WHERE `id` = :user_id;";

        $before_dm = $db->selectSingle($sql, [
            ':user_id' => $user_id,
        ]);

        if (!$before_dm)
        {
            $this->printMessage('user could not be found !', $this->createButtonBack());
        }

        if ($type == "add")
        {
            $sql = "UPDATE %%USERS%% SET 
            `darkmatter` = `darkmatter` + :dark 
            WHERE `id` = :user_id AND `universe` = :universe;";

            $db->update($sql, [
                ':dark'     => $dark,
                ':user_id'  => $user_id,
                ':universe' => Universe::getEmulated(),
            ]);

            $after_dm = [
                'darkmatter' => ($before_dm['darkmatter'] + $dark),
            ];

        }
        elseif ($type == "delete")
        {
            $sql = "UPDATE %%USERS%% 
            SET `darkmatter` = GREATEST(0, `darkmatter` - :dark) 
            WHERE `id` = :user_id;";

            $db->update($sql, [
                ':dark'    => $dark,
                ':user_id' => $user_id,
            ]);

            $after_dm = [
                'darkmatter' => ($before_dm['darkmatter'] - $dark),
            ];

        }

        $log = new Log(1);
        $log->target = $user_id;
        $log->universe = $before_dm['universe'];
        $log->old = $before_dm;
        $log->new = $after_dm;
        $log->save();

        if ($type == "add")
        {
            $this->printMessage($LNG['ad_add_res_sucess'], $this->createButtonBack());
        }
        elseif ($type == "delete")
        {
            $this->printMessage($LNG['ad_delete_res_sucess'], $this->createButtonBack());
        }

        $this->display('page.accounts.resources.tpl');
    }

    public function ships(): void
    {
        global $reslist, $resource;

        $input = [];
        foreach ($reslist['fleet'] as $row_id)
        {
            $input[$row_id] = [
                'type' => $resource[$row_id],
            ];
        }

        $this->assign([
            'inputlist' => $input,
        ]);

        $this->display('page.accounts.ships.tpl');
    }

    public function shipsSend(): void
    {
        global $reslist, $resource, $LNG;

        $type = HTTP::_GP('type', 'add');

        if (!in_array($type, ['add', 'delete']))
        {
            $this->printMessage('Wrong type !', $this->createButtonBack());
        }

        $db = Database::get();

        $sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planetId;";

        $planet_info = $db->selectSingle($sql, [
            ':planetId' => HTTP::_GP('id', 0),
        ]);

        if (!$planet_info)
        {
            $this->printMessage('Target planet does not exist !', $this->createButtonBack());
        }

        $before = $after = [];
        foreach ($reslist['fleet'] as $row_id)
        {
            $before[$row_id] = $planet_info[$resource[$row_id]];
        }

        if ($type == "add")
        {
            $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
            foreach ($reslist['fleet'] as $row_id)
            {
                $qry_update[] = "`" . $resource[$row_id] . 
                "` = `" . $resource[$row_id] . "` + '" . 
                max(0, round(HTTP::_GP($resource[$row_id], 0.0))) . "'";

                $after[$row_id] = $before[$row_id] + max(0, round(HTTP::_GP($resource[$row_id], 0.0)));
            }
            $sql .= implode(", ", $qry_update);
            $sql .= "WHERE ";
            $sql .= "`id` = :planetId AND `universe` = :universe;";

            $db->update($sql, [
                ':planetId' => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);

        }
        elseif ($type == "delete")
        {
            $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";

            foreach ($reslist['fleet'] as $row_id)
            {
                $qry_update[] = "`".$resource[$row_id]."` = GREATEST(0,  `".$resource[$row_id]."` - '".max(0, round(HTTP::_GP($resource[$row_id], 0.0)))."')";
                $after[$row_id] = max($before[$row_id] - max(0, round(HTTP::_GP($resource[$row_id], 0.0))), 0);
            }

            $sql .= implode(", ", $qry_update);
            $sql .= "WHERE ";
            $sql .= "`id` = :planetId AND `universe` = :universe;";
            
            $db->update($sql, [
                ':planetId' => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $log = new Log(2);
        $log->target = HTTP::_GP('id', 0);
        $log->universe = $planet_info['universe'];
        $log->old = $before;
        $log->new = $after;
        $log->save();

        if ($type == "add")
        {
            $this->printMessage($LNG['ad_add_ships_sucess'], $this->createButtonBack());
        }
        elseif ($type == "delete")
        {
            $this->printMessage($LNG['ad_delete_ships_sucess'], $this->createButtonBack());
        }

    }

    public function defenses(): void
    {
        global $reslist, $resource;

        $input = [];
        foreach ($reslist['defense'] as $row_id)
        {
            $input[$row_id] = [
                'type' => $resource[$row_id],
            ];
        }

        $this->assign([
            'inputlist' => $input,
        ]);

        $this->display('page.accounts.defenses.tpl');

    }

    public function defensesSend(): void
    {
        global $reslist, $resource, $LNG;

        $type = HTTP::_GP('type', 'add');

        $planetId = HTTP::_GP('id', 0);

        if (!in_array($type, ['add', 'delete']))
        {
            $this->printMessage('Wrong type !', $this->createButtonBack());
        }

        $db = Database::get();

        $sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planetId;";

        $planet_info = $db->selectSingle($sql, [
            ':planetId' => $planetId,
        ]);

        if (!$planet_info)
        {
            $this->printMessage('Target planet does not exist !', $this->createButtonBack());
        }

        $before = $after = [];

        foreach ($reslist['defense'] as $row_id)
        {
            $before[$row_id] = $planet_info[$resource[$row_id]];
        }
        if ($type == 'add')
        {
            $sql = "UPDATE %%PLANETS%% SET ";
            
            $qry_update = [];
            foreach ($reslist['defense'] as $row_id)
            {
                $qry_update[] = "`".$resource[$row_id]."` = `".$resource[$row_id]."` + '".max(0, round(HTTP::_GP($resource[$row_id], 0.0)))."'";
                $after[$row_id] = $before[$row_id] + max(0, round(HTTP::_GP($resource[$row_id], 0.0)));
            }

            $sql .= implode(", ", $qry_update);
            $sql .= "WHERE ";
            $sql .= "`id` = :planetId AND `universe` = :universe;";

            $db->update($sql, [
                ':planetId' => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);

        }
        elseif ($type == 'delete')
        {
            $sql = "UPDATE %%PLANETS%% SET ";

            $qry_update = [];
            foreach ($reslist['defense'] as $row_id)
            {
                $qry_update[] = "`".$resource[$row_id]."` = GREATEST (0, `".$resource[$row_id]."` - '".max(0, round(HTTP::_GP($resource[$row_id], 0.0)))."')";
                $after[$row_id] = max($before[$row_id] - max(0, round(HTTP::_GP($resource[$row_id], 0.0))), 0);
            }

            $sql .= implode(", ", $qry_update);
            $sql .= "WHERE ";
            $sql .= "`id` = :planetId AND `universe` = :universe;";
            $db->update($sql, [
                ':planetId' => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);

        }

        $log = new Log(2);
        $log->target = HTTP::_GP('id', 0);
        $log->universe = $planet_info['universe'];
        $log->old = $before;
        $log->new = $after;
        $log->save();

        if ($type == 'add')
        {
            $this->printMessage($LNG['ad_add_defenses_success'], $this->createButtonBack());
        }
        elseif ($type == 'delete')
        {
            $this->printMessage($LNG['ad_delete_defenses_success'], $this->createButtonBack());
        }

    }

    public function buildings(): void
    {
        global $reslist, $resource;

        $input = [];
        foreach ($reslist['build'] as $row_id)
        {
            $input[$row_id] = [
                'type' => $resource[$row_id],
            ];
        }

        $this->assign([
            'inputlist' => $input,
        ]);

        $this->display('page.accounts.buildings.tpl');

    }

    public function buildingsSend(): void
    {

        global $reslist, $resource, $LNG;

        $type = HTTP::_GP('type', 'add');

        $planet_id = HTTP::_GP('id', 0);

        if (!in_array($type, ['add', 'delete']))
        {
            $this->printMessage('Wrong type !', $this->createButtonBack());
        }

        $db = Database::get();

        $sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planet_id;";

        $planet_info = $db->selectSingle($sql, [
            ':planet_id' => $planet_id,
        ]);

        if (!$planet_info)
        {
            $this->printMessage($LNG['ad_add_not_exist'], $this->createButtonBack());
        }

        $before = $after = [];

        foreach ($reslist['allow'][$planet_info['planet_type']] as $row_id)
        {
            $before[$row_id] = $planet_info[$resource[$row_id]];
        }

        if ($type == 'add')
        {
            $fields = 0;
            $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
            
            foreach ($reslist['allow'][$planet_info['planet_type']] as $row_id)
            {
                $count = max(0, round(HTTP::_GP($resource[$row_id], 0.0)));
                $QryUpdate[] = "`".$resource[$row_id]."` = `".$resource[$row_id]."` + '".$count."'";
                $after[$row_id] = $before[$row_id] + $count;
                $fields += $count;
            }

            $sql .= implode(", ", $QryUpdate);
            $sql .= ", `field_current` = `field_current` + :fields WHERE 
            `id` = :planet_id AND `universe` = :universe;";

            $db->update($sql, [
                ':fields'   => $fields,
                ':planet_id' => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);

        }
        elseif ($type == 'delete')
        {
            $fields = 0;
            $qry_update = [];
            foreach ($reslist['allow'][$planet_info['planet_type']] as $row_id)
            {
                $count = max(0, round(HTTP::_GP($resource[$row_id], 0.0)));
                $qry_update[] = "`" . $resource[$row_id] . "` = GREATEST(0, `".$resource[$row_id]."` - '".$count."'" . ")";
                $after[$row_id] = max($before[$row_id] - $count, 0);
                $fields += $count;
            }

            $sql = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
            $sql .= implode(", ", $qry_update);
            $sql .= ", `field_current` = GREATEST(0, `field_current` - :fields) WHERE `id` = :planetId AND `universe` = :universe;";
            
            $db->update($sql, [
                ':fields'   => $fields,
                ':planetId' => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $log = new Log(2);
        $log->target = HTTP::_GP('id', 0);
        $log->universe = Universe::getEmulated();
        $log->old = $before;
        $log->new = $after;
        $log->save();

        if ($type == 'add')
        {
            $this->printMessage($LNG['ad_add_build_success'], $this->createButtonBack());
        }
        elseif ($type == 'delete')
        {
            $this->printMessage($LNG['ad_delete_build_success'], $this->createButtonBack());
        }

    }

    public function researchs(): void
    {
        global $reslist, $resource;

        $input = [];
        foreach ($reslist['tech'] as $row_id)
        {
            $input[$row_id] = [
                'type' => $resource[$row_id],
            ];
        }

        $this->assign([
            'inputlist' => $input,
        ]);

        $this->display('page.accounts.researchs.tpl');

    }

    public function researchsSend(): void
    {
        global $reslist, $resource, $LNG;

        $user_id = HTTP::_GP('id', 0);

        $type = HTTP::_GP('type', 'add');

        if (!in_array($type, ['add', 'delete']))
        {
            $this->printMessage('Wrong type !', $this->createButtonBack());
        }

        $db = Database::get();

        $sql = "SELECT * FROM %%USERS%% WHERE `id` = :user_id;";

        $user_info = $db->selectSingle($sql, [
            ':user_id' => $user_id,
        ]);

        if (!$user_info)
        {
            $this->printMessage('User not found !', $this->createButtonBack());
        }

        $before = $after = [];

        foreach ($reslist['tech'] as $row_id)
        {
            $before[$row_id] = $user_info[$resource[$row_id]];
        }

        if ($type == 'add')
        {

            foreach ($reslist['tech'] as $row_id)
            {
                $QryUpdate[] = "`".$resource[$row_id]."` = `".$resource[$row_id]."` + '".max(0, round(HTTP::_GP($resource[$row_id], 0.0)))."'";
                $after[$row_id] = $before[$row_id] + max(0, round(HTTP::_GP($resource[$row_id], 0.0)));
            }
            
            $sql = "UPDATE %%USERS%% SET ";
            $sql .= implode(", ", $QryUpdate);
            $sql .= "WHERE ";
            $sql .= "`id` = :userId AND `universe` = :universe;";

            $db->update($sql, [
                ':userId'   => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);

        }
        elseif ($type == 'delete')
        {
            foreach ($reslist['tech'] as $row_id)
            {
                $QryUpdate[] = "`".$resource[$row_id]."` = GREATEST(0, `".$resource[$row_id]."` - '".max(0, round(HTTP::_GP($resource[$row_id], 0.0)))."')";
                $after[$row_id] = max($before[$row_id] - max(0, round(HTTP::_GP($resource[$row_id], 0.0))), 0);
            }

            $sql = "UPDATE %%USERS%% SET ";
            $sql .= implode(", ", $QryUpdate);
            $sql .= "WHERE ";
            $sql .= "`id` = :userId AND `universe` = :universe;";

            $db->update($sql, [
                ':userId'   => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);

        }

        $log = new Log(1);
        $log->target = HTTP::_GP('id', 0);
        $log->universe = $user_info['universe'];
        $log->old = $before;
        $log->new = $after;
        $log->save();

        if ($type == 'add')
        {
            $this->printMessage($LNG['ad_add_tech_success'], $this->createButtonBack());
        }
        elseif ($type == 'delete')
        {
            $this->printMessage($LNG['ad_delete_tech_success'], $this->createButtonBack());
        }
        exit;

    }

    public function personal(): void
    {
        global $LNG;

        $this->assign([
            'Selector' => ['' => $LNG['select_option'], 'yes' => $LNG['one_is_no_1'], 'no' => $LNG['one_is_no_0']],
        ]);

        $this->display('page.accounts.personal.tpl');

    }

    public function personalSend(): void
    {
        global $LNG;

        $id = HTTP::_GP('id', 0);

        if ($id == 0)
        {
            $this->printMessage('Wrong user ID', $this->createButtonBack());
        }

        $username = HTTP::_GP('username', '', UTF8_SUPPORT);
        $password = HTTP::_GP('password', '', true);
        $email = HTTP::_GP('email', '');
        $email_2 = HTTP::_GP('email_2', '');
        $vacation = HTTP::_GP('vacation', '');

        if (empty($username) 
            && empty($password) 
            && empty($email) 
            && empty($email_2) 
            && empty($vacation))
        {
            $this->printMessage('Form is empty !', $this->createButtonBack());
        }

        $db = Database::get();

        $sql = "SELECT `username`,`email`,`email_2`,`password`,`urlaubs_modus`,`urlaubs_until`
		FROM %%USERS%% WHERE `id` = :user_id;";

        $user_info = $db->selectSingle($sql, [
            ':user_id' => $id,
        ]);

        if (!$user_info)
        {
            $this->printMessage('user could not be found !', $this->createButtonBack());
        }

        $after = [];

        $personal_qry = "UPDATE %%USERS%% SET ";

        if (!empty($username) && $id != ROOT_USER)
        {
            $personal_qry .= "`username` = :username, ";
            $after['username'] = $username;
        }

        if (!empty($email) && $id != ROOT_USER)
        {
            $personal_qry .= "`email` = :email, ";
            $after['email'] = $email;
        }

        if (!empty($email_2) && $id != ROOT_USER)
        {
            $personal_qry .= "`email_2` = :email_2, ";
            $after['email_2'] = $email_2;
        }

        if (!empty($password) && $id != ROOT_USER)
        {
            $personal_qry .= "`password` = :password, ";
            $after['password'] = (PlayerUtil::cryptPassword($password) != $user_info['password']) ? 'CHANGED' : '';
        }

        $user_info['password'] = '';

        $answer = 0;
        $answer_time = 0;

        if ($vacation == 'yes')
        {
            $answer = 1;
            $after['urlaubs_modus'] = 1;
            $answer_time = TIMESTAMP + $_POST['d'] * 86400 + $_POST['h'] * 3600 + $_POST['m'] * 60 + $_POST['s'];
            $after['urlaubs_until'] = $answer_time;
        }

        $personal_qry .= "`urlaubs_modus` = :answer, `urlaubs_until` = :answer_time ";
        $personal_qry .= "WHERE `id` = :id AND `universe` = :universe";

        $db->update($personal_qry, [
            ':username' => $username,
            ':email'    => $email,
            ':email_2'  => $email_2,
            ':password' => PlayerUtil::cryptPassword($password),
            ':answer'   => $answer,
            ':answer_time'  => $answer_time,
            ':id'       => $id,
            ':universe' => Universe::getEmulated(),
        ]);

        $log = new Log(1);
        $log->target = $id;
        $log->universe = $user_info['universe'];
        $log->old = $user_info;
        $log->new = $after;
        $log->save();

        $this->printMessage($LNG['ad_personal_succes'], $this->createButtonBack());

    }

    public function alliance(): void
    {
        $this->display('page.accounts.alliance.tpl');
    }

    public function allianceSend(): void
    {
        global $LNG;

        $id = HTTP::_GP('id', 0);

        if ($id == 0)
        {
            $this->printMessage('Alliance id is not entered !', $this->createButtonBack());
        }

        $name = HTTP::_GP('name', '', UTF8_SUPPORT);
        $change_leader = HTTP::_GP('changeleader', 0);
        $tag = HTTP::_GP('tag', '', UTF8_SUPPORT);
        $externo = HTTP::_GP('externo', '', true);
        $interno = HTTP::_GP('interno', '', true);
        $solicitud = HTTP::_GP('solicitud', '', true);
        $delete = HTTP::_GP('delete', '');
        $delete_u = HTTP::_GP('delete_u', '');

        $db = Database::get();

        $sql = "SELECT * FROM %%ALLIANCE%% WHERE `id` = :id AND `ally_universe` = :universe;";

        $QueryF = $db->selectSingle($sql, [
            ':id'       => $id,
            ':universe' => Universe::getEmulated(),
        ]);

        if (!$QueryF)
        {
            $this->printMessage('Alliance is not found !', $this->createButtonBack());
        }

        if (!empty($name))
        {
            $sql = "UPDATE %%ALLIANCE%% SET `ally_name` = :name 
            WHERE `id` = :id AND `ally_universe` = :universe;";

            $db->update($sql, [
                ':name'     => $name,
                ':id'       => $id,
                ':universe' => Universe::getEmulated(),
            ]);

        }

        if (!empty($tag))
        {
            $sql = "UPDATE %%ALLIANCE%% SET `ally_tag` = :tag 
            WHERE `id` = :id AND `ally_universe` = :universe;";

            $db->update($sql, [
                ':tag'      => $tag,
                ':id'       => $id,
                ':universe' => Universe::getEmulated(),
            ]);

        }

        $sql = "SELECT ally_id FROM %%USERS%% 
        WHERE `id` = :change_leader;";

        $QueryF2 = $db->selectSingle($sql, [
            ':change_leader' => $change_leader,
        ]);

        $sql = "UPDATE %%ALLIANCE%% SET `ally_owner` = :change_leader 
        WHERE `id` = :id AND `ally_universe` = :universe;";

        $db->update($sql, [
            ':change_leader' => $change_leader,
            ':id'           => $id,
            ':universe'     => Universe::getEmulated(),
        ]);

        $sql = "UPDATE %%USERS%% SET `ally_rank_id` = '0' 
        WHERE `id` = :change_leader;";

        $db->update($sql, [
            ':changeleader' => $change_leader,
        ]);

        if (!empty($externo))
        {
            $sql = "UPDATE %%ALLIANCE%% SET `ally_description` = :externo 
            WHERE `id` = :id AND `ally_universe` = :universe;";

            $db->update($sql, [
                ':externo'  => $externo,
                ':id'       => $id,
                ':universe' => Universe::getEmulated(),
            ]);
        }

        if (!empty($interno))
        {
            $sql = "UPDATE %%ALLIANCE%% SET `ally_text` = :interno 
            WHERE `id` = :id AND `ally_universe` = :universe;";

            $db->update($sql, [
                ':interno'  => $interno,
                ':id'       => $id,
                ':universe' => Universe::getEmulated(),
            ]);
        }

        if (!empty($solicitud))
        {

            $sql = "UPDATE %%ALLIANCE%% SET `ally_request` = :solicitud 
            WHERE `id` = :id AND `ally_universe` = :universe;";

            $db->update($sql, [
                ':solicitud' => $solicitud,
                ':id'        => $id,
                ':universe'  => Universe::getEmulated(),
            ]);

        }

        if ($delete == 'on')
        {
            $sql = "DELETE FROM %%ALLIANCE%% 
            WHERE `id` = :id AND `ally_universe` = :universe;";

            $db->delete($sql, [
                ':id'       => $id,
                ':universe' => Universe::getEmulated(),
            ]);

            $sql = "UPDATE %%USERS%% SET `ally_id` = '0', `ally_rank_id` = '0', `ally_register_time` = '0' WHERE `ally_id` = :id;";

            $db->update($sql, [
                ':id' => $id,
            ]);
        }

        if (!empty($delete_u))
        {
            $sql = "UPDATE %%ALLIANCE%% SET `ally_members` = ally_members - 1 
            WHERE `id` = :id AND `ally_universe` = :universe;";

            $db->update($sql, [
                ':id'       => $id,
                ':universe' => Universe::getEmulated(),
            ]);

            $sql = "UPDATE %%USERS%% SET `ally_id` = '0', `ally_rank_id` = '0', `ally_register_time` = '0' WHERE `id` = :delete_u AND `ally_id` = :id;";

            $db->update($sql, [
                ':delete_u' => $delete_u,
                ':id'       => $id,
            ]);
        }

        $this->printMessage($LNG['ad_ally_succes'], $this->createButtonBack());
    }

    public function officers(): void
    {
        global $reslist, $resource;

        $input = [];
        foreach ($reslist['officier'] as $row_id)
        {
            $input[$row_id] = [
                'type' => $resource[$row_id],
            ];
        }

        $this->assign([
            'inputlist' => $input,
        ]);

        $this->display('page.accounts.officers.tpl');
    }

    public function officersSend(): void
    {
        global $reslist, $resource, $LNG;

        $id = HTTP::_GP('id', 0);
        $type = HTTP::_GP('type', 'add');

        if (!in_array($type, ['add', 'delete']))
        {
            $this->printMessage('Wrong type !', $this->createButtonBack());
        }

        $db = Database::get();

        $sql = "SELECT * FROM %%USERS%% 
        WHERE `id` = :id;";

        $userInfo = $db->selectSingle($sql, [
            ':id' => $id,
        ]);

        if (!$userInfo)
        {
            $this->printMessage('User is not found !', $this->createButtonBack());
        }

        $before = $after = [];

        foreach ($reslist['officier'] as $row_id)
        {
            $before[$row_id] = $userInfo[$resource[$row_id]];
        }

        if ($type == 'add')
        {
            $qry_update = [];
            foreach ($reslist['officier'] as $row_id)
            {
                $qry_update[] = "`".$resource[$row_id]."` = `".$resource[$row_id]."` + '".max(0, round(HTTP::_GP($resource[$row_id], 0.0)))."'";
                $after[$row_id] = $before[$row_id] + max(0, round(HTTP::_GP($resource[$row_id], 0.0)));
            }

            $sql = "UPDATE %%USERS%% SET ";
            $sql .= implode(", ", $qry_update);
            $sql .= "WHERE ";
            $sql .= "`id` = :id AND `universe` = :universe;";

            $db->update($sql, [
                ':id'       => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);

        }
        elseif ($type == 'delete')
        {

            $qry_update = [];
            foreach ($reslist['officier'] as $row_id)
            {
                $qry_update[] = "`".$resource[$row_id]."` = `".$resource[$row_id]."` - '".max(0, round(HTTP::_GP($resource[$row_id], 0.0)))."'";
                $after[$row_id] = max($before[$row_id] - max(0, round(HTTP::_GP($resource[$row_id], 0.0))), 0);
            }

            $sql = "UPDATE %%USERS%% SET ";
            $sql .= implode(", ", $qry_update);
            $sql .= "WHERE ";
            $sql .= "`id` = :id AND `universe` = :universe;";
            $db->update($sql, [
                ':id'       => HTTP::_GP('id', 0),
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $log = new Log(1);
        $log->target = HTTP::_GP('id', 0);
        $log->universe = $userInfo['universe'];
        $log->old = $before;
        $log->new = $after;
        $log->save();

        $message = ($type == 'add') ? $LNG['ad_add_offi_success'] : $LNG['ad_delete_offi_success'];

        $this->printMessage($message, $this->createButtonBack());

    }

    public function planets(): void
    {
        $this->display('page.accounts.planets.tpl');
    }

    public function planetsSend(): void
    {
        global $reslist, $resource, $LNG;

        $planet_id = HTTP::_GP('id', 0);
        $name = HTTP::_GP('name', '', UTF8_SUPPORT);
        $diameter = HTTP::_GP('diameter', 0);
        $fields = HTTP::_GP('fields', 0);
        $buildings = HTTP::_GP('0_buildings', '');
        $ships = HTTP::_GP('0_ships', '');
        $defenses = HTTP::_GP('0_defenses', '');
        $c_hangar = HTTP::_GP('0_c_hangar', '');
        $c_buildings = HTTP::_GP('0_c_buildings', '');
        $change_pos = HTTP::_GP('change_position', '');
        $galaxy = HTTP::_GP('g', 0);
        $system = HTTP::_GP('s', 0);
        $planet = HTTP::_GP('p', 0);

        if ($planet_id == 0)
        {
            $this->printMessage('Wrong planet ID', $this->createButtonBack());
        }

        $db = Database::get();

        if (!empty($name))
        {
            $sql = "UPDATE %%PLANETS%% SET `name` = :name 
            WHERE `id` = :planet_id AND `universe` = :universe;";

            $db->update($sql, [
                ':name'     => $name,
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);
        }

        if ($buildings == 'on')
        {
            $build = [];
            foreach ($reslist['build'] as $row_id)
            {
                $build[] = "`".$resource[$row_id]."` = '0'";
            }

            $sql = "UPDATE %%PLANETS%% SET " . 
            implode(', ', $build) . 
            " WHERE `id` = :planet_id AND `universe` = :universe;";

            $db->update($sql, [
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);
        }

        if ($ships == 'on')
        {
            $ships_qry = [];
            foreach ($reslist['fleet'] as $row_id)
            {
                $ships_qry[] = "`".$resource[$row_id]."` = '0'";
            }

            $sql = "UPDATE %%PLANETS%% SET " .
            implode(', ', $ships_qry) .
            " WHERE `id` = :planet_id AND `universe` = :universe;";

            $db->update($sql, [
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);

        }

        if ($defenses == 'on')
        {
            $defs = [];
            foreach ($reslist['defense'] as $row_id)
            {
                $defs[] = "`".$resource[$row_id]."` = '0'";
            }

            $sql = "UPDATE %%PLANETS%% SET " . 
            implode(', ', $defs) . 
            " WHERE `id` = :planet_id AND `universe` = :universe;";

            $db->update($sql, [
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);

        }

        if ($c_hangar == 'on')
        {
            $sql = "UPDATE %%PLANETS%% SET 
            `b_hangar` = '0', 
            `b_hangar_plus` = '0', 
            `b_hangar_id` = '' 
            WHERE `id` = :planet_id AND `universe` = :universe;";

            $db->update($sql, [
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);

        }

        if ($c_buildings == 'on')
        {

            $sql = "UPDATE %%PLANETS%% 
            SET `b_building` = '0', 
            `b_building_id` = '' 
            WHERE `id` = :planet_id 
            AND `universe` = :universe;";

            $db->update($sql, [
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);
        }

        if (!empty($diameter))
        {
            $sql = "UPDATE %%PLANETS%% SET 
            `diameter` = :diameter 
            WHERE `id` = :planet_id AND `universe` = :universe;";

            $db->update($sql, [
                ':diameter' => $diameter,
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);

        }

        if (!empty($fields))
        {
            $sql = "UPDATE %%PLANETS%% 
            SET `field_max` = :fields 
            WHERE `id` = :planet_id 
            AND `universe` = :universe;";

            $db->update($sql, [
                ':fields'   => $fields,
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);

        }

        $config = Config::get(Universe::getEmulated());
        if ($change_pos == 'on' 
            && $galaxy > 0 
            && $system > 0 
            && $planet > 0 
            && $galaxy <= $config->max_galaxy 
            && $system <= $config->max_system 
            && $planet <= $config->max_planets)
        {
            $sql = "SELECT galaxy, system, planet, planet_type 
            FROM %%PLANETS%% 
            WHERE `id` = :planet_id AND `universe` = :universe;";

            $planet_info = $db->selectSingle($sql, [
                ':planet_id' => $planet_id,
                ':universe' => Universe::getEmulated(),
            ]);

            if ($planet_info['planet_type'] == '1')
            {
                if (PlayerUtil::checkPosition(Universe::getEmulated(), $galaxy, $system, $planet, $planet_info['planet_type']))
                {
                    $this->printMessage($LNG['ad_pla_error_planets3'], $this->createButtonBack());
                    return;
                }

                $sql = "UPDATE %%PLANETS%% SET 
                `galaxy` = :galaxy, 
                `system` = :system, 
                `planet` = :planet 
                WHERE `id` = :planet_id AND `universe` = :universe;";

                $db->update($sql, [
                    ':galaxy'   => $galaxy,
                    ':system'   => $system,
                    ':planet'   => $planet,
                    ':planet_id' => $planet_id,
                    ':universe' => Universe::getEmulated(),
                ]);

            }
            else
            {
                if (PlayerUtil::checkPosition(Universe::getEmulated(), 
                $galaxy, $system, $planet, $planet_info['planet_type']))
                {
                    $this->printMessage($LNG['ad_pla_error_planets5'], $this->createButtonBack());
                    return;
                }

                $sql = "SELECT id_luna FROM %%PLANETS%% 
                WHERE `galaxy` = :galaxy 
                AND `system` = :system 
                AND `planet` = :planet 
                AND `planet_type` = '1';";

                $Target = $db->selectSingle($sql, [
                    ':galaxy' => $galaxy,
                    ':system' => $system,
                    ':planet' => $planet,
                ]);

                if ($Target['id_luna'] != '0')
                {
                    $this->printMessage($LNG['ad_pla_error_planets4'], $this->createButtonBack());
                    return;
                }

                $sql = "UPDATE %%PLANETS%% 
                SET `id_luna` = '0' 
                WHERE `galaxy` = :galaxy 
                AND `system` = :system 
                AND `planet` = :planet 
                AND `planet_type` = '1';";

                $db->update($sql, [
                    ':galaxy' => $planet_info['galaxy'],
                    ':system' => $planet_info['system'],
                    ':planet' => $planet_info['planet'],
                ]);

                $sql = "UPDATE %%PLANETS%% SET `id_luna` = :id 
                WHERE `galaxy` = :galaxy 
                AND `system` = :system 
                AND `planet` = :planet 
                AND planet_type = '1';";

                $db->update($sql, [
                    ':id'     => $planet_id,
                    ':galaxy' => $galaxy,
                    ':system' => $system,
                    ':planet' => $planet,
                ]);

                $sql = "UPDATE %%PLANETS%% SET 
                `galaxy` = :galaxy, 
                `system` = :system, 
                `planet` = :planet 
                WHERE `id` = :planet_id AND `universe` = :universe;";

                $db->update($sql, [
                    ':galaxy'   => $galaxy,
                    ':system'   => $system,
                    ':planet'   => $planet,
                    ':planet_id' => $planet_id,
                    ':universe' => Universe::getEmulated(),
                ]);

                $sql = "SELECT id_owner 
                FROM %%PLANETS%% 
                WHERE `galaxy` = :galaxy 
                AND `system` = :system 
                AND `planet` = :planet;";

                $QMOON2 = $db->selectSingle($sql, [
                    ':galaxy' => $galaxy,
                    ':system' => $system,
                    ':planet' => $planet,
                ]);

                $sql = "UPDATE %%PLANETS%% SET 
                `galaxy` = :galaxy, 
                `system` = :system, 
                `planet` = :planet, 
                `id_owner` = :id_owner 
                WHERE `id` = :id AND `universe` = :universe AND `planet_type` = '3';";

                $db->update($sql, [
                    ':galaxy'   => $galaxy,
                    ':system'   => $system,
                    ':planet'   => $planet,
                    ':id_owner' => $QMOON2['id_owner'],
                    ':id'       => $planet_id,
                    ':universe' => Universe::getEmulated(),
                ]);
            }
        }

        $this->printMessage($LNG['ad_pla_succes'], $this->createButtonBack());

    }

}
