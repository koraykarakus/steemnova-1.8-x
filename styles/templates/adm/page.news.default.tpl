{block name="content"}
	<form method="POST" action="?page=news&amp;mode=createSend">
		{if isset($news_id)}<input name="id" type="hidden" value="{$news_id}">{/if}
		<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
			<tr>
				<th colspan="2"></th>
			</tr>
			<tr>
			<tr>
				<td width="25%">{$LNG.nws_title}</td>
				<td><input type="text" name="title" value="{if isset($news_title)}{$news_title}{/if}"></td>
			</tr>
			<tr>
				<td>{$LNG.nws_content}</td>
				<td><textarea cols="70" rows="10" name="text">{if isset($news_text)}{$news_text}{/if}</textarea></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="Submit" value="{$LNG.button_submit}"></td>
			</tr>
		</table>
	</form>
	<table class="table table-dark table-striped table-sm fs-12 my-5 mx-auto">
		<tr>
			<th colspan="5">{$LNG.nws_news}</thd>
		</tr>
		<tr>
			<td>{$LNG.nws_id}</td>
			<td>{$LNG.nws_title}</td>
			<td>{$LNG.nws_date}</td>
			<td>{$LNG.nws_from}</td>
			<td>{$LNG.nws_del}</td>
		</tr>
		{foreach $news_list as $c_news}<tr>
				<td>
					<a href="?page=news&amp;mode=edit&amp;id={$c_news.id}">{$c_news.id}</a>
				</td>
				<td>
					<a href="?page=news&amp;mode=edit&amp;id={$c_news.id}">{$c_news.title}</a>
				</td>
				<td>
					<a href="?page=news&amp;mode=edit&amp;id={$c_news.id}">{$c_news.date}</a>
				</td>
				<td>
					<a href="?page=news&amp;mode=edit&amp;id={$c_news.id}">{$c_news.user}</a>
				</td>
				<td>
					<a href="?page=news&amp;mode=delete&amp;id={$c_news.id}" onclick="return confirm('{$c_news.confirm}');"><img
							border="0" src="./styles/resource/images/alliance/CLOSE.png" width="16" height="16">
					</a>
				</td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="5"><a href="?page=news&amp;mode=create">{$LNG.nws_create}</a></td>
		</tr>
		<tr>
			<td colspan="5">{$news_total}</td>
		</tr>
	</table>
{/block}