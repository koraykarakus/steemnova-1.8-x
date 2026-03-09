<footer id="footer">
	{foreach $cronjobs as $cronjob}
		<img src="cronjob.php?cronjobID={$cronjob}" alt="">
	{/foreach}
	{if isModuleAvailable($smarty.const.MODULE_SERVER_INFO)}
		<span class="" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-html="true" title="
			<table class='table-tooltip bg-black'>
				<thead>
					<tr>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class='text-start color-red fw-bold'>{$LNG.si_game_speed}:</td>
						<td>{$game_speed}</td>
					</tr>
					<tr>
						<td class='text-start color-red'>{$LNG.si_fleet_speed}:</td>
						<td>{$fleet_speed}</td>
					</tr>
					<tr>
						<td class='text-start color-red'>{$LNG.si_production_speed}:</td>
						<td>{$production_speed}</td>
					</tr>
					<tr>
						<td class='text-start color-red'>{$LNG.si_storage_multiplier}:</td>
						<td>{$storage_multiplier}</td>
					</tr>
				</tbody>
			</table>
			">{$LNG.si_universe_info}</span>
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_BANLIST)}
		<a class="" href="game.php?page=banList">{$LNG.lm_banned}</a>
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_RECORDS)}
		<a class="" href="game.php?page=records">{$LNG.lm_records}</a>
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_BATTLEHALL)}
		<a class="" href="game.php?page=battleHall">{$LNG.lm_topkb}</a>
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_SIMULATOR)}
		<a class="" href="game.php?page=battleSimulator">{$LNG.lm_battlesim}</a>
	{/if}

	<a class="" href="index.php?page=rules" target="rules">{$LNG.lm_rules}</a>

	{if isModuleAvailable($smarty.const.MODULE_FORUM)}
		{if !empty($hasBoard)}
			<a class="" href="game.php?page=board" target="forum">{$LNG.lm_forums}</a>
		{/if}
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_DISCORD)}
		<a class="" href="{$discordUrl}" target="copy">Discord</a>
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_CHAT)}
		<a class="" href="game.php?page=chat">{$LNG.lm_chat}</a>
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_FLIGHT_SIMULATOR)}
		<a class="" href="game.php?page=flightSimulator">{$LNG.lm_flight_simulator}</a>
	{/if}
	{if isModuleAvailable($smarty.const.MODULE_CHANGELOG)}
		<a class="" href="game.php?page=changeLog">{$LNG.lm_changelog}</a>
	{/if}
</footer>

{if $ga_active}
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '{$ga_key}']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script');
			ga.type = 'text/javascript';
			ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') +
				'.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();
	</script>
{/if}
{if $debug == 1}
	<script type="text/javascript">
		onerror = handleErr;
	</script>
{/if}