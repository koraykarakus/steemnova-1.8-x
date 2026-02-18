<?php

/**
 *  2Moons / Steemnova
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package Steemnova
 * @author Adam Jordanek <dotevo@gmail.com>
 * @licence MIT
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 */

class MissionCaseTransfer extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        $sql = 'SELECT name FROM %%PLANETS%% WHERE `id` = :planetId;';

        $startPlanetName = Database::get()->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_start_id'],
        ], 'name');

        $targetPlanetName = Database::get()->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
        ], 'name');

        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $Message = sprintf(
            $LNG['sys_transfer_mess_owner'],
            $targetPlanetName,
            GetTargetAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        $fleet = FleetFunctions::unserialize($this->_fleet['fleet_array']);

        foreach ($fleet as $elementID => $amount)
        {
            $Message .= '<br>'.$LNG['tech'][$elementID].': '.pretty_number($amount);
        }

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            5,
            $LNG['sys_mess_transport'],
            $Message,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $LNG = $this->getLanguage(null, $this->_fleet['fleet_target_owner']);
        $Message = sprintf(
            $LNG['sys_transfer_mess_user'],
            $startPlanetName,
            GetStartAddressLink($this->_fleet, ''),
            $targetPlanetName,
            GetTargetAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        foreach ($fleet as $elementID => $amount)
        {
            $Message .= '<br>'.$LNG['tech'][$elementID].': '.pretty_number($amount);
        }

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_target_owner'],
            0,
            $LNG['sys_mess_tower'],
            5,
            $LNG['sys_mess_transport'],
            $Message,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->StoreGoodsToPlanet();
        $this->RestoreFleet(false);
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $this->RestoreFleet();
    }
}
