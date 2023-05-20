{block name="content"}

<form action="?page=disclamer&mode=saveSettings" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="2">{$LNG.se_server_parameters}</th>
	<th>(?)</th>
</tr><tr>
	<td>{$LNG.se_disclaimerAddress}</td>
	<td><textarea name="disclaimerAddress" cols="80" rows="5">{$disclaimerAddress}</textarea></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$LNG.se_disclaimerPhone}</td>
	<td><input name="disclaimerPhone" size="40" value="{$disclaimerPhone}" type="text"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$LNG.se_disclaimerMail}</td>
	<td><input name="disclaimerMail" size="40" value="{$disclaimerMail}" type="text"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$LNG.se_disclaimerNotice}</td>
	<td><textarea name="disclaimerNotice" cols="80" rows="5">{$disclaimerNotice}</textarea></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan="3"><input value="{$LNG.se_save_parameters}" type="submit"></td>
</tr>
</table>
</form>

{/block}
