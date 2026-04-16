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

class FleetFunctions
{
    public static $allowed_speed = [
        10 => 100,
        9  => 90,
        8  => 80,
        7  => 70,
        6  => 60,
        5  => 50,
        4  => 40,
        3  => 30,
        2  => 20,
        1  => 10];

    private static function GetShipConsumption($ship, $player)
    {
        global $PRICELIST;

        return (($player['impulse_motor_tech'] >= 5 && $ship == 202) || ($player['hyperspace_motor_tech'] >= 8 && $ship == 211)) ? $PRICELIST[$ship]['consumption2'] : $PRICELIST[$ship]['consumption'];
    }

    private static function OnlyShipByID($ships, $ship_id)
    {
        return isset($ships[$ship_id]) && count($ships) === 1;
    }

    private static function GetShipSpeed($ship, $player)
    {
        global $PRICELIST;

        $tech_speed = $PRICELIST[$ship]['tech'];

        if ($tech_speed == 4)
        {
            $tech_speed = $player['impulse_motor_tech'] >= 5 ? 2 : 1;
        }
        if ($tech_speed == 5)
        {
            $tech_speed = $player['hyperspace_motor_tech'] >= 8 ? 3 : 2;
        }

        $base_speed = $PRICELIST[$ship]['speed'];

        if ($player['impulse_motor_tech'] >= 5 && $ship == 202)
        {
            $base_speed = $PRICELIST[$ship]['speed2'];
        }
        if ($player['hyperspace_motor_tech'] >= 8 && $ship == 211)
        {
            $base_speed = $PRICELIST[$ship]['speed2'];
        }
        if ($player['impulse_motor_tech'] >= 17 && $ship == 209)
        {
            $base_speed = $PRICELIST[$ship]['speed2'];
        }
        if ($player['hyperspace_motor_tech'] >= 15 && $ship == 209)
        {
            $base_speed = 6000;  // This should be $PRICELIST[$ship]['speed3'];
            // But this needs more changes
        }

        switch ($tech_speed)
        {
            case 1:
                $speed = $base_speed * (1 + (0.1 * $player['combustion_tech']));
                break;
            case 2:
                $speed = $base_speed * (1 + (0.2 * $player['impulse_motor_tech']));
                break;
            case 3:
                $speed = $base_speed * (1 + (0.3 * $player['hyperspace_motor_tech']));
                break;
            default:
                $speed = 0;
                break;
        }

        return $speed;
    }

    public static function getExpeditionLimit($user)
    {
        return floor(sqrt($user[$GLOBALS['RESOURCE'][124]])) + $user['factor']['Expedition'];
    }

    public static function getDMMissionLimit($user)
    {
        return Config::get($user['universe'])->max_dm_missions;
    }

    public static function getMissileRange($level)
    {
        return max(($level * 5) - 1, 0);
    }

    public static function CheckUserSpeed($speed)
    {
        return isset(self::$allowed_speed[$speed]);
    }

    public static function GetTargetDistance($start, $target)
    {
        if ($start[0] != $target[0])
        {
            return abs($start[0] - $target[0]) * 20000;
        }

        if ($start[1] != $target[1])
        {
            return abs($start[1] - $target[1]) * 95 + 2700;
        }

        if ($start[2] != $target[2])
        {
            return abs($start[2] - $target[2]) * 5 + 1000;
        }

        return 5;
    }

    public static function GetMissionDuration(
        $speed_factor,
        $max_fleet_speed,
        $distance,
        $game_speed,
        $user
    ) {
        $speed_factor = (3500 / ($speed_factor * 0.1));
        $speed_factor *= pow($distance * 10 / $max_fleet_speed, 0.5);
        $speed_factor += 10;
        $speed_factor /= $game_speed;

        if (isset($user['factor']['FlyTime']))
        {
            $speed_factor *= max(0, 1 + $user['factor']['FlyTime']);
        }

        return max($speed_factor, MIN_FLEET_TIME);
    }

    public static function GetMIPDuration($start_system, $target_system)
    {
        $distance = abs($start_system - $target_system);
        $duration = max(round((30 + 60 * $distance) / self::GetGameSpeedFactor()), MIN_FLEET_TIME);

        return $duration;
    }

    public static function GetGameSpeedFactor()
    {
        return Config::get()->fleet_speed / 2500;
    }

    public static function GetMaxFleetSlots($user)
    {
        global $RESOURCE;
        return 1 + $user[$RESOURCE[108]] + $user['factor']['FleetSlots'];
    }

