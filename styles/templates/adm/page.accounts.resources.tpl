{block name="content"}

<form action="?page=accounts&mode=resourcesSend" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
<td colspan="3" align="left"><a href="?page=accounts&mode=show">
<img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> {$LNG.ad_back_to_menu}</a></td>
</tr>
<tr>
	<th class="text-center" colspan="2">{$LNG.resources_title}</th>
</tr>
<tr>
	<td>{$LNG.input_id_p_m}</td>
	<td><input name="id" type="text" value="0" size="3"></td>
</tr>
<tr>
	<td>Coordinate</td>
	<td>
		<input name="galaxy" type="text" value="0" size="3">
		<input name="system" type="text" value="0" size="3">
		<input name="planet" type="text" value="0" size="3">
		<select name="planet_type">
			<option value="1">Planet</option>
			<option value="3">Moon</option>
		</select>
	</td>
</tr>
<tr>
	<td>{$LNG.tech.901}</td>
	<td><input name="metal" type="text" value="0"></td>
</tr>
<tr>
	<td>{$LNG.tech.902}</td>
	<td><input name="cristal" type="text" value="0"></td>
</tr>
<tr>
	<td>{$LNG.tech.903}</td>
	<td><input name="deut" type="text" value="0"></td>
</tr>
<tr>
	<td colspan="2">
		<input type="reset" value="{$LNG.button_reset}">
		<select name="type">
			<option value="add" selected>{$LNG.button_add}</option>
			<option value="delete">{$LNG.button_delete}</option>
		</select>
		<input type="submit" value="{$LNG.button_submit}">
</td>
</tr>
</table>
</form>
<form action="?page=accounts&mode=darkmatterSend" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th class="text-center" colspan="2">{$LNG.tech.921}</th>
</tr>
<tr>
	<td>{$LNG.input_id_user}</td>
	<td><input name="user_id" type="text" value="0" size="3"></td>
</tr>
<tr>
	<td>{$LNG.tech.921}</td>
	<td><input name="dark" type="text" value="0"></td>
</tr>
<tr>
	<td colspan="2">
		<input type="reset" value="{$LNG.button_reset}">
		<select name="type">
			<option value="add" selected>{$LNG.button_add}</option>
			<option value="delete">{$LNG.button_delete}</option>
		</select>
		<input type="submit" value="{$LNG.button_submit}">
 </td>
</tr>
</table>
</form>

{/block}
