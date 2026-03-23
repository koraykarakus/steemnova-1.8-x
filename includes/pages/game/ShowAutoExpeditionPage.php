<?php

class ShowAutoExpeditionPage extends AbstractGamePage
{
    public static $require_module = MODULE_AUTOEXPEDITION;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $PLANET, $LNG, $RESOURCE, $RESLIST, $config;

        $db = Database::get();

        $max_fleet_slots = FleetFunctions::GetMaxFleetSlots($USER);
        $active_fleet_slots = FleetFunctions::GetCurrentFleets($USER['id']);
        $room = $max_fleet_slots - $active_fleet_slots;

        $active_expedition = FleetFunctions::GetCurrentFleets($USER['id'], 15, true);
        $max_exp_slots = FleetFunctions::getExpeditionLimit($USER);

        if ($max_exp_slots <= $active_expedition)
        {
            $this->printMessage($LNG['ae_error_1'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        if (inVacationMode($USER))
        {
            $this->printMessage($LNG['ae_error_2'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        if ($room <= 0)
        {
            $this->printMessage($LNG['ae_error_1'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        if ($config->google_recaptcha_active)
        {
            require_once 'includes/libs/reCAPTCHA/src/autoload.php';
            $recaptcha = new \ReCaptcha\ReCaptcha($config->google_recaptcha_private_key);

            $resp = $recaptcha->verify(HTTP::_GP('g-recaptcha-response', ''), Session::getClientIp());

            if (!$resp->isSuccess())
            {
                $this->printMessage($LNG['ae_error_3'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }

        }

        //variable $j is used to put time between expedition fleets

        $planet_ress_obj = new ResourceUpdate();
        $sql = "SELECT * FROM %%PLANETS%% 
        WHERE id_owner = :user_id AND destroyed = '0' AND id = :planet_id";

        $planets_raw = $db->select($sql, [
            ':user_id'   => $USER['id'],
            ':planet_id' => $PLANET['id'],
        ]);

        foreach ($planets_raw as $c_planet)
        {
            list($USER, $c_planet) = $planet_ress_obj->CalcResource($USER, $c_planet, true);
            $PLANETS[] = $c_planet;
            unset($c_planet);
        }

        $sql = 'SELECT COUNT(*) as count FROM %%FLEETS%%
                WHERE fleet_mission = 1 AND fleet_target_owner = :uid 
                AND hasCanceled = 0 AND fleet_mess = 0
                AND fleet_start_time < :limit_time';

        $attack = $db->selectSingle($sql, [
            ':uid'        => $USER['id'],
            ':limit_time' => TIMESTAMP + 5 * 60,
        ], 'count');

        if ($attack > 0)
        {
            $this->printMessage($LNG['ft_error_exists_attack'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        $target_galaxy = HTTP::_GP('expedition_galaxy', 0);
        $target_system = HTTP::_GP('expedition_system', 0);
        $target_planet = $config->max_planets + 1;

        $target_system = max(1, min($target_system, $config->max_system));
        $target_galaxy = max(1, min($target_galaxy, $config->max_galaxy));

        $target_type = 1;
        $target_mission = 15;
        // percentage speed, 10 = %100 , 9 = %90,..
        $fleet_speed = 10;
        $fleet_group = 0;
        $target_planet_data = ['id' => 0, 'id_owner' => 0, 'planettype' => 1];

        $fleet = [];
        $possible = min($room, $max_exp_slots - $active_expedition);

        foreach ($RESLIST['fleet'] as $element_id)
        {

            if ($element_id == 212
                || $element_id == 221
                || floor($PLANET[$RESOURCE[$element_id]] / $possible) == 0)
            {
                continue;
            }

            $fleet = $fleet + [
                $element_id => floor($PLANET[$RESOURCE[$element_id]] / $possible),
            ];
        }

        if (empty($fleet))
        {
            $this->printMessage($LNG['ae_error_4']);
        }

        $time_between_fleets = 0;
        for ($i = 0; $i < $possible; $i++)
        {
            $game_speed_factor = FleetFunctions::GetGameSpeedFactor();
            $max_fleet_speed = FleetFunctions::GetFleetMaxSpeed($fleet, $USER);
            $distance = FleetFunctions::GetTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$target_galaxy, $target_system, $target_planet]
            );
            $duration = FleetFunctions::GetMissionDuration(
                $fleet_speed,
                $max_fleet_speed,
                $distance,
                $game_speed_factor,
                $USER
            );
            $consumption = FleetFunctions::GetFleetConsumption(
                $fleet,
                $duration,
                $distance,
                $USER,
                $game_speed_factor
            );

            $stay_time = HTTP::_GP('staytime', 0);

            $expedition_speed = $config->expedition_speed;
            $stay_duration = round(($stay_time / $expedition_speed) * 3600, 0);

            $possible_min_speed = round(1 / $expedition_speed, 2) * 3600;

            $stay_duration = max($stay_duration, $possible_min_speed);

            if ($consumption > $PLANET['deuterium'])
            {
                $this->printMessage($LNG['ft_error_not_enough_deuterium']);
            }

            $token = getRandomString();
            $_SESSION['fleet'][$token] = [
                'time'         => TIMESTAMP + $time_between_fleets,
                'fleet'        => $fleet,
                'fleetRoom'    => 0,
                'speed'        => $max_fleet_speed,
                'distance'     => $distance,
                'targetGalaxy' => $target_galaxy,
                'targetSystem' => $target_system,
                'targetPlanet' => $target_planet,
                'targetType'   => $target_type,
                'fleetGroup'   => $fleet_group,
                'fleetSpeed'   => $fleet_speed,
                'ownPlanet'    => $PLANET['id'],
            ];

            $fleet_start_time = $duration + TIMESTAMP + $time_between_fleets;
            $fleet_stay_time = $fleet_start_time + $stay_duration;
            $fleet_end_time = $fleet_stay_time + $duration;

            $fleet_resource = [
                901 => 0,
                902 => 0,
                903 => 0,
            ];

            FleetFunctions::sendFleet(
                $fleet,
                $target_mission,
                $USER['id'],
                $PLANET['id'],
                $PLANET['galaxy'],
                $PLANET['system'],
                $PLANET['planet'],
                $PLANET['planet_type'],
                $target_planet_data['id_owner'],
                $target_planet_data['id'],
                $target_galaxy,
                $target_system,
                $target_planet,
                $target_type,
                $fleet_resource,
                $fleet_start_time,
                $fleet_stay_time,
                $fleet_end_time,
                $fleet_group,
                0,
                0,
                0
            );

            $PLANET['deuterium'] = $PLANET['deuterium'] - $consumption;

            $time_between_fleets += 10;

        }

        $this->printMessage($LNG['ae_success'], [[
            'label' => $LNG['sys_back'],
            'url'   => 'game.php?page=fleetTable',
        ]]);

    }

}
