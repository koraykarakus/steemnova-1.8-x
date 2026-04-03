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

class MissionCaseACS extends MissionFunctions implements Mission
{
    public function __construct($fleet)
    {
        $this->_fleet = $fleet;
    }

    public function TargetEvent()
    {
        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
        return;
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);
        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planet_id;';
        $planet_name = Database::get()->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_start_id'],
        ], 'name');

        $message = sprintf(
            $LNG['sys_fleet_won'],
            $planet_name,
            GetTargetAddressLink($this->_fleet, ''),
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

        $this->RestoreFleet();
    }
}
