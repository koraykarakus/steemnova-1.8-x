{block name="title" prepend}{$LNG.lm_changelog}{/block}
{block name="content"}
	<table class="table-gow table_full">
		<tr>
			<th>{$LNG.lm_changelog}</th>
		</tr>
		{foreach $ChangelogList as $item}
			<tr>
				<td>{$item}</td>
			</tr>
		{/foreach}
	</table>
{/block}