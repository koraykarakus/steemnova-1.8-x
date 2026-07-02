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

class ShowFleetStep3Page extends AbstractGamePage
{
    public static int $require_module = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $PLANET, $RESOURCE, $LNG;

        if (inVacationMode($USER))
        {
            FleetFunctions::GotoFleetPage(0);
        }

        $target_mission = HTTP::_GP('mission', 3);
        $transport_metal = max(0, round(HTTP::_GP('metal', 0.0)));
        $transport_crystal = max(0, round(HTTP::_GP('crystal', 0.0)));
        $transport_deuterium = max(0, round(HTTP::_GP('deuterium', 0.0)));
        $wanted_res_type = HTTP::_GP('resEx', 0);
        $wanted_res_amount = max(0, round(HTTP::_GP('exchange', 0.0)));
        $market_type = HTTP::_GP('markettype', 0);
        $visibility = HTTP::_GP('visibility', 0);
        $max_flight_time = HTTP::_GP('maxFlightTime', 0);
        $stay_time = HTTP::_GP('staytime', 0);
        $token = HTTP::_GP('token', '');

        $config = Config::get();

        if (!isset($_SESSION['fleet'][$token]))
        {
            FleetFunctions::GotoFleetPage(1);
        }

        if ($_SESSION['fleet'][$token]['time'] < TIMESTAMP - 600)
        {
            unset($_SESSION['fleet'][$token]);
            FleetFunctions::GotoFleetPage(0);
        }

        $form_data = $_SESSION['fleet'][$token];
        unset($_SESSION['fleet'][$token]);

        $distance = $form_data['distance'];
        $target_galaxy = $form_data['targetGalaxy'];
        $target_system = $form_data['targetSystem'];
        $target_planet = $form_data['targetPlanet'];
        $target_type = $form_data['targetType'];
        $fleet_group = $form_data['fleetGroup'];
        $fleet_array = $form_data['fleet'];
        $fleet_storage = $form_data['fleetRoom'];
        $fleet_speed = $form_data['fleetSpeed'];
        $own_planet = $form_data['ownPlanet'];

