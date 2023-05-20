{block name="content"}

<form action="?page=create&mode=createMoon" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="3">{$LNG.po_add_moon}</th>
</tr>
<tr>
	<td>{$LNG.input_id_planet}</td>
	<td colspan="2"><input  type="text" name="add_moon" value="" size="3"></td>
</tr>
<tr>
	<td>{$LNG.mo_moon_name}</td>
	<td colspan="2"><input type="text" value="{$LNG.mo_moon}" name="name"></td>
</tr>
<tr>
	<td>{$LNG.mo_diameter}</td>
	<td colspan="2"><input type="text" name="diameter" size="5" maxlength="5">
	<input type="checkbox" checked="checked" name="diameter_check"> ({$LNG.mo_moon_random})</td>
</tr>
<tr>
	<td>{$LNG.mo_fields_avaibles}</td>
	<td colspan="2"><input type="text" name="field_max" size="5" maxlength="5" value="1"></td>
</tr>
<tr>
	<td colspan="3"><input type="submit" value="{$LNG.button_add}"></td>
</tr><tr>
   <td colspan="2" style="text-align:left;"><a href="?page=create">{$LNG.new_creator_go_back}</a>&nbsp;<a href="?page=create&mode=moon">{$LNG.new_creator_refresh}</a></td>
</tr>
</table>
</form>

{/block}
