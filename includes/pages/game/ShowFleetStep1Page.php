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

class ShowFleetStep1Page extends AbstractGamePage
{
    public static $require_module = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $PLANET, $PRICELIST, $RESLIST, $LNG;

        $target_galaxy = HTTP::_GP('galaxy', (int) $PLANET['galaxy']);
        $target_system = HTTP::_GP('system', (int) $PLANET['system']);
        $target_planet = HTTP::_GP('planet', (int) $PLANET['planet']);
        $target_type = HTTP::_GP('type', (int) $PLANET['planet_type']);

        $mission = HTTP::_GP('target_mission', 0);

        $fleet_array = [];
        $fleet_room = 0;
        foreach ($RESLIST['fleet'] as $id => $ship_id)
        {
            $amount = max(0, round(HTTP::_GP('ship'.$ship_id, 0.0, 0.0)));

            if ($amount < 1
                || $ship_id == 212)
            {
                continue;
            }

            $fleet_array[$ship_id] = $amount;
            $fleet_room += $PRICELIST[$ship_id]['capacity'] * $amount;
        }

        $fleet_room *= 1 + $USER['factor']['ShipStorage'];

        if (empty($fleet_array))
        {
            FleetFunctions::GotoFleetPage();
        }

        $fleet_data = [
            'fleetroom'        => floatToString($fleet_room),
            'gamespeed'        => FleetFunctions::GetGameSpeedFactor(),
            'fleetspeedfactor' => max(0, 1 + $USER['factor']['FlyTime']),
            'planet'           => ['galaxy' => $PLANET['galaxy'], 'system' => $PLANET['system'], 'planet' => $PLANET['planet'], 'planet_type' => $PLANET['planet_type']],
            'maxspeed'         => FleetFunctions::GetFleetMaxSpeed($fleet_array, $USER),
            'ships'            => FleetFunctions::GetFleetShipInfo($fleet_array, $USER),
            'fleetMinDuration' => MIN_FLEET_TIME,
        ];

        $token = getRandomString();

        $_SESSION['fleet'][$token] = [
            'time'      => TIMESTAMP,
            'fleet'     => $fleet_array,
            'fleetRoom' => $fleet_room,
        ];

        $shortcut_list = $this->GetUserShotcut();
        $colony_list = $this->GetColonyList();
        $acs_list = $this->GetAvalibleACS();

        $shortcut_amount = 0;
        if (!empty($shortcut_list))
        {
            $shortcut_amount = max(array_keys($shortcut_list));
        }

        $this->tpl_obj->loadscript('flotten.js');
        $this->tpl_obj->execscript('updateVars();FleetTime();var relativeTime3 = Math.floor(Date.now() / 1000);window.setInterval(function() {if(relativeTime3 < Math.floor(Date.now() / 1000)) {FleetTime();relativeTime3++;}}, 25);');

        $this->assign([
            'token'        => $token,
            'mission'      => $mission,
            'shortcutList' => $shortcut_list,
            'shortcutMax'  => $shortcut_amount,
            'colonyList'   => $colony_list,
            'ACSList'      => $acs_list,
            'galaxy'       => $target_galaxy,
            'system'       => $target_system,
            'planet'       => $target_planet,
            'type'         => $target_type,
            'speedSelect'  => FleetFunctions::$allowedSpeed,
            'typeSelect'   => [1 => $LNG['type_planet_1'], 2 => $LNG['type_planet_2'], 3 => $LNG['type_planet_3']],
            'fleetdata'    => $fleet_data,
        ]);

