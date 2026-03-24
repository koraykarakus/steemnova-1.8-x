{block name="content"}
	<script>
		$(document).ready(function() {
			$("#searchInUniverseSettings").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#universeSettings label").filter(function() {

					if ($(this).text().toLowerCase().indexOf(value) > -1) {
						$(this).parent().removeClass('d-none');
					} else {
						$(this).parent().addClass('d-none');
					}


				});



			});
		});
	</script>

	<form id="universeSettings" class="bg-black w-75  p-3 my-3 mx-auto fs-12"
		action="?page=universe&mode=saveSettings" method="post">
		<input type="hidden" name="opt_save" value="1">

		<div class="form-gorup d-flex justify-content-between my-3">
			<input style="max-width:250px;" id="searchInUniverseSettings"
				class="form-control py-1 bg-dark  my-1 border border-secondary" type="text" name=""
				placeholder="search..">
		</div>

		<ul class="nav nav-tabs" id="" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_1" type="button">
					{$LNG.se_server_parameters}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_2" type="button">
					{$LNG.se_buildlist}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_3" type="button">
					{$LNG.se_ref}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_4" type="button">
					{$LNG.se_server_colonisation_config}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_5" type="button">
					{$LNG.se_server_planet_parameters}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_6" type="button">
					{$LNG.se_several_parameters}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_7" type="button">
					{$LNG.se_trader_head}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_8" type="button">
					{$LNG.se_news_head}
				</button>
			</li>
		</ul>

		<div class="tab-content mt-3">
			<div class="tab-pane fade show active" id="tab_1">
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="uni_name">{$LNG.se_uni_name}</label>
					<input id="uni_name" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="uni_name" value="{$uni_name}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="lang">{$LNG.se_lang}</label>
					<select id="lang" class="form-select py-1 bg-dark  my-1 border border-secondary" name="lang">
						{foreach $Selector.langs as $clangshort => $clang}
							<option class="py-1 bg-dark  my-1" value="{$clangshort}"
								{if $clangshort == $lang}selected{/if}>
								{$clang}</option>
						{/foreach}
					</select>
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="game_speed">{$LNG.se_general_speed}</label>
					<input id="game_speed" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="game_speed" value="{$game_speed}" type="text" maxlength="5">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="fleet_speed">{$LNG.se_fleet_speed}</label>
					<input id="fleet_speed" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="fleet_speed" value="{$fleet_speed}" type="text" maxlength="5">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="resource_multiplier">{$LNG.se_resources_producion_speed}</label>
					<input id="resource_multiplier"
						class="form-control py-1 bg-dark  my-1 border border-secondary" name="resource_multiplier"
						value="{$resource_multiplier}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="storage_multiplier">{$LNG.se_storage_producion_speed}</label>
					<input id="storage_multiplier" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="storage_multiplier" value="{$storage_multiplier}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="expedition_speed">{$LNG.se_expedition_speed}</label>
					<input id="expedition_speed" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="expedition_speed" value="{$expedition_speed}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="energySpeed">{$LNG.se_energy_speed}</label>
					<input id="energySpeed" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="energySpeed" value="{$energySpeed}" type="text">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="show_unlearned_ships">
						{$LNG.se_show_not_learned_ships}
					</label>
					<input class="mx-2" id="show_unlearned_ships" name="show_unlearned_ships" {if $show_unlearned_ships}
						checked="checked" {/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="show_unlearned_buildings">
						{$LNG.se_show_not_learned_buildings}
					</label>
					<input class="mx-2" id="show_unlearned_buildings" name="show_unlearned_buildings"
						{if $show_unlearned_buildings} checked="checked" {/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="show_unlearned_technology">
						{$LNG.se_show_not_learned_tech}
					</label>
					<input class="mx-2" id="show_unlearned_technology" name="show_unlearned_technology"
						{if $show_unlearned_technology} checked="checked" {/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="show_tech_no_research">
						{$LNG.se_show_tech_without_research_center}
					</label>
					<input class="mx-2" id="show_tech_no_research" name="show_tech_no_research" {if $show_tech_no_research}
						checked="checked" {/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="show_ships_no_shipyard">
						{$LNG.se_show_ships_without_shipyard}
					</label>
					<input class="mx-2" id="show_ships_no_shipyard" name="show_ships_no_shipyard"
						{if $show_ships_no_shipyard} checked="checked" {/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="forum_url">{$LNG.se_forum_link}</label>
					<input id="forum_url" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="forum_url" size="60" maxlength="254" value="{$forum_url}" type="text">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="closed">{$LNG.se_server_op_close}</label>
					<input class="mx-2" id="closed" name="closed" {if $game_disable === 1} checked="checked" {/if}
						type="checkbox">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="close_reason">{$LNG.se_server_status_message}</label>
					<textarea id="close_reason" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="close_reason" cols="80" rows="5">{$close_reason}</textarea>
				</div>
			</div>
			<div class="tab-pane fade" id="tab_2">
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_elements_build">{$LNG.se_max_elements_build}</label>
					<input id="max_elements_build" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_elements_build" maxlength="3" size="3" value="{$max_elements_build}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_elements_tech">{$LNG.se_max_elements_tech}</label>
					<input id="max_elements_tech" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_elements_tech" maxlength="3" size="3" value="{$max_elements_tech}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_elements_ships">{$LNG.se_max_elements_ships}</label>
					<input id="max_elements_ships" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_elements_ships" maxlength="3" size="3" value="{$max_elements_ships}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_fleet_per_build">{$LNG.se_max_fleet_per_build}</label>
					<input id="max_fleet_per_build"
						class="form-control py-1 bg-dark  my-1 border border-secondary" name="max_fleet_per_build"
						maxlength="20" size="15" value="{$max_fleet_per_build}" type="text">
				</div>
			</div>
			<div class="tab-pane fade" id="tab_3">
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="ref_active">{$LNG.se_ref_active}</label>
					<input class="mx-2" id="ref_active" name="ref_active" {if $ref_active} checked="checked" {/if}
						type="checkbox">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="ref_bonus">{$LNG.se_ref_bonus}</label>
					<input id="ref_bonus" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="ref_bonus" maxlength="6" size="8" value="{$ref_bonus}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="ref_minpoints">{$LNG.se_ref_minpoints}</label>
					<input id="ref_minpoints" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="ref_minpoints" maxlength="20" size="25" value="{$ref_minpoints}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="ref_max_referals">{$LNG.se_ref_max_referals}</label>
					<input id="ref_max_referals" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="ref_max_referals" maxlength="6" size="8" value="{$ref_max_referals}" type="text">
				</div>
			</div>
			<div class="tab-pane fade" id="tab_4">
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="min_player_planets">{$LNG.se_planets_min}</label>
					<input id="min_player_planets" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="min_player_planets" maxlength="11" size="11" value="{$min_player_planets}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="planets_tech">{$LNG.se_planets_tech}</label>
					<input id="planets_tech" name="planets_tech"
						class="form-control py-1 bg-dark  my-1 border border-secondary" maxlength="11" size="11"
						value="{$planets_tech}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="planets_officers">{$LNG.se_planets_officers}</label>
					<input id="planets_officers" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="planets_officers" maxlength="11" size="11" value="{$planets_officers}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="planets_per_tech">{$LNG.se_planets_per_tech}</label>
					<input id="planets_per_tech" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="planets_per_tech" maxlength="11" size="11" value="{$planets_per_tech}" type="text">
				</div>
			</div>
			<div class="tab-pane fade" id="tab_5">
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="darkmatter_start">{$LNG.se_darkmatter_start}</label>
					<input id="darkmatter_start" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="darkmatter_start" maxlength="11" size="11" value="{$darkmatter_start}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="initial_fields">{$LNG.se_initial_fields}</label>
					<input id="initial_fields" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="initial_fields" maxlength="10" size="10" value="{$initial_fields}" type="text">
					{$LNG.se_fields}
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="metal_basic_income">{$LNG.se_metal_production}</label>
					<input id="metal_basic_income" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="metal_basic_income" maxlength="10" size="10" value="{$metal_basic_income}" type="text">
					{$LNG.se_per_hour}
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="crystal_basic_income">{$LNG.se_crystal_production}</label>
					<input id="crystal_basic_income"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="crystal_basic_income" maxlength="10" size="10" value="{$crystal_basic_income}" type="text">
					{$LNG.se_per_hour}
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="deuterium_basic_income">{$LNG.se_deuterium_production}</label>
					<input id="deuterium_basic_income"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="deuterium_basic_income" maxlength="10" size="10" value="{$deuterium_basic_income}"
						type="text">
					{$LNG.se_per_hour}
				</div>
			</div>
			<div class="tab-pane fade" id="tab_6">
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="min_build_time">{$LNG.se_min_build_time}</label>
					<input id="min_build_time" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="min_build_time" maxlength="2" size="5" value="{$min_build_time}" type="text">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="reg_closed">{$LNG.se_reg_closed}</label>
					<input class="mx-2" id="reg_closed" name="reg_closed" {if $reg_closed} checked="checked" {/if}
						type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="user_valid">{$LNG.se_verfiy_mail}</label>
					<input class="mx-2" id="user_valid" name="user_valid" {if $user_valid} checked="checked" {/if}
						type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="adm_attack">{$LNG.se_admin_protection}</label>
					<input class="mx-2" id="adm_attack" name="adm_attack" {if $adm_attack} checked="checked" {/if}
						type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="debug">{$LNG.se_debug_mode}</label>
					<input class="mx-2" id="debug" name="debug" {if $debug} checked="checked" {/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="debris_percentage_fleet">{$LNG.se_ships_cdr}&nbsp;(%)</label>
					<input id="debris_percentage_fleet"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="debris_percentage_fleet" maxlength="3" size="3" value="{$shiips}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="debris_percentage_defense">{$LNG.se_def_cdr}&nbsp;(%)</label>
					<input id="debris_percentage_defense"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="debris_percentage_defense" maxlength="3" size="3" value="{$defenses}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_galaxy">{$LNG.se_max_galaxy}</label>
					<input id="max_galaxy" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_galaxy" maxlength="3" size="3" value="{$max_galaxy}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_system">{$LNG.se_max_system}</label>
					<input id="max_system" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_system" maxlength="5" size="5" value="{$max_system}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_planets">{$LNG.se_max_planets}</label>
					<input id="max_planets" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_planets" maxlength="3" size="3" value="{$max_planets}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="planet_factor">{$LNG.se_planet_factor}</label>
					<input id="planet_factor" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="planet_factor" maxlength="3" size="3" value="{$planet_factor}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_overflow">{$LNG.se_max_overflow}</label>
					<input id="max_overflow" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_overflow" maxlength="3" size="3" value="{$max_overflow}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="moon_factor">{$LNG.se_moon_factor}</label>
					<input id="moon_factor" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="moon_factor" maxlength="3" size="3" value="{$moon_factor}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="moon_chance">{$LNG.se_moon_chance}</label>
					<input id="moon_chance" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="moon_chance" maxlength="3" size="3" value="{$moon_chance}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="deuterium_cost_galaxy">{$LNG.se_deuterium_cost_galaxy}</label>
					<input id="deuterium_cost_galaxy"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="deuterium_cost_galaxy" maxlength="11" size="11" value="{$deuterium_cost_galaxy}" type="text">
					{$LNG.tech.903}
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="darkmatter_cost_trader">{$LNG.se_darkmatter_cost_trader}</label>
					<input id="darkmatter_cost_trader"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="darkmatter_cost_trader" maxlength="11" size="11" value="{$darkmatter_cost_trader}"
						type="text">
					{$LNG.tech.921}
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="factor_university">{$LNG.se_factor_university}</label>
					<input id="factor_university" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="factor_university" maxlength="3" size="3" value="{$factor_university}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_fleets_per_acs">{$LNG.se_max_fleets_per_acs}</label>
					<input id="max_fleets_per_acs" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_fleets_per_acs" maxlength="3" size="3" value="{$max_fleets_per_acs}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="silo_factor">{$LNG.se_silo_factor}</label>
					<input id="silo_factor" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="silo_factor" maxlength="2" size="2" value="{$silo_factor}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="vmode_min_time">{$LNG.se_vmode_min_time}</label>
					<input id="vmode_min_time" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="vmode_min_time" maxlength="11" size="11" value="{$vmode_min_time}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="gate_wait_time">{$LNG.se_gate_wait_time}</label>
					<input id="gate_wait_time" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="gate_wait_time" maxlength="11" size="11" value="{$gate_wait_time}" type="text">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="debris_moon">{$LNG.se_debris_moon}</label>
					<input class="mx-2" id="debris_moon" name="debris_moon" {if $debris_moon} checked="checked" {/if}
						type="checkbox">
				</div>
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="noob_protection">{$LNG.se_noob_protect}</label>
					<input class="mx-2" id="noob_protection" name="noob_protection" {if $noob_protection} checked="checked"
						{/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="noob_protection_max_points">{$LNG.se_noob_protect2}</label>
					<input id="noob_protection_max_points"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="noob_protection_max_points" value="{$noob_protection_max_points}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="noob_protection_multiplier">{$LNG.se_noob_protect3}</label>
					<input id="noob_protection_multiplier"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="noob_protection_multiplier" value="{$noob_protection_multiplier}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="max_dm_missions">{$LNG.se_max_dm_missions}</label>
					<input id="max_dm_missions" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="max_dm_missions" maxlength="3" size="3" value="{$max_dm_missions}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="alliance_create_min_points">{$LNG.se_alliance_create_min_points}</label>
					<input id="alliance_create_min_points"
						class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="alliance_create_min_points" maxlength="20" size="25" value="{$alliance_create_min_points}"
						type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline" for="user_max_notes">User maximum
						notes</label>
					<input id="user_max_notes" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="user_max_notes" maxlength="20" size="25" value="{$user_max_notes}" type="text">
				</div>
			</div>
			<div class="tab-pane fade" id="tab_7">
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="trade_allowed_ships">{$LNG.se_trader_ships}</label>
					<input id="trade_allowed_ships"
						class="form-control py-1 bg-dark  my-1 border border-secondary" name="trade_allowed_ships"
						maxlength="255" size="60" value="{$trade_allowed_ships}" type="text">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="trade_charge">{$LNG.se_trader_charge}</label>
					<input id="trade_charge" class="form-control py-1 bg-dark  my-1 border border-secondary"
						name="trade_charge" maxlength="5" size="10" value="{$trade_charge}" type="text">
				</div>
			</div>
			<div class="tab-pane fade" id="tab_8">
				<div class="form-gorup d-flex my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="display_announcement_frame">{$LNG.se_news_active}</label>
					<input class="mx-2" id="display_announcement_frame" name="display_announcement_frame"
						{if $display_announcement_frame} checked="checked" {/if} type="checkbox">
				</div>
				<div class="form-gorup d-flex flex-column my-1 p-2 ">
					<label class="text-start my-1 cursor-pointer hover-underline"
						for="announcement_text">{$LNG.se_news}</label>
					<textarea id="announcement_text"
						class="form-control py-1 bg-dark  my-1 border border-secondary" name="announcement_text"
						cols="80" rows="5">{$announcement_text}</textarea>
				</div>
			</div>
		</div>
















		<div class="form-gorup d-flex flex-column my-1 p-2 ">
			<input class="btn btn-primary " value="{$LNG.se_save_parameters}" type="submit">
		</div>
	</form>
{/block}