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

class MissionCaseStay extends MissionFunctions implements Mission
{
    public function __construct($fleet)
    {
        $this->_fleet = $fleet;
    }

    public function TargetEvent()
    {

        $sql = 'SELECT * FROM %%USERS%% WHERE id = :userId;';
        $sender_user = Database::get()->selectSingle($sql, [
            ':userId' => $this->_fleet['fleet_owner'],
        ]);

        $sender_user['factor'] = getFactors($sender_user, 'basic', $this->_fleet['fleet_start_time']);

        $fleet_array = FleetFunctions::unserialize($this->_fleet['fleet_array']);
        $duration = $this->_fleet['fleet_start_time'] - $this->_fleet['start_time'];

        $speed_factor = FleetFunctions::GetGameSpeedFactor();
        $distance = FleetFunctions::GetTargetDistance(
            [$this->_fleet['fleet_start_galaxy'], $this->_fleet['fleet_start_system'], $this->_fleet['fleet_start_planet']],
            [$this->_fleet['fleet_end_galaxy'], $this->_fleet['fleet_end_system'], $this->_fleet['fleet_end_planet']]
        );

        $consumption = FleetFunctions::GetFleetConsumption(
            $fleet_array,
            $duration,
            $distance,
            $sender_user,
            $speed_factor
        );

        $this->UpdateFleet('fleet_resource_deuterium', $this->_fleet['fleet_resource_deuterium'] + $consumption / 2);

        $LNG = $this->getLanguage($sender_user['lang']);
        $target_user_id = $this->_fleet['fleet_target_owner'];
        $target_message = sprintf(
            $LNG['sys_stat_mess'],
            GetTargetAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        PlayerUtil::sendMessage(
            $target_user_id,
            0,
            $LNG['sys_mess_tower'],
            5,
            $LNG['sys_stat_mess_stay'],
            $target_message,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->savePlanetProduction($this->_fleet['fleet_end_id'], $this->_fleet['fleet_start_time']);

        $this->RestoreFleet(false);
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $message = sprintf(
            $LNG['sys_stat_mess'],
            GetStartAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            4,
            $LNG['sys_mess_fleetback'],
            $message,
            $this->_fleet['fleet_end_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->savePlanetProduction($this->_fleet['fleet_start_id'], $this->_fleet['fleet_end_time']);

        $this->RestoreFleet();
    }
}
