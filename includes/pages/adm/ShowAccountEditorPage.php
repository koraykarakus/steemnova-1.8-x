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

# Actions not logged: Planet-Edit, Alliance-Edit

if (!allowedTo(str_replace(array(dirname(__FILE__), '\\', '/', '.php'), '', __FILE__))) throw new Exception("Permission error!");

function ShowAccountEditorPage()
{
	global $LNG, $reslist, $resource;
	$template 	= new template();
	$db = Database::get();

	$editType = HTTP::_GP('edit','');

	switch($editType)
	{
		case 'resources':
			$id         = HTTP::_GP('id', 0);
			$id_dark    = HTTP::_GP('id_dark', 0);
			$metal      = max(0, round(HTTP::_GP('metal', 0.0)));
			$cristal    = max(0, round(HTTP::_GP('cristal', 0.0)));
			$deut       = max(0, round(HTTP::_GP('deut', 0.0)));
			$dark		= HTTP::_GP('dark', 0);

			if ($_POST)
			{
				if (!empty($id)){

					$sql = "SELECT `metal`,`crystal`,`deuterium`,`universe`  FROM %%PLANETS%% WHERE `id` = :id;";

					$before = $db->selectSingle($sql,array(
						':id' => $id
					));

				}
				if (!empty($id_dark)){

					$sql = "SELECT `darkmatter` FROM %%USERS%% WHERE `id` = :id_dark;";

					$before_dm = $db->selectSingle($sql,array(
						':id_dark' => $id_dark
					));

				}
				if ($_POST['add'])
				{
					if (!empty($id)) {
						$SQL  = "UPDATE %%PLANETS%% SET ";
						$SQL .= "`metal` = `metal` + :metal, ";
						$SQL .= "`crystal` = `crystal` + :cristal, ";
						$SQL .= "`deuterium` = `deuterium` + :deut ";
						$SQL .= "WHERE ";
						$SQL .= "`id` = :id AND `universe` = :universe;";

						$db->update($SQL,array(
							':metal' => $metal,
							':cristal' => $cristal,
							':deut' => $deut,
							':id' => $id,
							':universe' => Universe::getEmulated(),
						));

						$after 		= array(
							'metal' => ($before['metal'] + $metal),
							'crystal' => ($before['crystal'] + $cristal),
							'deuterium' => ($before['deuterium'] + $deut)
						);

					}

					if (!empty($id_dark)) {
						$SQL  = "UPDATE %%USERS%% SET ";
						$SQL .= "`darkmatter` = `darkmatter` + :dark ";
						$SQL .= "WHERE ";
						$SQL .= "`id` = :id_dark AND `universe` = :universe;";

						$db->update($SQL,array(
							':dark' => $dark,
							':id_dark' => $id_dark,
							':universe' => Universe::getEmulated(),
						));

						$after_dm 	= array(
							'darkmatter' => ($before_dm['darkmatter'] + $dark)
						);

					}
				}
				elseif ($_POST['delete'])
				{
					if (!empty($id)) {
						$SQL  = "UPDATE %%PLANETS%% SET ";
						$SQL .= "`metal` = GREATEST(0, `metal` - :metal), ";
						$SQL .= "`crystal` = GREATEST(0, `crystal` - :cristal), ";
						$SQL .= "`deuterium` = GREATEST(0, `deuterium` - :deut) ";
						$SQL .= "WHERE ";
						$SQL .= "`id` = :id AND `universe` = :universe;";

						$db->update($SQL,array(
							':metal' => $metal,
							':cristal' => $cristal,
							':deut' => $deut,
							':id' => $id,
							':universe' => Universe::getEmulated()
						));

						$after 		= array(
							'metal' => ($before['metal'] - $metal),
							'crystal' => ($before['crystal'] - $cristal),
							'deuterium' => ($before['deuterium'] - $deut)
						);

					}

					if (!empty($id_dark)) {
						$SQL  = "UPDATE %%USERS%% SET ";
						$SQL .= "`darkmatter` = GREATEST(0, `darkmatter` - :dark) ";
						$SQL .= "WHERE ";
						$SQL .= "`id` = :id_dark;";

						$db->update($SQL,array(
							':dark' => $dark,
							':id_dark' => $id_dark
						));

						$after_dm 	= array(
							'darkmatter' => ($before_dm['darkmatter'] - $dark)
						);

					}
				}

				if (!empty($id)) {
					$LOG = new Log(2);
					$LOG->target = $id;
					$LOG->universe = $before_dm['universe'];
					$LOG->old = $before;
					$LOG->new = $after;
					$LOG->save();
				}

				if (!empty($id_dark)) {
					$LOG = new Log(1);
					$LOG->target = $id_dark;
					$LOG->universe = $before_dm['universe'];
					$LOG->old = $before_dm;
					$LOG->new = $after_dm;
					$LOG->save();
				}

				if ($_POST['add']) {
					$template->message($LNG['ad_add_res_sucess'], '?page=accounteditor&edit=resources');
				} else if ($_POST['delete']) {
					$template->message($LNG['ad_delete_res_sucess'], '?page=accounteditor&edit=resources');
				}
				exit;
			}


			$template->show('AccountEditorPageResources.tpl');
		break;
		case 'ships':
			if($_POST)
			{

				$sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planetId;";

				$before1 = $db->selectSingle($sql,array(
					':planetId' =>  HTTP::_GP('id', 0)
				));

				$before = array();
				$after = array();
				foreach($reslist['fleet'] as $ID)
				{
					$before[$ID] = $before1[$resource[$ID]];
				}
				if ($_POST['add'])
				{
					$SQL  = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
					foreach($reslist['fleet'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = `".$resource[$ID]."` + '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."'";
						$after[$ID] = $before[$ID] + max(0, round(HTTP::_GP($resource[$ID], 0.0)));
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :planetId AND `universe` = :universe;";

					$db->update($SQL,array(
						':planetId' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated(),
					));

				}
				elseif ($_POST['delete'])
				{
					$SQL  = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";

					foreach($reslist['fleet'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = GREATEST(0,  `".$resource[$ID]."` - '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."')";
						$after[$ID] = max($before[$ID] - max(0, round(HTTP::_GP($resource[$ID], 0.0))),0);
					}

					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :planetId AND `universe` = :universe;";
					$db->update($SQL,array(
						':planetId' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated()
					));
				}

				$LOG = new Log(2);
				$LOG->target = HTTP::_GP('id', 0);
				$LOG->universe = $before1['universe'];
				$LOG->old = $before;
				$LOG->new = $after;
				$LOG->save();

				if ($_POST['add']) {
					$template->message($LNG['ad_add_ships_sucess'], '?page=accounteditor&edit=ships');
				} else if ($_POST['delete']) {
					$template->message($LNG['ad_delete_ships_sucess'], '?page=accounteditor&edit=ships');
				}
				exit;
			}

			$parse['ships']	= "";
			foreach($reslist['fleet'] as $ID)
			{
				$INPUT[$ID]	= array(
					'type'	=> $resource[$ID],
				);
			}

			$template->assign_vars(array(
				'inputlist'			=> $INPUT,
			));

			$template->show('AccountEditorPageShips.tpl');
		break;

		case 'defenses':
			if($_POST)
			{
				$sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planetId;";

				$before1 = $db->selectSingle($sql,array(
				 ':planetId' => HTTP::_GP('id', 0)
				));

				$before = array();
				$after = array();
				foreach($reslist['defense'] as $ID)
				{
					$before[$ID] = $before1[$resource[$ID]];
				}
				if ($_POST['add'])
				{
					$SQL  = "UPDATE %%PLANETS%% SET ";
					foreach($reslist['defense'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = `".$resource[$ID]."` + '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."'";
						$after[$ID] = $before[$ID] + max(0, round(HTTP::_GP($resource[$ID], 0.0)));
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :planetId AND `universe` = :universe;";

					$db->update($SQL,array(
						':planetId' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated()
					));

				}
				elseif ($_POST['delete'])
				{
					$SQL  = "UPDATE %%PLANETS%% SET ";
					foreach($reslist['defense'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = GREATEST (0, `".$resource[$ID]."` - '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."')";
						$after[$ID] = max($before[$ID] - max(0, round(HTTP::_GP($resource[$ID], 0.0))),0);
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :planetId AND `universe` = :universe;";
					$db->update($SQL,array(
						':planetId' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated()
					));
					$Name	=	$LNG['log_nomoree'];
				}

				$LOG = new Log(2);
				$LOG->target = HTTP::_GP('id', 0);
				$LOG->universe = $before1['universe'];
				$LOG->old = $before;
				$LOG->new = $after;
				$LOG->save();

				if ($_POST['add']) {
					$template->message($LNG['ad_add_defenses_success'], '?page=accounteditor&edit=defenses');
				} else if ($_POST['delete']) {
					$template->message($LNG['ad_delete_defenses_success'], '?page=accounteditor&edit=defenses');
				}
				exit;
			}

			foreach($reslist['defense'] as $ID)
			{
				$INPUT[$ID]	= array(
					'type'	=> $resource[$ID],
				);
			}

			$template->assign_vars(array(
				'inputlist'			=> $INPUT,
			));

			$template->show('AccountEditorPageDefenses.tpl');
		break;
		break;

		case 'buildings':
			if($_POST)
			{
				$sql = "SELECT * FROM %%PLANETS%% WHERE `id` = :planetId;";

				$PlanetData = $db->selectSingle($sql,array(
					':planetId' => HTTP::_GP('id', 0)
				));

				if(!isset($PlanetData))
				{
					$template->message($LNG['ad_add_not_exist'], '?page=accounteditor&edit=buildings');
				}

				$before = array();

				$after = array();

				foreach($reslist['allow'][$PlanetData['planet_type']] as $ID)
				{
					$before[$ID] = $PlanetData[$resource[$ID]];
				}
				if ($_POST['add'])
				{
					$Fields	= 0;
					$SQL  = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";
					foreach($reslist['allow'][$PlanetData['planet_type']] as $ID)
					{
						$Count			= max(0, round(HTTP::_GP($resource[$ID], 0.0)));
						$QryUpdate[]	= "`".$resource[$ID]."` = `".$resource[$ID]."` + '".$Count."'";
						$after[$ID] 	= $before[$ID] + $Count;
						$Fields			+= $Count;
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= ", `field_current` = `field_current` + :Fields WHERE `id` = :planetId AND `universe` = :universe;";

					$db->update($SQL,array(
						':Fields' => $Fields,
						':planetId' => HTTP::_GP('id',0),
						':universe' => Universe::getEmulated()
					));

				}
				elseif ($_POST['delete'])
				{
					$Fields	= 0;
					$QryUpdate	= array();

					$SQL  = "UPDATE %%PLANETS%% SET `eco_hash` = '', ";

					foreach($reslist['allow'][$PlanetData['planet_type']] as $ID)
					{
						$Count			= max(0, round(HTTP::_GP($resource[$ID], 0.0)));
						$QryUpdate[]	= "`" . $resource[$ID] . "` = GREATEST(0, `".$resource[$ID]."` - '".$Count."'" . ")";
						$after[$ID]		= max($before[$ID] - $Count,0);
						$Fields			+= $Count;
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= ", `field_current` = GREATEST(0, `field_current` - :Fields) WHERE `id` = :planetId AND `universe` = :universe;";
					$db->update($SQL,array(
						':Fields' => $Fields,
						':planetId' => HTTP::_GP('id',0),
						':universe' => Universe::getEmulated()
					));
				}

				$LOG = new Log(2);
				$LOG->target = HTTP::_GP('id', 0);
				$LOG->universe = Universe::getEmulated();
				$LOG->old = $before;
				$LOG->new = $after;
				$LOG->save();

				if ($_POST['add']) {
					$template->message($LNG['ad_add_build_success'], '?page=accounteditor&edit=buildings');
				} else if ($_POST['delete']) {
					$template->message($LNG['ad_delete_build_success'], '?page=accounteditor&edit=buildings');
				}
				exit;
			}

			foreach($reslist['build'] as $ID)
			{
				$INPUT[$ID]	= array(
					'type'	=> $resource[$ID],
				);
			}

			$template->assign_vars(array(
				'inputlist'			=> $INPUT,
			));

			$template->show('AccountEditorPageBuilds.tpl');
		break;

		case 'researchs':
			if($_POST)
			{
				$sql = "SELECT * FROM %%USERS%% WHERE `id` = :userId;";

				$before1 = $db->selectSingle($sql,array(
					':userId' => HTTP::_GP('id', 0)
				));

				$before = array();
				$after = array();
				foreach($reslist['tech'] as $ID)
				{
					$before[$ID] = $before1[$resource[$ID]];
				}
				if ($_POST['add'])
				{
					$SQL  = "UPDATE %%USERS%% SET ";

					foreach($reslist['tech'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = `".$resource[$ID]."` + '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."'";
						$after[$ID] = $before[$ID] + max(0, round(HTTP::_GP($resource[$ID], 0.0)));
					}

					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :userId AND `universe` = :universe;";

					$db->update($SQL,array(
						':userId' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated()
					));

				}
				elseif ($_POST['delete'])
				{
					$SQL  = "UPDATE %%USERS%% SET ";
					foreach($reslist['tech'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = GREATEST(0, `".$resource[$ID]."` - '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."')";
						$after[$ID] = max($before[$ID] - max(0, round(HTTP::_GP($resource[$ID], 0.0))),0);
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :userId AND `universe` = :universe;";

					$db->update($SQL,array(
						':userId' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated()
					));

				}

				$LOG = new Log(1);
				$LOG->target = HTTP::_GP('id', 0);
				$LOG->universe = $before1['universe'];
				$LOG->old = $before;
				$LOG->new = $after;
				$LOG->save();

				if ($_POST['add']) {
					$template->message($LNG['ad_add_tech_success'], '?page=accounteditor&edit=researchs');
				} else if ($_POST['delete']) {
					$template->message($LNG['ad_delete_tech_success'], '?page=accounteditor&edit=researchs');
				}
				exit;
			}

			foreach($reslist['tech'] as $ID)
			{
				$INPUT[$ID]	= array(
					'type'	=> $resource[$ID],
				);
			}

			$template->assign_vars(array(
				'inputlist'			=> $INPUT,
			));

			$template->show('AccountEditorPageResearch.tpl');
		break;
		case 'personal':
			if ($_POST)
			{
				$id			= HTTP::_GP('id', 0);
				$username	= HTTP::_GP('username', '', UTF8_SUPPORT);
				$password	= HTTP::_GP('password', '', true);
				$email		= HTTP::_GP('email', '');
				$email_2	= HTTP::_GP('email_2', '');
				$vacation	= HTTP::_GP('vacation', '');

				$sql = "SELECT `username`,`email`,`email_2`,`password`,`urlaubs_modus`,`urlaubs_until`
				FROM %%USERS%% WHERE `id` = :userId;";

				$before = $db->selectSingle($sql,array(
					':userId' => HTTP::_GP('id', 0)
				));

				$after = array();

				$PersonalQuery    =    "UPDATE %%USERS%% SET ";

				if(!empty($username) && $id != ROOT_USER) {
					$PersonalQuery    .= "`username` = :username, ";
					$after['username'] = $username;
				}

				if(!empty($email) && $id != ROOT_USER) {
					$PersonalQuery    .= "`email` = :email, ";
					$after['email'] = $email;
				}

				if(!empty($email_2) && $id != ROOT_USER) {
					$PersonalQuery    .= "`email_2` = :email_2, ";
					$after['email_2'] = $email_2;
				}

				if(!empty($password) && $id != ROOT_USER) {
					$PersonalQuery    .= "`password` = :password, ";
					$after['password'] = (PlayerUtil::cryptPassword($password) != $before['password']) ? 'CHANGED' : '';
				}
				$before['password'] = '';

				$Answer		= 0;
				$TimeAns	= 0;

				if ($vacation == 'yes') {
					$Answer		= 1;
					$after['urlaubs_modus'] = 1;
					$TimeAns    = TIMESTAMP + $_POST['d'] * 86400 + $_POST['h'] * 3600 + $_POST['m'] * 60 + $_POST['s'];
					$after['urlaubs_until'] = $TimeAns;
				}

				$PersonalQuery    .=  "`urlaubs_modus` = :Answer, `urlaubs_until` = :TimeAns ";
				$PersonalQuery    .= "WHERE `id` = :id AND `universe` = :universe";
				$db->update($PersonalQuery,array(
					':username' => $username,
					':email' => $email,
					':email_2' => $email_2,
					':password' => PlayerUtil::cryptPassword($password),
					':Answer' => $Answer,
					':TimeAns' => $TimeAns,
					':id' => $id,
					':universe' => Universe::getEmulated()
				));

				$LOG = new Log(1);
				$LOG->target = $id;
				$LOG->universe = $before['universe'];
				$LOG->old = $before;
				$LOG->new = $after;
				$LOG->save();

				$template->message($LNG['ad_personal_succes'], '?page=accounteditor&edit=personal');
				exit;
			}

			$template->assign_vars(array(
				'Selector'				=> array(''	=> $LNG['select_option'], 'yes' => $LNG['one_is_no_1'], 'no' => $LNG['one_is_no_0']),
			));

			$template->show('AccountEditorPagePersonal.tpl');
		break;

		case 'officiers':
			if($_POST)
			{

				$sql = "SELECT * FROM %%USERS%% WHERE `id` = :id;";

				$before1 = $dv->selectSingle($sql,array(
					':id' => HTTP::_GP('id', 0)
				));

				$before = array();
				$after = array();
				foreach($reslist['officier'] as $ID)
				{
					$before[$ID] = $before1[$resource[$ID]];
				}
				if ($_POST['add'])
				{
					$SQL  = "UPDATE %%USERS%% SET ";
					foreach($reslist['officier'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = `".$resource[$ID]."` + '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."'";
						$after[$ID] = $before[$ID] + max(0, round(HTTP::_GP($resource[$ID], 0.0)));
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :id AND `universe` = :universe;";

					$db->update($SQL,array(
						':id' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated()
					));

				}
				elseif ($_POST['delete'])
				{
					$SQL  = "UPDATE %%USERS%% SET ";
					foreach($reslist['officier'] as $ID)
					{
						$QryUpdate[]	= "`".$resource[$ID]."` = `".$resource[$ID]."` - '".max(0, round(HTTP::_GP($resource[$ID], 0.0)))."'";
						$after[$ID] = max($before[$ID] - max(0, round(HTTP::_GP($resource[$ID], 0.0))),0);
					}
					$SQL .= implode(", ", $QryUpdate);
					$SQL .= "WHERE ";
					$SQL .= "`id` = :id AND `universe` = :universe;";
					$db->update($SQL,array(
						':id' => HTTP::_GP('id', 0),
						':universe' => Universe::getEmulated(),
					));
				}

				$LOG = new Log(1);
				$LOG->target = HTTP::_GP('id', 0);
				$LOG->universe = $before1['universe'];
				$LOG->old = $before;
				$LOG->new = $after;
				$LOG->save();

				if ($_POST['add']) {
					$template->message($LNG['ad_add_offi_success'], '?page=accounteditor&edit=officiers');
				} else if ($_POST['delete']) {
					$template->message($LNG['ad_delete_offi_success'], '?page=accounteditor&edit=officiers');
				}
				exit;
			}

			foreach($reslist['officier'] as $ID)
			{
				$INPUT[$ID]	= array(
					'type'	=> $resource[$ID],
				);
			}

			$template->assign_vars(array(
				'inputlist'			=> $INPUT,
			));

			$template->show('AccountEditorPageOfficiers.tpl');
		break;

		case 'planets':
			if ($_POST)
			{
				$id				= HTTP::_GP('id', 0);
				$name			= HTTP::_GP('name', '', UTF8_SUPPORT);
				$diameter		= HTTP::_GP('diameter', 0);
				$fields			= HTTP::_GP('fields', 0);
				$buildings		= HTTP::_GP('0_buildings', '');
				$ships			= HTTP::_GP('0_ships', '');
				$defenses		= HTTP::_GP('0_defenses', '');
				$c_hangar		= HTTP::_GP('0_c_hangar', '');
				$c_buildings	= HTTP::_GP('0_c_buildings', '');
				$change_pos		= HTTP::_GP('change_position', '');
				$galaxy			= HTTP::_GP('g', 0);
				$system			= HTTP::_GP('s', 0);
				$planet			= HTTP::_GP('p', 0);

				if (!empty($name)){
					$sql = "UPDATE %%PLANETS%% SET `name` = :name WHERE `id` = :id AND `universe` = :universe;";

					$db->update($sql,array(
						':name' => $name,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if ($buildings == 'on')
				{
					foreach($reslist['build'] as $ID) {
						$BUILD[]	= "`".$resource[$ID]."` = '0'";
					}

					$sql = "UPDATE %%PLANETS%% SET " . implode(', ',$BUILD) . " WHERE `id` = :id AND `universe` = :universe;";

					$db->update($sql,array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));
				}

				if ($ships == 'on')
				{
					foreach($reslist['fleet'] as $ID) {
						$SHIPS[]	= "`".$resource[$ID]."` = '0'";
					}

					$sql = "UPDATE %%PLANETS%% SET ".implode(', ',$SHIPS)." WHERE `id` = :id AND `universe` = :universe;";

					$db->update($sql,array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if ($defenses == 'on')
				{
					foreach($reslist['defense'] as $ID) {
						$DEFS[]	= "`".$resource[$ID]."` = '0'";
					}

					$sql = "UPDATE %%PLANETS%% SET ".implode(', ',$DEFS)." WHERE `id` = :id AND `universe` = :universe;";


					$db->update($sql, array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if ($c_hangar == 'on'){

					$sql = "UPDATE %%PLANETS%% SET `b_hangar` = '0', `b_hangar_plus` = '0', `b_hangar_id` = '' WHERE `id` = :id AND `universe` = :universe;";

					$db->update($sql,array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if ($c_buildings == 'on'){

					$sql = "UPDATE %%PLANETS%% SET `b_building` = '0', `b_building_id` = '' WHERE `id` = :id AND `universe` = :universe;";

					$db->update($sql,array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));
				}

				if (!empty($diameter)){

					$sql = "UPDATE %%PLANETS%% SET `diameter` = :diameter WHERE `id` = :id AND `universe` = :universe;";

					$db->update($sql,array(
						':diameter' => $diameter,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if (!empty($fields)){

					$sql = "UPDATE %%PLANETS%% SET `field_max` = :fields WHERE `id` = :id AND `universe` = :universe;";

					$db->update($sql,array(
						':fields' => $fields,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if ($change_pos == 'on' && $galaxy > 0 && $system > 0 && $planet > 0 && $galaxy <= Config::get(Universe::getEmulated())->max_galaxy && $system <= Config::get(Universe::getEmulated())->max_system && $planet <= Config::get(Universe::getEmulated())->max_planets)
				{
					$sql = "SELECT galaxy,system,planet,planet_type FROM %%PLANETS%% WHERE `id` = :id AND `universe` = :universe;";

					$P =	$db->selectSingle($sql,array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

					if ($P['planet_type'] == '1')
					{
						if (PlayerUtil::checkPosition(Universe::getEmulated(), $galaxy, $system, $planet,$P['planet_type']))
						{
							$template->message($LNG['ad_pla_error_planets3'], '?page=accounteditor&edit=planets');
							exit;
						}

						$sql = "UPDATE %%PLANETS%% SET `galaxy` = :galaxy, `system` = :system, `planet` = :planet WHERE `id` = :id AND `universe` = :universe;";

						$db->update($sql,array(
							':galaxy' => $galaxy,
							':system' => $system,
							':planet' => $planet,
							':id' => $id,
							':universe' => Universe::getEmulated()
						));

					} else {
						if(PlayerUtil::checkPosition(Universe::getEmulated(), $galaxy, $system, $planet, $P['planet_type']))
						{
							$template->message($LNG['ad_pla_error_planets5'], '?page=accounteditor&edit=planets');
							exit;
						}

						$sql = "SELECT id_luna FROM %%PLANETS%% WHERE `galaxy` = :galaxy AND `system` = :system AND `planet` = :planet AND `planet_type` = '1';";

						$Target	= $db->selectSingle($sql, array(
							':galaxy' => $galaxy,
							':system' => $system,
							':planet' => $planet,
						));

						if ($Target['id_luna'] != '0')
						{
							$template->message($LNG['ad_pla_error_planets4'], '?page=accounteditor&edit=planets');
							exit;
						}

						$sql = "UPDATE %%PLANETS%% SET `id_luna` = '0' WHERE `galaxy` = :galaxy AND `system` = :system AND `planet` = :planet AND `planet_type` = '1';";

						$db->update($sql,array(
							':galaxy' => $P['galaxy'],
							':system' => $P['system'],
							':planet' => $P['planet'],
						));

						$sql = "UPDATE %%PLANETS%% SET `id_luna` = :id  WHERE `galaxy` = :galaxy AND `system` = :system AND `planet` = :planet AND planet_type = '1';";

						$db->update($sql,array(
							':id' => $id,
							':galaxy' => $galaxy,
							':system' => $system,
							':planet' => $planet
						));

						$sql = "UPDATE %%PLANETS%% SET `galaxy` = :galaxy, `system` = :system, `planet` = :planet WHERE `id` = :id AND `universe` = :universe;";

						$db->update($sql,array(
							':galaxy' => $galaxy,
							':system' => $system,
							':planet' => $planet,
							':id' => $id,
							':universe' => Universe::getEmulated()
						));


						$sql = "SELECT id_owner FROM %%PLANETS%% WHERE `galaxy` = :galaxy AND `system` = :system AND `planet` = :planet;";

						$QMOON2	=	$db->selectSingle($sql,array(
							':galaxy' => $galaxy,
							':system' => $system,
							':planet' => $planet,
						));

						$sql = "UPDATE %%PLANETS%% SET `galaxy` = :galaxy, `system` = :system, `planet` = :planet, `id_owner` = :id_owner WHERE `id` = :id AND `universe` = :universe AND `planet_type` = '3';";

						$db->update($sql,array(
							':galaxy' => $galaxy,
							':system' => $system,
							':planet' => $planet,
							':id_owner' => $QMOON2['id_owner'],
							':id' => $id,
							':universe' => Universe::getEmulated()
						));
					}
				}

				$template->message($LNG['ad_pla_succes'], '?page=accounteditor&edit=planets');
				exit;
			}

			$template->show('AccountEditorPagePlanets.tpl');
		break;

		case 'alliances':
			if ($_POST)
			{
				$id				=	HTTP::_GP('id', 0);
				$name			=	HTTP::_GP('name', '', UTF8_SUPPORT);
				$changeleader	=	HTTP::_GP('changeleader', 0);
				$tag			=	HTTP::_GP('tag', '', UTF8_SUPPORT);
				$externo		=	HTTP::_GP('externo', '', true);
				$interno		=	HTTP::_GP('interno', '', true);
				$solicitud		=	HTTP::_GP('solicitud', '', true);
				$delete			=	HTTP::_GP('delete', '');
				$delete_u		=	HTTP::_GP('delete_u', '');

				$sql = "SELECT * FROM %%ALLIANCE%% WHERE `id` = :id AND `ally_universe` = :universe;";

				$QueryF	=	$db->selectSingle($sql,array(
					':id' => $id,
					':universe' => Universe::getEmulated()
				));

				if (!empty($name)){
					$sql = "UPDATE %%ALLIANCE%% SET `ally_name` = :name WHERE `id` = :id AND `ally_universe` = :universe;";

					$db->update($sql,array(
						':name' => $name,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));


				}

				if (!empty($tag)){
					$sql = "UPDATE %%ALLIANCE%% SET `ally_tag` = :tag WHERE `id` = :id AND `ally_universe` = :universe;";

					$db->update($sql,array(
						':tag' => $tag,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				$sql = "SELECT ally_id FROM %%USERS%% WHERE `id` = :changeleader;";

				$QueryF2	=	$db->selectSingle($sql,array(
					':changeleader' => $changeleader
				));

				$sql = "UPDATE %%ALLIANCE%% SET `ally_owner` = :changeleader WHERE `id` = :id AND `ally_universe` = :universe;";

				$db->update($sql,array(
					':changeleader' => $changeleader,
					':id' => $id,
					':universe' => Universe::getEmulated()
				));

				$sql = "UPDATE %%USERS%% SET `ally_rank_id` = '0' WHERE `id` = :changeleader;";

				$db->update($sql,array(
					':changeleader' => $changeleader
				));

				if (!empty($externo)){

					$sql = "UPDATE %%ALLIANCE%% SET `ally_description` = :externo WHERE `id` = :id AND `ally_universe` = :universe;";

					$db->update($sql,array(
						':externo' => $externo,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if (!empty($interno)){

					$sql = "UPDATE %%ALLIANCE%% SET `ally_text` = :interno WHERE `id` = :id AND `ally_universe` = :universe;";

					$db->update($sql,array(
						':interno' => $interno,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

				}

				if (!empty($solicitud)){

					$sql = "UPDATE %%ALLIANCE%% SET `ally_request` = :solicitud WHERE `id` = :id AND `ally_universe` = :universe;";

					$db->update($sql,array(
						':solicitud' => $solicitud,
						':id' => $id,
						':universe' => Universe::getEmulated()
					));


				}

				if ($delete == 'on')
				{

					$sql = "DELETE FROM %%ALLIANCE%% WHERE `id` = :id AND `ally_universe` = :universe;";

					$db->delete($sql,array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

					$sql = "UPDATE %%USERS%% SET `ally_id` = '0', `ally_rank_id` = '0', `ally_register_time` = '0' WHERE `ally_id` = :id;";

					$db->update($sql,array(
						':id' => $id
					));

				}

				if (!empty($delete_u))
				{

					$sql = "UPDATE %%ALLIANCE%% SET `ally_members` = ally_members - 1 WHERE `id` = :id AND `ally_universe` = :universe;";

					$db->update($sql,array(
						':id' => $id,
						':universe' => Universe::getEmulated()
					));

					$sql = "UPDATE %%USERS%% SET `ally_id` = '0', `ally_rank_id` = '0', `ally_register_time` = '0' WHERE `id` = :delete_u AND `ally_id` = :id;";

					$db->update($sql,array(
						':delete_u' => $delete_u,
						':id' => $id
					));

				}


				$template->message($LNG['ad_ally_succes'], '?page=accounteditor&edit=alliances');
				exit;
			}

			$template->show('AccountEditorPageAlliance.tpl');
		break;

		default:
			$template->show('AccountEditorPageMenu.tpl');
		break;
	}
}
