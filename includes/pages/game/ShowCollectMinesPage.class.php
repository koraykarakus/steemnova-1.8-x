<?php



/**
 *
 */


class ShowCollectMinesPage extends AbstractGamePage
{
  public static $requireModule = MODULE_COLLECT_MINES;

  function __construct()
  {
    parent::__construct();
  }

  function show(){

    global $USER, $PLANET, $resource, $LNG, $db, $config;

    //Don't allow user to collect mine if in vacation mode
    if (isVacationMode($USER)){
      $this->printMessage($LNG['cm_error_1']);
    }

    $from = HTTP::_GP('from','');

    if (!$config->collect_mines_under_attack) {

      $sql = "SELECT COUNT(*) as count FROM %%FLEETS%% WHERE
      fleet_owner != :userId AND fleet_mess = 0 AND
      fleet_target_owner = :userId AND fleet_mission = 1 AND hasCanceled = 0 AND fleet_start_time < :limitTime;";

    	$attackingFleetsCount = $db->selectSingle($sql,array(
    		':userId' => $USER['id'],
        ':limitTime' => TIMESTAMP + 5 * 60
    	),'count');

      if ($attackingFleetsCount > 0){
        $this->printMessage($LNG['cm_error_2']);
      }

    }


  	$timelimit = $config->collect_mine_time_minutes * 60;

  	$lastcollect = TIMESTAMP - $USER['last_collect_mine_time'];

  	//if conditions is not satisfied return without calculating anything ..
  	if ($lastcollect < $timelimit){
      $this->printMessage(sprintf($LNG['cm_error_3'], $config->collect_mine_time_minutes));
    }

  	$PlanetRess	= new ResourceUpdate();

  	$sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :userID AND destruyed = '0'";

  	$PlanetsRAW = $db->select($sql, array(
  				':userID'   => $USER['id']
  	));

  	foreach ($PlanetsRAW as $CPLANET)
  	{
			list($USER, $CPLANET)	= $PlanetRess->CalcResource($USER, $CPLANET, true);
			$PLANETS[]	= $CPLANET;
			unset($CPLANET);
  	}


  	$metal = $crystal = $deuterium = array();

  	foreach ($PLANETS as $currentPlanet) {
  		if ($currentPlanet['id'] != $PLANET['id']) {
  			$metal[] =	$currentPlanet['metal'];
  			$crystal[] =	$currentPlanet['crystal'];
  			$deuterium[] =	$currentPlanet['deuterium'];
  		}
  	}

    //reset resources of other planets as 0
    $sql_reset = "UPDATE %%PLANETS%% SET metal = :metal, deuterium = :deuterium, crystal = :crystal
    WHERE id_owner = :userId AND id != :planetID;";

    $db->update($sql_reset, array(
          ':metal'=> 0,
          ':deuterium'=>0,
          ':crystal'=>0,
          ':userId' => $USER['id'],
          ':planetID' => $PLANET['id'],
    ));

    $metalSum = $crystalSum = $deuteriumSum = 0;

    foreach($metal as $val){
      $metalSum += $val;
    }

    foreach($crystal as $val){
      $crystalSum += $val;
    }

    foreach($deuterium as $val){
      $deuteriumSum += $val;
    }


  	$PLANET[$resource[901]] += $metalSum;
  	$PLANET[$resource[902]] += $crystalSum;
  	$PLANET[$resource[903]] += $deuteriumSum;


  	$sql = "UPDATE %%USERS%% SET last_collect_mine_time = :timeCollect WHERE id = :userId;";

  	$db->update($sql,array(
  			':timeCollect' => TIMESTAMP,
  			':userId' => $USER['id'],
  	));

  	$this->redirectTo("game.php?page=$from");

  }

}









 ?>
