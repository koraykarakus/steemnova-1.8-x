
{block name="content"}

{nocache}
<form method="POST" action="?page=news&mode=createSend">
{if isset($news_id)}<input name="id" type="hidden" value="{$news_id}">{/if}
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="2">{$nws_head}</th>
</tr>
<tr>
<tr>
	<td width="25%">{$LNG.nws_title}</td><td><input type="text" name="title" value="{if isset($news_title)}{$news_title}{/if}"></td>
</tr>
<tr>
	<td>{$LNG.nws_content}</td><td><textarea cols="70" rows="10" name="text">{if isset($news_text)}{$news_text}{/if}</textarea></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" name="Submit" value="{$LNG.button_submit}"></td>
</tr>
</table>
</form>
{/nocache}

{/block}
