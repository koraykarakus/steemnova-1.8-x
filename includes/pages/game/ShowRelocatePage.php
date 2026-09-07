<?php

class ShowRelocatePage extends AbstractGamePage
{
    public static int $require_module = MODULE_RELOCATE;

    public function __construct()
    {
        parent::__construct();
    }

    public function send(): void
    {
        global $USER, $PLANET, $LNG, $RESLIST, $RESOURCE, $config;

        $db = Database::get();
        $galaxy = HTTP::_GP('galaxy', 0);
        $system = HTTP::_GP('system', 0);
        $planet = HTTP::_GP('planet', 0);

        // cannot relocate if user in vacation mode
        if (inVacationMode($USER))
        {
            $this->printMessage($LNG['cannot_use_in_vac']);
        }

        // you cannot move planet if there is building in construction
        if ($PLANET['b_building'] != 0)
        {
            $this->printMessage($LNG['rl_error_type_5']);
        }

        // you cannot move planet if there is a research which is started from this planet
        if ($USER['b_tech'] != 0
            && $USER['b_tech_planet'] == $PLANET['id'])
        {
            $this->printMessage($LNG['rl_error_type_6']);
        }

        // you cannot move planet if there is a shipyard production
        if (!empty(unserialize($PLANET['b_shipyard_id'])))
        {
            $this->printMessage($LNG['rl_error_type_9']);
        }

        // you cannot move planet if there is a fleet which is started from this planet at the time of relocation
        if ($PLANET['id_moon'] != 0)
        {
            $sql = "SELECT COUNT(*) as count FROM %%FLEETS%% 
            WHERE fleet_owner = :user_id AND (fleet_start_id = :planet_id 
            OR fleet_start_id =:moon_id) AND fleet_end_time > :this_time ;";

            $active_fleets = $db->selectSingle($sql, [
                ':user_id'   => $USER['id'],
                ':planet_id' => $PLANET['id'],
                ':this_time' => TIMESTAMP,
                ':moon_id'   => $PLANET['id_moon'],
            ], 'count');
        }
        else
        {
            $sql = "SELECT COUNT(*) as count FROM %%FLEETS%% 
            WHERE fleet_owner = :user_id AND fleet_start_id = :planet_id 
            AND fleet_end_time > :this_time ;";

            $active_fleets = $db->selectSingle($sql, [
                ':user_id'   => $USER['id'],
                ':planet_id' => $PLANET['id'],
                ':this_time' => TIMESTAMP,
            ], 'count');
        }

        if ($active_fleets > 0)
        {
            $this->printMessage($LNG['rl_error_type_7']);
        }

        if (empty($galaxy)
            || empty($system)
            || empty($planet))
        {
            $this->printMessage($LNG['rl_error_type_1']);
        }

        // user cannot start this from moon !
        if ($PLANET['planet_type'] == 3)
        {
            $this->printMessage($LNG['rl_error_type_2']);
        }

        // you cannot relocate if someone is attacking to the planet or moon

        $sql = "SELECT COUNT(*) as count FROM %%FLEETS%% WHERE
			fleet_owner != :user_id AND fleet_mess = 0 AND
			fleet_target_owner = :user_id AND fleet_mission IN (1,9) 
            AND hasCanceled = 0 AND fleet_end_id IN (:planet_id, :moon_id);";

        $attack_fleets = $db->selectSingle($sql, [
            ':user_id'   => $USER['id'],
            ':planet_id' => $PLANET['id'],
            ':moon_id'   => $PLANET['id_moon'],
        ], 'count');

        if ($attack_fleets > 0)
        {
            $this->printMessage($LNG['rl_error_type_10']);
        }

        if (!PlayerUtil::isPositionFree(Universe::current(), $galaxy, $system, $planet))
        {
            $this->printMessage($LNG['rl_error_type_3']);
        }

        if (!PlayerUtil::checkPosition(Universe::current(), $galaxy, $system, $planet))
        {
            $this->printMessage($LNG['rl_error_type_4']);
        }

        if ($USER['darkmatter'] < $config->relocate_price)
        {
            $this->printMessage($LNG['rl_error_type_11']);
        }

        // NOTE: you can only attempt to move a planet once per 24h (even if it fails)
        if (TIMESTAMP - $PLANET['last_relocate'] < $config->relocate_next_time * 60 * 60)
        {
            $this->printMessage(sprintf($LNG['rl_error_type_8'], $config->relocate_next_time));
        }

        // NOTE: Add countdown 24 (? divided to universe fleet speed) hours then move the planet
        // NOTE: fleet comes to new planet after planet relocation is succeed

        $res_update_obj = new ResourceUpdate();
        $res_update_obj->CalcResource($USER, $PLANET, true);

        $fleet = $fleet_moon = [];

        foreach ($RESLIST['fleet'] as $key => $fleet_id)
        {
            if ($fleet_id == 212
                || $fleet_id == 221
                || $PLANET[$RESOURCE[$fleet_id]] == 0)
            {
                continue;
            }
            $fleet = $fleet + [
                $fleet_id => $PLANET[$RESOURCE[$fleet_id]],
            ];
        }

        if ($PLANET['id_moon'] != 0)
        {
            $sql = "SELECT * FROM %%PLANETS%% WHERE id = :id_luna;";
            $MOON = $db->selectSingle($sql, [
                ':id_luna' => $PLANET['id_moon'],
            ]);

            foreach ($RESLIST['fleet'] as $key => $fleet_id)
            {
                if ($fleet_id == 212
                    || $fleet_id == 221
                    || $MOON[$RESOURCE[$fleet_id]] == 0)
                {
                    continue;
                }
                $fleet_moon = $fleet_moon + [
                    $fleet_id => $MOON[$RESOURCE[$fleet_id]],
                ];
            }

        }

        if (!$config->relocate_move_fleet_directly)
        {
            $fleet_speed = 10;

            $target_planet_data = [
                'id'         => $PLANET['id'],
                'id_owner'   => $PLANET['id_owner'],
                'planettype' => $PLANET['planet_type'],
            ];

            $game_speed_factor = FleetFunctions::GetGameSpeedFactor();

            $distance = FleetFunctions::GetTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$galaxy, $system, $planet]
            );

