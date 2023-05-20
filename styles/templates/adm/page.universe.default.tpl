{block name="content"}
<form action="?page=universe&mode=saveSettings" method="post">
<input type="hidden" name="opt_save" value="1">
<table class="table caption-top table-dark table-striped fs-12 my-5 mx-auto w-50">
<caption class="text-center fs-14 fw-bold text-white bg-dark">{$LNG.se_server_parameters}</caption>
<thead></thead>
<tbody class="align-middle">
	<tr>
		<td>{$LNG.se_uni_name}</td>
		<td>
			<input class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="uni_name" value="{$uni_name}" type="text">
		</td>
	</tr>
	<tr>
		<td>{$LNG.se_lang}</td>
		<td>
			<select class="form-select py-1 bg-dark text-white my-1 border border-secondary" name="lang">
				{foreach $Selector.langs as $clangshort => $clang}
				<option class="py-1 bg-dark text-white my-1" value="{$clangshort}" {if $clangshort == $lang}selected{/if}>{$clang}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>{$LNG.se_general_speed}</td>
		<td><input class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="game_speed" value="{$game_speed}" type="text" maxlength="5"></td>
	</tr>
	<tr>
		<td>{$LNG.se_fleet_speed}</td>
		<td><input class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="fleet_speed" value="{$fleet_speed}" type="text" maxlength="5"></td>
	</tr>
	<tr>
		<td>{$LNG.se_resources_producion_speed}</td>
		<td><input class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="resource_multiplier" value="{$resource_multiplier}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_storage_producion_speed}</td>
		<td><input name="storage_multiplier" value="{$storage_multiplier}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_halt_speed}</td>
		<td><input name="halt_speed" value="{$halt_speed}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_energy_speed}</td>
		<td><input name="energySpeed" value="{$energySpeed}" type="text"></td>
	</tr>
	<tr>
		<td>Show unlearned ships<br></td>
		<td><input name="show_unlearned_ships" {if $show_unlearned_ships} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>Show unlearned buildings<br></td>
		<td><input name="show_unlearned_buildings" {if $show_unlearned_buildings} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>Show unlearned technology<br></td>
		<td><input name="show_unlearned_technology" {if $show_unlearned_technology} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_forum_link}</td>
		<td><input name="forum_url" size="60" maxlength="254" value="{$forum_url}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_server_op_close}<br></td>
		<td><input name="closed"{if $game_disable == '1'} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_server_status_message}<br></td>
		<td><textarea name="close_reason" cols="80" rows="5">{$close_reason}</textarea></td>
	</tr>
	<tr>
		<th colspan="2">{$LNG.se_buildlist}</th>
	</tr>
	<tr>
		<td>{$LNG.se_max_elements_build}</td>
		<td><input name="max_elements_build" maxlength="3" size="3" value="{$max_elements_build}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_elements_tech}</td>
		<td><input name="max_elements_tech" maxlength="3" size="3" value="{$max_elements_tech}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_elements_ships}</td>
		<td><input name="max_elements_ships" maxlength="3" size="3" value="{$max_elements_ships}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_fleet_per_build}</td>
		<td><input name="max_fleet_per_build" maxlength="20" size="15" value="{$max_fleet_per_build}" type="text"></td>
	</tr>
	<tr>
		<th colspan="2">{$LNG.se_ref}</th><th>&nbsp;</th>
	</tr>
	<tr>
		<td>{$LNG.se_ref_active}</td>
		<td><input name="ref_active"{if $ref_active} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_ref_bonus}</td>
		<td><input name="ref_bonus" maxlength="6" size="8" value="{$ref_bonus}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_ref_minpoints}</td>
		<td><input name="ref_minpoints" maxlength="20" size="25" value="{$ref_minpoints}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_ref_max_referals}</td>
		<td><input name="ref_max_referals" maxlength="6" size="8" value="{$ref_max_referals}" type="text"></td>
	</tr>
	<tr>
		<th>{$LNG.se_server_colonisation_config}</th><th>&nbsp;</th>
	</tr>
	<tr>
		<td>{$LNG.se_planets_min}</td>
		<td><input name="min_player_planets" maxlength="11" size="11" value="{$min_player_planets}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_planets_tech}</td>
		<td><input name="planets_tech" maxlength="11" size="11" value="{$planets_tech}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_planets_officier}</td>
		<td><input name="planets_officier" maxlength="11" size="11" value="{$planets_officier}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_planets_per_tech}</td>
		<td><input name="planets_per_tech" maxlength="11" size="11" value="{$planets_per_tech}" type="text"></td>
	</tr>
	<tr>
		<th >{$LNG.se_server_planet_parameters}</th><th>&nbsp;</th>
	</tr>
	<tr>
		<td>{$LNG.se_metal_start}</td>
		<td><input name="metal_start" maxlength="11" size="11" value="{$metal_start}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_crystal_start}</td>
		<td><input name="crystal_start" maxlength="11" size="11" value="{$crystal_start}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_deuterium_start}</td>
		<td><input name="deuterium_start" maxlength="11" size="11" value="{$deuterium_start}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_darkmatter_start}</td>
		<td><input name="darkmatter_start" maxlength="11" size="11" value="{$darkmatter_start}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_initial_fields}</td>
		<td><input name="initial_fields" maxlength="10" size="10" value="{$initial_fields}" type="text"> {$LNG.se_fields} </td>
	</tr>
	<tr>
		<td>{$LNG.se_metal_production}</td>
		<td><input name="metal_basic_income" maxlength="10" size="10" value="{$metal_basic_income}" type="text"> {$LNG.se_per_hour}</td>
	</tr>
	<tr>
		<td>{$LNG.se_crystal_production}</td>
		<td><input name="crystal_basic_income" maxlength="10" size="10" value="{$crystal_basic_income}" type="text"> {$LNG.se_per_hour}</td>
	</tr>
	<tr>
		<td>{$LNG.se_deuterium_production}</td>
		<td><input name="deuterium_basic_income" maxlength="10" size="10" value="{$deuterium_basic_income}" type="text"> {$LNG.se_per_hour}</td>
	</tr>
	<tr>
		<th>{$LNG.se_several_parameters}</th><th>&nbsp;</th>
	</tr>
	<tr>
		<td>{$LNG.se_min_build_time}</td>
		<td><input name="min_build_time" maxlength="2" size="5" value="{$min_build_time}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_reg_closed}<br></td>
		<td><input name="reg_closed"{if $reg_closed} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_verfiy_mail}<br></td>
		<td><input name="user_valid"{if $user_valid} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_admin_protection}</td>
	    <td><input name="adm_attack"{if $adm_attack} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_debug_mode}</td>
		<td><input name="debug"{if $debug} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_ships_cdr}</td>
		<td><input name="Fleet_Cdr" maxlength="3" size="3" value="{$shiips}" type="text"> %</td>
	</tr>
	<tr>
		<td>{$LNG.se_def_cdr}</td>
		<td><input name="Defs_Cdr" maxlength="3" size="3" value="{$defenses}" type="text"> %</td>
	</tr>
	<tr>
		<td>{$LNG.se_max_galaxy}</td>
		<td><input name="max_galaxy" maxlength="3" size="3" value="{$max_galaxy}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_system}</td>
		<td><input name="max_system" maxlength="5" size="5" value="{$max_system}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_planets}</td>
		<td><input name="max_planets" maxlength="3" size="3" value="{$max_planets}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_planet_factor}</td>
		<td><input name="planet_factor" maxlength="3" size="3" value="{$planet_factor}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_overflow}</td>
		<td><input name="max_overflow" maxlength="3" size="3" value="{$max_overflow}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_moon_factor}</td>
		<td><input name="moon_factor" maxlength="3" size="3" value="{$moon_factor}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_moon_chance}</td>
		<td><input name="moon_chance" maxlength="3" size="3" value="{$moon_chance}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_deuterium_cost_galaxy}</td>
		<td><input name="deuterium_cost_galaxy" maxlength="11" size="11" value="{$deuterium_cost_galaxy}" type="text"> {$LNG.tech.903}</td>
	</tr>
	<tr>
		<td>{$LNG.se_darkmatter_cost_trader}</td>
		<td><input name="darkmatter_cost_trader" maxlength="11" size="11" value="{$darkmatter_cost_trader}" type="text"> {$LNG.tech.921}</td>
	</tr>
	<tr>
		<td>{$LNG.se_factor_university}</td>
		<td><input name="factor_university" maxlength="3" size="3" value="{$factor_university}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_fleets_per_acs}</td>
		<td><input name="max_fleets_per_acs" maxlength="3" size="3" value="{$max_fleets_per_acs}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_silo_factor}</td>
		<td><input name="silo_factor" maxlength="2" size="2" value="{$silo_factor}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_vmode_min_time}</td>
		<td><input name="vmode_min_time" maxlength="11" size="11" value="{$vmode_min_time}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_gate_wait_time}</td>
		<td><input name="gate_wait_time" maxlength="11" size="11" value="{$gate_wait_time}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_debris_moon}</td>
		<td><input name="debris_moon"{if $debris_moon} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_noob_protect}</td>
		<td><input name="noobprotection"{if $noobprot} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
		<td>{$LNG.se_noob_protect2}</td>
		<td><input name="noobprotectiontime" value="{$noobprot2}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_noob_protect3}</td>
		<td><input name="noobprotectionmulti" value="{$noobprot3}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_max_dm_missions}</td>
		<td><input name="max_dm_missions" maxlength="3" size="3" value="{$max_dm_missions}" type="text"></td>
	</tr>
	<tr>
		<td>{$LNG.se_alliance_create_min_points}</td>
		<td><input name="alliance_create_min_points" maxlength="20" size="25" value="{$alliance_create_min_points}" type="text"></td>
	</tr>
	<tr>
		<td>User maximum notes</td>
		<td><input name="user_max_notes" maxlength="20" size="25" value="{$user_max_notes}" type="text"></td>
	</tr>
	<tr>
		<th>{$LNG.se_trader_head}</th><th>&nbsp;</th>
	</tr>
	<tr>
	    <td>{$LNG.se_trader_ships}</td>
	    <td><input name="trade_allowed_ships" maxlength="255" size="60" value="{$trade_allowed_ships}" type="text"></td>
	</tr>
	<tr>
	    <td>{$LNG.se_trader_charge}</td>
	    <td><input name="trade_charge" maxlength="5" size="10" value="{$trade_charge}" type="text"></td>
	</tr>
	<tr>
		<th>{$LNG.se_news_head}</th><th>&nbsp;</th>
	</tr>
	<tr>
	    <td>{$LNG.se_news_active}</td>
	    <td><input name="newsframe"{if $newsframe} checked="checked"{/if} type="checkbox"></td>
	</tr>
	<tr>
	    <td>{$LNG.se_news}</td>
	    <td><textarea name="NewsText" cols="80" rows="5">{$NewsTextVal}</textarea></td>
	</tr>
	<tr>
		<td colspan="3"><input value="{$LNG.se_save_parameters}" type="submit"></td>
	</tr>
</tbody>

</table>
</form>
{/block}
