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
	function __construct($Fleet)
	{
		$this->_fleet	= $Fleet;
	}

	function TargetEvent()
	{
		$sql = 'SELECT name FROM %%PLANETS%% WHERE `id` = :planetId;';

		$startPlanetName	= Database::get()->selectSingle($sql, array(
			':planetId'	=> $this->_fleet['fleet_start_id']
		), 'name');

		$targetPlanetName	= Database::get()->selectSingle($sql, array(
			':planetId'	=> $this->_fleet['fleet_end_id']
		), 'name');

		$LNG			= $this->getLanguage(NULL, $this->_fleet['fleet_owner']);


		/**
		 * If target exists, deploy resources.
		 * If it is a destroyed moon, avoid to call StoreGoodsToPlanet()
		 */
		if ($targetPlanetName) {
			$Message		= sprintf(
				$LNG['sys_tran_mess_owner'],
				$targetPlanetName,
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
				$Message,
				$this->_fleet['fleet_start_time'],
				NULL,
				1,
				$this->_fleet['fleet_universe']
			);

			if ($this->_fleet['fleet_target_owner'] != $this->_fleet['fleet_owner']) {
				$LNG			= $this->getLanguage(NULL, $this->_fleet['fleet_target_owner']);
				$Message        = sprintf(
					$LNG['sys_tran_mess_user'],
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

				$new = array();

				$new['startPlanet'] 		= $this->_fleet['fleet_start_id'];
				$new['startPlanetName']	 	= $startPlanetName;
				$new['metal'] 				= $this->_fleet['fleet_resource_metal'];
				$new['crystal'] 			= $this->_fleet['fleet_resource_crystal'];
				$new['deuterium'] 			= $this->_fleet['fleet_resource_deuterium'];
				$new['targetPlanet'] 		= $this->_fleet['fleet_end_id'];
				$new['targetPlanetName'] 	= $targetPlanetName;

				require_once 'includes/classes/class.Log.php';

				$LOG = new Log(5);
				$LOG->target = $this->_fleet['fleet_target_owner'];
				$LOG->admin = $this->_fleet['fleet_owner'];
				$LOG->old = $new;
				$LOG->new = $new;
				$LOG->saveTr();

				PlayerUtil::sendMessage(
					$this->_fleet['fleet_target_owner'],
					0,
					$LNG['sys_mess_tower'],
					5,
					$LNG['sys_mess_transport'],
					$Message,
					$this->_fleet['fleet_start_time'],
					NULL,
					1,
					$this->_fleet['fleet_universe']
				);
			}

			$this->savePlanetProduction($this->_fleet['fleet_end_id'],$this->_fleet['fleet_start_time']);
			$this->StoreGoodsToPlanet();
		}

		/**
		 * Check if returning planet exists.
		 * If is a player destroyed moon, redirect the fleet to the main planet.
		 */
		if (!$startPlanetName) {
			$originUser = Database::get()->selectSingle("SELECT id_planet, galaxy, system, planet FROM %%USERS%% WHERE id = :id", array(
				':id'	=> $this->_fleet['fleet_owner']
			));

			$this->UpdateFleet('fleet_start_id', $originUser['id_planet']);
			$this->UpdateFleet('fleet_start_galaxy', $originUser['galaxy']);
			$this->UpdateFleet('fleet_start_system', $originUser['system']);
			$this->UpdateFleet('fleet_start_planet', $originUser['planet']);
			$this->UpdateFleet('fleet_start_type', 1);
		}

		$this->setState(FLEET_RETURN);
		$this->SaveFleet();
	}

	function EndStayEvent()
	{
		return;
	}

	function ReturnEvent()
	{
		$LNG		= $this->getLanguage(NULL, $this->_fleet['fleet_owner']);
		$sql		= 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
		$planetName	= Database::get()->selectSingle($sql, array(
			':planetId'	=> $this->_fleet['fleet_start_id'],
		), 'name');

		$Message	= sprintf($LNG['sys_tran_mess_back'], $planetName, GetStartAddressLink($this->_fleet, ''));

		PlayerUtil::sendMessage(
			$this->_fleet['fleet_owner'],
			0,
			$LNG['sys_mess_tower'],
			4,
			$LNG['sys_mess_fleetback'],
			$Message,
			$this->_fleet['fleet_end_time'],
			NULL,
			1,
			$this->_fleet['fleet_universe']
		);

		$this->savePlanetProduction($this->_fleet['fleet_start_id'],$this->_fleet['fleet_end_time']);

		$this->RestoreFleet();
	}
}
