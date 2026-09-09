{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}
	<form action="game.php?page=fleetStep2" method="post" onsubmit="return CheckTarget()" id="form">
		<input type="hidden" name="token" value="{$token}">
		<input type="hidden" name="fleet_group" value="0">
		<input type="hidden" name="target_mission" value="{$mission}">
		<table class="table_game table_full">
			<tr style="height:20px;">
				<th colspan="2">{$LNG.fl_send_fleet}</th>
			</tr>
			<tr style="height:20px;">
				<td style="width:50%">{$LNG.fl_destiny}</td>
				<td>
					<input style="width:32px;" type="text" id="galaxy" name="galaxy" size="3" maxlength="2"
						onkeyup="updateVars()" value="{$galaxy}">
					<input style="width:32px;" type="text" id="system" name="system" size="3" maxlength="3"
						onkeyup="updateVars()" value="{$system}">
					<input style="width:32px;" type="text" id="planet" name="planet" size="3" maxlength="2"
						onkeyup="updateVars()" value="{$planet}">
					<select class="text-yellow" id="type" name="type" onchange="updateVars()">
						{html_options options=$type_select selected=$type}
					</select>
				</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_fleet_speed}</td>
				<td>
					<select class="text-yellow" id="speed" name="speed" onChange="updateVars(false)">
						{html_options options=$speed_select}
					</select> %
				</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_distance}</td>
				<td id="distance">-</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_flying_time}</th>
				<td id="duration">-</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_flying_arrival}</th>
				<td id="arrival">-</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_flying_return}</th>
				<td id="return">-</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_fuel_consumption}</td>
				<td id="consumption">-</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_max_speed}</td>
				<td id="maxspeed">-</td>
			</tr>
			<tr style="height:20px;">
				<td>{$LNG.fl_cargo_capacity}</td>
				<td id="storage">-</td>
			</tr>
		</table>

		<table class="table_game table_full">
			<tr>
				<th class="text_center" colspan="2">{$LNG.fl_system_places}</th>
			</tr>
			<tr>
				<td class="text_center">
					<a
						href="javascript:setTarget({$galaxy},{$system},16,1);updateVars();">{$LNG.type_mission_15}[{$galaxy}:{$system}:16]</a>
				</td>
				<td class="text_center">
					<a
						href="javascript:setTarget({$galaxy},{$system},17,1);updateVars();">{$LNG.type_mission_16}[{$galaxy}:{$system}:17]</a>
				</td>
			</tr>
		</table>

		{if isModuleAvailable($smarty.const.MODULE_SHORTCUTS)}
			<table id="shortcut_list" class="table_game table_full">
				<tr>
					<th colspan="4">{$LNG.fl_shortcut}</th>
				</tr>
				{foreach $shortcut_list as $id => $c_row}
				{if $c_row@iteration % 4 == 1}
        			<tr>
    			{/if}
					<td id="sc_{$id}" style="width: 25%;">
					<div class="sc_wrapper">
						<a href="javascript:setTarget({$c_row.galaxy},{$c_row.system},{$c_row.planet},{$c_row.type});updateVars();">
								{$c_row.name}
							{if $c_row.type == 1}
								{$LNG.fl_planet_shortcut}
							{elseif $c_row.type == 2}
								{$LNG.fl_debris_shortcut}
							{elseif $c_row.type == 3}
								{$LNG.fl_moon_shortcut}
							{/if}
							&nbsp;[{$c_row.galaxy}:{$c_row.system}:{$c_row.planet}]
						</a>
						<button class="sc_delete" type="button" onclick="deleteShortCut({$id})"></button>
						</div>	
					</td>
				{if $c_row@iteration % 4 == 0}
        			</tr>
    			{/if}
				{foreachelse}
				<tr>
					<td class="text_center">{$LNG.fl_no_shortcuts}</td>
				</tr>
				{/foreach}
			</table>
			<table class="table_game table_full">
			<tr>
				<th colspan="2">{$LNG.fl_shortcut_save}</th>
				</tr>
				<tr>
					<td class="text_center" style="width: 80%;">
						<div style="display:flex;justify-content:space-around">
							<input id="sc_name" type="text" class="" name="sc_name" placeholder="{$LNG.fl_shortcut_name}">
							<i class="galaxy_icon">
								<span class="tooltip tooltip_top">{$LNG.gl_galaxy}</span>
							</i>
							<input id="sc_galaxy" type="text" class="" name="sc_galaxy" value="" size="3" maxlength="2"
								placeholder="G" pattern="[0-9]*">
							<i class="system_icon">
								<span class="tooltip tooltip_top">{$LNG.gl_solar_system}</span>
							</i>
							<input id="sc_system" type="text"
							name="sc_system" value="" size="3" maxlength="3" placeholder="S"
							pattern="[0-9]*">
							<input id="sc_planet" type="text" name="sc_planet" value=""
							size="3" maxlength="2" placeholder="P" pattern="[0-9]*">
							<select id="sc_type" class="" name="sc_type">
								{html_options options=$type_select}
							</select>
						</div>
					</td>
					<td class="text_center" style="width: 20%;">
						<button type="button" onclick="AddShortCut();">{$LNG.fl_shortcut_add}</button>
					</td>
				</tr>
			</table>
		{/if}
		<table class="table_game table_full">
			<tr>
				<th colspan="4" class="text_center">{$LNG.fl_my_planets}</th>
			</tr>
			{foreach $colony_list as $c_row}
			{if $c_row@iteration % 4 == 1}
			<tr>
			{/if}
				<td class="text_center">
					<a href="javascript:setTarget({$c_row.galaxy},{$c_row.system},{$c_row.planet},{$c_row.type});updateVars();">{$c_row.name}{if $c_row.type == 3}{$LNG.fl_moon_shortcut}{/if}
						[{$c_row.galaxy}:{$c_row.system}:{$c_row.planet}]
					</a>
				</td>
			{if $c_row@iteration % 4 == 0 || $c_row@last}
			</tr>
			{/if}
			{foreachelse}
			<tr>
				<td class="text_center">{$LNG.fl_no_colony}</td>
			</tr>
			{/foreach}
		</table>
		{if $acs_list}
			<table class="table_game table_full">
				<tr style="height:20px;">
					<th>{$LNG.fl_acs_title}</th>
				</tr>
				{foreach $acs_list as $ACSRow}
					<tr style="height:20px;">
						<td><a
								href="javascript:setACSTarget({$ACSRow.galaxy},{$ACSRow.system},{$ACSRow.planet},{$ACSRow.planet_type},{$ACSRow.id});">{$ACSRow.name}
								- [{$ACSRow.galaxy}:{$ACSRow.system}:{$ACSRow.planet}]</a></td>
					</tr>
						<td>&nbsp;</td>
					</tr>
				{/foreach}
			</table>
		{/if}
		<table class="table_game table_full">
			<tr>
				<td class="text_center">
					<input class="button-upgrade" type="submit" value="{$LNG.fl_continue}">
				</td>
			</tr>
		</table>
	</form>
	<script type="text/javascript">
		data			= {$fleet_data|json};
		shortCutRows	= 0;
		fl_no_shortcuts	= '{$LNG.fl_no_shortcuts}';
	</script>

	<script type="text/javascript">
		$(document).ready(function() {
			updateVars();
		});
	</script>


{/block}