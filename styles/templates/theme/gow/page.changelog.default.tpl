{block name="title" prepend}{$LNG.lm_changelog}{/block}
{block name="content"}
	<table class="table table-gow fs-12">
		<tr>
			<th>{$LNG.lm_changelog}</th>
		</tr>
		{foreach $ChangelogList as $item}
			<tr>
				<td class="left d-flex align-items-center">{$item}</td>
			</tr>
		{/foreach}
	</table>
{/block}