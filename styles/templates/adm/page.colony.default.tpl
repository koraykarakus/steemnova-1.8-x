{block name="content"}

  <form id="colonySettings" class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12"
    action="?page=colonySettings&mode=saveSettings" method="post">
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="metal_start">{$LNG.cs_metal_start}</label>
      <input id="metal_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="metal_start"
        value="{$metal_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="crystal_start">{$LNG.cs_crystal_start}</label>
      <input id="crystal_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="crystal_start" value="{$crystal_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="deuterium_start">{$LNG.cs_deuterium_start}</label>
      <input id="deuterium_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="deuterium_start" value="{$deuterium_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="metal_mine_start">{$LNG.cs_metal_mine_start}</label>
      <input id="metal_mine_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="metal_mine_start" value="{$metal_mine_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="crystal_mine_start">{$LNG.cs_crystal_mine_start}</label>
      <input id="crystal_mine_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="crystal_mine_start" value="{$crystal_mine_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="deuterium_synthesizer_start">{$LNG.cs_deuterium_synthesizer_start}</label>
      <input id="deuterium_synthesizer_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="deuterium_synthesizer_start" value="{$deuterium_synthesizer_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="solar_plant_start">{$LNG.cs_solar_plant_start}</label>
      <input id="solar_plant_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="solar_plant_start" value="{$solar_plant_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="fusion_plant_start">{$LNG.cs_fusion_plant_start}</label>
      <input id="fusion_plant_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="fusion_plant_start" value="{$fusion_plant_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="robot_factory_start">{$LNG.cs_robot_factory_start}</label>
      <input id="robot_factory_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="robot_factory_start" value="{$robot_factory_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="nanite_factory_start">{$LNG.cs_nanite_factory_start}</label>
      <input id="nanite_factory_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="nanite_factory_start" value="{$nanite_factory_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="shipyard_start">{$LNG.cs_shipyard_start}</label>
      <input id="shipyard_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="shipyard_start" value="{$shipyard_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="metal_storage_start">{$LNG.cs_metal_storage_start}</label>
      <input id="metal_storage_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="metal_storage_start" value="{$metal_storage_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="crystal_storage_start">{$LNG.cs_crystal_storage_start}</label>
      <input id="crystal_storage_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="crystal_storage_start" value="{$crystal_storage_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="deuterium_tank_start">{$LNG.cs_deuterium_tank_start}</label>
      <input id="deuterium_tank_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="deuterium_tank_start" value="{$deuterium_tank_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="research_lab_start">{$LNG.cs_research_lab_start}</label>
      <input id="research_lab_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="research_lab_start" value="{$research_lab_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="terraformer_start">{$LNG.cs_terraformer_start}</label>
      <input id="terraformer_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="terraformer_start" value="{$terraformer_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="university_start">{$LNG.cs_university_start}</label>
      <input id="university_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="university_start" value="{$university_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="ally_deposit_start">{$LNG.cs_ally_deposit_start}</label>
      <input id="ally_deposit_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="ally_deposit_start" value="{$ally_deposit_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="missile_silo_start">{$LNG.cs_missile_silo_start}</label>
      <input id="missile_silo_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="silo_start" value="{$missile_silo_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="small_cargo_start">{$LNG.cs_small_cargo_start}</label>
      <input id="small_cargo_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="small_cargo_start" value="{$small_cargo_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="big_cargo_start">{$LNG.cs_big_cargo_start}</label>
      <input id="big_cargo_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="big_cargo_start" value="{$big_cargo_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="light_hunter_start">{$LNG.cs_light_hunter_start}</label>
      <input id="light_hunter_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="light_hunter_start" value="{$light_hunter_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="heavy_hunter_start">{$LNG.cs_heavy_hunter_start}</label>
      <input id="heavy_hunter_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="heavy_hunter_start" value="{$heavy_hunter_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="cruiser_start">{$LNG.cs_cruiser_start}</label>
      <input id="cruiser_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="cruiser_start" value="{$cruiser_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="battle_ship_start">{$LNG.cs_battle_ship_start}</label>
      <input id="battle_ship_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="battle_ship_start" value="{$battle_ship_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="colony_ship_start">{$LNG.cs_colony_ship_start}</label>
      <input id="colony_ship_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="colony_ship_start" value="{$colony_ship_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="recycler_start">{$LNG.cs_recycler_start}</label>
      <input id="recycler_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="recycler_start" value="{$recycler_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="espionage_probe_start">{$LNG.cs_espionage_probe_start}</label>
      <input id="espionage_probe_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="espionage_probe_start" value="{$espionage_probe_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="bomber_ship_start">{$LNG.cs_bomber_ship_start}</label>
      <input id="bomber_ship_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="bomber_ship_start" value="{$bomber_ship_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="solar_satellite_start">{$LNG.cs_solar_satellite_start}</label>
      <input id="solar_satellite_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="solar_satellite_start" value="{$solar_satellite_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="destroyer_start">{$LNG.cs_destroyer_start}</label>
      <input id="destroyer_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="destroyer_start" value="{$destroyer_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="death_star_start">{$LNG.cs_death_star_start}</label>
      <input id="death_star_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="death_star_start" value="{$death_star_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="ev_transporter_start">{$LNG.cs_ev_transporter_start}</label>
      <input id="ev_transporter_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="ev_transporter_start" value="{$ev_transporter_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="star_crasher_start">{$LNG.cs_star_crasher_start}</label>
      <input id="star_crasher_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="star_crasher_start" value="{$star_crasher_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="dm_ship_start">{$LNG.cs_dm_ship_start}</label>
      <input id="dm_ship_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="dm_ship_start" value="{$dm_ship_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="orbital_station_start">{$LNG.cs_orbital_station_start}</label>
      <input id="orbital_station_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="orbital_station_start" value="{$orbital_station_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="misil_launcher_start">{$LNG.cs_misil_launcher_start}</label>
      <input id="misil_launcher_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="misil_launcher_start" value="{$misil_launcher_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="small_laser_start">{$LNG.cs_small_laser_start}</label>
      <input id="small_laser_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="small_laser_start" value="{$small_laser_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="big_laser_start">{$LNG.cs_big_laser_start}</label>
      <input id="big_laser_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="big_laser_start" value="{$big_laser_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="gauss_canyon_start">{$LNG.cs_gauss_canyon_start}</label>
      <input id="gauss_canyon_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="gauss_canyon_start" value="{$gauss_canyon_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="ionic_canyon_start">{$LNG.cs_ionic_canyon_start}</label>
      <input id="ionic_canyon_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="ionic_canyon_start" value="{$ionic_canyon_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="buster_canyon_start">{$LNG.cs_buster_canyon_start}</label>
      <input id="buster_canyon_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="buster_canyon_start" value="{$buster_canyon_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="small_protection_shield_start">{$LNG.cs_small_protection_shield_start}</label>
      <input id="small_protection_shield_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="small_protection_shield_start" value="{$small_protection_shield_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="planet_protector_start">{$LNG.cs_planet_protector_start}</label>
      <input id="planet_protector_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="planet_protector_start" value="{$planet_protector_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="big_protection_shield_start">{$LNG.cs_big_protection_shield_start}</label>
      <input id="big_protection_shield_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="big_protection_shield_start" value="{$big_protection_shield_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="graviton_canyon_start">{$LNG.cs_graviton_canyon_start}</label>
      <input id="graviton_canyon_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="graviton_canyon_start" value="{$graviton_canyon_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="interceptor_misil_start">{$LNG.cs_interceptor_misil_start}</label>
      <input id="interceptor_misil_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="interceptor_misil_start" value="{$interceptor_misil_start}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="interplanetary_misil_start">{$LNG.cs_interplanetary_misil_start}</label>
      <input id="interplanetary_misil_start" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
        name="interplanetary_misil_start" value="{$interplanetary_misil_start}" type="text" maxlength="5">
    </div>

    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <input class="btn btn-primary text-white" value="{$LNG.se_save_parameters}" type="submit">
    </div>
  </form>
{/block}