    public static function GetFleetRoom($fleet)
    {
        global $PRICELIST, $USER;
        $fleet_room = 0;
        foreach ($fleet as $ship_id => $amount)
        {
            $fleet_room += $PRICELIST[$ship_id]['capacity'] * $amount * (1 + $USER['factor']['ShipStorage']);
        }
        return $fleet_room;
    }

    public static function GetFleetMaxSpeed($fleets, $player)
    {
        if (empty($fleets))
        {
            return 0;
        }

        $fleet_array = (!is_array($fleets)) ? [$fleets => 1] : $fleets;
        $speed_array = [];

        foreach ($fleet_array as $ship => $Count)
        {
            $speed_array[$ship] = self::GetShipSpeed($ship, $player);
        }

        return min($speed_array);
    }

    public static function GetFleetConsumption(
        $fleet_array,
        $mission_duration,
        $mission_distance,
        $player,
        $game_speed
    ) {
        $consumption = 0;

        foreach ($fleet_array as $ship => $count)
        {
            $ship_speed = self::GetShipSpeed($ship, $player);
            $ship_consumption = self::GetShipConsumption($ship, $player);

            $spd = 35000 / max($mission_duration * $game_speed - 10, 1) * sqrt($mission_distance * 10 / $ship_speed);
            $basic_consumption = $ship_consumption * $count;
            $consumption += $basic_consumption * $mission_distance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
        }
        return (round($consumption) + 1);
    }

    public static function GetFleetMissions($user, $mis_info, $planet)
    {
        global $RESOURCE;
        $missions = self::GetAvailableMissions($user, $mis_info, $planet);
        $stay_block = [];
        $exchange = false;

        $expedition_speed = Config::get($user['universe'])->expedition_speed;

        if (in_array(15, $missions))
        {
            for ($i = 1;$i <= $user[$RESOURCE[124]];$i++)
            {
                $stay_block[$i] = round($i / $expedition_speed, 2);
            }
        }
        elseif (in_array(11, $missions))
        {
            $stay_block = [1 => 1];
        }
        elseif (in_array(5, $missions))
        {
            $stay_block = [1 => 1, 2 => 2, 4 => 4, 8 => 8, 12 => 12, 16 => 16, 32 => 32];
        }
        elseif (in_array(16, $missions))
        {
            $stay_block = [1 => 1, 2 => 2, 4 => 4, 8 => 8, 12 => 12, 24 => 24, 48 => 48, 48 => 48, 168 => 168];
            $exchange = true;
        }

        return ['MissionSelector' => $missions, 'StayBlock' => $stay_block, 'Exchange' => $exchange];
    }

    /*
     *
     * Unserialize an Fleetstring to an array
     *
     * @param string
     *
     * @return array
     *
     */

    public static function unserialize($fleet_amount)
    {
        $fleet_typs = explode(';', $fleet_amount);

        $fleet_amount = [];

        foreach ($fleet_typs as $fleet_typ)
        {
            $temp = explode(',', $fleet_typ);

            if (empty($temp[0]))
            {
                continue;
            }

            if (!isset($fleet_amount[$temp[0]]))
            {
                $fleet_amount[$temp[0]] = 0;
            }

            $fleet_amount[$temp[0]] += $temp[1];
        }

        return $fleet_amount;
    }

    public static function GetACSDuration($acs_id)
    {
        if (empty($acs_id))
        {
            return 0;
        }

        $sql = 'SELECT arrive_time FROM %%ACS%% WHERE id = :acs_id;';
        $acs_end_time = Database::get()->selectSingle($sql, [
            ':acs_id' => $acs_id,
        ], 'arrive_time');

        return empty($acs_end_time) ? $acs_end_time - TIMESTAMP : 0;
    }

    public static function setACSTime($time_diff, $acs_id)
    {
        if (empty($acs_id))
        {
            throw new InvalidArgumentException('Missing acs_id on '.__CLASS__.'::'.__METHOD__);
        }

        $db = Database::get();

        $sql = 'UPDATE %%ACS%% SET arrive_time = arrive_time + :time WHERE id = :acs_id;';
        $db->update($sql, [
            ':time'   => $time_diff,
            ':acs_id' => $acs_id,
        ]);

        $sql = 'UPDATE %%FLEETS%%, %%FLEETS_EVENT%% SET
		fleet_start_time = fleet_start_time + :time,
		fleet_end_stay   = fleet_end_stay + :time,
		fleet_end_time   = fleet_end_time + :time,
		time             = time + :time
		WHERE fleet_group = :acs_id AND fleet_id = fleetID;';

        $db->update($sql, [
            ':time'   => $time_diff,
            ':acs_id' => $acs_id,
        ]);

        return true;
    }

