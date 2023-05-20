{block name="content"}

<form action="?page=banned&mode=banUser" method="post" >
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
  <input type="hidden" name="target_id" value="{$target_id}">
<tr>
	<th colspan="3">{$bantitle}</th>
</tr>
<tr>
	<td>{$LNG.bo_username}</td>
	<td colspan="2">
    <input name="ban_name" type="text" value="{$name}" readonly="true" />
  </td>
</tr>
<tr>
	<td>{$LNG.bo_reason} <br><br>{$LNG.bo_characters_1}<input id="result2" value="50" size="2" readonly="true" class="character"></td>
	<td colspan="2">
    <textarea name="ban_reason" maxlength="50" cols="20" rows="5" onkeyup="$('#result2').val(50 - parseInt($(this).val().length));">{$reas}</textarea>
  </td>
</tr>
	{$timesus}
<tr>
	<th colspan="2">{$changedate}</th>
	{$changedate_advert}
</tr>
<tr>
	<td>{$LNG.bo_permanent}</td>
	<td><input name="ban_permanently" type="checkbox"></td>
	{if $changedate_advert}<td>&nbsp;</td>{/if}
</tr>
<tr>
	<td>{$LNG.time_days}</td>
	<td><input name="days" type="text" value="0" size="5"></td>
	{if $changedate_advert}<td>&nbsp;</td>{/if}
</tr>
<tr>
	<td>{$LNG.time_hours}</td>
	<td><input name="hour" type="text" value="0" size="5"></td>
	{if $changedate_advert}<td>&nbsp;</td>{/if}
</tr>
<tr>
	<td>{$LNG.time_minutes}</td>
	<td><input name="mins" type="text" value="0" size="5"></td>
	{if $changedate_advert}<td>&nbsp;</td>{/if}
</tr>
<tr>
	<td>{$LNG.time_seconds}</td>
	<td><input name="secs" type="text" value="0" size="5"></td>
	{if $changedate_advert}<td>&nbsp;</td>{/if}
</tr>
<tr>
	<th colspan="3">{$LNG.bo_vacaations}</th>
</tr>
<tr>
	<td>{$LNG.bo_vacation_mode}</td>
	<td colspan="2"><input name="vacat" type="checkbox"{if $vacation} checked = "checked"{/if}></td>
</tr>
<tr>
	<td colspan="3">
	<input type="submit" value="{$LNG.button_submit}" name="bannow" />
</tr>
</table>
</form>

{/block}
