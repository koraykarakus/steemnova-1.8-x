{block name="title" prepend}{$LNG.gl_phalanx}{/block}
{block name="content"}
<table class="table_game table_full">
	<tr>
		<th colspan="2">{$LNG.px_scan_position} [{$galaxy}:{$system}:{$planet}] ({$name})</th>
	</tr>
	<tr>
		<th colspan="2">{$LNG.px_fleet_movement}</th>
	</tr>
	{foreach $fleetTable as $index => $fleet}
	<tr>
		<td id="fleettime_{$index}" class="fleets" data-fleet-end-time="{$fleet.returntime}" data-fleet-time="{$fleet.resttime}">00:00:00</td>
		<td>{$fleet.text}</td>
	</tr>
	{foreachelse}
	<tr>
		<td class="text_center" colspan="2">{$LNG.px_no_fleet}</td>
	</tr>
	{/foreach}
</table>
{/block}