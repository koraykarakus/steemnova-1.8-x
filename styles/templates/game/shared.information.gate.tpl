<table class="table_game table_full">
	<tbody>
		<tr>
			<th colspan="3" class="left">{$LNG.in_jump_gate_select_ships}</th>
		</tr>
		{if $gateData.restTime != 0}
		<tr>
			<td colspan="3">
				{$LNG.in_jump_gate_wait_time} {$gateData.nextTime}&nbsp;(<span class="countdown" data-time="{$gateData.restTime}">{pretty_fly_time($gateData.restTime)}</span>)
			</td>
		</tr>
		{else}
			<tr>
				<td>{$LNG.in_jump_gate_start_moon}</td>
				<td colspan="2">{$gateData.startLink}</td>
			</tr>
			{if !empty($gateData.gateList)}
				<tr>
					<td>{$LNG.in_jump_gate_finish_moon}</td>
					<td colspan="2">{html_options options=$gateData.gateList name="jmpto" class="jumpgate"}</td>
				</tr>
				<tr>
					<td>{$LNG.fl_ship_type}</td>
					<td class="text_center">{$LNG.fl_ship_available}</td>
					<td></td>
				</tr>
				{foreach $gateData.fleetList as $fleetID => $amount}
				<tr>
					<td>{$LNG.tech.$fleetID}</td>
					<td class="text_center">
						<span id="ship{$fleetID}_value">{$amount|number}</span>
					</td>
					<td class="text_center">
						<input class="jumpgate" name="ship[{$fleetID}]" id="ship{$fleetID}_input" size="7" value="0" type="text"><input onclick="Gate.max({$fleetID});" value="max" type="button">
					</td>
				</tr>
				{/foreach}
				<tr>
					<td class="text_center" colspan="3">
						<input value="{$LNG.in_jump_gate_jump}" type="button" onclick="Gate.submit();">
					</td>
				</tr>
			{else}
				<tr>
					<td class="text_center" colspan="3">
						<span style="color:red">{$LNG.in_jump_gate_no_target}</span>
					</td>
				</tr>
			{/if}
		{/if}
	</tbody>
</table>