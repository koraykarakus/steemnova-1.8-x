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

/**
 *
 */
class ShowColonySettingsPage extends AbstractAdminPage
{
    protected $colony_settings;

    public function __construct()
    {
        parent::__construct();
        $this->getColonySettings();
    }

    private function getColonySettings(): void
    {
        $db = Database::get();

        $sql = "SELECT * FROM %%COLONY_SETTINGS%%;";

        $this->colony_settings = $db->selectSingle($sql);
    }

    public function show(): void
    {

        $this->assign([
            'metal_start'                   => $this->colony_settings['metal_start'],
            'crystal_start'                 => $this->colony_settings['crystal_start'],
            'deuterium_start'               => $this->colony_settings['deuterium_start'],
            'metal_mine_start'              => $this->colony_settings['metal_mine_start'],
            'crystal_mine_start'            => $this->colony_settings['crystal_mine_start'],
            'deuterium_synthesizer_start'          => $this->colony_settings['deuterium_synthesizer_start'],
            'solar_plant_start'             => $this->colony_settings['solar_plant_start'],
            'fusion_plant_start'            => $this->colony_settings['fusion_plant_start'],
            'robot_factory_start'           => $this->colony_settings['robot_factory_start'],
            'nanite_factory_start'          => $this->colony_settings['nanite_factory_start'],
            'shipyard_start'                => $this->colony_settings['shipyard_start'],
            'metal_storage_start'             => $this->colony_settings['metal_storage_start'],
            'crystal_storage_start'           => $this->colony_settings['crystal_storage_start'],
            'deuterium_tank_start'         => $this->colony_settings['deuterium_tank_start'],
            'research_lab_start'              => $this->colony_settings['research_lab_start'],
            'terraformer_start'             => $this->colony_settings['terraformer_start'],
            'university_start'              => $this->colony_settings['university_start'],
            'ally_deposit_start'            => $this->colony_settings['ally_deposit_start'],
            'missile_silo_start'                    => $this->colony_settings['missile_silo_start'],
            'small_cargo_start'             => $this->colony_settings['small_cargo_start'],
            'big_cargo_start'               => $this->colony_settings['big_cargo_start'],
            'light_hunter_start'            => $this->colony_settings['light_hunter_start'],
            'heavy_hunter_start'            => $this->colony_settings['heavy_hunter_start'],
            'cruiser_start'                 => $this->colony_settings['cruiser_start'],
            'battle_ship_start'             => $this->colony_settings['battle_ship_start'],
            'colony_ship_start'             => $this->colony_settings['colony_ship_start'],
            'recycler_start'                => $this->colony_settings['recycler_start'],
            'espionage_probe_start'         => $this->colony_settings['espionage_probe_start'],
            'bomber_ship_start'             => $this->colony_settings['bomber_ship_start'],
            'solar_satellite_start'         => $this->colony_settings['solar_satellite_start'],
            'destroyer_start'               => $this->colony_settings['destroyer_start'],
            'death_star_start'              => $this->colony_settings['death_star_start'],
            'ev_transporter_start'          => $this->colony_settings['ev_transporter_start'],
            'star_crasher_start'            => $this->colony_settings['star_crasher_start'],
            'dm_ship_start'                 => $this->colony_settings['dm_ship_start'],
            'orbital_station_start'         => $this->colony_settings['orbital_station_start'],
            'misil_launcher_start'          => $this->colony_settings['misil_launcher_start'],
            'small_laser_start'             => $this->colony_settings['small_laser_start'],
            'big_laser_start'               => $this->colony_settings['big_laser_start'],
            'gauss_canyon_start'            => $this->colony_settings['gauss_canyon_start'],
            'ionic_canyon_start'            => $this->colony_settings['ionic_canyon_start'],
            'buster_canyon_start'           => $this->colony_settings['buster_canyon_start'],
            'small_protection_shield_start' => $this->colony_settings['small_protection_shield_start'],
            'planet_protector_start'        => $this->colony_settings['planet_protector_start'],
            'big_protection_shield_start'   => $this->colony_settings['big_protection_shield_start'],
            'graviton_canyon_start'         => $this->colony_settings['graviton_canyon_start'],
            'interceptor_misil_start'       => $this->colony_settings['interceptor_misil_start'],
            'interplanetary_misil_start'    => $this->colony_settings['interplanetary_misil_start'],
        ]);

        $this->display('page.colony.default.tpl');

    }

