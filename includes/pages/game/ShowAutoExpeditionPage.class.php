<?php



class ShowAutoExpeditionPage extends AbstractGamePage
{

  public static $requireModule = MODULE_AUTOEXPEDITION;

	function __construct()
	{
		parent::__construct();
	}

	function show()
	{
    global $USER, $PLANET, $LNG, $resource, $reslist, $config;

    $db = Database::get();

    $maxFleetSlots	= FleetFunctions::GetMaxFleetSlots($USER);
    $activeFleetSlots = FleetFunctions::GetCurrentFleets($USER['id']);
    $room = $maxFleetSlots - $activeFleetSlots;

    $activeExpedition	= FleetFunctions::GetCurrentFleets($USER['id'], 15, true);
    $maxExpSlots = FleetFunctions::getExpeditionLimit($USER);



    if ($maxExpSlots <= $activeExpedition) {
      $this->printMessage($LNG['ae_error_1'], array(array(
        'label'	=> $LNG['sys_back'],
        'url'	=> 'game.php?page=fleetTable'
      )));
    }

    if (IsVacationMode($USER)) {
      $this->printMessage($LNG['ae_error_2'], array(array(
        'label'	=> $LNG['sys_back'],
        'url'	=> 'game.php?page=fleetTable'
      )));
    }

    if ($room <= 0) {
      $this->printMessage($LNG['ae_error_1'], array(array(
        'label'	=> $LNG['sys_back'],
        'url'	=> 'game.php?page=fleetTable'
      )));
    }


    if ($config->capaktiv) {
      require_once 'includes/libs/reCAPTCHA/src/autoload.php';
      $recaptcha = new \ReCaptcha\ReCaptcha($config->capprivate);

      $resp = $recaptcha->verify(HTTP::_GP('g-recaptcha-response', ''), Session::getClientIp());

      if (!$resp->isSuccess())
      {
        $this->printMessage($LNG['ae_error_3'], array(array(
          'label'	=> $LNG['sys_back'],
          'url'	=> 'game.php?page=fleetTable'
        )));
      }

    }


    //variable $j is used to put time between expedition fleets
    $j = 0;

    $PlanetRess	= new ResourceUpdate();
    $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :userId AND destruyed = '0' AND id = :planetId";

    $PlanetsRAW = $db->select($sql, array(
        ':userId'   => $USER['id'],
        ':planetId' => $PLANET['id']
    ));

    foreach ($PlanetsRAW as $CPLANET)
    {
      list($USER, $CPLANET)	= $PlanetRess->CalcResource($USER, $CPLANET, true);
      $PLANETS[]	= $CPLANET;
      unset($CPLANET);
    }

    $sql = "SELECT COUNT(*) as count FROM %%FLEETS%%
    WHERE fleet_mission = 1 AND fleet_target_owner = :uid AND hasCanceled = 0 AND fleet_mess = 0
    AND fleet_start_time < :limitTime";

    $attack = $db->selectSingle($sql,array(
      ':uid'=> $USER['id'],
      ':limitTime' => TIMESTAMP + 5 * 60
    ),'count');

    if ($attack > 0) {
      $this->printMessage($LNG['ft_error_exists_attack'], array(array(
        'label'	=> $LNG['sys_back'],
        'url'	=> 'game.php?page=fleetTable'
      )));
    }

    $targetGalaxy = HTTP::_GP('expedition_galaxy',0);
    $targetSystem = HTTP::_GP('expedition_system',0);
    $targetPlanet = $config->max_planets + 1;

    $targetSystem = max(1,min($targetSystem,$config->max_system));
    $targetGalaxy = max(1,min($targetGalaxy,$config->max_galaxy));

    $targetType = 1;
    $targetMission = 15;
    $fleetSpeed = 10; //10 = %100 , 9 = %90,..
    $fleetGroup = 0;
    $targetPlanetData	= array('id' => 0, 'id_owner' => 0, 'planettype' => 1);

    $fleet = array();
    $possible = min($room,$maxExpSlots - $activeExpedition);



    $i = 0;

    foreach ($reslist['fleet'] as $fkey => $fval) {

      if ($fval == 212 || $fval == 221 || floor($PLANET[$resource[$fval]] / $possible) == 0) {
        continue;
      }

      /* future concerns
      if (!in_array($fval,unserialize($USER['auto_expedition_ship_types']))) {
        continue;
      }
      */

      $fleet = $fleet + array(
        $fval => floor($PLANET[$resource[$fval]] / $possible)
      );
    }

    if (empty($fleet)) {
      $this->printMessage($LNG['ae_error_4']);
    }


 for ($i=0; $i < $possible; $i++) {

    $MisInfo = array(
      'galaxy' => $targetGalaxy,
      'system' => $targetSystem,
      'planet' => $targetPlanet,
      'planettype' => $targetType,
      'IsAKS' => $fleetGroup,
      'Ship' => $fleet
    );

    $MissionOutput = FleetFunctions::GetFleetMissions($USER, $MisInfo, $targetPlanetData);
    $GameSpeedFactor = FleetFunctions::GetGameSpeedFactor();
    $MaxFleetSpeed = FleetFunctions::GetFleetMaxSpeed($fleet, $USER);
    $distance = FleetFunctions::GetTargetDistance(array($PLANET['galaxy'], $PLANET['system'], $PLANET['planet']), array($targetGalaxy, $targetSystem, $targetPlanet));
    $duration = FleetFunctions::GetMissionDuration($fleetSpeed, $MaxFleetSpeed, $distance, $GameSpeedFactor, $USER);
    $consumption = FleetFunctions::GetFleetConsumption($fleet, $duration, $distance, $USER, $GameSpeedFactor);
    $availableMissions = FleetFunctions::GetFleetMissions($USER, $MisInfo, $targetPlanetData);

    $Staytime	= HTTP::_GP('staytime', 0);

    $haltSpeed = $config->halt_speed;
    $StayDuration = round(($Staytime / $haltSpeed) * 3600, 0);

    $possible_min_speed =  round(1 / $haltSpeed, 2) * 3600;

    $StayDuration = max($StayDuration, $possible_min_speed);

    if($consumption > $PLANET['deuterium'])
    {
      $this->printMessage($LNG['ft_error_not_enough_deuterium']);
    }


    $token		= getRandomString();
    $_SESSION['fleet'][$token]	= array(
        'time'		=> TIMESTAMP + $j,
        'fleet'		=> $fleet,
        'fleetRoom'	=> 0,
        'speed' => $MaxFleetSpeed,
        'distance' => $distance,
        'targetGalaxy' => $targetGalaxy,
        'targetSystem' => $targetSystem,
        'targetPlanet' => $targetPlanet,
        'targetType' => $targetType,
        'fleetGroup' => $fleetGroup,
        'fleetSpeed' => $fleetSpeed,
        'ownPlanet' => $PLANET['id']
      );


      $targetPlayerData	= array(
        'id'				=> 0,
        'onlinetime'		=> TIMESTAMP,
        'ally_id'			=> 0,
        'urlaubs_modus'		=> 0,
        'authattack'		=> 0,
        'total_points'		=> 0,
        'lang' => "en",
      );

      $fleetStartTime		= $duration + TIMESTAMP + $j;
      $fleetStayTime		= $fleetStartTime + $StayDuration;
      $fleetEndTime		= $fleetStayTime + $duration;

      $fleetResource	= array(
        901	=> 0,
        902	=> 0,
        903	=> 0,
      );


      FleetFunctions::sendFleet($fleet, $targetMission, $USER['id'], $PLANET['id'], $PLANET['galaxy'],
      $PLANET['system'], $PLANET['planet'], $PLANET['planet_type'], $targetPlanetData['id_owner'],
      $targetPlanetData['id'], $targetGalaxy, $targetSystem, $targetPlanet, $targetType, $fleetResource,
      $fleetStartTime, $fleetStayTime, $fleetEndTime, $fleetGroup, 0, 0, 0, $USER['lang'], $targetPlayerData['lang'],$PLANET['name'],$LNG['gl_infinite_aways']);



      $PLANET['deuterium']	= $PLANET['deuterium'] - $consumption;

      $j += 10;

 }

 $this->printMessage($LNG['ae_success'], array(array(
  'label'	=> $LNG['sys_back'],
  'url'	=> 'game.php?page=fleetTable'
 )));


}


}
