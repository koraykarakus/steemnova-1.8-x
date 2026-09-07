{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}
<table class="table_game table_full">
	<tr>
		<th colspan="2" class="success">{$LNG.fl_fleet_sended}</span></th>
	</tr>
    <tr>
        <td>{$LNG.fl_mission}</td>
        <td>{$LNG["type_mission_{$target_mission}"]}</td>
	</tr>
    <tr>
        <td>{$LNG.fl_distance}</td>
        <td>{$distance|number}</td>
    </tr>
    <tr>
        <td>{$LNG.fl_fleet_speed}</td>
        <td>{$max_fleet_speed|number}</td>
    </tr>
    <tr>
        <td>{$LNG.fl_fuel_consumption}</td>
        <td>{$consumption|number}</td>
    </tr>
    <tr>
        <td>{$LNG.fl_from}</td>
        <td>{$from}</td>
    </tr>
    <tr>
        <td>{$LNG.fl_destiny}</td>
        <td>{$destination}</td>
    </tr>
    <tr>
        <td>{$LNG.fl_arrival_time}</td>
        <td>{$fleet_start_time}</td>
    </tr>
    <tr>
        <td>{$LNG.fl_return_time}</td>
        <td>{$fleet_end_time}</td>
    </tr>
    <tr>
        <th colspan="2">{$LNG.fl_fleet}</th>
    </tr>
	{foreach $fleet_list as $ShipID => $ShipCount}
	<tr>
		<td>{$LNG.tech.{$ShipID}}</td>
		<td>{$ShipCount|number}</td>
	</tr>
	{/foreach}
</table>
{/block}
