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

class PlayerUtil
{
    public static function cryptPassword(string $password): string
    {
        $result = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);

        if ($result === false)
        {
            throw new Exception("cryptPassword : Error Processing Request");
        }

        return $result;
    }

    public static function isPositionFree(
        int $universe,
        int $galaxy,
        int $system,
        int $position,
        int $type = 1
    ): bool {
        $db = Database::get();
        $sql = "SELECT COUNT(*) as record
		FROM %%PLANETS%%
		WHERE `universe` = :universe
		AND `galaxy` = :galaxy
		AND `system` = :system
		AND `planet` = :position
		AND `planet_type` = :type;";

        $count = $db->selectSingle($sql, [
            ':universe' => $universe,
            ':galaxy'   => $galaxy,
            ':system'   => $system,
            ':position' => $position,
            ':type'     => $type,
        ], 'record');

        return $count == 0;
    }

    public static function isNameValid(string $name): bool
    {
        if (UTF8_SUPPORT)
        {
            $result = preg_match('/^[\p{L}\p{N}_\-. ]*$/u', $name);
        }
        else
        {
            $result = preg_match('/^[A-Za-z0-9_.\- ]*$/', $name);
        }

        if ($result === false)
        {
            throw new Exception("isNameValid : preg_match fail !");
        }

        return $result === 1;
    }

    public static function isMailValid(string $address): bool
    {
        if (function_exists('filter_var'))
        {
            return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
        }
        else
        {
            $result = preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $address);
            if ($result === false)
            {
                throw new Exception("isMailValid : preg_match failed !");
            }
            return $result === 1;
        }
    }

    public static function checkPosition(
        int $universe,
        int $galaxy,
        int $system,
        int $position
    ): bool {
        $config = Config::get($universe);

        return !(1 > $galaxy
            || 1 > $system
            || 1 > $position
            || $config->max_galaxy < $galaxy
            || $config->max_system < $system
            || $config->max_planets < $position);
    }

    public static function createPlayer(
        int $universe,
        string $user_name,
        string $user_pass,
        string $user_mail,
        string $user_lang,
        int $galaxy = 0,
        int $system = 0,
        int $position = 0,
        $name = null,
        $authlevel = 0,
        $user_ip_addr = null,
        $user_secret_question_id = 0,
        $user_secret_question_answer = ''
    ): array {
        $config = Config::get($universe);

        if (isset($universe)
            && $galaxy !== 0
            && $system !== 0
            && $position !== 0)
        {
            if (!self::checkPosition($universe, $galaxy, $system, $position))
            {
                throw new Exception(sprintf("Try to create a planet at position: %d:%d:%d!", $galaxy, $system, $position));
            }

            if (!self::isPositionFree($universe, $galaxy, $system, $position))
            {
                throw new Exception(sprintf("Position is not empty: %d:%d:%d!", $galaxy, $system, $position));
            }
        }
        else
        {
            $galaxy = $config->last_settled_galaxy_pos;
            $system = $config->last_settled_system_pos;
            $planet = $config->last_settled_planet_pos;

            if ($galaxy > $config->max_galaxy)
            {
                $galaxy = 1;
            }

            if ($system > $config->max_system)
            {
                $system = 1;
            }

            do
            {
                $position = mt_rand(round($config->max_planets * 0.2), round($config->max_planets * 0.8));
                if ($planet < 3)
                {
                    $planet += 1;
                }
                else
                {
                    if ($system >= $config->max_system)
                    {
                        $system = 1;
                        if ($galaxy >= $config->max_galaxy)
                        {
                            $galaxy = 1;
                        }
                        else
                        {
                            $galaxy += 1;
                        }
                    }
                    else
                    {
                        $system += 1;
                    }
                }
            }
            while (self::isPositionFree($universe, $galaxy, $system, $position) === false);

            // Update last coordinates to config table
            $config->last_settled_galaxy_pos = $galaxy;
            $config->last_settled_system_pos = $system;
            $config->last_settled_planet_pos = $planet;
        }

        $params = [
            ':username'                    => $user_name,
            ':email'                       => $user_mail,
            ':email2'                      => $user_mail,
            ':user_secret_question_id'     => $user_secret_question_id,
            ':user_secret_question_answer' => $user_secret_question_answer,
            ':authlevel'                   => $authlevel,
            ':universe'                    => $universe,
            ':language'                    => $user_lang,
            ':registerAddress'             => !empty($user_ip_addr) ? $user_ip_addr : Session::getClientIp(),
            ':onlinetime'                  => TIMESTAMP,
            ':registerTimestamp'           => TIMESTAMP,
            ':password'                    => $user_pass,
            ':timezone'                    => $config->timezone,
            ':nameLastChanged'             => 0,
            ':darkmatter_start'            => $config->darkmatter_start,
        ];

        $sql = 'INSERT INTO %%USERS%% SET
		`username`		= :username,
		`email`			= :email,
		`email_2`			= :email2,
		`user_secret_question_id` = :user_secret_question_id,
		`user_secret_question_answer` = :user_secret_question_answer,
		`authlevel`		= :authlevel,
		`universe`		= :universe,
		`lang`			= :language,
		`ip_at_reg`		= :registerAddress,
		`onlinetime`		= :onlinetime,
		`register_time`	= :registerTimestamp,
		`password`		= :password,
		`timezone`		= :timezone,
		`uctime`			= :nameLastChanged,
		`darkmatter`		= :darkmatter_start;';

        $db = Database::get();

        $db->insert($sql, $params);

        $user_id = $db->lastInsertId();
        $planet_id = self::createPlanet($galaxy, $system, $position, $universe, $user_id, $name, true, $authlevel);

        $currentUserAmount = $config->users_amount + 1;
        $config->users_amount = $currentUserAmount;

        $sql = "UPDATE %%USERS%% SET
		`galaxy` = :galaxy,
		`system` = :system,
		`planet` = :position,
		`id_planet` = :planet_id
		WHERE id = :user_id;";

        $db->update($sql, [
            ':galaxy'    => $galaxy,
            ':system'    => $system,
            ':position'  => $position,
            ':planet_id' => $planet_id,
            ':user_id'   => $user_id,
        ]);

        $sql = "UPDATE %%PLANETS%% SET metal = :metal_start, crystal = :crystal_start, 
        deuterium = :deuterium_start WHERE id = :planet_id;";

        $db->update($sql, [
            ':metal_start'     => $config->metal_start,
            ':crystal_start'   => $config->crystal_start,
            ':deuterium_start' => $config->deuterium_start,
            ':planet_id'       => $planet_id,
        ]);

        $sql = "SELECT MAX(total_rank) as rank FROM %%USER_POINTS%% WHERE universe = :universe;";

        $rank = $db->selectSingle($sql, [
            ':universe' => $universe,
        ], 'rank');

        $sql = "INSERT INTO %%USER_POINTS%% SET
				`id_owner`	= :user_id,
				`universe`	= :universe,
				`tech_rank`	= :rank,
				`build_rank`	= :rank,
				`defs_rank`	= :rank,
				`fleet_rank`	= :rank,
				`total_rank`	= :rank;";

        $db->insert($sql, [
            ':universe' => $universe,
            ':user_id'  => $user_id,
            ':rank'     => $rank + 1,
        ]);

        $config->save();

        return [$user_id, $planet_id];
    }

    public static function updateColonyWithStartValues(int $planetID): void
    {

        $db = Database::get();

        $sql = "SELECT * FROM %%COLONY_SETTINGS%%;";

        $colony_settings = $db->selectSingle($sql);

        $sql = "UPDATE %%PLANETS%% SET
		`metal` = :metal_start,
		`crystal` = :crystal_start,
		`deuterium` = :deuterium_start,
		`metal_mine` = :metal_mine_start,
		`crystal_mine` = :crystal_mine_start,
		`deuterium_synthesizer` = :deuterium_synthesizer_start,
		`solar_plant` = :solar_plant_start,
		`fusion_plant` = :fusion_plant_start,
		`robot_factory` = :robot_factory_start,
		`nanite_factory` = :nanite_factory_start,
		`shipyard` = :shipyard_start,
		`metal_storage` = :metal_storage_start,
		`crystal_storage` = :crystal_storage_start,
		`deuterium_tank` = :deuterium_tank_start,
		`research_lab` = :research_lab_start,
		`terraformer` = :terraformer_start,
		`university` = :university_start,
		`ally_deposit` = :ally_deposit_start,
		`missile_silo` = :missile_silo_start,
		`small_cargo` = :small_cargo_start,
		`big_cargo` = :big_cargo_start,
		`light_hunter` = :light_hunter_start,
		`heavy_hunter` = :heavy_hunter_start,
		`cruiser` = :cruiser_start,
		`battle_ship` = :battle_ship_start,
		`colony_ship` = :colony_ship_start,
		`recycler` = :recycler_start,
		`espionage_probe` = :espionage_probe_start,
		`bomber_ship` = :bomber_ship_start,
		`solar_satellite` = :solar_satellite_start,
		`destroyer` = :destroyer_start,
		`death_star` = :death_star_start,
		`battle_cruiser` = :battle_cruiser_start,
		`ev_transporter` = :ev_transporter_start,
		`star_crasher` = :star_crasher_start,
		`giga_recycler` = :giga_recycler_start,
		`dm_ship` = :dm_ship_start,
		`orbital_station` = :orbital_station_start,
		`rocket_launcher` = :rocket_launcher_start,
		`light_laser` = :light_laser_start,
		`heavy_laser` = :heavy_laser_start,
		`gauss_cannon` = :gauss_cannon_start,
		`ion_cannon` = :ion_cannon_start,
		`plasma_turret` = :plasma_turret_start,
		`small_protection_shield` = :small_protection_shield_start,
		`planet_protector` = :planet_protector_start,
		`big_protection_shield` = :big_protection_shield_start,
		`graviton_cannon` = :graviton_cannon_start,
		`interceptor_misil` = :interceptor_misil_start,
		`interplanetary_misil` = :interplanetary_misil_start
		WHERE id = :planetID;";

        Database::get()->update($sql, [
            ':metal_start'                   => $colony_settings['metal_start'],
            ':crystal_start'                 => $colony_settings['crystal_start'],
            ':deuterium_start'               => $colony_settings['deuterium_start'],
            ':metal_mine_start'              => $colony_settings['metal_mine_start'],
            ':crystal_mine_start'            => $colony_settings['crystal_mine_start'],
            ':deuterium_synthesizer_start'   => $colony_settings['deuterium_synthesizer_start'],
            ':solar_plant_start'             => $colony_settings['solar_plant_start'],
            ':fusion_plant_start'            => $colony_settings['fusion_plant_start'],
            ':robot_factory_start'           => $colony_settings['robot_factory_start'],
            ':nanite_factory_start'          => $colony_settings['nanite_factory_start'],
            ':shipyard_start'                => $colony_settings['shipyard_start'],
            ':metal_storage_start'           => $colony_settings['metal_storage_start'],
            ':crystal_storage_start'         => $colony_settings['crystal_storage_start'],
            ':deuterium_tank_start'          => $colony_settings['deuterium_tank_start'],
            ':research_lab_start'            => $colony_settings['research_lab_start'],
            ':terraformer_start'             => $colony_settings['terraformer_start'],
            ':university_start'              => $colony_settings['university_start'],
            ':ally_deposit_start'            => $colony_settings['ally_deposit_start'],
            ':missile_silo_start'            => $colony_settings['missile_silo_start'],
            ':small_cargo_start'             => $colony_settings['small_cargo_start'],
            ':big_cargo_start'               => $colony_settings['big_cargo_start'],
            ':light_hunter_start'            => $colony_settings['light_hunter_start'],
            ':heavy_hunter_start'            => $colony_settings['heavy_hunter_start'],
            ':cruiser_start'                 => $colony_settings['cruiser_start'],
            ':battle_ship_start'             => $colony_settings['battle_ship_start'],
            ':colony_ship_start'             => $colony_settings['colony_ship_start'],
            ':recycler_start'                => $colony_settings['recycler_start'],
            ':espionage_probe_start'         => $colony_settings['espionage_probe_start'],
            ':bomber_ship_start'             => $colony_settings['bomber_ship_start'],
            ':solar_satellite_start'         => $colony_settings['solar_satellite_start'],
            ':destroyer_start'               => $colony_settings['destroyer_start'],
            ':death_star_start'              => $colony_settings['death_star_start'],
            ':battle_cruiser_start'          => $colony_settings['battle_cruiser_start'],
            ':ev_transporter_start'          => $colony_settings['ev_transporter_start'],
            ':star_crasher_start'            => $colony_settings['star_crasher_start'],
            ':giga_recycler_start'           => $colony_settings['giga_recycler_start'],
            ':dm_ship_start'                 => $colony_settings['dm_ship_start'],
            ':orbital_station_start'         => $colony_settings['orbital_station_start'],
            ':rocket_launcher_start'         => $colony_settings['rocket_launcher_start'],
            ':light_laser_start'             => $colony_settings['light_laser_start'],
            ':heavy_laser_start'             => $colony_settings['heavy_laser_start'],
            ':gauss_cannon_start'            => $colony_settings['gauss_cannon_start'],
            ':ion_cannon_start'              => $colony_settings['ion_cannon_start'],
            ':plasma_turret_start'           => $colony_settings['plasma_turret_start'],
            ':small_protection_shield_start' => $colony_settings['small_protection_shield_start'],
            ':planet_protector_start'        => $colony_settings['planet_protector_start'],
            ':big_protection_shield_start'   => $colony_settings['big_protection_shield_start'],
            ':graviton_cannon_start'         => $colony_settings['graviton_cannon_start'],
            ':interceptor_misil_start'       => $colony_settings['interceptor_misil_start'],
            ':interplanetary_misil_start'    => $colony_settings['interplanetary_misil_start'],
            ':planetID'                      => $planetID,
        ]);

    }

    public static function createPlanet(
        int $galaxy,
        int $system,
        int $position,
        int $universe,
        int $user_id,
        ?string $name = null,
        bool $isHome = false,
        int $authlevel = 0
    ) {
        global $LNG;

        if (self::checkPosition($universe, $galaxy, $system, $position) === false)
        {
            throw new Exception(sprintf("Try to create a planet at position: %s:%s:%s!", $galaxy, $system, $position));
        }

        if (self::isPositionFree($universe, $galaxy, $system, $position) === false)
        {
            throw new Exception(sprintf("Position is not empty: %s:%s:%s!", $galaxy, $system, $position));
        }

        $planetData = [];
        require 'includes/PlanetData.php';

        $config = Config::get($universe);

        $dataIndex = (int) ceil($position / ($config->max_planets / count($planetData)));
        $maxTemperature = $planetData[$dataIndex]['temp'];
        $minTemperature = $maxTemperature - 40;

        if ($isHome)
        {
            $maxFields = $config->initial_fields;
        }
        else
        {
            $maxFields = (int) floor($planetData[$dataIndex]['fields'] * $config->planet_factor);
        }

        $diameter = (int) floor(1000 * sqrt($maxFields));

        $imageNames = array_keys($planetData[$dataIndex]['image']);
        $imageNameType = $imageNames[array_rand($imageNames)];
        $imageName = $imageNameType;
        $imageName .= 'planet';
        $imageName .= $planetData[$dataIndex]['image'][$imageNameType] < 10 ? '0' : '';
        $imageName .= $planetData[$dataIndex]['image'][$imageNameType];

        if (empty($name))
        {
            $name = $isHome ? $LNG['fcm_mainplanet'] : $LNG['fcp_colony'];
        }

        $params = [
            ':name'            => $name,
            ':universe'        => $universe,
            ':user_id'         => $user_id,
            ':galaxy'          => $galaxy,
            ':system'          => $system,
            ':position'        => $position,
            ':updateTimestamp' => TIMESTAMP,
            ':type'            => 1,
            ':imageName'       => $imageName,
            ':diameter'        => $diameter,
            ':maxFields'       => $maxFields,
            ':minTemperature'  => $minTemperature,
            ':maxTemperature'  => $maxTemperature,
        ];

        $sql = 'INSERT INTO %%PLANETS%% SET
		`name`		= :name,
		`universe`	= :universe,
		`id_owner`	= :user_id,
		`galaxy`		= :galaxy,
		`system`		= :system,
		`planet`		= :position,
		`last_update`	= :updateTimestamp,
		`planet_type`	= :type,
		`image`		= :imageName,
		`diameter`	= :diameter,
		`field_max`	= :maxFields,
		`temp_min` 	= :minTemperature,
		`temp_max` 	= :maxTemperature;';

        $db = Database::get();
        $db->insert($sql, $params);

        return $db->lastInsertId();
    }

    public static function createMoon(
        int $universe,
        int $galaxy,
        int $system,
        int $position,
        int $user_id,
        $chance,
        $diameter = null,
        $moon_name = null
    ) {
        global $LNG;

        $db = Database::get();

        $sql = "SELECT `id_moon`, `planet_type`, `id`, `name`, `temp_max`, `temp_min`
				FROM %%PLANETS%%
				WHERE `universe` = :universe
				AND `galaxy` = :galaxy
				AND `system` = :system
				AND `planet` = :position
				AND `planet_type` = :type;";

        $parent_planet = $db->selectSingle($sql, [
            ':universe' => $universe,
            ':galaxy'   => $galaxy,
            ':system'   => $system,
            ':position' => $position,
            ':type'     => 1,
        ]);

        if ($parent_planet['id_moon'] != 0)
        {
            return false;
        }

        if (is_null($diameter))
        {
            # New Calculation - 23.04.2011
            $diameter = floor(pow(mt_rand(10, 20) + 3 * $chance, 0.5) * 1000);
        }

        $max_temp = $parent_planet['temp_max'] - mt_rand(10, 45);
        $min_temp = $parent_planet['temp_min'] - mt_rand(10, 45);

        if (empty($moon_name))
        {
            $moon_name = $LNG['type_planet_3'];
        }

        $sql = "INSERT INTO %%PLANETS%% SET
		`name`				= :name,
		`id_owner`			= :owner,
		`universe`			= :universe,
		`galaxy`			= :galaxy,
		`system`			= :system,
		`planet`			= :planet,
		`last_update`		= :update_time,
		`planet_type`		= :type,
		`image`				= :image,
		`diameter`			= :diameter,
		`field_max`			= :fields,
		`temp_min`			= :min_temp,
		`temp_max`			= :max_temp,
		`metal`				= :metal,
		`metal_perhour`		= :metal_perhour,
		`crystal`			= :crystal,
		`crystal_perhour`	= :crystal_perhour,
		`deuterium`			= :deuterium,
		`deuterium_perhour`	= :deuterium_perhour;";

        $db->insert($sql, [
            ':name'              => $moon_name,
            ':owner'             => $user_id,
            ':universe'          => $universe,
            ':galaxy'            => $galaxy,
            ':system'            => $system,
            ':planet'            => $position,
            ':update_time'       => TIMESTAMP,
            ':type'              => 3,
            ':image'             => 'mond',
            ':diameter'          => $diameter,
            ':fields'            => 1,
            ':min_temp'          => $min_temp,
            ':max_temp'          => $max_temp,
            ':metal'             => 0,
            ':metal_perhour'     => 0,
            ':crystal'           => 0,
            ':crystal_perhour'   => 0,
            ':deuterium'         => 0,
            ':deuterium_perhour' => 0,
        ]);

        $id_moon = $db->lastInsertId();

        $sql = "UPDATE %%PLANETS%% SET id_moon = :id_moon WHERE id = :planet_id;";

        $db->update($sql, [
            ':id_moon'   => $id_moon,
            ':planet_id' => $parent_planet['id'],
        ]);

        return $id_moon;
    }

    public static function deletePlayer(int $user_id): bool
    {
        if (ROOT_USER == $user_id)
        {
            // superuser can not be deleted.
            throw new Exception("Superuser #".ROOT_USER." can't be deleted!");
        }

        $db = Database::get();
        $sql = 'SELECT universe, ally_id FROM %%USERS%% WHERE id = :user_id;';
        $user_data = $db->selectSingle($sql, [
            ':user_id' => $user_id,
        ]);

        if (empty($user_data))
        {
            return false;
        }

        if (!empty($user_data['ally_id']))
        {
            $sql = 'SELECT ally_members FROM %%ALLIANCE%% WHERE id = :alliance_id;';
            $member_count = $db->selectSingle($sql, [
                ':alliance_id' => $user_data['ally_id'],
            ], 'ally_members');

            if ($member_count > 1)
            {
                $sql = 'UPDATE %%ALLIANCE%% SET ally_members = ally_members - 1 
                WHERE id = :alliance_id;';
                $db->update($sql, [
                    ':alliance_id' => $user_data['ally_id'],
                ]);
            }
            else
            {
                $sql = 'DELETE FROM %%ALLIANCE%% WHERE id = :alliance_id;';
                $db->delete($sql, [
                    ':alliance_id' => $user_data['ally_id'],
                ]);

                $sql = 'DELETE FROM %%ALLIANCE_POINTS%% WHERE id_owner = :alliance_id;';
                $db->delete($sql, [
                    ':alliance_id' => $user_data['ally_id'],
                    ':type'        => 2,
                ]);

                $sql = 'UPDATE %%USER_POINTS%% SET id_ally = :resetId WHERE id_ally = :alliance_id;';
                $db->update($sql, [
                    ':alliance_id' => $user_data['ally_id'],
                    ':resetId'     => 0,
                ]);
            }
        }

        $sql = 'DELETE FROM %%ALLIANCE_REQUEST%% WHERE user_id = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $sql = 'DELETE FROM %%BUDDY%% WHERE owner = :user_id OR sender = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
		FROM %%FLEETS%% LEFT JOIN %%FLEETS_EVENT%% on fleet_id = fleetId
		WHERE fleet_owner = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $sql = 'DELETE FROM %%MESSAGES%% WHERE message_owner = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $sql = 'DELETE FROM %%NOTES%% WHERE owner = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $sql = 'DELETE FROM %%PLANETS%% WHERE id_owner = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $sql = 'DELETE FROM %%USERS%% WHERE id = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $sql = 'DELETE FROM %%USER_POINTS%% WHERE id_owner = :user_id;';
        $db->delete($sql, [
            ':user_id' => $user_id,
        ]);

        $fleet_ids = $db->select('SELECT fleet_id FROM %%FLEETS%% WHERE fleet_target_owner = :user_id;', [
            ':user_id' => $user_id,
        ]);

        foreach ($fleet_ids as $fid)
        {
            FleetFunctions::SendFleetBack(['id' => $user_id], $fid['fleet_id']);
        }

        /*
        $sql	= 'UPDATE %%UNIVERSE%% SET userAmount = userAmount - 1 WHERE universe = :universe;';
        $db->update($sql, array(
            ':universe' => $user_data['universe']
        ));

        Cache::get()->flush('universe');
        */

        return true;
    }

    public static function deletePlanet(int $planet_id)
    {
        $db = Database::get();

        $sql = "SELECT `id_owner`, `planet_type`, `id_moon` FROM %%PLANETS%%
		WHERE `id` = :planet_id AND `id` NOT IN (SELECT `id_planet` FROM %%USERS%%);";

        $planetData = $db->selectSingle($sql, [
            ':planet_id' => $planet_id,
        ]);

        if (empty($planetData))
        {
            throw new Exception("Can not found planet #".$planet_id."!");
        }

        $sql = "SELECT `fleet_id` FROM %%FLEETS%%
		WHERE `fleet_end_id` = :planet_id OR (`fleet_end_type` = 3 AND `fleet_end_id` = :moon_id);";

        $fleetIds = $db->select($sql, [
            ':planet_id' => $planet_id,
            ':moon_id'   => $planetData['id_moon'],
        ]);

        foreach ($fleetIds as $fleetId)
        {
            FleetFunctions::SendFleetBack(['id' => $planetData['id_owner']], $fleetId['fleet_id']);
        }

        if ($planetData['planet_type'] == 3)
        {
            $sql = "DELETE FROM %%PLANETS%% WHERE `id` = :planet_id;";
            $db->delete($sql, [
                ':planet_id' => $planet_id,
            ]);

            $sql = "UPDATE %%PLANETS%% SET `id_moon` = :reset_id WHERE `id_moon` = :planet_id;";
            $db->update($sql, [
                ':reset_id'  => 0,
                ':planet_id' => $planet_id,
            ]);
        }
        else
        {
            $sql = "DELETE FROM %%PLANETS%% WHERE `id` = :planet_id OR `id_moon` = :planet_id;";
            $db->delete($sql, [
                ':planet_id' => $planet_id,
            ]);
        }

        return true;
    }

    public static function maxPlanetCount(array $USER): int
    {
        global $RESOURCE;
        $config = Config::get($USER['universe']);

        $planetPerTech = $config->planets_tech;
        $planetPerBonus = $config->planets_officers;

        if ($config->min_player_planets == 0)
        {
            $planetPerTech = 999;
        }

        if ($config->min_player_planets == 0)
        {
            $planetPerBonus = 999;
        }

        // http://owiki.de/index.php/Astrophysik#.C3.9Cbersicht
        return (int) ceil($config->min_player_planets + min($planetPerTech, $USER[$RESOURCE[124]] * $config->planets_per_tech) + min($planetPerBonus, $USER['factor']['Planets']));
    }

    public static function allowPlanetPosition(int $position, array $USER): bool
    {
        // http://owiki.de/index.php/Astrophysik#.C3.9Cbersicht

        global $RESOURCE;
        $config = Config::get($USER['universe']);

        switch ($position)
        {
            case 1:
            case ($config->max_planets):
                return $USER[$RESOURCE[124]] >= 8;
            case 2:
            case ($config->max_planets - 1):
                return $USER[$RESOURCE[124]] >= 6;
            case 3:
            case ($config->max_planets - 2):
                return $USER[$RESOURCE[124]] >= 4;
            default:
                return $USER[$RESOURCE[124]] >= 1;
        }
    }

    public static function sendMessage(
        int $user_id,
        int $sender_id,
        string $sender_name,
        int $message_type,
        string $subject,
        string $text,
        int $time,
        $parent_id = null,
        int $unread = 1,
        int $universe = -1
    ): void {
        if ($universe === -1)
        {
            $universe = Universe::current();
        }

        $db = Database::get();

        $sql = "INSERT INTO %%MESSAGES%% SET
		`message_owner`		= :user_id,
		`message_sender`	= :sender,
		`message_time`		= :message_time,
		`message_type`		= :type,
		`message_from`		= :message_from,
		`message_subject` 	= :subject,
		`message_text`		= :message_text,
		`message_unread`	= :unread,
		`message_universe` 	= :universe;";

        $db->insert($sql, [
            ':user_id'      => $user_id,
            ':sender'       => $sender_id,
            ':message_time' => $time,
            ':type'         => $message_type,
            ':message_from' => $sender_name,
            ':subject'      => $subject,
            ':message_text' => $text,
            ':unread'       => $unread,
            ':universe'     => $universe,
        ]);
    }
}
