{block name="title" prepend}{$LNG.lm_empire}{/block}
{block name="content"}
<table class="table_game table_full">
	<thead>
		<tr>
			<th colspan="4">
				<a href="?page=findDebris">Show Debris (range : {$range})</a>
			</th>
		</tr>
		<tr>
			<th>Coordinates:</th>
			<th>Metal</th>
			<th>Crsyal</th>
			<th>Recyclers</th>
		</tr>
	</thead>
	<tbody>
		{foreach $debris_data as $c_row}
			<tr>
				<td class="text_center">[{$c_row.galaxy}:{$c_row.system}:{$c_row.planet}]</td>
				<td class="text_center">{$c_row.debris_metal}</td>
				<td class="text_center">{$c_row.debris_crystal}</td>
				<td class="text_center">{$c_row.needed_recycler_num}</td>
			</tr>
		{foreachelse}
			<tr>
				<td class="text_center" colspan='4'>There are no debris in your range</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<table>
	<tr style="display: none;" id="fleetstatusrow">
		<th colspan="6">Fleet...</th>
	</tr>
</table>

<script type="text/javascript">
	MaxFleetSetting = {$user_maxfleetsettings};
</script>

<script>
function doit(missionID, planetID) {
	$.getJSON("game.php?page=fleetAjax&ajax=1&mission="+missionID+"&planetID="+planetID, function(data)
	{
		$('#slots').text(data.slots);
		if(typeof data.ships !== "undefined")
		{
			$.each(data.ships, function(elementID, value) {
				$('#elementID'+elementID).text(number_format(value));
			});
		}
		
		var statustable	= $('#fleetstatusrow');
		var messages	= statustable.find("~tr");
		if(messages.length == MaxFleetSetting) {
			messages.filter(':last').remove();
		}
		var element		= $('<td />').attr('colspan', 8).attr('class', data.code == 600 ? "success" : "error").text(data.mess).wrap('<tr />').parent();
		statustable.removeAttr('style').after(element);
	});
}
</script>
{/block}