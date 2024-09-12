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

 class ShowResetPage extends AbstractAdminPage
 {

 	function __construct()
 	{
 		parent::__construct();
 	}
	function show()
	{
		global $LNG, $reslist, $resource;

		$config	= Config::get(ROOT_UNI);



		$this->assign(array(
			'button_submit'						=> $LNG['button_submit'],
			're_reset_universe_confirmation'	=> $LNG['re_reset_universe_confirmation'],
			're_reset_all'						=> $LNG['re_reset_all'],
			're_reset_all'						=> $LNG['re_reset_all'],
			're_defenses_and_ships'				=> $LNG['re_defenses_and_ships'],
			're_reset_buldings'					=> $LNG['re_reset_buldings'],
			're_buildings_lu'					=> $LNG['re_buildings_lu'],
			're_buildings_pl'					=> $LNG['re_buildings_pl'],
			're_buldings'						=> $LNG['re_buldings'],
			're_reset_hangar'					=> $LNG['re_reset_hangar'],
			're_ships'							=> $LNG['re_ships'],
			're_defenses'						=> $LNG['re_defenses'],
			're_resources_met_cry'				=> $LNG['re_resources_met_cry'],
			're_resources_dark'					=> $LNG['re_resources_dark'],
			're_resources'						=> $LNG['re_resources'],
			're_reset_invest'					=> $LNG['re_reset_invest'],
			're_investigations'					=> $LNG['re_investigations'],
			're_ofici'							=> $LNG['re_ofici'],
			're_inve_ofis'						=> $LNG['re_inve_ofis'],
			're_reset_statpoints'				=> $LNG['re_reset_statpoints'],
			're_reset_messages'					=> $LNG['re_reset_messages'],
			're_reset_banned'					=> $LNG['re_reset_banned'],
			're_reset_errors'					=> $LNG['re_reset_errors'],
			're_reset_fleets'					=> $LNG['re_reset_fleets'],
			're_reset_allys'					=> $LNG['re_reset_allys'],
			're_reset_buddies'					=> $LNG['re_reset_buddies'],
			're_reset_rw'						=> $LNG['re_reset_rw'],
			're_reset_notes'					=> $LNG['re_reset_notes'],
			're_reset_moons'					=> $LNG['re_reset_moons'],
			're_reset_planets'					=> $LNG['re_reset_planets'],
			're_reset_player'					=> $LNG['re_reset_player'],
			're_player_and_planets'				=> $LNG['re_player_and_planets'],
			're_general'						=> $LNG['re_general'],
		));

		$this->display('ResetPage.tpl');
	}


	function send(){
		global $reslist, $resource, $LNG;


		foreach($reslist['build'] as $ID)
		{
			$dbcol['build'][$ID]	= "`".$resource[$ID]."` = '0'";
		}

		foreach($reslist['tech'] as $ID)
		{
			$dbcol['tech'][$ID]		= "`".$resource[$ID]."` = '0'";
		}

		foreach($reslist['fleet'] as $ID)
		{
			$dbcol['fleet'][$ID]	= "`".$resource[$ID]."` = '0'";
		}

		foreach($reslist['defense'] as $ID)
		{
			$dbcol['defense'][$ID]	= "`".$resource[$ID]."` = '0'";
		}

		foreach($reslist['officier'] as $ID)
		{
			$dbcol['officier'][$ID]	= "`".$resource[$ID]."` = '0'";
		}

		foreach($reslist['resstype'][1] as $ID)
		{
			if(isset($config->{$resource[$ID].'_start'}))
			{
				$dbcol['resource_planet_start'][$ID]	= "`".$resource[$ID]."` = ".$config->{$resource[$ID].'_start'};
			}
		}

		foreach($reslist['resstype'][3] as $ID)
		{
			if(isset($config->{$resource[$ID].'_start'}))
			{
				$dbcol['resource_user_start'][$ID]	= "`".$resource[$ID]."` = ".$config->{$resource[$ID].'_start'};
			}
		}

		// Players and Planets

		$deletePlayers = (HTTP::_GP('players', 'off') == 'on') ? true : false;
		$deletePlanets = (HTTP::_GP('planets', 'off') == 'on') ? true : false;
		$deleteMoons = (HTTP::_GP('moons', 'off') == 'on') ? true : false;
		$deleteDefenses = (HTTP::_GP('defenses', 'off') == 'on') ? true : false;
		$deleteShips = (HTTP::_GP('ships', 'off') == 'on') ? true : false;
		$deleteHd = (HTTP::_GP('h_d', 'off') == 'on') ? true : false;
		$deleteEdif_p = (HTTP::_GP('edif_p', 'off') == 'on') ? true : false;
		$deleteEdif_l = (HTTP::_GP('edif_l', 'off') == 'on') ? true : false;
		$deleteEdif = (HTTP::_GP('edif', 'off') == 'on') ? true : false;
		$deleteInves = (HTTP::_GP('inves', 'off') == 'on') ? true : false;
		$deleteOfis = (HTTP::_GP('ofis', 'off') == 'on') ? true : false;
		$deleteInves_c = (HTTP::_GP('inves_c', 'off') == 'on') ? true : false;
		$deleteDark = (HTTP::_GP('dark', 'off') == 'on') ? true : false;
		$deleteResources = (HTTP::_GP('resources', 'off') == 'on') ? true : false;
		$deleteNotes = (HTTP::_GP('notes', 'off') == 'on') ? true : false;
		$deleteRW = (HTTP::_GP('rw', 'off') == 'on') ? true : false;
		$deleteFriends = (HTTP::_GP('rw', 'off') == 'on') ? true : false;
		$deleteAlliances = (HTTP::_GP('alliances', 'off') == 'on') ? true : false;
		$deleteFleets = (HTTP::_GP('fleets', 'off') == 'on') ? true : false;
		$deleteBanneds = (HTTP::_GP('banneds', 'off') == 'on') ? true : false;
		$deleteMessages = (HTTP::_GP('messages', 'off') == 'on') ? true : false;
		$deleteStatpoints = (HTTP::_GP('statpoints', 'off') == 'on') ? true : false;

		#players
		if ($deletePlayers){
			$ID	= $GLOBALS['DATABASE']->getFirstCell("SELECT `id_owner` FROM ".PLANETS." WHERE `universe` = ".Universe::getEmulated()." AND `galaxy` = '1' AND `system` = '1' AND `planet` = '1';");
			$GLOBALS['DATABASE']->multi_query("DELETE FROM ".USERS." WHERE `universe` = ".Universe::getEmulated()." AND `id` != '".$ID."';DELETE FROM ".PLANETS." WHERE `universe` = ".Universe::getEmulated()." AND `id_owner` != '".$ID."';");
		}

		#planets
		if ($deletePlanets){
			$GLOBALS['DATABASE']->multi_query("DELETE FROM ".PLANETS." WHERE `universe` = ".Universe::getEmulated()." AND `id` NOT IN (SELECT id_planet FROM ".USERS." WHERE `universe` = ".Universe::getEmulated().");UPDATE ".PLANETS." SET `id_luna` = '0' WHERE `universe` = ".Universe::getEmulated().";");
		}

		#moons
		if ($deleteMoons){
			$GLOBALS['DATABASE']->multi_query("DELETE FROM ".PLANETS." WHERE `planet_type` = '3' AND `universe` = ".Universe::getEmulated().";UPDATE ".PLANETS." SET `id_luna` = '0' WHERE `universe` = ".Universe::getEmulated().";");
		}

		# shipyard & defenses
		if ($deleteDefenses)
			$GLOBALS['DATABASE']->query("UPDATE ".PLANETS." SET ".implode(", ",$dbcol['defense'])." WHERE `universe` = ".Universe::getEmulated().";");

		if ($deleteShips)
			$GLOBALS['DATABASE']->query("UPDATE ".PLANETS." SET ".implode(", ",$dbcol['fleet'])." WHERE `universe` = ".Universe::getEmulated().";");

		if ($deleteHd)
			$GLOBALS['DATABASE']->query("UPDATE ".PLANETS." SET `b_hangar` = '0', `b_hangar_id` = '' WHERE `universe` = ".Universe::getEmulated().";");


		# buildings
		if ($deleteEdif_p)
			$GLOBALS['DATABASE']->query("UPDATE ".PLANETS." SET ".implode(", ",$dbcol['build']).", `field_current` = '0' WHERE `planet_type` = '1' AND `universe` = ".Universe::getEmulated().";");

		if ($deleteEdif_l)
			$GLOBALS['DATABASE']->query("UPDATE ".PLANETS." SET ".implode(", ",$dbcol['build']).", `field_current` = '0' WHERE `planet_type` = '3' AND `universe` = ".Universe::getEmulated().";");

		if ($deleteEdif)
			$GLOBALS['DATABASE']->query("UPDATE ".PLANETS." SET `b_building` = '0', `b_building_id` = '' WHERE `universe` = ".Universe::getEmulated().";");


		# research & officers
		if ($deleteInves)
			$GLOBALS['DATABASE']->query("UPDATE ".USERS." SET ".implode(", ",$dbcol['tech'])." WHERE `universe` = ".Universe::getEmulated().";");

		if ($deleteOfis)
			$GLOBALS['DATABASE']->query("UPDATE ".USERS." SET ".implode(", ",$dbcol['officier'])." WHERE `universe` = ".Universe::getEmulated().";");

		if ($deleteInves_c)
			$GLOBALS['DATABASE']->query("UPDATE ".USERS." SET `b_tech_planet` = '0', `b_tech` = '0', `b_tech_id` = '0', `b_tech_queue` = '' WHERE `universe` = ".Universe::getEmulated().";");


		# Resources
		if ($deleteDark){
			$GLOBALS['DATABASE']->query("UPDATE ".USERS." SET ".implode(", ",$dbcol['resource_user_start'])." WHERE `universe` = ".Universe::getEmulated().";");
		}

		if ($deleteResources){
			$GLOBALS['DATABASE']->query("UPDATE ".PLANETS." SET ".implode(", ",$dbcol['resource_planet_start'])." WHERE `universe` = ".Universe::getEmulated().";");
		}

		// GENERAL
		if ($deleteNotes){
			$GLOBALS['DATABASE']->query("DELETE FROM ".NOTES." WHERE `universe` = ".Universe::getEmulated().";");
		}

		if ($deleteRW){
			$GLOBALS['DATABASE']->query("DELETE FROM ".TOPKB." WHERE `universe` = ".Universe::getEmulated().";");
		}

		if ($deleteFriends){
			$GLOBALS['DATABASE']->query("DELETE FROM ".BUDDY." WHERE `universe` = ".Universe::getEmulated().";");
		}

		if ($deleteAlliances){
			$GLOBALS['DATABASE']->multi_query("DELETE FROM ".ALLIANCE." WHERE `ally_universe` = '".Universe::getEmulated()."';UPDATE ".USERS." SET `ally_id` = '0', `ally_register_time` = '0', `ally_rank_id` = '0' WHERE `universe` = ".Universe::getEmulated().";");
		}

		if ($deleteFleets){
			$GLOBALS['DATABASE']->query("DELETE FROM ".FLEETS." WHERE `fleet_universe` = '".Universe::getEmulated()."';");
		}

		if ($deleteBanneds){
			$GLOBALS['DATABASE']->multi_query("DELETE FROM ".BANNED." WHERE `universe` = ".Universe::getEmulated().";UPDATE ".USERS." SET `bana` = '0', `banaday` = '0' WHERE `universe` = ".Universe::getEmulated().";");
		}

		if ($deleteMessages){
			$GLOBALS['DATABASE']->multi_query("DELETE FROM ".MESSAGES." WHERE `message_universe` = '".Universe::getEmulated()."';");
		}

		if ($deleteStatpoints){
			$GLOBALS['DATABASE']->query("DELETE FROM ".STATPOINTS." WHERE `universe` = ".Universe::getEmulated().";");
		}

		$this->printMessage($LNG['re_reset_excess']);
	}

}
