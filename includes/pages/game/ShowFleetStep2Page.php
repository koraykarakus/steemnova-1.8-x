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

class ShowFleetStep2Page extends AbstractGamePage
{
    public static $require_module = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $PLANET, $LNG;

        $this->tpl_obj->loadscript('flotten.js');

        $target_galaxy = HTTP::_GP('galaxy', 0);
        $target_system = HTTP::_GP('system', 0);
        $target_planet = HTTP::_GP('planet', 0);
        $target_type = HTTP::_GP('type', 0);
        $target_mission = HTTP::_GP('target_mission', 0);
        $fleet_speed = HTTP::_GP('speed', 0);
        $fleet_group = HTTP::_GP('fleet_group', 0);
        $token = HTTP::_GP('token', '');

        if (!isset($_SESSION['fleet'][$token]))
        {
            FleetFunctions::GotoFleetPage();
        }

        $fleet_array = $_SESSION['fleet'][$token]['fleet'];

        $db = Database::get();
        $sql = "SELECT id, id_owner, debris_metal, debris_crystal FROM %%PLANETS%% 
        WHERE universe = :universe AND galaxy = :target_galaxy 
        AND system = :target_system AND planet = :target_planet AND planet_type = '1';";

        $target_planet_data = $db->selectSingle($sql, [
            ':universe'      => Universe::current(),
            ':target_galaxy' => $target_galaxy,
            ':target_system' => $target_system,
            ':target_planet' => $target_planet,
        ]);

        if ($target_type == 2
            && $target_planet_data['debris_metal'] == 0
            && $target_planet_data['debris_crystal'] == 0)
        {
            $this->printMessage($LNG['fl_error_empty_derbis'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        $mission_info = [];
        $mission_info['galaxy'] = $target_galaxy;
        $mission_info['system'] = $target_system;
        $mission_info['planet'] = $target_planet;
        $mission_info['planettype'] = $target_type;
        $mission_info['IsAKS'] = $fleet_group;
        $mission_info['Ship'] = $fleet_array;

        $mission_output = FleetFunctions::GetFleetMissions(
            $USER,
            $mission_info,
            $target_planet_data
        );

        if (empty($mission_output['MissionSelector']))
        {
            $this->printMessage($LNG['fl_empty_target'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        $game_speed_factor = FleetFunctions::GetGameSpeedFactor();
        $max_fleet_speed = FleetFunctions::GetFleetMaxSpeed($fleet_array, $USER);
        $distance = FleetFunctions::GetTargetDistance(
            [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
            [$target_galaxy, $target_system, $target_planet]
        );
        $duration = FleetFunctions::GetMissionDuration(
            $fleet_speed,
            $max_fleet_speed,
            $distance,
            $game_speed_factor,
            $USER
        );
        $consumption = FleetFunctions::GetFleetConsumption(
            $fleet_array,
            $duration,
            $distance,
            $USER,
            $game_speed_factor
        );

        if ($consumption > $PLANET['deuterium'])
        {
            $this->printMessage($LNG['fl_not_enough_deuterium'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        if (!FleetFunctions::CheckUserSpeed($fleet_speed))
        {
            FleetFunctions::GotoFleetPage(0);
        }

        $_SESSION['fleet'][$token]['speed'] = $max_fleet_speed;
        $_SESSION['fleet'][$token]['distance'] = $distance;
        $_SESSION['fleet'][$token]['targetGalaxy'] = $target_galaxy;
        $_SESSION['fleet'][$token]['targetSystem'] = $target_system;
        $_SESSION['fleet'][$token]['targetPlanet'] = $target_planet;
        $_SESSION['fleet'][$token]['targetType'] = $target_type;
        $_SESSION['fleet'][$token]['fleetGroup'] = $fleet_group;
        $_SESSION['fleet'][$token]['fleetSpeed'] = $fleet_speed;
        $_SESSION['fleet'][$token]['ownPlanet'] = $PLANET['id'];

        if (!empty($fleet_group))
        {
            $target_mission = 2;
        }

        $fleet_data = [
            'fleetroom'   => floatToString($_SESSION['fleet'][$token]['fleetRoom']),
            'consumption' => floatToString($consumption),
        ];

        $this->tpl_obj->execscript('calculateTransportCapacity();');
        $this->assign([
            'fleetdata'           => $fleet_data,
            'consumption'         => floatToString($consumption),
            'mission'             => $target_mission,
            'galaxy'              => $PLANET['galaxy'],
            'system'              => $PLANET['system'],
            'planet'              => $PLANET['planet'],
            'type'                => $PLANET['planet_type'],
            'MissionSelector'     => $mission_output['MissionSelector'],
            'StaySelector'        => $mission_output['StayBlock'],
            'Exchange'            => $mission_output['Exchange'],
            'fl_dm_alert_message' => sprintf(
                $LNG['fl_dm_alert_message'],
                $LNG['type_mission_11'],
                $LNG['tech'][921]
            ),
            'fl_continue' => $LNG['fl_continue'],
            'token'       => $token,
        ]);

        $this->display('page.fleetStep2.default.tpl');
    }
}
