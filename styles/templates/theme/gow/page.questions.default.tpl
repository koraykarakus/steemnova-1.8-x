{block name="title" prepend}{$LNG.lm_faq}{/block}
{block name="content"}
	<table class="table-gow table_full">
		<tr>
			<th class="text-center">{$LNG.faq_overview}</th>
		</tr>
		<tr>
			<td class="left">
				{foreach $LNG.questions as $categoryID => $categoryRow}
					<h2 class="fs-14">{$categoryRow.category}</h2>
					<ul>
						{foreach $categoryRow as $questionID => $questionRow}
							{if is_numeric($questionID)}
								<li>
									<a
										href="game.php?page=questions&amp;mode=single&amp;categoryID={$categoryID}&amp;questionID={$questionID}">{$questionRow.title}</a>
								</li>
							{/if}
						{/foreach}
					</ul>
				{/foreach}
			</td>
		</tr>
	</table>
{/block}