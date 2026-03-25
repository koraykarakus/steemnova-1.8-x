{block name="title" prepend}{$LNG.ti_read} - {$LNG.lm_support}{/block}
{block name="content"}

<table class="table-gow table_full">
<form action="game.php?page=ticket&mode=send" method="post" id="form">
<input type="hidden" name="id" value="{$ticketID}">
	{foreach $answerList as $answerID => $answerRow}
	{if $answerRow@first}
	<thead>
		<tr>
			<th colspan="2">
				<span class="color-blue">{$LNG.ti_read}:{$answerRow.subject}</span>
			</th>
		</tr>
	</thead>
	{/if}
	<tbody>
	<tr>
		<td colspan="2">
			<span class="color-blue">
				{$LNG.ti_msgtime} {$answerRow.time} {$LNG.ti_from} {$answerRow.owner_name}
			</span>
			{if $answerRow@first}
			<span class="color-blue">
				{$LNG.ti_category}: {$categoryList[$answerRow.category_id]}
			</span>
			{/if}
			<p style="word-break: break-word;overflow-wrap: break-word;white-space: normal;" class="">
				{$answerRow.message}
			</p>
		</td>
	</tr>
	{/foreach}
	{if $status < 2}
	<tr>
		<th colspan="2">{$LNG.ti_answer}</th>
	</tr>
	<tr>
		<td style="width:30%">
			<label for="message">{$LNG.ti_message}</label>
		</td>
		<td style="width:70%">
			<textarea style="width: 100%;height:120px;resize:none;"  class="validate[required] " id="message" name="message" rows="60" cols="8" style="height:100px;"></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input class="" type="submit" value="{$LNG.ti_submit}">
		</td>
	</tr>
	</tbody>
	{/if}
	</form>
</table>

{block name="script" append}
<script>
$(document).ready(function() {
	$("#form").validationEngine('attach');
});
</script>
{/block}
{/block}