            $consumption = $stay_time = $stay_duration = 0;

            $fleet_resource = [
                901 => 0,
                902 => 0,
                903 => 0,
            ];

            if (!empty($fleet))
            {
                $max_fleet_speed = FleetFunctions::GetFleetMaxSpeed($fleet, $USER);
                $duration = FleetFunctions::GetMissionDuration(
                    $fleet_speed,
                    $max_fleet_speed,
                    $distance,
                    $game_speed_factor,
                    $USER
                );
                $fleet_start_time = $duration + TIMESTAMP ;
                $fleet_stay_time = $fleet_start_time + $stay_duration;
                $fleet_end_time = $fleet_stay_time + $duration;

                $fleet_id = FleetFunctions::sendFleet(
                    $fleet,
                    4,
                    $USER['id'],
                    $PLANET['id'],
                    $PLANET['galaxy'],
                    $PLANET['system'],
                    $PLANET['planet'],
                    $PLANET['planet_type'],
                    $PLANET['id_owner'],
                    $PLANET['id'],
                    $galaxy,
                    $system,
                    $planet,
                    1,
                    $fleet_resource,
                    $fleet_start_time,
                    $fleet_stay_time,
                    $fleet_end_time,
                    0,
                    0,
                    0,
                    0
                );

                $sql = "UPDATE %%FLEETS%% SET fleet_no_m_return = 1 WHERE fleet_id = :fleet_id;";
                $db->update($sql, [
                    ':fleet_id' => $fleet_id,
                ]);
            }

