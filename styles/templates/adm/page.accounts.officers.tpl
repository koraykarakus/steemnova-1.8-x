{block name="content"}

<form action="?page=accounts&mode=officersSend" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
<td colspan="3" align="left"><a href="?page=accounts">
<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ad_back_to_menu}</a></td>
</tr><tr>
	<th colspan="7">{$LNG.ad_offi_title}</th>
</tr><tr>
	<td colspan="2">{$LNG.input_id_user}</td>
	<td><input name="id" type="text" value="0" size="3"></td>
</tr><tr>
	<th>{$LNG.ad_number}</th>
	<th>{$LNG.ad_offi}</th>
	<th>{$LNG.ad_count}</th>
</tr>
{foreach key=id item=input from=$inputlist}
<tr><td>{$id}</td><td>{$LNG.tech.{$id}}</td><td><input name="{$input.type}" type="text" value="0"></td></tr>
{/foreach}
<tr>
	<td colspan="3">
	<input type="reset" value="{$LNG.button_reset}">
	<select class="" name="type">
		<option value="add" selected>{$LNG.button_add}</option>
		<option value="delete">{$LNG.button_delete}</option>
	</select>
	<input type="submit" name="" value="{$LNG.button_submit}">
	</td>
</tr>
</table>
</form>

{/block}
