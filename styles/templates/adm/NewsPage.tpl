{include file="overall_header.tpl"}
{nocache}{if isset($mode)}
<form method="POST" action="?page=news&amp;action=send&amp;mode={$mode}">
{if isset($news_id)}<input name="id" type="hidden" value="{$news_id}">{/if}
<table>
<tr>
	<th colspan="2">{$nws_head}</th>
</tr>
<tr>
<tr>
	<td width="25%">{if isset($nws_title)}{$nws_title}{/if}</td><td><input type="text" name="title" value="{if isset($news_title)}{$news_title}{/if}"></td>
</tr>
<tr>
	<td>{$nws_content}</td><td><textarea cols="70" rows="10" name="text">{if isset($news_text)}{$news_text}{/if}</textarea></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" name="Submit" value="{$button_submit}"></td>
</tr>
</table>
</form>
{/if}{/nocache}
<table width="450">
<tr>
	<th colspan="5">{$nws_news}</thd>
</tr>
<tr>
	<td>{$nws_id}</td>
	<td>{$nws_title}</td>
	<td>{$nws_date}</td>
	<td>{$nws_from}</td>
	<td>{$nws_del}</td>
</tr>
{foreach name=NewsList item=NewsRow from=$NewsList}<tr>
<td><a href="?page=news&amp;action=edit&amp;id={$NewsRow.id}">{$NewsRow.id}</a></td>
<td><a href="?page=news&amp;action=edit&amp;id={$NewsRow.id}">{$NewsRow.title}</a></td>
<td><a href="?page=news&amp;action=edit&amp;id={$NewsRow.id}">{$NewsRow.date}</a></td>
<td><a href="?page=news&amp;action=edit&amp;id={$NewsRow.id}">{$NewsRow.user}</a></td>
<td><a href="?page=news&amp;action=delete&amp;id={$NewsRow.id}" onclick="return confirm('{$NewsRow.confirm}');"><img border="0" src="./styles/resource/images/alliance/CLOSE.png" width="16" height="16"></a></td>
</tr>
{/foreach}
<tr><td colspan="5"><a href="?page=news&amp;action=create">{$nws_create}</a></td></tr>
<tr><td colspan="5">{$nws_total}</td></tr>
</table>
{include file="overall_footer.tpl"}
