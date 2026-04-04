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
class MissionCaseTransport extends MissionFunctions implements Mission
{
    public function __construct($fleet)
    {
        $this->_fleet = $fleet;
    }

    public function TargetEvent()
    {
        $sql = 'SELECT name FROM %%PLANETS%% WHERE `id` = :planet_id;';

        $start_planet_name = Database::get()->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_start_id'],
        ], 'name');

        $target_planet_name = Database::get()->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_end_id'],
        ], 'name');

        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        // If target exists, deploy resources.
        // If it is a destroyed moon, avoid to call StoreGoodsToPlanet()
        if ($target_planet_name)
        {
            $message = sprintf(
                $LNG['sys_tran_mess_owner'],
                $target_planet_name,
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
                5,
                $LNG['sys_mess_transport'],
                $message,
                $this->_fleet['fleet_start_time'],
                null,
                1,
                $this->_fleet['fleet_universe']
            );

            if ($this->_fleet['fleet_target_owner'] != $this->_fleet['fleet_owner'])
            {
                $LNG = $this->getLanguage(null, $this->_fleet['fleet_target_owner']);
                $message = sprintf(
                    $LNG['sys_tran_mess_user'],
                    $start_planet_name,
                    GetStartAddressLink($this->_fleet, ''),
                    $target_planet_name,
                    GetTargetAddressLink($this->_fleet, ''),
                    pretty_number($this->_fleet['fleet_resource_metal']),
                    $LNG['tech'][901],
                    pretty_number($this->_fleet['fleet_resource_crystal']),
                    $LNG['tech'][902],
                    pretty_number($this->_fleet['fleet_resource_deuterium']),
                    $LNG['tech'][903]
                );

                $new = [];

                $new['startPlanet'] = $this->_fleet['fleet_start_id'];
                $new['startPlanetName'] = $start_planet_name;
                $new['metal'] = $this->_fleet['fleet_resource_metal'];
                $new['crystal'] = $this->_fleet['fleet_resource_crystal'];
                $new['deuterium'] = $this->_fleet['fleet_resource_deuterium'];
                $new['targetPlanet'] = $this->_fleet['fleet_end_id'];
                $new['targetPlanetName'] = $target_planet_name;

                require_once 'includes/classes/class.Log.php';

                $log = new Log(5);
                $log->target = $this->_fleet['fleet_target_owner'];
                $log->admin = $this->_fleet['fleet_owner'];
                $log->old = $new;
                $log->new = $new;
                $log->saveTr();

                PlayerUtil::sendMessage(
                    $this->_fleet['fleet_target_owner'],
                    0,
                    $LNG['sys_mess_tower'],
                    5,
                    $LNG['sys_mess_transport'],
                    $message,
                    $this->_fleet['fleet_start_time'],
                    null,
                    1,
                    $this->_fleet['fleet_universe']
                );
            }

            $this->savePlanetProduction($this->_fleet['fleet_end_id'], $this->_fleet['fleet_start_time']);
            $this->StoreGoodsToPlanet();
        }

        // Check if returning planet exists.
        // If is a player destroyed moon, redirect the fleet to the main planet.
        if (!$start_planet_name)
        {
            $origin_user = Database::get()->selectSingle("SELECT id_planet, galaxy, system, planet FROM %%USERS%% WHERE id = :id", [
                ':id' => $this->_fleet['fleet_owner'],
            ]);

            $this->UpdateFleet('fleet_start_id', $origin_user['id_planet']);
            $this->UpdateFleet('fleet_start_galaxy', $origin_user['galaxy']);
            $this->UpdateFleet('fleet_start_system', $origin_user['system']);
            $this->UpdateFleet('fleet_start_planet', $origin_user['planet']);
            $this->UpdateFleet('fleet_start_type', 1);
        }

        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
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
            $LNG['sys_tran_mess_back'],
            $planet_name,
            GetStartAddressLink($this->_fleet, '')
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