    public static function GetCurrentFleets($user_id, $fleet_mission = 10, $this_mission = false)
    {
        if ($this_mission)
        {
            $sql = 'SELECT COUNT(*) as state
			FROM %%FLEETS%%
			WHERE fleet_owner = :user_id
			AND fleet_mission = :fleet_mission;';
        }
        else
        {
            $sql = 'SELECT COUNT(*) as state
			FROM %%FLEETS%%
			WHERE fleet_owner = :user_id
			AND fleet_mission != :fleet_mission;';
        }

        $actual_fleets = Database::get()->selectSingle($sql, [
            ':user_id'       => $user_id,
            ':fleet_mission' => $fleet_mission,
        ]);
        return $actual_fleets['state'];
    }

    public static function SendFleetBack($user, $fleet_id)
    {
        $db = Database::get();

        $sql = 'SELECT start_time, fleet_start_time, fleet_mission, 
        fleet_group, fleet_owner, fleet_mess 
        FROM %%FLEETS%% 
        WHERE fleet_id = :fleet_id AND fleet_no_m_return = :fleetNoMReturn;';

        $fleet_result = $db->selectSingle($sql, [
            ':fleet_id'       => $fleet_id,
            ':fleetNoMReturn' => 0,
        ]);

        if (empty($fleet_result['start_time']))
        {
            $fleet_result['start_time'] = 0;
        }
        if (empty($fleet_result['fleet_start_time']))
        {
            $fleet_result['fleet_start_time'] = 0;
        }
        if (empty($fleet_result['fleet_mission']))
        {
            $fleet_result['fleet_mission'] = 0;
        }
        if (empty($fleet_result['fleet_group']))
        {
            $fleet_result['fleet_group'] = 0;
        }
        if (empty($fleet_result['fleet_owner']))
        {
            $fleet_result['fleet_owner'] = 0;
        }
        if (empty($fleet_result['fleet_mess']))
        {
            $fleet_result['fleet_mess'] = 0;
        }

        if ($fleet_result['fleet_owner'] != $user['id']
            || $fleet_result['fleet_mess'] == 1)
        {
            return false;
        }

        $sql_where = 'fleet_id';

        if ($fleet_result['fleet_mission'] == 1
            && $fleet_result['fleet_group'] != 0)
        {
            $sql = 'SELECT COUNT(*) as state 
            FROM %%USERS_ACS%% 
            WHERE acs_id = :acs_id;';

            $is_in_group = $db->selectSingle($sql, [
                ':acs_id' => $fleet_result['fleet_group'],
            ], 'state');

            if ($is_in_group)
            {
                $sql = 'DELETE %%ACS%%, %%USERS_ACS%%
				FROM %%ACS%%
				LEFT JOIN %%USERS_ACS%% ON acs_id = %%ACS%%.id
				WHERE %%ACS%%.id = :acs_id;';

                $db->delete($sql, [
                    ':acs_id' => $fleet_result['fleet_group'],
                ]);

                $fleet_id = $fleet_result['fleet_group'];
                $sql_where = 'fleet_group';
            }
        }

        if (($fleet_result['fleet_mission'] == 5
            || $fleet_result['fleet_mission'] == 16)
            && $fleet_result['fleet_mess'] == FLEET_HOLD)
        {
            $fleet_end_time = ($fleet_result['fleet_start_time'] - $fleet_result['start_time']) + TIMESTAMP;
        }
        else
        {
            $fleet_end_time = (TIMESTAMP - $fleet_result['start_time']) + TIMESTAMP;
        }

        $sql = 'UPDATE %%FLEETS%%, %%FLEETS_EVENT%% SET
		fleet_group			= :fleet_group,
		fleet_end_stay		= :end_stay_time,
		fleet_end_time		= :fleet_end_time,
		fleet_mess			= :fleet_state,
		hasCanceled			= :has_canceled,
		time				= :fleet_end_time
		WHERE '.$sql_where.' = :id AND fleet_id = fleetID;';

        $db->update($sql, [
            ':id'             => $fleet_id,
            ':end_stay_time'  => TIMESTAMP,
            ':fleet_end_time' => $fleet_end_time,
            ':fleet_group'    => 0,
            ':has_canceled'   => 1,
            ':fleet_state'    => FLEET_RETURN,
        ]);

        $sql = 'UPDATE %%LOG_FLEETS%% SET
		fleet_end_stay	= :end_stay_time,
		fleet_end_time	= :fleet_end_time,
		fleet_mess		= :fleet_state,
		fleet_state		= 2
		WHERE '.$sql_where.' = :id;';

        $db->update($sql, [
            ':id'             => $fleet_id,
            ':end_stay_time'  => TIMESTAMP,
            ':fleet_end_time' => $fleet_end_time,
            ':fleet_state'    => FLEET_RETURN,
        ]);

        return true;
    }

