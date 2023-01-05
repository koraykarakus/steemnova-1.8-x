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
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

if (!allowedTo(str_replace(array(dirname(__FILE__), '\\', '/', '.php'), '', __FILE__))) throw new Exception("Permission error!");

function ShowActivePage()
{
	global $LNG, $USER;

	$db = Database::get();

	$id = HTTP::_GP('id', 0);

	if($_GET['action'] == 'delete' && !empty($id)){

		$sql = "DELETE FROM %%USERS_VALID% WHERE `validationID` = :validationID AND `universe` = :universe;";

		$db->delete($sql,array(
			':validationID' => $id,
			':universe' => Universe::getEmulated()
		));

	}

	$sql = "SELECT * FROM %%USERS_VALID%% WHERE `universe` = :universe ORDER BY validationID ASC;";

	$query = $db->select($sql,array(
		':universe' => Universe::getEmulated(),
	));

	$Users	= array();
	foreach ($query as $User) {
		$Users[]	= array(
			'id'			=> $User['validationID'],
			'name'			=> $User['userName'],
			'date'			=> _date($LNG['php_tdformat'], $User['date'], $USER['timezone']),
			'email'			=> $User['email'],
			'ip'			=> $User['ip'],
			'password'		=> $User['password'],
			'validationKey'	=> $User['validationKey'],
		);
	}

	$template	= new template();

	$template->assign_vars(array(
		'Users'				=> $Users,
		'uni'				=> Universe::getEmulated(),
	));

	$template->show('ActivePage.tpl');
}
