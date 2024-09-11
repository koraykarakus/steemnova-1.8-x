{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}

<table class="table table-sm fs-12 table-gow">
	<thead>
		<tr>
			<th colspan="9" class="border border-secondary">
				<div class="transparent" style="text-align:left;float:left;">{$LNG.fl_fleets}: ({$activeFleetSlots} / {$maxFleetSlots})</div>
				<div class="transparent" style="text-align:right;float:right;">{$LNG.fl_expeditions}: ({$activeExpedition} / {$maxExpedition}) </div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{$LNG.fl_number}</td>
			<td>{$LNG.fl_mission}</td>
			<td>{$LNG.fl_ammount}</td>
			<td>{$LNG.fl_beginning}</td>
			<td>{$LNG.fl_departure}</td>
			<td>{$LNG.fl_destiny}</td>
			<td>{$LNG.fl_arrival}</td>
			<td>{$LNG.fl_objective}</td>
			<td>{$LNG.fl_order}</td>
		</tr>
		{foreach name=FlyingFleets item=FlyingFleetRow from=$FlyingFleetList}
		<tr>
		<td style="vertical-align:middle;">{$smarty.foreach.FlyingFleets.iteration}</td>
		<td style="vertical-align:middle;">
			<a data-bs-toggle="tooltip"
			data-bs-placement="top"
			data-bs-html="true" title="
			<table class='fs-12'>
				<thead></thead>
				<tbody>
					<tr>
						<td style='width:50%;color:white'>{$LNG['tech'][901]}</td>
						<td style='width:50%;color:white'>{$FlyingFleetRow.metal}</td>
					</tr>
					<tr>
						<td style='width:50%;color:white'>{$LNG['tech'][902]}</td><td style='width:50%;color:white'>{$FlyingFleetRow.crystal}</td>
					</tr>
					<tr>
						<td style='width:50%;color:white'>{$LNG['tech'][903]}</td><td style='width:50%;color:white'>{$FlyingFleetRow.deuterium}</td>
					</tr>
					<tr>
						<td style='width:50%;color:white'>{$LNG['tech'][921]}</td><td style='width:50%;color:white'>{$FlyingFleetRow.dm}</td>
					</tr>
				</tbody>
			</table>">
				{$LNG["type_mission_{$FlyingFleetRow.mission}"]}
			</a>
		{if $FlyingFleetRow.state == 1}
			<br><a title="{$LNG.fl_returning}">{$LNG.fl_r}</a>
		{else}
			<br><a title="{$LNG.fl_onway}">{$LNG.fl_a}</a>
		{/if}
		</td>
		<td style="vertical-align:middle;">
			<a data-bs-toggle="tooltip"
			data-bs-placement="top"
			data-bs-html="true"
			title="<table><tr><th colspan='2' style='text-align:center;'>{$LNG.fl_info_detail}</th></tr>{foreach $FlyingFleetRow.FleetList as $shipID => $shipCount}<tr><td class='transparent'>{$LNG.tech.{$shipID}}:</td><td class='transparent'>{$shipCount}</td></tr>{/foreach}</table>">
			{$FlyingFleetRow.amount}
			</a>
		</td>
		<td style="vertical-align:middle;">
			<a href="game.php?page=galaxy&amp;galaxy={$FlyingFleetRow.startGalaxy}&amp;system={$FlyingFleetRow.startSystem}">[{$FlyingFleetRow.startGalaxy}:{$FlyingFleetRow.startSystem}:{$FlyingFleetRow.startPlanet}]</a>
		</td>
		<td style="vertical-align:middle;" {if $FlyingFleetRow.state == 0}style="color:lime"{/if}>{$FlyingFleetRow.startTime}</td>
		<td style="vertical-align:middle;"><a href="game.php?page=galaxy&amp;galaxy={$FlyingFleetRow.endGalaxy}&amp;system={$FlyingFleetRow.endSystem}">[{$FlyingFleetRow.endGalaxy}:{$FlyingFleetRow.endSystem}:{$FlyingFleetRow.endPlanet}]</a></td>
		{if $FlyingFleetRow.mission == 4 && $FlyingFleetRow.state == 0}
		<td style="vertical-align:middle;">-</td>
		{else}
		<td style="vertical-align:middle;" {if $FlyingFleetRow.state != 0}style="color:lime"{/if}>{$FlyingFleetRow.endTime}</td>
		{/if}
		<td style="vertical-align:middle;" id="fleettime_{$smarty.foreach.FlyingFleets.iteration}" class="fleets" data-fleet-end-time="{$FlyingFleetRow.returntime}" data-fleet-time="{$FlyingFleetRow.resttime}">{pretty_fly_time({$FlyingFleetRow.resttime})}</td>
		<td style="vertical-align:middle;">
		{if !$isVacation && $FlyingFleetRow.state != 1 && $FlyingFleetRow.no_returnable != 1}
			<form action="game.php?page=fleetTable&amp;action=sendfleetback" method="post">
			<input name="fleetID" value="{$FlyingFleetRow.id}" type="hidden">
			<input class="btn bg-black px-1 py-1 fs-12 text-white" value="{$LNG.fl_send_back}" type="submit">
			</form>
			{if $FlyingFleetRow.mission == 1}
			<form action="game.php?page=fleetTable&amp;action=acs" method="post">
			<input name="fleetID" value="{$FlyingFleetRow.id}" type="hidden">
			<input value="{$LNG.fl_acs}" type="submit">
			</form>
			{/if}
		{else}
		&nbsp;-&nbsp;
		{/if}
		</td>
		</tr>
		{foreachelse}
		<tr>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
		</tr>
		{/foreach}
		{if $maxFleetSlots == $activeFleetSlots}
		<tr><td colspan="9">{$LNG.fl_no_more_slots}</td></tr>
		{/if}
	</tbody>
