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

class ShowFleetAjaxPage extends AbstractGamePage
{
    public static $require_module = MODULE_FLEET_TABLE;
    public $return_data = [];

    public function __construct()
    {
        parent::__construct();
        $this->setWindow('ajax');
    }

    private function sendData($code, $message): void
    {
        $this->return_data['code'] = $code;
        $this->return_data['mess'] = $message;
        $this->sendJSON($this->return_data);
    }

    public function show(): void
    {
        global $USER, $PLANET, $RESOURCE, $LNG, $PRICELIST;
        $config = Config::get();

        $user_deu = $PLANET['deuterium'];

        $target_mission = HTTP::_GP('mission', 0);
        $galaxy = HTTP::_GP('galaxy', 1);
        $galaxy = max(1,min($galaxy, $config->max_galaxy), $galaxy);

        $system = HTTP::_GP('system', 1);
        $system = max(1,min($system, $config->max_system), $system);

        $planet = HTTP::_GP('planet', 1);
        $planet = max(1,min($planet, $config->max_planets), $planet);

        $planet_type = HTTP::_GP('planet_type', 0);       

        $active_slots = FleetFunctions::GetCurrentFleets($USER['id']);
        $max_slots = FleetFunctions::GetMaxFleetSlots($USER);

        $this->return_data['slots'] = $active_slots;

        if (inVacationMode($USER))
        {
            $this->sendData(620, $LNG['fa_vacation_mode_current']);
        }

        if ($max_slots <= $active_slots)
        {
            $this->sendData(612, $LNG['fa_no_more_slots']);
        }

        $fleet_array = [];

        $db = Database::get();

        switch ($target_mission)
        {
            case 6:
                if (!isModuleAvailable(MODULE_MISSION_SPY))
                {
                    $this->sendData(699, $LNG['sys_module_inactive']);
                }

                $ships = min($USER['spio_anz'], $PLANET[$RESOURCE[210]]);

                if (empty($ships))
                {
                    $this->sendData(611, $LNG['fa_no_spios']);
                }

                $fleet_array = [210 => $ships];
                $this->return_data['ships'][210] = $PLANET[$RESOURCE[210]] - $ships;
                break;
            case 7:
                if (!isModuleAvailable(MODULE_MISSION_COLONY))
                {
                    $this->sendData(699, $LNG['sys_module_inactive']);
                }

                if ($PLANET[$RESOURCE[208]] < 1) 
                {
                    $this->sendData(611, $LNG['fa_no_colony_ships']);
                }

                if (!PlayerUtil::allowPlanetPosition($planet, $USER)) 
                {
                    $this->sendData(611, $LNG['fa_not_enough_tech_colony']);
                }

                $fleet_array = [208 => 1];

                break;
            case 8:
                if (!isModuleAvailable(MODULE_MISSION_RECYCLE))
                {
                    $this->sendData(699, $LNG['sys_module_inactive']);
                }

                $sql = "SELECT (debris_metal + debris_crystal) as sum 
                FROM %%PLANETS%% 
                WHERE galaxy = :galaxy 
                AND system = :system
                AND planet = :planet
                AND planet_type = :planet_type;";

                $total_debris = $db->selectSingle($sql, [
                    ':galaxy' => $galaxy,
                    ':system' => $system,
                    ':planet' => $planet,
                    ':planet_type' => $planet_type
                ], 'sum');

                $rec_element_ids = [219, 209];

                $fleet_array = [];

                foreach ($rec_element_ids as $c_element_id)
                {
                    $a = $PRICELIST[$c_element_id]['capacity'] * (1 + $USER['factor']['ShipStorage']);
                    $shipsNeed = min(ceil($total_debris / $a), $PLANET[$RESOURCE[$c_element_id]]);
                    $total_debris -= ($shipsNeed * $a);

                    $fleet_array[$c_element_id] = $shipsNeed;
                    $this->return_data['ships'][$c_element_id] = $PLANET[$RESOURCE[$c_element_id]] - $shipsNeed;

                    if ($total_debris <= 0)
                    {
                        break;
                    }
                }

                if (empty($fleet_array))
                {
                    $this->sendData(611, $LNG['fa_no_recyclers']);
                }
                break;
            default:
                $this->sendData(610, $LNG['fa_mission_not_available']);
                break;
        }

        $fleet_array = array_filter($fleet_array);

        if (empty($fleet_array))
        {
            $this->sendData(610, $LNG['fa_not_enough_probes']);
        }

        $sql = "SELECT p.id_owner as id_owner,
        p.id as planet_id,
		p.galaxy as galaxy,
		p.system as system,
		p.planet as planet,
		p.planet_type as planet_type,
		total_points, onlinetime, urlaubs_modus, banaday, authattack
		FROM %%PLANETS%% p
		INNER JOIN %%USERS%% u ON p.id_owner = u.id
		LEFT JOIN %%USER_POINTS%% as s ON s.id_owner = u.id
		WHERE p.galaxy = :galaxy 
        AND p.system = :system 
        AND p.planet = :planet 
        AND p.planet_type = :planet_type;";

        if ($target_mission == 7) 
        {
            $target_data['id_owner'] = 0;
            $target_data['planet_id'] = 0;
            $target_data['galaxy'] = $galaxy;
            $target_data['system'] = $system;
            $target_data['planet'] = $planet;
            $target_data['planet_type'] = 1;
        }
        else 
        {
            $target_data = $db->selectSingle($sql, [
            ':galaxy' => $galaxy,
            ':system' => $system,
            ':planet' => $planet,
            ':planet_type' => $planet_type
            ]);
        }

        if ($target_data === false)
        {
            $this->sendData(601, $LNG['fa_planet_not_exist']);
        }

        if ($target_mission == 6)
        {
            if (Config::get()->adm_attack == 1
                && $target_data['authattack'] > $USER['authlevel'])
            {
                $this->sendData(619, $LNG['fa_action_not_allowed']);
            }

            if (inVacationMode($target_data))
            {
                $this->sendData(605, $LNG['fa_vacation_mode']);
            }

            $sql = 'SELECT total_points
			FROM %%USER_POINTS%%
			WHERE id_owner = :userId;';

            $USER += Database::get()->selectSingle($sql, [
                ':userId' => $USER['id'],
            ]);

            $is_noob_protect = CheckNoobProtec($USER, $target_data, $target_data);

            if ($is_noob_protect['NoobPlayer'])
            {
                $this->sendData(603, $LNG['fa_week_player']);
            }

            if ($is_noob_protect['StrongPlayer'])
            {
                $this->sendData(604, $LNG['fa_strong_player']);
            }

            if ($USER['id'] == $target_data['id_owner'])
            {
                $this->sendData(618, $LNG['fa_not_spy_yourself']);
            }
        }

        $speed_per_max = 10;
        $speed_factor = $distance = $speed_all_min = $duration = $consumption = 0;
        for ($i = 0; $i < $speed_per_max ; $i++)
        {

            $speed_percentage = $speed_per_max - $i;

            $speed_factor = FleetFunctions::GetGameSpeedFactor();

            $distance = FleetFunctions::GetTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$target_data['galaxy'], $target_data['system'], $target_data['planet']]
            );