    public static function GetFleetShipInfo($fleet_array, $player)
    {
        $fleet_info = [];
        foreach ($fleet_array as $ship_id => $amount)
        {
            $fleet_info[$ship_id] = [
                'consumption' => self::GetShipConsumption($ship_id, $player),
                'speed'       => self::GetFleetMaxSpeed($ship_id, $player),
                'amount'      => floatToString($amount)];
        }
        return $fleet_info;
    }

    public static function GotoFleetPage($code = 0)
    {
        global $LNG;
        if (Config::get()->debug == 1)
        {
            $temp = debug_backtrace();
            echo str_replace(
                $_SERVER["DOCUMENT_ROOT"],
                '.',
                $temp[0]['file']
            ) . " on " .
            $temp[0]['line'] .
            " | Code: " .
            $code .
            " | Error: ".
            (isset($LNG['fl_send_error'][$code]) ? $LNG['fl_send_error'][$code] : '');
            exit;
        }

        HTTP::redirectTo('game.php?page=fleetTable&code='.$code);
    }

    public static function GetAvailableMissions($user, $mission_info, $get_info_planet)
    {
        $your_planet = (!empty($get_info_planet['id_owner']) && $get_info_planet['id_owner'] == $user['id']) ? true : false;
        $used_planet = (!empty($get_info_planet['id_owner'])) ? true : false;
        $available_missions = [];

        if ($mission_info['planet'] == (Config::get($user['universe'])->max_planets + 1)
            && isModuleAvailable(MODULE_MISSION_EXPEDITION))
        {
            $available_missions[] = 15;
        }
        elseif ($mission_info['planet'] == (Config::get($user['universe'])->max_planets + 2)
            && isModuleAvailable(MODULE_MISSION_TRADE))
        {
            $available_missions[] = 16;
        }
        elseif ($mission_info['planettype'] == 2)
        {
            if ((isset($mission_info['Ship'][209])
                || isset($mission_info['Ship'][219]))
                && isModuleAvailable(MODULE_MISSION_RECYCLE)
                && !($get_info_planet['debris_metal'] == 0
                && $get_info_planet['debris_crystal'] == 0))
            {
                $available_missions[] = 8;
            }
        }
        else
        {
            if (!$used_planet)
            {
                if (isset($mission_info['Ship'][208])
                    && $mission_info['planettype'] == 1
                    && isModuleAvailable(MODULE_MISSION_COLONY))
                {
                    $available_missions[] = 7;
                }
            }
            else
            {
                if (isModuleAvailable(MODULE_MISSION_TRANSPORT))
                {
                    $mission_info['planet'];
                    $available_missions[] = 3;
                }

                if (!$your_planet
                    && self::OnlyShipByID($mission_info['Ship'], 210)
                    && isModuleAvailable(MODULE_MISSION_SPY))
                {
                    $available_missions[] = 6;
                }

                if (!$your_planet)
                {
                    if (isModuleAvailable(MODULE_MISSION_TRANSFER))
                    {
                        $available_missions[] = 17;
                    }

                    if (isModuleAvailable(MODULE_MISSION_ATTACK))
                    {
                        $available_missions[] = 1;
                    }
                    if (isModuleAvailable(MODULE_MISSION_HOLD))
                    {
                        $available_missions[] = 5;
                    }
                }
                elseif (isModuleAvailable(MODULE_MISSION_STATION))
                {
                    $available_missions[] = 4;
                }

                if (!empty($mission_info['IsAKS'])
                    && !$your_planet
                    && isModuleAvailable(MODULE_MISSION_ATTACK)
                    && isModuleAvailable(MODULE_MISSION_ACS))
                {
                    $available_missions[] = 2;
                }

                if (!$your_planet
                    && $mission_info['planettype'] == 3
                    && isset($mission_info['Ship'][214])
                    && isModuleAvailable(MODULE_MISSION_DESTROY))
                {
                    $available_missions[] = 9;
                }

                if ($your_planet
                    && $mission_info['planettype'] == 3
                    && self::OnlyShipByID($mission_info['Ship'], 220)
                    && isModuleAvailable(MODULE_MISSION_DARKMATTER))
                {
                    $available_missions[] = 11;
                }
            }
        }

        return $available_missions;
    }

