{block name="content"}

<form action="?page=create&mode=createPlanet" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="3">{$LNG.po_add_planet}</th>
</tr>
<tr>
	<td>{$LNG.input_id_user}</td>
	<td><input name="id" type="text" size="4"></td>
</tr><tr>
	<td>{$LNG.new_creator_coor}</td>
	<td><input name="galaxy" type="text" size="3" maxlength="1" title="{$LNG.po_galaxy}">&nbsp; :
	<input name="system" type="text" size="3" maxlength="3"  title="{$LNG.po_system}">&nbsp; :
	<input name="planet" type="text" size="3" maxlength="2"  title="{$LNG.po_planet}"><br>
	</td>
</tr><tr>
	<td>{$LNG.po_name_planet}</td>
	<td><input name="name" type="text" size="15" maxlength="25" value="{$LNG.po_colony}"></td>
</tr><tr>
	<td>{$LNG.po_fields_max}</td>
	<td><input name="field_max" type="text" size="6" maxlength="10"></td>
</tr><tr>
	<td colspan="2"><input type="Submit" value="{$LNG.button_add}"></td>
</tr><tr>
	<td colspan="2" style="text-align:left;"><a href="?page=create">{$LNG.new_creator_go_back}</a>&nbsp;<a href="?page=create&amp;mode=planet">{$LNG.new_creator_refresh}</a></td>
</tr>
</table>
</form>

{/block}