</table>

{if  isModuleAvailable($smarty.const.MODULE_AUTOEXPEDITION) && empty($targetMission)}
<form action="?page=AutoExpedition" method="post">
<table class="table table-sm fs-12 table-gow my-2">
	<thead>
		<th class="text-center border border-secondary" colspan="3">{$LNG.ae_autoexp}</th>
	</thead>
	<tbody>
			<tr>
				<td class="text-center">{$LNG.ae_galaxy}</td>
				<td class="text-center">{$LNG.ae_system}</td>
				<td class="text-center">{$LNG.ae_planet}</td>
			</tr>
			<tr>
				<td>
					<input class="form-control bg-dark text-white text-center p-0 my-0 mx-auto w-50 fs-12" name="expedition_galaxy" value="{$galaxy}">
				</td>
				<td>
					<input class="form-control bg-dark text-white text-center p-0 my-0 mx-auto w-50 fs-12" name="expedition_system" value="{$system}">
				</td>
				<td>
					<input class="form-control bg-dark text-white text-center p-0 my-0 mx-auto w-50 fs-12" name="expedition_planet" value="16">
				</td>
			</tr>
			<tr>
				<td class="text-center" colspan="2">
					<span>{$LNG.fl_hold_time}</span>
					<select class="" name="">
					{foreach $StaySelector as $cKey => $cSelector}
					<option value="{$cKey}">{$cSelector}</option>
					{/foreach}
					</select>
					<span>{$LNG.fl_hours}</span>
				</td>
				<td class="text-center" colspan="1">
					<span onclick="return Dialog.fleetDivideSettings();" class="settingsoverview">{$LNG.ae_settings}</span>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<div class="g-recaptcha mx-auto d-flex justify-content-center" data-theme="dark" data-sitekey="{$recaptchaPublicKey}"></div>
				</td>
			</tr>
			<tr>
				<td class="text-center" colspan="3">
					<button class="btn btn-dark py-0 px-1 border border-secondary fs-12 text-yellow" type="submit">{$LNG.ae_send}</button>
				</td>
			</tr>
	</tbody>
</table>
</form>
{/if}


