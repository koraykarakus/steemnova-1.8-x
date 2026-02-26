{block name="title" prepend}{$LNG.siteTitleBanList}{/block}
{block name="content"}
	{if $isMultiUniverse}
		<p>
			{html_options options=$universe_select selected=$UNI class="changeUni" id="universe" name="universe"}
		</p>
	{/if}
	<table>
		<tr>
			<th colspan="5">{$LNG.bn_players_banned_list}</th>
		</tr>
		{if $ban_count}
			<tr>
				<td style="text-align:right;" colspan="5">{$LNG.mg_page}:
					{if $page_number != 1}
						<a href="index.php?page=banList&amp;side={$page_number - 1}">&laquo;</a>&nbsp;
					{/if}
					{for $site=1 to $max_page}
						<a
							href="index.php?page=banList&amp;side={$site}">{if $site == $page_number}<b>[{$site}]</b>{else}[{$site}]{/if}</a>
						{if $site != $max_page}&nbsp;{/if}
					{/for}
					{if $page_number != $max_page}
						&nbsp;<a href="index.php?page=banList&amp;side={$page_number + 1}">&raquo;</a>
					{/if}
				</td>
			</tr>
		{/if}
		<tr>
			<td>{$LNG.bn_from}</td>
			<td>{$LNG.bn_until}</td>
			<td>{$LNG.bn_player}</td>
			<td>{$LNG.bn_by}</td>
			<td>{$LNG.bn_reason}</td>
		</tr>
		{if $ban_count}
			{foreach $ban_list as $c_ban}
				<tr>
					<td>{$c_ban.from}</td>
					<td>{$c_ban.to}</td>
					<td>{$c_ban.player}</td>
					<td><a href="mailto:{$c_ban.mail}" title="{$c_ban.info}">{$c_ban.admin}</a></td>
					<td>{$c_ban.theme}</td>
				</tr>
			{/foreach}
			<tr>
				<td style="text-align:right;" colspan="5">{$LNG.mg_page}: {if $page_number != 1}<a
						href="index.php?page=banList&amp;side={$page_number - 1}">&laquo;</a>&nbsp;{/if}
					{for $site=1 to $max_page}<a
							href="index.php?page=banList&amp;side={$site}">{if $site == $page_number}<b>[{$site}]</b>{else}[{$site}]{/if}</a>{if $site != $max_page}&nbsp;{/if}{/for}{if $page_number != $max_page}&nbsp;<a
						href="index.php?page=banList&amp;side={$page_number + 1}">&raquo;</a>{/if}</td>
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