            if (!empty($fleet_moon))
            {
                $max_fleet_speed = FleetFunctions::GetFleetMaxSpeed($fleet_moon, $USER);
                $duration = FleetFunctions::GetMissionDuration(
                    $fleet_speed,
                    $max_fleet_speed,
                    $distance,
                    $game_speed_factor,
                    $USER
                );
                $fleet_start_time = $duration + TIMESTAMP ;
                $fleet_stay_time = $fleet_start_time + $stay_duration;
                $fleet_end_time = $fleet_stay_time + $duration;

                $fleet_id = FleetFunctions::sendFleet(
                    $fleet_moon,
                    4,
                    $USER['id'],
                    $MOON['id'],
                    $MOON['galaxy'],
                    $MOON['system'],
                    $MOON['planet'],
                    $MOON['planet_type'],
                    $MOON['id_owner'],
                    $PLANET['id'],
                    $galaxy,
                    $system,
                    $planet,
                    1,
                    $fleet_resource,
                    $fleet_start_time,
                    $fleet_stay_time,
                    $fleet_end_time,
                    0,
                    0,
                    0,
                    0,
                );

                $sql = "UPDATE %%FLEETS%% SET fleet_no_m_return = 1 WHERE fleet_id = :fleet_id;";
                $db->update($sql, [
                    ':fleet_id' => $fleet_id,
                ]);
            }
        }

        // NOTE: relocation will be canceled after countdown if construction / research / or fleet movement
        // NOTE: timer green if relocation will succeed, red if not succeed
        // NOTE: incoming attacking/supporting fleet won´t block the movement, fleets will return after reaching empty position

        // NOTE: temperature and picture of planet should be changed
        $planet_data = [];
        require 'includes/PlanetData.php';

        $data_index = (int) ceil($planet / ($config->max_planets / count($planet_data)));
        $max_temp = $planet_data[$data_index]['temp'];
        $min_temp = $max_temp - 40;

        $image_names = array_keys($planet_data[$data_index]['image']);
        $image_name_type = $image_names[array_rand($image_names)];
        $image_name = $image_name_type;
        $image_name .= 'planet';
        $image_name .= $planet_data[$data_index]['image'][$image_name_type] < 10 ? '0' : '';
        $image_name .= $planet_data[$data_index]['image'][$image_name_type];

        $sql = "UPDATE %%PLANETS%% SET galaxy = :galaxy, system = :system, planet = :planet,
		temp_min = :temp_min, temp_max = :temp_max, image = :image_name, last_relocate = :relocate_time
		WHERE id = :planet_id;";

        $db->update($sql, [
            ':galaxy'        => $galaxy,
            ':system'        => $system,
            ':planet'        => $planet,
            ':temp_min'      => $min_temp,
            ':temp_max'      => $max_temp,
            ':image_name'    => $image_name,
            ':planet_id'     => $PLANET['id'],
            ':relocate_time' => TIMESTAMP,
        ]);

        if ($PLANET['id_moon'] != 0)
        {
            // NOTE: jumpgate is deactivated for 24 hours to the new location
            // NOTE: divided to fleet speed ? no info ?
            $next_jump_time = TIMESTAMP + ($config->relocate_jump_gate_active * 60 * 60) / ($config->fleet_speed / 2500);

            $sql = "UPDATE %%PLANETS%% SET 
            galaxy = :galaxy, 
            system = :system, 
            planet = :planet,last_jump_time =:relocateTime 
            WHERE id = :moon_id;";

            $db->update($sql, [
                ':galaxy'       => $galaxy,
                ':system'       => $system,
                ':planet'       => $planet,
                ':moon_id'      => $PLANET['id_moon'],
                ':relocateTime' => $next_jump_time,
            ]);
        }

        $USER['darkmatter'] -= $config->relocate_price;

        if ($PLANET['id'] == $USER['id_planet'])
        {
            $sql = "UPDATE %%USERS%% SET galaxy = :galaxy, system = :system, planet = :planet 
            WHERE id = :user_id;";
            $db->update($sql, [
                ':galaxy'  => $galaxy,
                ':system'  => $system,
                ':planet'  => $planet,
                ':user_id' => $USER['id'],
            ]);
        }

        // NOTE: recalculate planet production
        //part 1 : update $PLANET array
        $sql = "SELECT * FROM %%PLANETS%% WHERE id = :planet_id;";
        $PLANET_NEW = $db->selectSingle($sql, [
            ':planet_id' => $PLANET['id'],
        ]);
        //part 2: update hash
        $this->eco_obj->setData($USER, $PLANET_NEW);
        $this->eco_obj->ReBuildCache();
        list($USER, $PLANET) = $this->eco_obj->getData();
        $PLANET['eco_hash'] = $this->eco_obj->CreateHash();
        $this->printMessage($LNG['rl_success'] . " [$galaxy:$system:$planet]");
    }

    public function show(): void
    {
        global $LNG, $PLANET,$config;

        $this->assign([
            'info'      => sprintf($LNG['rl_info'], pretty_number($config->relocate_price)),
            'page'      => HTTP::_GP('page', ''),
            'planet_id' => $PLANET['id'],
            'galaxy'    => $PLANET['galaxy'],
            'system'    => $PLANET['system'],
            'planet'    => $PLANET['planet'],
        ]);

        $this->display('page.relocate.default.tpl');
    }
}