{if !empty($acsData)}
{include file="shared.fleetTable.acsTable.tpl"}
{/if}
<form action="?page=fleetStep1" method="post">
<input type="hidden" name="galaxy" value="{$targetGalaxy}">
<input type="hidden" name="system" value="{$targetSystem}">
<input type="hidden" name="planet" value="{$targetPlanet}">
<input type="hidden" name="type" value="{$targetType}">
<input type="hidden" name="target_mission" value="{$targetMission}">
<table class="table table-sm bg-black fs-12 table-gow my-1">
	<thead>
		<tr>
			<th class="text-center  border border-secondary" colspan="5">{$LNG.fl_new_mission_title}</th>
		</tr>
	</thead>
	<tbody>
		<tr style="height:20px;">
			<td>{$LNG.fl_ship_type}</td>
			<td>{$LNG.fl_ship_available}</td>
			<td>-</td>
			<td>-</td>
		</tr>
		{foreach $FleetsOnPlanet as $FleetRow}
		<tr style="height:20px;">
			<td class="align-middle">
				{if $FleetRow.speed != 0}
				 <a class="hover-underline hover-pointer" data-bs-toggle="tooltip"
				 data-bs-placement="left"
				 data-bs-html="true" title='
				 <table class="table-tooltip">
					 <thead>
						 <tr>
							 <td>{$LNG.fl_speed_title}</td>
						 </tr>
					 </thead>
					 <tbody>
						 <tr>
							 <td>{$FleetRow.speed}</td>
						 </tr>
					 </tbody>
				 </table>'>{$LNG.tech.{$FleetRow.id}}</a>
				 {else}
				 {$LNG.tech.{$FleetRow.id}}
				 {/if}
			 </td>
			<td class="align-middle" id="ship{$FleetRow.id}_value">{$FleetRow.count}</td>
			{if $FleetRow.speed != 0}
			<td class="align-middle text-center">
				<button type="button" class="btn btn-dark py-0 px-1 border border-secondary fs-12 text-yellow" onclick="maxShip('ship{$FleetRow.id}');">{$LNG.fl_max}</button>
			</td>
			<td class="align-middle text-center">
				<button type="button" class="btn btn-dark py-0 px-1 border border-secondary fs-12 text-yellow" onclick="minShip('ship{$FleetRow.id}');">{$LNG.fl_min}</button>
			</td>
			<td>
				<input class="form-control bg-dark text-white text-center p-0 my-0 mx-auto w-50 fs-12" name="ship{$FleetRow.id}" id="ship{$FleetRow.id}_input" size="10" value="0">
			</td>
			{else}

			{/if}
		</tr>
		{/foreach}
		<tr style="height:20px;">
		{if count($FleetsOnPlanet) == 0}
		<td colspan="4">{$LNG.fl_no_ships}</td>
		{else}
		<td class="text-center" colspan="2">
			<button type="button" class="btn btn-dark py-0 px-1 border border-secondary fs-12 text-yellow" onclick="noShips();">{$LNG.fl_remove_all_ships}</a>
		</td>
		<td class="text-center" colspan="3">
			<button type="button" class="btn btn-dark py-0 px-1 border border-secondary fs-12 text-yellow"  onclick="maxShips();">{$LNG.fl_select_all_ships}</a>
		</td>
		{/if}
		</tr>
		{if $maxFleetSlots != $activeFleetSlots}
		<tr style="height:20px;">
		<td class="text-center" colspan="5">
			<input class="button-upgrade" type="submit" value="{$LNG.fl_continue}">
		</td>
		{/if}
	</tbody>
</table>
</form>
<br>
<table class="table table-sm bg-black fs-12 table-gow">
	<thead>
		<tr>
			<th class="text-center border border-secondary" colspan="3">{$LNG.fl_bonus}</th>
		</tr>
		<tr>
			<th style="width:33%;" class="text-center border border-secondary">{$LNG.fl_bonus_attack}</th>
			<th style="width:33%;" class="text-center border border-secondary">{$LNG.fl_bonus_defensive}</th>
			<th style="width:33%;" class="text-center border border-secondary">{$LNG.fl_bonus_shield}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="text-center  border border-secondary">+{$bonusAttack} %</td>
			<td class="text-center border border-secondary">+{$bonusDefensive} %</td>
			<td class="text-center border border-secondary">+{$bonusShield} %</td>
		</tr>
		<tr>
			<th class="text-center border border-secondary">{$LNG.tech.115}</th>
			<th class="text-center border border-secondary">{$LNG.tech.117}</th>
			<th class="text-center border border-secondary">{$LNG.tech.118}</th>
		</tr>
		<tr>
			<td class="text-center">+{$bonusCombustion} %</td>
			<td class="text-center">+{$bonusImpulse} %</td>
			<td class="text-center">+{$bonusHyperspace} %</td>
		</tr>
	</tbody>
</table>

{block name="script" append}
<script src="scripts/game/fleetTable.js"></script>

<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl={$lang}"></script>

{/block}

{/block}