    public static function CheckBash($target)
    {
        global $USER;
        $db = Database::get();

        $sql = "SELECT id_owner FROM %%PLANETS%% WHERE id = :id";
        $planet_owner = $db->selectSingle($sql, [
            ':id' => $target,
        ], 'id_owner');

        $sql = "SELECT onlinetime FROM %%USERS%% WHERE id = :id";
        $inactivity = $db->selectSingle($sql, [
            ':id' => $planet_owner,
        ], 'onlinetime');

        if ($inactivity < TIMESTAMP - INACTIVE)
        {
            return false;
        }

        if (!BASH_ON)
        {
            return false;
        }

        $sql = 'SELECT COUNT(*) as state
		FROM %%LOG_FLEETS%%
		WHERE fleet_owner = :fleet_owner
		AND fleet_end_id = :fleet_end_id
		AND fleet_state != :fleet_state
		AND fleet_start_time > :fleet_start_time
		AND fleet_mission IN (1,2,9);';

        $count = Database::get()->selectSingle($sql, [
            ':fleet_owner'      => $USER['id'],
            ':fleet_end_id'     => $target,
            ':fleet_state'      => 2,
            ':fleet_start_time' => (TIMESTAMP - BASH_TIME),
        ]);

        return $count['state'] >= BASH_COUNT;
    }

