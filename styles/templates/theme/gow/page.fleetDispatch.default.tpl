{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}

	<table class="table-gow table_full">
		<thead>
			<tr>
				<th colspan="9">
					<div style="text-align:left;float:left;">{$LNG.fl_fleets}: ({$activeFleetSlots} /
						{$maxFleetSlots})</div>
					<div style="text-align:right;float:right;">{$LNG.fl_expeditions}:
						({$activeExpedition} / {$maxExpedition}) </div>
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
					<td class="text_center">{$smarty.foreach.FlyingFleets.iteration}</td>
					<td class="text_center">
						<a class="fleet_resources">
							<div class="tooltip tooltip_bottom">	
								<table>
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
								</table>
							</div>
							{$LNG["type_mission_{$FlyingFleetRow.mission}"]}
						</a>
						{if $FlyingFleetRow.state == 1}
							<br><a title="{$LNG.fl_returning}">{$LNG.fl_r}</a>
						{else}
							<br><a title="{$LNG.fl_onway}">{$LNG.fl_a}</a>
						{/if}
					</td>
					<td class="text_center">
						<a class="ship_types">
							<div class="tooltip tooltip_bottom">
								<table>
									<tr>
										<th colspan='2' style='text-align:center;'>{$LNG.fl_info_detail}</th>
									</tr>
									{foreach $FlyingFleetRow.FleetList as $shipID => $shipCount}
									<tr>
										<td>{$LNG.tech.{$shipID}}:</td>
										<td>{$shipCount}</td>
									</tr>
									{/foreach}
								</table>
							</div>
							{$FlyingFleetRow.amount}
						</a>
					</td>
					<td class="text_center">
						<a
							href="game.php?page=galaxy&amp;galaxy={$FlyingFleetRow.startGalaxy}&amp;system={$FlyingFleetRow.startSystem}">[{$FlyingFleetRow.startGalaxy}:{$FlyingFleetRow.startSystem}:{$FlyingFleetRow.startPlanet}]</a>
					</td>
					<td class="text_center" {if $FlyingFleetRow.state == 0}style="color:lime" {/if}>
						{$FlyingFleetRow.startTime}</td>
					<td class="text_center">
					<a href="game.php?page=galaxy&amp;galaxy={$FlyingFleetRow.endGalaxy}&amp;system={$FlyingFleetRow.endSystem}">[{$FlyingFleetRow.endGalaxy}:{$FlyingFleetRow.endSystem}:{$FlyingFleetRow.endPlanet}]</a>
					</td>
					{if $FlyingFleetRow.mission == 4 && $FlyingFleetRow.state == 0}
					<td class="text_center">-</td>
					{else}
					<td class="text_center" {if $FlyingFleetRow.state != 0}style="color:lime" {/if}>
						{$FlyingFleetRow.endTime}
					</td>
					{/if}
					<td id="fleettime_{$smarty.foreach.FlyingFleets.iteration}" class="fleets text_center"
						data-fleet-end-time="{$FlyingFleetRow.returntime}" data-fleet-time="{$FlyingFleetRow.resttime}">
						{pretty_fly_time({$FlyingFleetRow.resttime})}</td>
					<td class="text_center">
						{if !$isVacation && $FlyingFleetRow.state != 1 && $FlyingFleetRow.no_returnable != 1}
							<form action="game.php?page=fleetDispatch&amp;action=sendfleetback" method="post">
								<input name="fleetID" value="{$FlyingFleetRow.id}" type="hidden">
								<input class="text-white" value="{$LNG.fl_send_back}" type="submit">
							</form>
							{if $FlyingFleetRow.mission == 1}
								<form action="game.php?page=fleetDispatch&amp;action=acs" method="post">
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
				<tr>
					<td colspan="9">{$LNG.fl_no_more_slots}</td>
				</tr>
			{/if}
		</tbody>
	</table>

	{if !empty($acsData)}
		{include file="shared.fleetTable.acsTable.tpl"}
	{/if}

{/block}