{block name="title" prepend}{$LNG.lm_faq}{/block}
{block name="content"}
	<table class="table_game table_full">
		<tr>
			<th class="text_center">{$LNG.faq_overview}</th>
		</tr>
		<tr>
			<td>
				<table class="table_game table_full">
				{foreach $LNG.questions as $categoryID => $categoryRow}
					<thead>
						<tr>
							<th>{$categoryRow.category}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $categoryRow as $questionID => $questionRow}
							{if is_numeric($questionID)}
								<tr>
								<td>
									<a href="game.php?page=questions&amp;mode=single&amp;categoryID={$categoryID}&amp;questionID={$questionID}">{$questionRow.title}</a>
								</td>
								</tr>
							{/if}
						{/foreach}
					</tbody>
				{/foreach}
				</table>
			</td>
		</tr>
	</table>
{/block}