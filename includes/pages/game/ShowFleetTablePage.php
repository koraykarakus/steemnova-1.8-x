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

class ShowFleetTablePage extends AbstractGamePage
{
    public static $require_module = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function createACS($fleetID, $fleetData): array
    {
        global $USER;

        $rand = mt_rand(100000, 999999999);
        $acs_name = 'AG'.$rand;
        $acs_creator = $USER['id'];

        $db = Database::get();
        $sql = "INSERT INTO %%ACS%% SET name = :acs_name, arrive_time = :time, target = :target;";
        $db->insert($sql, [
            ':acs_name' => $acs_name,
            ':time'     => $fleetData['fleet_start_time'],
            ':target'   => $fleetData['fleet_end_id'],
        ]);

        $acs_id = $db->lastInsertId();

        $sql = "INSERT INTO %%USERS_ACS%% SET acsID = :acs_id, userID = :user_id;";
        $db->insert($sql, [
            ':acs_id'  => $acs_id,
            ':user_id' => $acs_creator,
        ]);

        $sql = "UPDATE %%FLEETS%% SET fleet_group = :acs_id WHERE fleet_id = :fleetID;";
        $db->update($sql, [
            ':acs_id'  => $acs_id,
            ':fleetID' => $fleetID,
        ]);

        return [
            'name' => $acs_name,
            'id'   => $acs_id,
        ];
    }

    // TODO : return type
    public function loadACS($fleetData): bool|array
    {
        global $USER;

        $db = Database::get();
        $sql = "SELECT id, name FROM %%USERS_ACS%% INNER JOIN %%ACS%% ON acsID = id 
        WHERE userID = :user_id AND acsID = :acs_id;";
        $acs_result = $db->selectSingle($sql, [
            ':user_id' => $USER['id'],
            ':acs_id'  => $fleetData['fleet_group'],
        ]);

        return $acs_result;
    }

    public function getACSPageData($fleet_id): array
    {
        global $USER, $LNG;

        $db = Database::get();

        $sql = "SELECT fleet_start_time, fleet_end_id, fleet_group, fleet_mess 
        FROM %%FLEETS%% 
        WHERE fleet_id = :fleet_id;";
        $fleet_data = $db->selectSingle($sql, [
            ':fleet_id' => $fleet_id,
        ]);

        if ($db->rowCount() != 1)
        {
            return [];
        }

        if ($fleet_data['fleet_mess'] == 1
            || $fleet_data['fleet_start_time'] <= TIMESTAMP)
        {
            return [];
        }

        if ($fleet_data['fleet_group'] == 0)
        {
            $acs_data = $this->createACS($fleet_id, $fleet_data);
        }
        else
        {
            $acs_data = $this->loadACS($fleet_data);
        }

        if (empty($acs_data))
        {
            return [];
        }

        $acs_name = HTTP::_GP('acsName', '', UTF8_SUPPORT);
        if (!empty($acs_name))
        {
            if (!PlayerUtil::isNameValid($acs_name))
            {
                $this->sendJSON($LNG['fl_acs_newname_alphanum']);
            }

            $sql = "UPDATE %%ACS%% SET name = :acs_name WHERE id = :acs_id;";
            $db->update($sql, [
                ':acs_name' => $acs_name,
                ':acs_id'   => $acs_data['id'],
            ]);
            $this->sendJSON(false);
        }

        $invited_users = [];

        $sql = "SELECT id, username FROM %%USERS_ACS%% INNER JOIN %%USERS%% ON userID = id 
        WHERE acsID = :acs_id;";
        $user_result = $db->select($sql, [
            ':acs_id' => $acs_data['id'],
        ]);

        foreach ($user_result as $c_user)
        {
            $invited_users[$c_user['id']] = $c_user['username'];
        }

        $new_user = HTTP::_GP('username', '', UTF8_SUPPORT);
        $status_msg = "";
        if (!empty($new_user))
        {
            $sql = "SELECT id FROM %%USERS%% 
            WHERE universe = :universe AND username = :username;";
            $new_user_id = $db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':username' => $new_user,
            ], 'id');