    public static function sendFleet(
        $fleet_array,
        $fleet_mission,
        $fleet_start_owner,
        $fleet_start_planet_id,
        $fleet_start_planet_galaxy,
        $fleet_start_planet_system,
        $fleet_start_planet_planet,
        $fleet_start_planet_type,
        $fleet_target_owner,
        $fleet_target_planet_id,
        $fleet_target_planet_galaxy,
        $fleet_target_planet_system,
        $fleet_target_planet_planet,
        $fleet_target_planet_type,
        $fleet_resource,
        $fleet_start_time,
        $fleet_stay_time,
        $fleet_end_time,
        $fleet_group = 0,
        $missile_target = 0,
        $fleet_no_m_return = 0,
        $consumption = 0
    ) {
        global $RESOURCE;
        $fleet_ship_count = array_sum($fleet_array);
        $fleet_data = [];

        $db = Database::get();

        $params = [':planetId' => $fleet_start_planet_id];

        $planet_query = [];
        foreach ($fleet_array as $ship_id => $ship_count)
        {
            $fleet_data[] = $ship_id.','.floatToString($ship_count);
            $planet_query[] = $RESOURCE[$ship_id]." = ".$RESOURCE[$ship_id]." - :".$RESOURCE[$ship_id];

            $params[':'.$RESOURCE[$ship_id]] = floatToString($ship_count);
        }

        if ($consumption > 0)
        {
            $planet_query[] = $RESOURCE[903]." = ".$RESOURCE[903]." - :".$RESOURCE[903];
            $params[':'.$RESOURCE[903]] = $consumption;
        }

        $sql = 'UPDATE %%PLANETS%% SET '.implode(', ', $planet_query).' WHERE id = :planetId;';

        $db->update($sql, $params);

        $sql = 'INSERT INTO %%FLEETS%% SET
		fleet_owner					= :fleet_start_owner,
		fleet_target_owner			= :fleet_target_owner,
		fleet_mission				= :fleet_mission,
		fleet_amount				= :fleet_ship_count,
		fleet_array					= :fleet_data,
		fleet_universe				= :universe,
		fleet_start_time			= :fleet_start_time,
		fleet_end_stay				= :fleet_stay_time,
		fleet_end_time				= :fleet_end_time,
		fleet_start_id				= :fleet_start_planet_id,
		fleet_start_galaxy			= :fleet_start_planet_galaxy,
		fleet_start_system			= :fleet_start_planet_system,
		fleet_start_planet			= :fleet_start_planet_planet,
		fleet_start_type			= :fleet_start_planet_type,
		fleet_end_id				= :fleet_target_planet_id,
		fleet_end_galaxy			= :fleet_target_planet_galaxy,
		fleet_end_system			= :fleet_target_planet_system,
		fleet_end_planet			= :fleet_target_planet_planet,
		fleet_end_type				= :fleet_target_planet_type,
		fleet_resource_metal		= :fleetResource901,
		fleet_resource_crystal		= :fleetResource902,
		fleet_resource_deuterium	= :fleetResource903,
		fleet_no_m_return = :fleet_no_m_return,
		fleet_group					= :fleet_group,
		fleet_target_obj			= :missile_target,
		start_time					= :timestamp;';

        $db->insert($sql, [
            ':fleet_start_owner'          => $fleet_start_owner,
            ':fleet_target_owner'         => $fleet_target_owner,
            ':fleet_mission'              => $fleet_mission,
            ':fleet_ship_count'           => $fleet_ship_count,
            ':fleet_data'                 => implode(';', $fleet_data),
            ':fleet_start_time'           => $fleet_start_time,
            ':fleet_stay_time'            => $fleet_stay_time,
            ':fleet_end_time'             => $fleet_end_time,
            ':fleet_start_planet_id'      => $fleet_start_planet_id,
            ':fleet_start_planet_galaxy'  => $fleet_start_planet_galaxy,
            ':fleet_start_planet_system'  => $fleet_start_planet_system,
            ':fleet_start_planet_planet'  => $fleet_start_planet_planet,
            ':fleet_start_planet_type'    => $fleet_start_planet_type,
            ':fleet_target_planet_id'     => $fleet_target_planet_id,
            ':fleet_target_planet_galaxy' => $fleet_target_planet_galaxy,
            ':fleet_target_planet_system' => $fleet_target_planet_system,
            ':fleet_target_planet_planet' => $fleet_target_planet_planet,
            ':fleet_target_planet_type'   => $fleet_target_planet_type,
            ':fleetResource901'           => $fleet_resource[901],
            ':fleetResource902'           => $fleet_resource[902],
            ':fleetResource903'           => $fleet_resource[903],
            ':fleet_no_m_return'          => $fleet_no_m_return,
            ':fleet_group'                => $fleet_group,
            ':missile_target'             => $missile_target,
            ':timestamp'                  => TIMESTAMP,
            ':universe'                   => Universe::current(),
        ]);

        $fleetId = $db->lastInsertId();

        $sql = 'INSERT INTO %%FLEETS_EVENT%% SET fleetID	= :fleetId, `time` = :endTime;';
        $db->insert($sql, [
            ':fleetId' => $fleetId,
            ':endTime' => $fleet_start_time,
        ]);

        $sql = 'INSERT INTO %%LOG_FLEETS%% SET
		fleet_id					= :fleetId,
		fleet_owner					= :fleet_start_owner,
		fleet_target_owner			= :fleet_target_owner,
		fleet_mission				= :fleet_mission,
		fleet_amount				= :fleet_ship_count,
		fleet_array					= :fleet_data,
		fleet_universe				= :universe,
		fleet_start_time			= :fleet_start_time,
		fleet_end_stay				= :fleet_stay_time,
		fleet_end_time				= :fleet_end_time,
		fleet_start_id				= :fleet_start_planet_id,
		fleet_start_galaxy			= :fleet_start_planet_galaxy,
		fleet_start_system			= :fleet_start_planet_system,
		fleet_start_planet			= :fleet_start_planet_planet,
		fleet_start_type			= :fleet_start_planet_type,
		fleet_end_id				= :fleet_target_planet_id,
		fleet_end_galaxy			= :fleet_target_planet_galaxy,
		fleet_end_system			= :fleet_target_planet_system,
		fleet_end_planet			= :fleet_target_planet_planet,
		fleet_end_type				= :fleet_target_planet_type,
		fleet_resource_metal		= :fleetResource901,
		fleet_resource_crystal		= :fleetResource902,
		fleet_resource_deuterium	= :fleetResource903,
		fleet_no_m_return = :fleet_no_m_return,
		fleet_group					= :fleet_group,
		fleet_target_obj			= :missile_target,
		start_time					= :timestamp;';

        $db->insert($sql, [
            ':fleetId'                    => $fleetId,
            ':fleet_start_owner'          => $fleet_start_owner,
            ':fleet_target_owner'         => $fleet_target_owner,
            ':fleet_mission'              => $fleet_mission,
            ':fleet_ship_count'           => $fleet_ship_count,
            ':fleet_data'                 => implode(';', $fleet_data),
            ':fleet_start_time'           => $fleet_start_time,
            ':fleet_stay_time'            => $fleet_stay_time,
            ':fleet_end_time'             => $fleet_end_time,
            ':fleet_start_planet_id'      => $fleet_start_planet_id,
            ':fleet_start_planet_galaxy'  => $fleet_start_planet_galaxy,
            ':fleet_start_planet_system'  => $fleet_start_planet_system,
            ':fleet_start_planet_planet'  => $fleet_start_planet_planet,
            ':fleet_start_planet_type'    => $fleet_start_planet_type,
            ':fleet_target_planet_id'     => $fleet_target_planet_id,
            ':fleet_target_planet_galaxy' => $fleet_target_planet_galaxy,
            ':fleet_target_planet_system' => $fleet_target_planet_system,
            ':fleet_target_planet_planet' => $fleet_target_planet_planet,
            ':fleet_target_planet_type'   => $fleet_target_planet_type,
            ':fleetResource901'           => $fleet_resource[901],
            ':fleetResource902'           => $fleet_resource[902],
            ':fleetResource903'           => $fleet_resource[903],
            ':fleet_no_m_return'          => $fleet_no_m_return,
            ':fleet_group'                => $fleet_group,
            ':missile_target'             => $missile_target,
            ':timestamp'                  => TIMESTAMP,
            ':universe'                   => Universe::current(),
        ]);
        return $fleetId;
    }

