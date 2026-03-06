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

require 'includes/classes/class.FlyingFleetsTable.php';

class ShowFleetsPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {

        global $LNG, $USER;
        $db = Database::get();

        $sql = "SELECT
		fleet.*,
		event.`lock`,
		COUNT(event.fleetID) as error,
		pstart.name as startPlanetName,
		ptarget.name as targetPlanetName,
		ustart.username as startUserName,
		utarget.username as targetUserName,
		acs.name as acsName
		FROM %%FLEETS%% fleet
		LEFT JOIN %%FLEETS_EVENT%% event ON fleetID = fleet_id
		LEFT JOIN %%PLANETS%% pstart ON pstart.id = fleet_start_id
		LEFT JOIN %%PLANETS%% ptarget ON ptarget.id = fleet_end_id
		LEFT JOIN %%USERS%% ustart ON ustart.id = fleet_owner
		LEFT JOIN %%USERS%% utarget ON utarget.id = fleet_target_owner
		LEFT JOIN %%AKS%% acs ON acs.id = fleet_group
		WHERE fleet_universe = :universe
		GROUP BY event.fleetID
		ORDER BY fleet_id;";

        $fleet_result = $db->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        $fleet_list = [];

        foreach ($fleet_result as $c_fleet)
        {
            $shipList = [];
            $shipArray = array_filter(explode(';', $c_fleet['fleet_array']));
            foreach ($shipArray as $ship)
            {
                $shipDetail = explode(',', $ship);
                $shipList[$shipDetail[0]] = $shipDetail[1];
            }

            $fleet_list[] = [
                'fleetID'            => $c_fleet['fleet_id'],
                'lock'               => !empty($c_fleet['lock']),
                'count'              => $c_fleet['fleet_amount'],
                'error'              => !$c_fleet['error'],
                'ships'              => $shipList,
                'state'              => $c_fleet['fleet_mess'],
                'starttime'          => _date($LNG['php_tdformat'], $c_fleet['start_time'], $USER['timezone']),
                'arrivaltime'        => _date($LNG['php_tdformat'], $c_fleet['fleet_start_time'], $USER['timezone']),
                'stayhour'           => round(($c_fleet['fleet_end_stay'] - $c_fleet['fleet_start_time']) / 3600),
                'staytime'           => $c_fleet['fleet_start_time'] !== $c_fleet['fleet_end_stay'] ? _date($LNG['php_tdformat'], $c_fleet['fleet_end_stay'], $USER['timezone']) : 0,
                'endtime'            => _date($LNG['php_tdformat'], $c_fleet['fleet_end_time'], $USER['timezone']),
                'missionID'          => $c_fleet['fleet_mission'],
                'acsID'              => $c_fleet['fleet_group'],
                'acsName'            => $c_fleet['acsName'],
                'startUserID'        => $c_fleet['fleet_owner'],
                'startUserName'      => $c_fleet['startUserName'],
                'startPlanetID'      => $c_fleet['fleet_start_id'],
                'startPlanetName'    => $c_fleet['startPlanetName'],
                'startPlanetGalaxy'  => $c_fleet['fleet_start_galaxy'],
                'startPlanetSystem'  => $c_fleet['fleet_start_system'],
                'startPlanetPlanet'  => $c_fleet['fleet_start_planet'],
                'startPlanetType'    => $c_fleet['fleet_start_type'],
                'targetUserID'       => $c_fleet['fleet_target_owner'],
                'targetUserName'     => $c_fleet['targetUserName'],
                'targetPlanetID'     => $c_fleet['fleet_end_id'],
                'targetPlanetName'   => $c_fleet['targetPlanetName'],
                'targetPlanetGalaxy' => $c_fleet['fleet_end_galaxy'],
                'targetPlanetSystem' => $c_fleet['fleet_end_system'],
                'targetPlanetPlanet' => $c_fleet['fleet_end_planet'],
                'targetPlanetType'   => $c_fleet['fleet_end_type'],
                'resource'           => [
                    901 => $c_fleet['fleet_resource_metal'],
                    902 => $c_fleet['fleet_resource_crystal'],
                    903 => $c_fleet['fleet_resource_deuterium'],
                    921 => $c_fleet['fleet_resource_darkmatter'],
                ],
            ];
        }

        $this->assign([
            'FleetList' => $fleet_list,
        ]);

        $this->display('page.fleets.default.tpl');
    }

    public function lock(): void
    {
        $id = HTTP::_GP('id', 0);

        $lock = HTTP::_GP('lock', 0);

        $db = Database::get();

        $sql = "UPDATE %%FLEETS%% SET `fleet_busy` = :lock 
        WHERE `fleet_id` = :id AND `fleet_universe` = :universe;";

        $db->update($sql, [
            ':lock'     => $lock,
            ':id'       => $id,
            ':universe' => Universe::getEmulated(),
        ]);

        if ($lock == 0)
        {
            $sql = "UPDATE %%FLEETS_EVENT%% SET `lock` = NULL WHERE `fleetID` = :id;";
        }
        else
        {
            $sql = "UPDATE %%FLEETS_EVENT%% SET `lock` = 'ADM_LOCK' WHERE `fleetID` = :id;";
        }

        $db->update($sql, [
            ':id' => $id,
        ]);

        $this->show();
    }

}