        if ($own_planet != $PLANET['id'])
        {
            $this->printMessage($LNG['fl_own_planet_error'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        if ($target_mission != 2)
        {
            $fleet_group = 0;
        }

        if ($PLANET['galaxy'] == $target_galaxy
            && $PLANET['system'] == $target_system
            && $PLANET['planet'] == $target_planet
            && $PLANET['planet_type'] == $target_type)
        {
            $this->printMessage($LNG['fl_error_same_planet'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        if ($target_galaxy < 1 || $target_galaxy > $config->max_galaxy
            || $target_system < 1 || $target_system > $config->max_system
            || $target_planet < 1 || $target_planet > ($config->max_planets + 2)
            || ($target_type !== 1 && $target_type !== 2 && $target_type !== 3))
        {
            $this->printMessage($LNG['fl_invalid_target'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        // Transport and market type 0 have to contain resources
        if (($target_mission == 3
            || ($target_mission == 16
            && $market_type == 0))
            && $transport_metal + $transport_crystal + $transport_deuterium < 1)
        {
            $this->printMessage($LNG['fl_no_noresource'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }
        // Market typ 1 cannot contain resources
        if ($target_mission == 16
            && $market_type == 1
            && $transport_metal + $transport_crystal + $transport_deuterium != 0)
        {
            $this->printMessage($LNG['fl_resources'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        if ($target_mission == 16
            && $wanted_res_amount < 1)
        {
            $this->printMessage($LNG['fl_no_noresource_exchange'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        if ($target_mission == 16
            && $wanted_res_amount > pow(10, 50))
        {
            $this->printMessage($LNG['fl_invalid_mission'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        $actual_fleets = FleetFunctions::GetCurrentFleets($USER['id']);

        if (FleetFunctions::GetMaxFleetSlots($USER) <= $actual_fleets)
        {
            $this->printMessage($LNG['fl_no_slots'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        $acs_time = 0;

        $db = Database::get();

        if (!empty($fleet_group))
        {
            $sql = "SELECT arrive_time FROM %%USERS_ACS%% INNER JOIN %%ACS%% ON id = acs_id
			WHERE acs_id = :acs_id AND :max_fleets > (SELECT COUNT(*) FROM %%FLEETS%% WHERE fleet_group = :acs_id);";
            $acs_time = $db->selectSingle($sql, [
                ':acs_id'     => $fleet_group,
                ':max_fleets' => $config->max_fleets_per_acs,
            ], 'arrive_time');

            if (empty($acs_time))
            {
                $fleet_group = 0;
                $target_mission = 1;
            }
        }

        $sql = "SELECT id, id_owner, debris_metal, debris_crystal, destroyed, ally_deposit 
        FROM %%PLANETS%% WHERE universe = :universe 
        AND galaxy = :target_galaxy AND system = :target_system 
        AND planet = :target_planet AND planet_type = :target_type;";
        $target_planet_data = $db->selectSingle($sql, [
            ':universe'      => Universe::current(),
            ':target_galaxy' => $target_galaxy,
            ':target_system' => $target_system,
            ':target_planet' => $target_planet,
            ':target_type'   => ($target_type == 2 ? 1 : $target_type),
        ]);

        if ($target_mission == 7)
        {
            if (!empty($target_planet_data))
            {
                $this->printMessage($LNG['fl_target_exists'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }

            if ($target_type != 1)
            {
                $this->printMessage($LNG['fl_only_planets_colonizable'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }
        }

        if ($target_mission == 7
            || $target_mission == 15
            || $target_mission == 16)
        {
            $target_planet_data = ['id' => 0, 'id_owner' => 0, 'planettype' => 1];
        }
        else
        {
            if (!empty($target_planet_data["destroyed"]))
            {
                $this->printMessage($LNG['fl_no_target'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }

            if (empty($target_planet_data))
            {
                $this->printMessage($LNG['fl_no_target'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }
        }

        foreach ($fleet_array as $ship => $count)
        {
            if ($count > $PLANET[$RESOURCE[$ship]])
            {
                $this->printMessage($LNG['fl_not_all_ship_avalible'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        if ($target_mission == 11)
        {
            $active_expedition = FleetFunctions::GetCurrentFleets($USER['id'], 11, true);
            $max_expedition = FleetFunctions::getDMMissionLimit($USER);

            if ($active_expedition >= $max_expedition)
            {
                $this->printMessage($LNG['fl_no_expedition_slot'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }
        elseif ($target_mission == 15)
        {
            $active_expedition = FleetFunctions::GetCurrentFleets($USER['id'], 15, true);
            $max_expedition = FleetFunctions::getExpeditionLimit($USER);

            if ($active_expedition >= $max_expedition)
            {
                $this->printMessage($LNG['fl_no_expedition_slot'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        $used_planet = isset($target_planet_data['id_owner']);
        $my_planet = $used_planet && $target_planet_data['id_owner'] == $USER['id'];
        $target_player_data = [];

        if ($target_mission == 7
            || $target_mission == 15
            || $target_mission == 16)
        {
            $target_player_data = [
                'id'            => 0,
                'onlinetime'    => TIMESTAMP,
                'ally_id'       => 0,
                'vacation_mode' => 0,
                'authattack'    => 0,
                'total_points'  => 0,
            ];
        }
        elseif ($my_planet)
        {
            $target_player_data = $USER;
        }
        elseif (!empty($target_planet_data['id_owner']))
        {
            $sql = "SELECT user.*, stat.total_points
                FROM %%USERS%% as user
                LEFT JOIN %%USER_POINTS%% as stat ON stat.id_owner = user.id 
                WHERE user.id = :ownerID;";

            $target_player_data = $db->selectSingle($sql, [
                ':ownerID' => $target_planet_data['id_owner'],
            ]);
        }

        if (empty($target_player_data))
        {
            $this->printMessage($LNG['fl_empty_target'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        $mis_info = [];
        $mis_info['galaxy'] = $target_galaxy;
        $mis_info['system'] = $target_system;
        $mis_info['planet'] = $target_planet;
        $mis_info['planettype'] = $target_type;
        $mis_info['IsAKS'] = $fleet_group;
        $mis_info['Ship'] = $fleet_array;

        $available_missions = FleetFunctions::GetFleetMissions($USER, $mis_info, $target_planet_data);

        if (!in_array($target_mission, $available_missions['MissionSelector']))
        {
            $this->printMessage($LNG['fl_invalid_mission'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        if ($target_mission != 8
            && inVacationMode($target_player_data))
        {
            $this->printMessage($LNG['fl_target_exists'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        if ($target_mission == 1
            || $target_mission == 2
            || $target_mission == 9)
        {
            if (FleetFunctions::CheckBash($target_planet_data['id']))
            {
                $this->printMessage($LNG['fl_bash_protection'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        if ($target_mission == 1
            || $target_mission == 2
            || $target_mission == 5
            || $target_mission == 6
            || $target_mission == 9)
        {
            if (Config::get()->adm_attack == 1
                && $target_player_data['authattack'] > $USER['authlevel'])
            {
                $this->printMessage($LNG['fl_admin_attack'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }

            $sql = 'SELECT total_points
			FROM %%USER_POINTS%%
			WHERE id_owner = :userId;';

            $USER += Database::get()->selectSingle($sql, [
                ':userId' => $USER['id'],
            ]);

            $is_noob_protec = CheckNoobProtec($USER, $target_player_data, $target_player_data);

            if ($is_noob_protec['NoobPlayer'])
            {
                $this->printMessage($LNG['fl_player_is_noob'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }

            if ($is_noob_protec['StrongPlayer'])
            {
                $this->printMessage($LNG['fl_player_is_strong'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        if ($target_mission == 5)
        {
            if ($target_player_data['ally_id'] != $USER['ally_id']
                || $USER['ally_id'] == 0)
            {
                $sql = "SELECT COUNT(*) as state FROM %%BUDDY%%
				WHERE id NOT IN (SELECT id FROM %%BUDDY_REQUEST%% WHERE %%BUDDY_REQUEST%%.id = %%BUDDY%%.id) AND
				(owner = :ownerID AND sender = :userID) OR (owner = :userID AND sender = :ownerID);";
                $buddy = $db->selectSingle($sql, [
                    ':ownerID' => $target_player_data['id'],
                    ':userID'  => $USER['id'],
                ], 'state');

                if ($buddy == 0)
                {
                    $this->printMessage($LNG['fl_no_same_alliance'], [[
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=fleetTable',
                    ]]);
                }
            }
        }

        $fleet_max_speed = FleetFunctions::GetFleetMaxSpeed($fleet_array, $USER);
        $speed_factor = FleetFunctions::GetGameSpeedFactor();
        $duration = FleetFunctions::GetMissionDuration(
            $fleet_speed,
            $fleet_max_speed,
            $distance,
            $speed_factor,
            $USER
        );
        $consumption = FleetFunctions::GetFleetConsumption(
            $fleet_array,
            $duration,
            $distance,
            $USER,
            $speed_factor
        );

        if ($PLANET[$RESOURCE[903]] < $consumption)
        {
            $this->printMessage($LNG['fl_not_enough_deuterium'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        $stay_duration = 0;

        if ($target_mission == 5
            || $target_mission == 11
            || $target_mission == 15
            || $target_mission == 16)
        {
            if (!isset($available_missions['StayBlock'][$stay_time]))
            {
                $this->printMessage($LNG['fl_hold_time_not_exists'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }

            $stay_duration = round($available_missions['StayBlock'][$stay_time] * 3600, 0);
        }

        $fleet_storage -= $consumption;

        $fleet_resource = [
            901 => min($transport_metal, floor($PLANET[$RESOURCE[901]])),
            902 => min($transport_crystal, floor($PLANET[$RESOURCE[902]])),
            903 => min($transport_deuterium, floor($PLANET[$RESOURCE[903]] - $consumption)),
        ];

        $storage_needed = array_sum($fleet_resource);

        if ($storage_needed > $fleet_storage)
        {
            $this->printMessage($LNG['fl_not_enough_space'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        if ($target_mission == 17)
        {
            $attack = $USER[$RESOURCE[109]] * 10 + $USER['factor']['Attack'] * 100;
            $defensive = $USER[$RESOURCE[110]] * 10 + $USER['factor']['Defensive'] * 100;
            $shield = $USER[$RESOURCE[111]] * 10 + $USER['factor']['Shield'] * 100;

            $target_player_data['factor'] = getFactors($target_player_data);

            $attack_targ = $target_player_data[$RESOURCE[109]] * 10 + $target_player_data['factor']['Attack'] * 100;
            $defensive_targ = $target_player_data[$RESOURCE[110]] * 10 + $target_player_data['factor']['Defensive'] * 100;
            $shield_targ = $target_player_data[$RESOURCE[111]] * 10 + $target_player_data['factor']['Shield'] * 100;

            if ($attack < $attack_targ
                || $defensive < $defensive_targ
                || $shield < $shield_targ)
            {
                $this->printMessage($LNG['fl_stronger_techs'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        $PLANET[$RESOURCE[901]] -= $fleet_resource[901];
        $PLANET[$RESOURCE[902]] -= $fleet_resource[902];
        $PLANET[$RESOURCE[903]] -= $fleet_resource[903] + $consumption;

        $fleet_start_time = $duration + TIMESTAMP;
        $time_difference = round(max(0, $fleet_start_time - $acs_time));

        if ($fleet_group != 0)
        {
            if ($time_difference != 0)
            {
                FleetFunctions::setACSTime($time_difference, $fleet_group);
            }
            else
            {
                $fleet_start_time = $acs_time;
            }
        }

        $fleet_stay_time = $fleet_start_time + $stay_duration;
        $fleet_end_time = $fleet_stay_time + $duration;

        $fleet_id = FleetFunctions::sendFleet(
            $fleet_array,
            $target_mission,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $target_planet_data['id_owner'],
            $target_planet_data['id'],
            $target_galaxy,
            $target_system,
            $target_planet,
            $target_type,
            $fleet_resource,
            $fleet_start_time,
            $fleet_stay_time,
            $fleet_end_time,
            $fleet_group,
            0
        );

        if ($target_mission == 16)
        {
            $sql = 'INSERT INTO %%TRADES%% SET
				transaction_type			= :transaction,
				seller_fleet_id				= :sellerFleet,
				filter_visibility			= :visibility,
				filter_flighttime			= :flightTime,
				ex_resource_type			= :resType,
				ex_resource_amount		= :resAmount;';

            $db->insert($sql, [
                ':transaction' => $market_type,
                ':sellerFleet' => $fleet_id,
                ':resType'     => $wanted_res_type,
                ':resAmount'   => $wanted_res_amount,
                ':flightTime'  => $max_flight_time * 3600,
                ':visibility'  => $visibility,
            ]);
        }

        // unused.
        foreach ($fleet_array as $ship => $count)
        {
            $fleet_list[$LNG['tech'][$ship]] = $count;
        }

        $this->tpl_obj->gotoside('game.php?page=fleetTable');
        $this->assign([
            'targetMission'  => $target_mission,
            'distance'       => $distance,
            'consumption'    => $consumption,
            'from'           => $PLANET['galaxy'] .":". $PLANET['system']. ":". $PLANET['planet'],
            'destination'    => $target_galaxy .":". $target_system .":". $target_planet,
            'fleetStartTime' => _date($LNG['php_tdformat'], $fleet_start_time, $USER['timezone']),
            'fleetEndTime'   => _date($LNG['php_tdformat'], $fleet_end_time, $USER['timezone']),
            'MaxFleetSpeed'  => $fleet_max_speed,
            'FleetList'      => $fleet_array,
        ]);

        $this->display('page.fleetStep3.default.tpl');
    }
}
