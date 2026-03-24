{block name="title" prepend}{$LNG.lm_statistics}{/block}
{block name="content"}
	<form name="stats" id="stats" method="post" action="">
		<table class="table-gow table_full">
			<tr>
				<th class="">{$LNG.st_statistics} ({$LNG.st_updated}: {$stat_date})</th>
			</tr>
			<tr>
				<td class="">
					<label for="who">{$LNG.st_show}</label>
					<select style="width:auto;" class="" name="who" id="who"
						onchange="$('#stats').submit();">
						{html_options options=$Selectors.who selected=$who}
					</select>
					<label for="type">{$LNG.st_per}</label>
					<select style="width:auto;" class="" name="type" id="type"
						onchange="$('#stats').submit();">
						{html_options options=$Selectors.type selected=$type}
					</select>
					<label for="range">{$LNG.st_in_the_positions}</label>
					<select style="width:auto;" class="" name="range" id="range"
						onchange="$('#stats').submit();">
						{html_options options=$Selectors.range selected=$range}
					</select>
				</td>
			</tr>
		</table>
	</form>
	<table class="table-gow table_full stats_table">
		{if $who == 1}
			{include file="shared.statistics.playerTable.tpl"}
		{elseif $who == 2}
			{include file="shared.statistics.allianceTable.tpl"}
		{/if}
	</table>
{/block}
{block name="script" append}
	<script src="scripts/game/statistics.js"></script>
{/block}