    public static function sendFleetTest(
        $fleetArray,
        $fleetMission,
        $fleetStartOwner,
        $fleetStartPlanetID,
        $fleetStartPlanetGalaxy,
        $fleetStartPlanetSystem,
        $fleetStartPlanetPlanet,
        $fleetStartPlanetType,
        $fleetTargetOwner,
        $fleetTargetPlanetID,
        $fleetTargetPlanetGalaxy,
        $fleetTargetPlanetSystem,
        $fleetTargetPlanetPlanet,
        $fleetTargetPlanetType,
        $fleetResource,
        $fleetStartTime,
        $fleetStayTime,
        $fleetEndTime,
        $fleetGroup = 0,
        $missileTarget = 0,
        $fleetNoMReturn = 0,
        $consumption = 0
    ) {
        global $RESOURCE;
        $fleetShipCount = array_sum($fleetArray);
        $fleetData = [];

        $db = Database::get();

        $params = [':planetId' => $fleetStartPlanetID];

        foreach ($fleetArray as $ShipID => $ShipCount)
        {
            $fleetData[] = $ShipID.','.floatToString($ShipCount);

            $params[':'.$RESOURCE[$ShipID]] = floatToString($ShipCount);
        }

        if ($consumption > 0)
        {
            $params[':'.$RESOURCE[903]] = $consumption;
        }

        $sql = 'INSERT INTO %%FLEETS%% SET
		fleet_owner					= :fleetStartOwner,
		fleet_target_owner			= :fleetTargetOwner,
		fleet_mission				= :fleetMission,
		fleet_amount				= :fleetShipCount,
		fleet_array					= :fleetData,
		fleet_universe				= :universe,
		fleet_start_time			= :fleetStartTime,
		fleet_end_stay				= :fleetStayTime,
		fleet_end_time				= :fleetEndTime,
		fleet_start_id				= :fleetStartPlanetID,
		fleet_start_galaxy			= :fleetStartPlanetGalaxy,
		fleet_start_system			= :fleetStartPlanetSystem,
		fleet_start_planet			= :fleetStartPlanetPlanet,
		fleet_start_type			= :fleetStartPlanetType,
		fleet_end_id				= :fleetTargetPlanetID,
		fleet_end_galaxy			= :fleetTargetPlanetGalaxy,
		fleet_end_system			= :fleetTargetPlanetSystem,
		fleet_end_planet			= :fleetTargetPlanetPlanet,
		fleet_end_type				= :fleetTargetPlanetType,
		fleet_resource_metal		= :fleetResource901,
		fleet_resource_crystal		= :fleetResource902,
		fleet_resource_deuterium	= :fleetResource903,
		fleet_no_m_return = :fleetNoMReturn,
		fleet_group					= :fleetGroup,
		fleet_target_obj			= :missileTarget,
		start_time					= :timestamp;';

        $db->insert($sql, [
            ':fleetStartOwner'         => $fleetStartOwner,
            ':fleetTargetOwner'        => $fleetTargetOwner,
            ':fleetMission'            => $fleetMission,
            ':fleetShipCount'          => $fleetShipCount,
            ':fleetData'               => implode(';', $fleetData),
            ':fleetStartTime'          => $fleetStartTime,
            ':fleetStayTime'           => $fleetStayTime,
            ':fleetEndTime'            => $fleetEndTime,
            ':fleetStartPlanetID'      => $fleetStartPlanetID,
            ':fleetStartPlanetGalaxy'  => $fleetStartPlanetGalaxy,
            ':fleetStartPlanetSystem'  => $fleetStartPlanetSystem,
            ':fleetStartPlanetPlanet'  => $fleetStartPlanetPlanet,
            ':fleetStartPlanetType'    => $fleetStartPlanetType,
            ':fleetTargetPlanetID'     => $fleetTargetPlanetID,
            ':fleetTargetPlanetGalaxy' => $fleetTargetPlanetGalaxy,
            ':fleetTargetPlanetSystem' => $fleetTargetPlanetSystem,
            ':fleetTargetPlanetPlanet' => $fleetTargetPlanetPlanet,
            ':fleetTargetPlanetType'   => $fleetTargetPlanetType,
            ':fleetResource901'        => $fleetResource[901],
            ':fleetResource902'        => $fleetResource[902],
            ':fleetResource903'        => $fleetResource[903],
            ':fleetNoMReturn'          => $fleetNoMReturn,
            ':fleetGroup'              => $fleetGroup,
            ':missileTarget'           => $missileTarget,
            ':timestamp'               => TIMESTAMP,
            ':universe'                => Universe::current(),
        ]);

        $fleetId = $db->lastInsertId();

        $sql = 'INSERT INTO %%FLEETS_EVENT%% SET fleetID	= :fleetId, `time` = :endTime;';
        $db->insert($sql, [
            ':fleetId' => $fleetId,
            ':endTime' => $fleetStartTime,
        ]);

        $sql = 'INSERT INTO %%LOG_FLEETS%% SET
		fleet_id					= :fleetId,
		fleet_owner					= :fleetStartOwner,
		fleet_target_owner			= :fleetTargetOwner,
		fleet_mission				= :fleetMission,
		fleet_amount				= :fleetShipCount,
		fleet_array					= :fleetData,
		fleet_universe				= :universe,
		fleet_start_time			= :fleetStartTime,
		fleet_end_stay				= :fleetStayTime,
		fleet_end_time				= :fleetEndTime,
		fleet_start_id				= :fleetStartPlanetID,
		fleet_start_galaxy			= :fleetStartPlanetGalaxy,
		fleet_start_system			= :fleetStartPlanetSystem,
		fleet_start_planet			= :fleetStartPlanetPlanet,
		fleet_start_type			= :fleetStartPlanetType,
		fleet_end_id				= :fleetTargetPlanetID,
		fleet_end_galaxy			= :fleetTargetPlanetGalaxy,
		fleet_end_system			= :fleetTargetPlanetSystem,
		fleet_end_planet			= :fleetTargetPlanetPlanet,
		fleet_end_type				= :fleetTargetPlanetType,
		fleet_resource_metal		= :fleetResource901,
		fleet_resource_crystal		= :fleetResource902,
		fleet_resource_deuterium	= :fleetResource903,
		fleet_no_m_return = :fleetNoMReturn,
		fleet_group					= :fleetGroup,
		fleet_target_obj			= :missileTarget,
		start_time					= :timestamp;';

        $db->insert($sql, [
            ':fleetId'                 => $fleetId,
            ':fleetStartOwner'         => $fleetStartOwner,
            ':fleetTargetOwner'        => $fleetTargetOwner,
            ':fleetMission'            => $fleetMission,
            ':fleetShipCount'          => $fleetShipCount,
            ':fleetData'               => implode(';', $fleetData),
            ':fleetStartTime'          => $fleetStartTime,
            ':fleetStayTime'           => $fleetStayTime,
            ':fleetEndTime'            => $fleetEndTime,
            ':fleetStartPlanetID'      => $fleetStartPlanetID,
            ':fleetStartPlanetGalaxy'  => $fleetStartPlanetGalaxy,
            ':fleetStartPlanetSystem'  => $fleetStartPlanetSystem,
            ':fleetStartPlanetPlanet'  => $fleetStartPlanetPlanet,
            ':fleetStartPlanetType'    => $fleetStartPlanetType,
            ':fleetTargetPlanetID'     => $fleetTargetPlanetID,
            ':fleetTargetPlanetGalaxy' => $fleetTargetPlanetGalaxy,
            ':fleetTargetPlanetSystem' => $fleetTargetPlanetSystem,
            ':fleetTargetPlanetPlanet' => $fleetTargetPlanetPlanet,
            ':fleetTargetPlanetType'   => $fleetTargetPlanetType,
            ':fleetResource901'        => $fleetResource[901],
            ':fleetResource902'        => $fleetResource[902],
            ':fleetResource903'        => $fleetResource[903],
            ':fleetNoMReturn'          => $fleetNoMReturn,
            ':fleetGroup'              => $fleetGroup,
            ':missileTarget'           => $missileTarget,
            ':timestamp'               => TIMESTAMP,
            ':universe'                => Universe::current(),
        ]);
        return $fleetId;
    }
}
