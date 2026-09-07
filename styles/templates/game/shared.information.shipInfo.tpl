<table class="table_game table_full">
	<tbody>
		{if !empty($fleet_info.tech)}
		<tr>
			<td style="width:50%">{$LNG.in_engine}</td>
			<td style="width:50%">{if $fleet_info.tech == 1}{$LNG.tech.115}{elseif $fleet_info.tech == 2}{$LNG.tech.117}{elseif $fleet_info.tech == 3}{$LNG.tech.118}{elseif $fleet_info.tech == 4}{$LNG.tech.115} <span style="color:yellow">({$LNG.tech.117})</span>{elseif $fleet_info.tech == 3}{$LNG.tech.117} <span style="color:yellow">({$LNG.tech.118})</span>{else}-{/if}</td>
		</tr>
		{/if}
		<tr>
			<td style="width:50%">{$LNG.in_struct_pt}</td>
			<td style="width:50%">{$fleet_info.structure|number}</td>
		</tr>
		<tr>
			<td style="width:50%">{$LNG.in_attack_pt}</td>
			<td style="width:50%">{$fleet_info.attack|number}</td>
		</tr>
		<tr>
			<td style="width:50%">{$LNG.in_shield_pt}</td>
			<td style="width:50%">{$fleet_info.shield|number}</td>
		</tr>
		{if !empty($fleet_info.capacity)}
		<tr>
			<td style="width:50%">{$LNG.in_capacity}</td>
			<td style="width:50%">{$fleet_info.capacity|number}</td>
		</tr>
		{/if}
		{if !empty($fleet_info.speed1)}
		<tr>
			<td style="width:50%">{$LNG.in_base_speed}</td>
			<td style="width:50%">{$fleet_info.speed1|number}{if $fleet_info.speed1 != $fleet_info.speed2} <span style="color:yellow">({$fleet_info.speed2|number})</span>{/if}</td>
		</tr>
		{/if}
		{if !empty($fleet_info.consumption1)}
		<tr>
			<td style="width:50%">{$LNG.in_consumption}</td>
			<td style="width:50%">{$fleet_info.consumption1|number}{if $fleet_info.consumption1 != $fleet_info.consumption2} <span style="color:yellow">({$fleet_info.consumption2|number})</span>{/if}</td>
		</tr>
		{/if}
	</tbody>
</table>
