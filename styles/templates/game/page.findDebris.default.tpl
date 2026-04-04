{block name="title" prepend}{$LNG.lm_empire}{/block}
{block name="content"}
<table class="table_game table_full">
	<tbody>
		<tr>
			<td><a href="?page=findDebris">Show Debris(range : {$range})</a></td>
		</tr>
			{$debris}
	</tbody>
			<table>
				<tr style="display: none;" id="fleetstatusrow">
				<th colspan="6">Fleet...</th>
				</tr>
			</table>
		</tr>
</table>
</div>
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