    public function saveSettings(): void
    {
        global $LNG;

        $config_before = [
            'metal_start'                   => $this->colony_settings['metal_start'],
            'crystal_start'                 => $this->colony_settings['crystal_start'],
            'deuterium_start'               => $this->colony_settings['deuterium_start'],
            'metal_mine_start'              => $this->colony_settings['metal_mine_start'],
            'crystal_mine_start'            => $this->colony_settings['crystal_mine_start'],
            'deuterium_synthesizer_start'          => $this->colony_settings['deuterium_synthesizer_start'],
            'solar_plant_start'             => $this->colony_settings['solar_plant_start'],
            'fusion_plant_start'            => $this->colony_settings['fusion_plant_start'],
            'robot_factory_start'           => $this->colony_settings['robot_factory_start'],
            'nanite_factory_start'          => $this->colony_settings['nanite_factory_start'],
            'shipyard_start'                => $this->colony_settings['shipyard_start'],
            'metal_storage_start'             => $this->colony_settings['metal_storage_start'],
            'crystal_storage_start'           => $this->colony_settings['crystal_storage_start'],
            'deuterium_tank_start'         => $this->colony_settings['deuterium_tank_start'],
            'research_lab_start'              => $this->colony_settings['research_lab_start'],
            'terraformer_start'             => $this->colony_settings['terraformer_start'],
            'university_start'              => $this->colony_settings['university_start'],
            'ally_deposit_start'            => $this->colony_settings['ally_deposit_start'],
            'missile_silo_start'                    => $this->colony_settings['missile_silo_start'],
            'small_cargo_start'             => $this->colony_settings['small_cargo_start'],
            'big_cargo_start'               => $this->colony_settings['big_cargo_start'],
            'light_hunter_start'            => $this->colony_settings['light_hunter_start'],
            'heavy_hunter_start'            => $this->colony_settings['heavy_hunter_start'],
            'cruiser_start'                 => $this->colony_settings['cruiser_start'],
            'battle_ship_start'             => $this->colony_settings['battle_ship_start'],
            'colony_ship_start'             => $this->colony_settings['colony_ship_start'],
            'recycler_start'                => $this->colony_settings['recycler_start'],
            'espionage_probe_start'         => $this->colony_settings['espionage_probe_start'],
            'bomber_ship_start'             => $this->colony_settings['bomber_ship_start'],
            'solar_satellite_start'         => $this->colony_settings['solar_satellite_start'],
            'destroyer_start'               => $this->colony_settings['destroyer_start'],
            'death_star_start'              => $this->colony_settings['death_star_start'],
            'ev_transporter_start'          => $this->colony_settings['ev_transporter_start'],
            'star_crasher_start'            => $this->colony_settings['star_crasher_start'],
            'dm_ship_start'                 => $this->colony_settings['dm_ship_start'],
            'orbital_station_start'         => $this->colony_settings['orbital_station_start'],
            'misil_launcher_start'          => $this->colony_settings['misil_launcher_start'],
            'small_laser_start'             => $this->colony_settings['small_laser_start'],
            'big_laser_start'               => $this->colony_settings['big_laser_start'],
            'gauss_canyon_start'            => $this->colony_settings['gauss_canyon_start'],
            'ionic_canyon_start'            => $this->colony_settings['ionic_canyon_start'],
            'buster_canyon_start'           => $this->colony_settings['buster_canyon_start'],
            'small_protection_shield_start' => $this->colony_settings['small_protection_shield_start'],
            'planet_protector_start'        => $this->colony_settings['planet_protector_start'],
            'big_protection_shield_start'   => $this->colony_settings['big_protection_shield_start'],
            'graviton_canyon_start'         => $this->colony_settings['graviton_canyon_start'],
            'interceptor_misil_start'       => $this->colony_settings['interceptor_misil_start'],
            'interplanetary_misil_start'    => $this->colony_settings['interplanetary_misil_start'],
        ];

        $metal_start = HTTP::_GP('metal_start', 500);
        $crystal_start = HTTP::_GP('crystal_start', 500);
        $deuterium_start = HTTP::_GP('deuterium_start', 0);
        $metal_mine_start = HTTP::_GP('metal_mine_start', 0);
        $crystal_mine_start = HTTP::_GP('crystal_mine_start', 0);
        $deuterium_synthesizer_start = HTTP::_GP('deuterium_synthesizer_start', 0);
        $solar_plant_start = HTTP::_GP('solar_plant_start', 0);
        $fusion_plant_start = HTTP::_GP('fusion_plant_start', 0);
        $robot_factory_start = HTTP::_GP('robot_factory_start', 0);
        $nanite_factory_start = HTTP::_GP('nanite_factory_start', 0);
        $shipyard_start = HTTP::_GP('shipyard_start', 0);
        $metal_storage_start = HTTP::_GP('metal_storage_start', 0);
        $crystal_storage_start = HTTP::_GP('crystal_storage_start', 0);
        $deuterium_tank_start = HTTP::_GP('deuterium_tank_start', 0);
        $research_lab_start = HTTP::_GP('research_lab_start', 0);
        $terraformer_start = HTTP::_GP('terraformer_start', 0);
        $university_start = HTTP::_GP('university_start', 0);
        $ally_deposit_start = HTTP::_GP('ally_deposit_start', 0);
        $missile_silo_start = HTTP::_GP('missile_silo_start', 0);
        $small_cargo_start = HTTP::_GP('small_cargo_start', 0);
        $big_cargo_start = HTTP::_GP('big_cargo_start', 0);
        $light_hunter_start = HTTP::_GP('light_hunter_start', 0);
        $heavy_hunter_start = HTTP::_GP('heavy_hunter_start', 0);
        $cruiser_start = HTTP::_GP('cruiser_start', 0);
        $battle_ship_start = HTTP::_GP('battle_ship_start', 0);
        $colony_ship_start = HTTP::_GP('colony_ship_start', 0);
        $recycler_start = HTTP::_GP('recycler_start', 0);
        $espionage_probe_start = HTTP::_GP('espionage_probe_start', 0);
        $bomber_ship_start = HTTP::_GP('bomber_ship_start', 0);
        $solar_satellite_start = HTTP::_GP('solar_satellite_start', 0);
        $destroyer_start = HTTP::_GP('destroyer_start', 0);
        $death_star_start = HTTP::_GP('death_star_start', 0);
        $battle_cruiser_start = HTTP::_GP('battle_cruiser_start', 0);
        $ev_transporter_start = HTTP::_GP('ev_transporter_start', 0);
        $star_crasher_start = HTTP::_GP('star_crasher_start', 0);
        $giga_recycler_start = HTTP::_GP('giga_recycler_start', 0);
        $dm_ship_start = HTTP::_GP('dm_ship_start', 0);
        $orbital_station_start = HTTP::_GP('orbital_station_start', 0);
        $misil_launcher_start = HTTP::_GP('misil_launcher_start', 0);
        $small_laser_start = HTTP::_GP('small_laser_start', 0);
        $big_laser_start = HTTP::_GP('big_laser_start', 0);
        $gauss_canyon_start = HTTP::_GP('gauss_canyon_start', 0);
        $ionic_canyon_start = HTTP::_GP('ionic_canyon_start', 0);
        $buster_canyon_start = HTTP::_GP('buster_canyon_start', 0);
        $small_protection_shield_start = HTTP::_GP('small_protection_shield_start', 0);
        $planet_protector_start = HTTP::_GP('planet_protector_start', 0);
        $big_protection_shield_start = HTTP::_GP('big_protection_shield_start', 0);
        $graviton_canyon_start = HTTP::_GP('graviton_canyon_start', 0);
        $interceptor_misil_start = HTTP::_GP('interceptor_misil_start', 0);
        $interplanetary_misil_start = HTTP::_GP('interplanetary_misil_start', 0);

        $sql = "UPDATE %%COLONY_SETTINGS%% SET
			`metal_start` = :metal_start,
			`crystal_start` = :crystal_start,
			`deuterium_start` = :deuterium_start,
			`metal_mine_start` = :metal_mine_start,
			`crystal_mine_start` = :crystal_mine_start,
			`deuterium_synthesizer_start` = :deuterium_synthesizer_start,
			`solar_plant_start` = :solar_plant_start,
			`fusion_plant_start` = :fusion_plant_start,
			`robot_factory_start` = :robot_factory_start,
			`nanite_factory_start` = :nanite_factory_start,
			`shipyard_start` = :shipyard_start,
			`metal_storage_start` = :metal_storage_start,
			`crystal_storage_start` = :crystal_storage_start,
			`deuterium_tank_start` = :deuterium_tank_start,
			`research_lab_start` = :research_lab_start,
			`terraformer_start` = :terraformer_start,
			`university_start` = :university_start,
			`ally_deposit_start` = :ally_deposit_start,
			`missile_silo_start` = :missile_silo_start,
			`small_cargo_start` = :small_cargo_start,
			`big_cargo_start` = :big_cargo_start,
			`light_hunter_start` = :light_hunter_start,
			`heavy_hunter_start` = :heavy_hunter_start,
			`cruiser_start` = :cruiser_start,
			`battle_ship_start` = :battle_ship_start,
			`colony_ship_start` = :colony_ship_start,
			`recycler_start` = :recycler_start,
			`espionage_probe_start` = :espionage_probe_start,
			`bomber_ship_start` = :bomber_ship_start,
			`solar_satellite_start` = :solar_satellite_start,
			`destroyer_start` = :destroyer_start,
			`death_star_start` = :death_star_start,
			`battle_cruiser_start` = :battle_cruiser_start,
			`ev_transporter_start` = :ev_transporter_start,
			`star_crasher_start` = :star_crasher_start,
			`giga_recycler_start` = :giga_recycler_start,
			`dm_ship_start` = :dm_ship_start,
			`orbital_station_start` = :orbital_station_start,
			`misil_launcher_start` = :misil_launcher_start,
			`small_laser_start` = :small_laser_start,
			`big_laser_start` = :big_laser_start,
			`gauss_canyon_start` = :gauss_canyon_start,
			`ionic_canyon_start` = :ionic_canyon_start,
			`buster_canyon_start` = :buster_canyon_start,
			`small_protection_shield_start` = :small_protection_shield_start,
			`planet_protector_start` = :planet_protector_start,
			`big_protection_shield_start` = :big_protection_shield_start,
			`graviton_canyon_start` = :graviton_canyon_start,
			`interceptor_misil_start` = :interceptor_misil_start,
			`interplanetary_misil_start` = :interplanetary_misil_start;";

        Database::get()->update($sql, [
            ':metal_start'                   => $metal_start,
            ':crystal_start'                 => $crystal_start,
            ':deuterium_start'               => $deuterium_start,
            ':metal_mine_start'              => $metal_mine_start,
            ':crystal_mine_start'            => $crystal_mine_start,
            ':deuterium_synthesizer_start'          => $deuterium_synthesizer_start,
            ':solar_plant_start'             => $solar_plant_start,
            ':fusion_plant_start'            => $fusion_plant_start,
            ':robot_factory_start'           => $robot_factory_start,
            ':nanite_factory_start'          => $nanite_factory_start,
            ':shipyard_start'                => $shipyard_start,
            ':metal_storage_start'             => $metal_storage_start,
            ':crystal_storage_start'           => $crystal_storage_start,
            ':deuterium_tank_start'         => $deuterium_tank_start,
            ':research_lab_start'              => $research_lab_start,
            ':terraformer_start'             => $terraformer_start,
            ':university_start'              => $university_start,
            ':ally_deposit_start'            => $ally_deposit_start,
            ':missile_silo_start'                    => $missile_silo_start,
            ':small_cargo_start'             => $small_cargo_start,
            ':big_cargo_start'               => $big_cargo_start,
            ':light_hunter_start'            => $light_hunter_start,
            ':heavy_hunter_start'            => $heavy_hunter_start,
            ':cruiser_start'                 => $cruiser_start,
            ':battle_ship_start'             => $battle_ship_start,
            ':colony_ship_start'             => $colony_ship_start,
            ':recycler_start'                => $recycler_start,
            ':espionage_probe_start'         => $espionage_probe_start,
            ':bomber_ship_start'             => $bomber_ship_start,
            ':solar_satellite_start'         => $solar_satellite_start,
            ':destroyer_start'               => $destroyer_start,
            ':death_star_start'              => $death_star_start,
            ':battle_cruiser_start'          => $battle_cruiser_start,
            ':ev_transporter_start'          => $ev_transporter_start,
            ':star_crasher_start'            => $star_crasher_start,
            ':giga_recycler_start'           => $giga_recycler_start,
            ':dm_ship_start'                 => $dm_ship_start,
            ':orbital_station_start'         => $orbital_station_start,
            ':misil_launcher_start'          => $misil_launcher_start,
            ':small_laser_start'             => $small_laser_start,
            ':big_laser_start'               => $big_laser_start,
            ':gauss_canyon_start'            => $gauss_canyon_start,
            ':ionic_canyon_start'            => $ionic_canyon_start,
            ':buster_canyon_start'           => $buster_canyon_start,
            ':small_protection_shield_start' => $small_protection_shield_start,
            ':planet_protector_start'        => $planet_protector_start,
            ':big_protection_shield_start'   => $big_protection_shield_start,
            ':graviton_canyon_start'         => $graviton_canyon_start,
            ':interceptor_misil_start'       => $interceptor_misil_start,
            ':interplanetary_misil_start'    => $interplanetary_misil_start,
        ]);

        $config_after = [
            'metal_start'                   => $metal_start,
            'crystal_start'                 => $crystal_start,
            'deuterium_start'               => $deuterium_start,
            'metal_mine_start'              => $metal_mine_start,
            'crystal_mine_start'            => $crystal_mine_start,
            'deuterium_synthesizer_start'          => $deuterium_synthesizer_start,
            'solar_plant_start'             => $solar_plant_start,
            'fusion_plant_start'            => $fusion_plant_start,
            'robot_factory_start'           => $robot_factory_start,
            'nanite_factory_start'          => $nanite_factory_start,
            'shipyard_start'                => $shipyard_start,
            'metal_storage_start'             => $metal_storage_start,
            'crystal_storage_start'           => $crystal_storage_start,
            'deuterium_tank_start'         => $deuterium_tank_start,
            'research_lab_start'              => $research_lab_start,
            'terraformer_start'             => $terraformer_start,
            'university_start'              => $university_start,
            'ally_deposit_start'            => $ally_deposit_start,
            'missile_silo_start'                    => $missile_silo_start,
            'small_cargo_start'             => $small_cargo_start,
            'big_cargo_start'               => $big_cargo_start,
            'light_hunter_start'            => $light_hunter_start,
            'heavy_hunter_start'            => $heavy_hunter_start,
            'cruiser_start'                 => $cruiser_start,
            'battle_ship_start'             => $battle_ship_start,
            'colony_ship_start'             => $colony_ship_start,
            'recycler_start'                => $recycler_start,
            'espionage_probe_start'         => $espionage_probe_start,
            'bomber_ship_start'             => $bomber_ship_start,
            'solar_satellite_start'         => $solar_satellite_start,
            'destroyer_start'               => $destroyer_start,
            'death_star_start'              => $death_star_start,
            'ev_transporter_start'          => $ev_transporter_start,
            'star_crasher_start'            => $star_crasher_start,
            'dm_ship_start'                 => $dm_ship_start,
            'orbital_station_start'         => $orbital_station_start,
            'misil_launcher_start'          => $misil_launcher_start,
            'small_laser_start'             => $small_laser_start,
            'big_laser_start'               => $big_laser_start,
            'gauss_canyon_start'            => $gauss_canyon_start,
            'ionic_canyon_start'            => $ionic_canyon_start,
            'buster_canyon_start'           => $buster_canyon_start,
            'small_protection_shield_start' => $small_protection_shield_start,
            'planet_protector_start'        => $planet_protector_start,
            'big_protection_shield_start'   => $big_protection_shield_start,
            'graviton_canyon_start'         => $graviton_canyon_start,
            'interceptor_misil_start'       => $interceptor_misil_start,
            'interplanetary_misil_start'    => $interplanetary_misil_start,
        ];

        $log = new Log(3);
        $log->target = 1;
        $log->old = $config_before;
        $log->new = $config_after;
        $log->save();

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=colonySettings&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirect_button);
    }

}
