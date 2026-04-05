{block name="title" prepend}{$LNG.lm_empire}{/block}
{block name="content"}
<table class="table_game table_full">
	<thead>
		<tr>
			<th colspan="8">
				<a href="?page=findDebris">Show Debris (range : {$range})</a>
			</th>
		</tr>
		<tr>
			<th>Coord:</th>
			<th>Metal</th>
			<th>Crsyal</th>
			<th>G. Recyclers (N)</th>
			<th>G. Recyclers (H)</th>
			<th>Recyclers (N)</th>
			<th>Recyclers (H)</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		{foreach $debris_data as $c_row}
			<tr>
				<td class="text_center">[{$c_row.galaxy}:{$c_row.system}:{$c_row.planet}]</td>
				<td class="text_center">{$c_row.debris_metal}</td>
				<td class="text_center">{$c_row.debris_crystal}</td>
				<td class="text_center">{$c_row.need_giga_recycler}</td>
				<td class="text_center">{$c_row.have_giga_recycler}</td>
				<td class="text_center">{$c_row.need_recycler}</td>
				<td class="text_center">{$c_row.have_recycler}</td>
				<td class="text_center">
					<button onclick='doit(8, {$c_row.galaxy},{$c_row.system},{$c_row.planet},1);'>Go</button>
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td class="text_center" colspan='8'>There are no debris in your range</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<table id="fleetstatusrow" class="hidden table_game table_full">
		<thead>
			<tr>
				<th colspan="8">{$LNG.cff_fleet_target}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
</table>

<script type="text/javascript">
	MaxFleetSetting = {$user_maxfleetsettings};
</script>
{/block}