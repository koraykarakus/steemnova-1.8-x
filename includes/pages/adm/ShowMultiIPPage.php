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

if (!allowedTo(str_replace(array(dirname(__FILE__), '\\', '/', '.php'), '', __FILE__))) throw new Exception("Permission error!");

function ShowMultiIPPage()
{
	global $LNG;

	$db = Database::get();

	if(!isset($_GET['action'])) { $_GET['action'] = ''; }
	switch($_GET['action'])
	{
		case 'known':

		$sql = "INSERT INTO %%MULTI%% SET userID = :userID;";


			$db->insert($sql,array(
				':userID' => HTTP::_GP('id',0)
			));

			HTTP::redirectTo("admin.php?page=multiips");
		break;
		case 'unknown':

		$sql = "DELETE FROM %%MULTI%% WHERE userID = :userID;";

			$db->delete($sql,array(
				':userID' => HTTP::_GP('id',0)
			));

			HTTP::redirectTo("admin.php?page=multiips");
		break;
	}

	$sql = "SELECT id, username, email, register_time, onlinetime, user_lastip, IFNULL(multiID, 0) as isKnown
	FROM %%USERS%% LEFT JOIN %%MULTI%% ON userID = id
	WHERE `universe` = :universe AND user_lastip IN (SELECT user_lastip FROM %%USERS%% WHERE `universe` = :universe GROUP BY user_lastip HAVING COUNT(*)>1) ORDER BY user_lastip, id ASC;";

	$Query	= $db->select($sql,array(
		':universe' => Universe::getEmulated()
	));

	$IPs	= array();
	foreach($Query as $Data) {
		if(!isset($IPs[$Data['user_lastip']]))
			$IPs[$Data['user_lastip']]	= array();

		$Data['register_time']	= _date($LNG['php_tdformat'], $Data['register_time']);
		$Data['onlinetime']		= _date($LNG['php_tdformat'], $Data['onlinetime']);

		$IPs[$Data['user_lastip']][$Data['id']]	= $Data;
	}

	$template	= new template();
	$template->assign_vars(array(
		'multiGroups'	=> $IPs,
	));
	$template->show('MultiIPs.tpl');
}