            if (empty($new_user_id))
            {
                $status_msg = $LNG['fl_player']." ".$new_user." ".$LNG['fl_dont_exist'];
            }
            elseif (isset($invited_users[$new_user_id]))
            {
                $status_msg = $LNG['fl_player']." ".$new_user." ".$LNG['fl_already_invited'];
            }
            else
            {
                $status_msg = $LNG['fl_player']." ".$new_user." ".$LNG['fl_add_to_attack'];

                $sql = "INSERT INTO %%USERS_ACS%% SET acsID = :acs_id, userID = :new_user_id;";
                $db->insert($sql, [
                    ':acs_id'      => $acs_data['id'],
                    ':new_user_id' => $new_user_id,
                ]);

                $invited_users[$new_user_id] = $new_user;

                // get target player language while sending ACS invite instead of attack owner.
                $get_target_lang = getLanguage(null, $new_user_id);
                $inviteTitle = $get_target_lang['fl_acs_invitation_title'];

                $inviteMessage = $get_target_lang['fl_player'] .
                $USER['username'] .
                $get_target_lang['fl_acs_invitation_message'];

                PlayerUtil::sendMessage(
                    $new_user_id,
                    $USER['id'],
                    $USER['username'],
                    1,
                    $inviteTitle,
                    $inviteMessage,
                    TIMESTAMP
                );
            }
        }

        return [
            'invitedUsers'  => $invited_users,
            'acsName'       => $acs_data['name'],
            'mainFleetID'   => $fleet_id,
            'statusMessage' => $status_msg,
        ];
    }

    public function show(): void
    {
        global $USER, $PLANET, $reslist, $resource, $LNG, $config;

        $acs_data = [];
        $fleet_id = HTTP::_GP('fleetID', 0);
        $get_action = HTTP::_GP('action', "");

        $db = Database::get();

        $this->tpl_obj->loadscript('flotten.js');

        if (!empty($fleet_id)
            && !inVacationMode($USER))
        {
            switch ($get_action)
            {
                case "sendfleetback":
                    FleetFunctions::SendFleetBack($USER, $fleet_id);
                    break;
                case "acs":
                    $acs_data = $this->getACSPageData($fleet_id);
                    break;
            }
        }

        $tech_expedition = $USER[$resource[124]];

        $active_expedition = 0;
        $max_expedition = 0;
        if ($tech_expedition >= 1)
        {
            $active_expedition = FleetFunctions::GetCurrentFleets($USER['id'], 15, true);
            $max_expedition = floor(sqrt($tech_expedition)) + $USER['factor']['Expedition'];
        }

        $max_fleet_slots = FleetFunctions::GetMaxFleetSlots($USER);

        $target_galaxy = HTTP::_GP('galaxy', (int) $PLANET['galaxy']);
        $target_system = HTTP::_GP('system', (int) $PLANET['system']);
        $target_planet = HTTP::_GP('planet', (int) $PLANET['planet']);
        $target_type = HTTP::_GP('planettype', (int) $PLANET['planet_type']);
        $target_mission = HTTP::_GP('target_mission', 0);

        $sql = "SELECT * FROM %%FLEETS%% 
        WHERE fleet_owner = :user_id AND fleet_mission <> 10 
        ORDER BY fleet_end_time ASC;";
        $fleet_result = $db->select($sql, [
            ':user_id' => $USER['id'],
        ]);

        $active_fleet_slots = $db->rowCount();

        $flying_fleet_list = [];

        foreach ($fleet_result as $c_fleet)
        {
            $fleet_list[$c_fleet['fleet_id']] = FleetFunctions::unserialize($c_fleet['fleet_array']);

            if ($c_fleet['fleet_mission'] == 4 && $c_fleet['fleet_mess'] == FLEET_OUTWARD)
            {
                $return_time = $c_fleet['fleet_start_time'];
            }
            else
            {
                $return_time = $c_fleet['fleet_end_time'];
            }

            $flying_fleet_list[] = [
                'id'            => $c_fleet['fleet_id'],
                'mission'       => $c_fleet['fleet_mission'],
                'state'         => $c_fleet['fleet_mess'],
                'no_returnable' => $c_fleet['fleet_no_m_return'],
                'startGalaxy'   => $c_fleet['fleet_start_galaxy'],
                'startSystem'   => $c_fleet['fleet_start_system'],
                'startPlanet'   => $c_fleet['fleet_start_planet'],
                'startTime'     => _date($LNG['php_tdformat'], $c_fleet['fleet_start_time'], $USER['timezone']),
                'endGalaxy'     => $c_fleet['fleet_end_galaxy'],
                'endSystem'     => $c_fleet['fleet_end_system'],
                'endPlanet'     => $c_fleet['fleet_end_planet'],
                'metal'         => $c_fleet['fleet_resource_metal'],
                'crystal'       => $c_fleet['fleet_resource_crystal'],
                'deuterium'     => $c_fleet['fleet_resource_deuterium'],
                'dm'            => $c_fleet['fleet_resource_darkmatter'],
                'endTime'       => _date($LNG['php_tdformat'], $c_fleet['fleet_end_time'], $USER['timezone']),
                'amount'        => pretty_number($c_fleet['fleet_amount']),
                'returntime'    => $return_time,
                'resttime'      => $return_time - TIMESTAMP,
                'FleetList'     => $fleet_list[$c_fleet['fleet_id']],
            ];
        }

        $fleets_on_planet = [];

        foreach ($reslist['fleet'] as $c_fleet_id)
        {
            if ($PLANET[$resource[$c_fleet_id]] == 0)
            {
                continue;
            }

            $fleets_on_planet[] = [
                'id'    => $c_fleet_id,
                'speed' => FleetFunctions::GetFleetMaxSpeed($c_fleet_id, $USER),
                'count' => $PLANET[$resource[$c_fleet_id]],
            ];
        }

        $stay_selector = [];

        for ($i = 1; $i <= $USER[$resource[124]]; $i++)
        {
            $stay_selector[$i] = $i / $config->expedition_speed;
        }

        $this->assign([
            'FleetsOnPlanet'     => $fleets_on_planet,
            'FlyingFleetList'    => $flying_fleet_list,
            'activeExpedition'   => $active_expedition,
            'maxExpedition'      => $max_expedition,
            'activeFleetSlots'   => $active_fleet_slots,
            'maxFleetSlots'      => $max_fleet_slots,
            'targetGalaxy'       => $target_galaxy,
            'targetSystem'       => $target_system,
            'targetPlanet'       => $target_planet,
            'targetType'         => $target_type,
            'targetMission'      => $target_mission,
            'acsData'            => $acs_data,
            'isVacation'         => inVacationMode($USER),
            'bonusAttack'        => $USER[$resource[109]] * 10 + $USER['factor']['Attack'] * 100,
            'bonusDefensive'     => $USER[$resource[110]] * 10 + $USER['factor']['Defensive'] * 100,
            'bonusShield'        => $USER[$resource[111]] * 10 + $USER['factor']['Shield'] * 100,
            'bonusCombustion'    => $USER[$resource[115]] * 10,
            'bonusImpulse'       => $USER[$resource[117]] * 20,
            'bonusHyperspace'    => $USER[$resource[118]] * 30,
            'galaxy'             => $PLANET['galaxy'],
            'system'             => $PLANET['system'],
            'StaySelector'       => $stay_selector,
            'recaptchaPublicKey' => $config->google_recaptcha_public_key,
        ]);

        $this->display('page.fleetTable.default.tpl');
    }

}
