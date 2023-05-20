{block name="content"}

<form action="admin.php?page=support&amp;mode=send" method="post" id="form">
<input type="hidden" name="id" value="0">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
	<tr>
		<th colspan="2">{$LNG.ti_create_head}</th>
	</tr>
	<tr>
		<td colspan="2" class="left">{$LNG.ti_create_info}</td>
	</tr>
	<tr>
		<td style="width:30%"><label for="category">{$LNG.ti_category}</label></td>
		<td style="width:70%"><select id="category" name="category">{html_options options=$categoryList}</select></td>
	</tr>
	<tr>
		<td><label for="subject">{$LNG.ti_subject}</label></td>
		<td><input class="validate[required]" type="text" id="subject" name="subject" size="40" maxlength="255"></td>
	</tr>
	<tr>
		<td><label for="message">{$LNG.ti_message}</label></td>
		<td><textarea class="validate[required]" id="message" name="message" row="60" cols="8" style="height:100px;"></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="{$LNG.ti_submit}"></td>
	</tr>
</table>
</form>

{/block}