        $this->display('page.fleetStep1.default.tpl');
    }

    public function saveShortcuts(): void
    {
        global $USER, $LNG;

        if (!isset($_REQUEST['shortcut']))
        {
            $this->sendJSON($LNG['fl_shortcut_saved']);
        }

        $db = Database::get();

        $shortcut_data = $_REQUEST['shortcut'];
        $shortcut_user = $this->GetUserShotcut();
        foreach ($shortcut_data as $id => $planet_data)
        {
            if (!isset($shortcut_user[$id]))
            {
                if (empty($planet_data['name'])
                    || empty($planet_data['galaxy'])
                    || empty($planet_data['system'])
                    || empty($planet_data['planet']))
                {
                    continue;
                }

                $sql = "INSERT INTO %%SHORTCUTS%% SET ownerID = :user_id, 
                `name` = :name, `galaxy` = :galaxy, 
                `system` = :system, `planet` = :planet, 
                `type` = :type;";

                $db->insert($sql, [
                    ':user_id' => $USER['id'],
                    ':name'    => $planet_data['name'],
                    ':galaxy'  => $planet_data['galaxy'],
                    ':system'  => $planet_data['system'],
                    ':planet'  => $planet_data['planet'],
                    ':type'    => $planet_data['type'],
                ]);
            }
            elseif (empty($planet_data['name']))
            {
                $sql = "DELETE FROM %%SHORTCUTS%% 
                WHERE shortcutID = :shortcut_id AND ownerID = :user_id;";
                $db->delete($sql, [
                    ':shortcut_id' => $id,
                    ':user_id'     => $USER['id'],
                ]);
            }
            else
            {
                $planet_data['ownerID'] = $USER['id'];
                $planet_data['shortcutID'] = $id;
                if ($planet_data != $shortcut_user[$id])
                {
                    $sql = "UPDATE %%SHORTCUTS%% SET 
                    name = :name, 
                    galaxy = :galaxy, 
                    system = :system, 
                    planet = :planet, 
                    type = :type 
                    WHERE shortcutID = :shortcut_id 
                    AND ownerID = :user_id;";

                    $db->update($sql, [
                        ':user_id'     => $USER['id'],
                        ':name'        => $planet_data['name'],
                        ':galaxy'      => $planet_data['galaxy'],
                        ':system'      => $planet_data['system'],
                        ':planet'      => $planet_data['planet'],
                        ':type'        => $planet_data['type'],
                        ':shortcut_id' => $id,
                    ]);
                }
            }
        }

        $this->sendJSON($LNG['fl_shortcut_saved']);
    }

    private function GetColonyList(): array
    {
        global $PLANET, $USER;

        $colony_list = [];

        foreach ($USER['PLANETS'] as $c_planet_id => $c_planet)
        {
            if ($PLANET['id'] == $c_planet['id'])
            {
                continue;
            }

            $colony_list[] = [
                'name'   => $c_planet['name'],
                'galaxy' => $c_planet['galaxy'],
                'system' => $c_planet['system'],
                'planet' => $c_planet['planet'],
                'type'   => $c_planet['planet_type'],
            ];
        }

        return $colony_list;
    }

    private function GetUserShotcut(): array
    {
        global $USER;

        if (!isModuleAvailable(MODULE_SHORTCUTS))
        {
            return [];
        }

        $db = Database::get();

        $sql = "SELECT * FROM %%SHORTCUTS%% WHERE ownerID = :userID;";
        $shortcut_result = $db->select($sql, [
            ':userID' => $USER['id'],
        ]);

        $shortcut_list = [];

        foreach ($shortcut_result as $c_shortcut)
        {
            $shortcut_list[$c_shortcut['shortcutID']] = $c_shortcut;
        }

        return $shortcut_list;
    }

    private function GetAvalibleACS(): array
    {
        global $USER;

        $db = Database::get();

        $sql = "SELECT acs.id, acs.name, planet.galaxy, planet.system, planet.planet, planet.planet_type
		FROM %%USERS_ACS%%
		INNER JOIN %%ACS%% acs ON acsID = acs.id
		INNER JOIN %%PLANETS%% planet ON planet.id = acs.target
		WHERE userID = :userID AND :maxFleets > (SELECT COUNT(*) FROM %%FLEETS%% WHERE fleet_group = acsID);";

        $acs_result = $db->select($sql, [
            ':userID'    => $USER['id'],
            ':maxFleets' => Config::get()->max_fleets_per_acs,
        ]);

        $acs_list = [];

        foreach ($acs_result as $c_acs)
        {
            $acs_list[] = $c_acs;
        }

        return $acs_list;
    }

    public function checkTarget(): void
    {
        global $PLANET, $LNG, $USER, $RESOURCE;

        $target_galaxy = HTTP::_GP('galaxy', 0);
        $target_system = HTTP::_GP('system', 0);
        $target_planet = HTTP::_GP('planet', 0);
        $target_planet_type = HTTP::_GP('planet_type', 1);

        if ($target_galaxy == $PLANET['galaxy']
            && $target_system == $PLANET['system']
            && $target_planet == $PLANET['planet']
            && $target_planet_type == $PLANET['planet_type'])
        {
            $this->sendJSON($LNG['fl_error_same_planet']);
        }

        // If target is expedition
        if ($target_planet != Config::get()->max_planets + 1)
        {
            $db = Database::get();
            $sql = "SELECT u.id, u.vacation_mode, u.user_lastip, u.authattack,
            	p.destroyed, p.debris_metal, p.debris_crystal, p.destroyed
                FROM %%USERS%% as u, %%PLANETS%% as p WHERE
                p.universe = :universe AND
                p.galaxy = :target_galaxy AND
                p.system = :target_system AND
                p.planet = :target_planet  AND
                p.planet_type = :target_type AND
                u.id = p.id_owner;";

            $planet_data = $db->selectSingle($sql, [
                ':universe'      => Universe::current(),
                ':target_galaxy' => $target_galaxy,
                ':target_system' => $target_system,
                ':target_planet' => $target_planet,
                ':target_type'   => (($target_planet_type == 2) ? 1 : $target_planet_type),
            ]);

            if ($target_planet_type == 3 && !isset($planet_data))
            {
                $this->sendJSON($LNG['fl_error_no_moon']);
            }

            if ($target_planet_type != 2 && !empty($planet_data['vacation_mode']))
            {
                $this->sendJSON($LNG['fl_in_vacation_player']);
            }

            if (!empty($planet_data))
            {
                if ($planet_data['id'] != $USER['id']
                    && Config::get()->adm_attack == 1
                    && $planet_data['authattack'] > $USER['authlevel'])
                {
                    $this->sendJSON($LNG['fl_admin_attack']);
                }
            }

            if (!empty($planet_data))
            {
                if ($planet_data['destroyed'] != 0)
                {
                    $this->sendJSON($LNG['fl_error_not_avalible']);
                }
            }

            if ($target_planet_type == 2
                && empty($planet_data['debris_metal'])
                && empty($planet_data['debris_crystal']))
            {
                $this->sendJSON($LNG['fl_error_empty_derbis']);
            }

            $sql = 'SELECT (
				(SELECT COUNT(*) FROM %%MULTI%% WHERE user_id = :user_id) +
				(SELECT COUNT(*) FROM %%MULTI%% WHERE user_id = :data_id)
			) as count;';

            if (!empty($planet_data))
            {
                $multi_count = $db->selectSingle($sql, [
                    ':user_id' => $USER['id'],
                    ':data_id' => $planet_data['id'],
                ], 'count');
            }

            if (ENABLE_MULTIALERT
                && $USER['id'] != $planet_data['id']
                && $USER['authlevel'] != AUTH_ADM
                && $USER['user_lastip'] == $planet_data['user_lastip']
                && $multi_count != 2)
            {
                $this->sendJSON($LNG['fl_multi_alarm']);
            }
        }
        else
        {
            if ($USER[$RESOURCE[124]] == 0)
            {
                $this->sendJSON($LNG['fl_target_not_exists']);
            }

            $active_expedition = FleetFunctions::GetCurrentFleets($USER['id'], 15, true);

            if ($active_expedition >= FleetFunctions::getExpeditionLimit($USER))
            {
                $this->sendJSON($LNG['fl_no_expedition_slot']);
            }
        }

        $this->sendJSON('OK');
    }
}
