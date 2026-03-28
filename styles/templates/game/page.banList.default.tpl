{block name="title" prepend}{$LNG.lm_banned}{/block}
{block name="content"}
	<table class="table_game table_full">
		<tr class="text-center">
			<th colspan="5">{$LNG.bn_players_banned_list}</th>
		</tr>
		<tr>
			<td>{$LNG.bn_from}</td>
			<td>{$LNG.bn_until}</td>
			<td>{$LNG.bn_player}</td>
			<td>{$LNG.bn_by}</td>
			<td>{$LNG.bn_reason}</td>
		</tr>
		{if $ban_count}
			<tr>
				<td class="right" colspan="5">{$LNG.mg_page}: {if $page_number != 1}<a
						href="game.php?page=banList&amp;side={$page_number - 1}">&laquo;</a>&nbsp;{/if}
					{for $site=1 to $max_page}<a
							href="game.php?page=banList&amp;side={$site}">{if $site == $page_number}<b>[{$site}]</b>{else}[{$site}]{/if}</a>{if $site != $max_page}&nbsp;{/if}{/for}{if $page_number != $max_page}&nbsp;<a
						href="game.php?page=banList&amp;side={$page_number + 1}">&raquo;</a>{/if}</td>
			</tr>
			{foreach $ban_list as $ban_row}
				<tr>
					<td>{$ban_row.from}</td>
					<td>{$ban_row.to}</td>
					<td>{$ban_row.player}</td>
					<td><a href="mailto:{$ban_row.mail}" title="{$ban_row.info}">{$ban_row.admin}</a></td>
					<td>{$ban_row.theme}</td>
				</tr>
			{/foreach}
			<tr>
				<td class="right" colspan="5">{$LNG.mg_page}: {if $page_number != 1}<a
						href="game.php?page=banList&amp;side={$page_number - 1}">&laquo;</a>&nbsp;{/if}
					{for $site=1 to $max_page}<a
							href="game.php?page=banList&amp;side={$site}">{if $site == $page_number}<b>[{$site}]</b>{else}[{$site}]{/if}</a>{if $site != $max_page}&nbsp;{/if}{/for}{if $page_number != $max_page}&nbsp;<a
						href="game.php?page=banList&amp;side={$page_number + 1}">&raquo;</a>{/if}</td>
			</tr>
			<tr>
				<td colspan="5">{$LNG.bn_exists}{$ban_count|number}{$LNG.bn_players_banned}</td>
			</tr>
		{else}
			<tr>
				<td colspan="5">{$LNG.bn_no_players_banned}</td>
			</tr>
		{/if}
	</table>
{/block}