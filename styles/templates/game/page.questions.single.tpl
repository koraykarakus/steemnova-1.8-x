{block name="title" prepend}{$LNG.lm_faq}{/block}
{block name="content"}
	<table class="table_game table_full">
		<tr>
			<th>{$LNG.faq_overview}</th>
		</tr>
		<tr>
			<th>{$question_row.title}</th>
		</tr>
		<tr>
			<td class="left ">
				{$question_row.body}
			</td>
		</tr>
		<tr>
			<th>
				<a href="game.php?page=questions">{$LNG.al_back}</a>
			</th>
		</tr>
	</table>
{/block}