            $speed_all_min = FleetFunctions::GetFleetMaxSpeed($fleet_array, $USER);

            $duration = FleetFunctions::GetMissionDuration(
                $speed_percentage,
                $speed_all_min,
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

            if ($consumption <= FleetFunctions::GetFleetRoom($fleet_array))
            {
                break;
            }

        }

        if ($consumption > FleetFunctions::GetFleetRoom($fleet_array)
            && $target_mission != 6)
        {
            $this->sendData(613, $LNG['fa_no_fleetroom']);
        }

        $user_deu -= $consumption;

        if ($user_deu < 0)
        {
            $this->sendData(613, $LNG['fa_not_enough_fuel']);
        }

        if (connection_aborted())
        {
            exit;
        }

        $this->return_data['slots']++;

        $fleet_resource = [
            901 => 0,
            902 => 0,
            903 => 0,
        ];

        $fleet_start_time = $duration + TIMESTAMP;
        $fleet_stay_time = $fleet_start_time;
        $fleet_end_time = $fleet_stay_time + $duration;

        $ship_ids = array_keys($fleet_array);
        $PLANET['deuterium'] -= $consumption;

        FleetFunctions::sendFleet(
            $fleet_array,
            $target_mission,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $target_data['id_owner'],
            $target_data['planet_id'],
            $target_data['galaxy'],
            $target_data['system'],
            $target_data['planet'],
            $target_data['planet_type'],
            $fleet_resource,
            $fleet_start_time,
            $fleet_stay_time,
            $fleet_end_time,
            0,
            0,
            0,
            $consumption
        );

        $this->sendData(
            600,
            $LNG['fa_sending'] .
            " " .
            array_sum($fleet_array) .
            " " .
            $LNG['tech'][$ship_ids[0]] .
            " " .
            $LNG['gl_to'] .
            " " .
            $target_data['galaxy'] .
            ":" .
            $target_data['system'].
            ":".
            $target_data['planet'].
            " ..."
        );
    }
}
