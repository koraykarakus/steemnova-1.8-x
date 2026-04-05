{block name="title" prepend}{$LNG.fd_title}{/block}
{block name="content"}
<table class="table_game table_full">
	<thead>
		<tr>
			<th colspan="8">
				<a href="?page=findDebris">{$LNG.fd_refresh} ({$LNG.fd_range} : {$range})</a>
			</th>
		</tr>
		<tr>
			<th>{$LNG.fd_coord}:</th>
			<th>{$LNG.tech.901}</th>
			<th>{$LNG.tech.902}</th>
			<th>{$LNG.fd_giga_recyclers_need}</th>
			<th>{$LNG.fd_giga_recyclers_have}</th>
			<th>{$LNG.fd_recyclers_need}</th>
			<th>{$LNG.fd_recyclers_have}</th>
			<th>{$LNG.fd_action}</th>
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
					<button onclick='doit(8, {$c_row.galaxy},{$c_row.system},{$c_row.planet},1);'>{$LNG.fd_go}</button>
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td class="text_center" colspan='8'>{$LNG.fd_no_debris}</td>
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