{block name="title" prepend}{$LNG.lm_support}{/block}
{block name="content"}
<table class="table_game table_full">
	<tr>
		<th colspan="5">{$LNG.ti_header}</th>
	</tr>
	<tr>
		<td class="text_center" colspan="5"><a href="game.php?page=ticket&amp;mode=create">{$LNG.ti_new}</a></td>
	</tr>
	{if !empty($ticket_list)}
	<tr>
		<th>{$LNG.ti_id}</td>
		<th>{$LNG.ti_subject}</td>
		<th>{$LNG.ti_answers}</td>
		<th>{$LNG.ti_date}</td>
		<th>{$LNG.ti_status}</td>
	</tr>
	{/if}
	{foreach $ticket_list as $ticket_id => $ticket_info}
	<tr>
		<td class="text_center">
			<a href="game.php?page=ticket&amp;mode=view&amp;id={$ticket_id}">#{$ticket_id}</a>
		</td>
		<td class="text_center">
			<a href="game.php?page=ticket&amp;mode=view&amp;id={$ticket_id}">{$ticket_info.subject}</a>
		</td>
		<td class="text_center">{$ticket_info.answer - 1}</td>
		<td class="text_center">{$ticket_info.time}</td>
		<td class="text_center">{if $ticket_info.status == 0}<span style="color:green">{$LNG.ti_status_open}</span>{elseif $ticket_info.status == 1}<span style="color:orange">{$LNG.ti_status_answer}</span>{else}<span style="color:red">{$LNG.ti_status_closed}</span>{/if}</td>
	</tr>
	{/foreach}
</table>
{/block}
