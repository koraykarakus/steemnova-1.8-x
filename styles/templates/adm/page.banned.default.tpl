{block name="content"}

{nocache}
{if isset($name)}
<form action="" method="post" >
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="3">{$bantitle}</th>
</tr>
<tr>
	<td>{$LNG.bo_username}</td>
	<td colspan="2"><input name="ban_name" type="text" value="{$name}" readonly="true" class="character"/></td>
</tr>
<tr>
	<td>{$LNG.bo_reason} <br><br>{$LNG.bo_characters_1}<input id="result2" value="50" size="2" readonly="true" class="character"></td>
	<td colspan="2"><textarea name="why" maxlength="50" cols="20" rows="5" onkeyup="$('#result2').val(50 - parseInt($(this).val().length));">{$reas}</textarea></td>
</tr>
	{$timesus}
<tr>
	<th colspan="2">{$changedate}</th>
	{$changedate_advert}
</tr>
<tr>
	<td>{$LNG.bo_permanent}</td>
	<td><input name="permanent" type="checkbox"></td>
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
	<input type="submit" value="{$LNG.button_submit}" name="bannow" style="width:20%;"/>
</tr>
</table>
</form>
{/if}
{/nocache}
<form action="?page=banned&mode=userDetail" method="post" name="users">
<table class="table table-dark table-striped table-sm fs-12 w-100 my-5 mx-auto">
<td style="border:0px;width:50%" class="transparent">
<table class="table table-dark table-striped table-sm fs-12 w-100 my-5 mx-auto">
<tr>
	<th>{$LNG.bo_ban_player}</th>
</tr>
<tr>
	<td>
	<select name="target_id" style="width:70%;" size="20">
	{$UserSelect.List}
	</select>
	<br>
	<a href="?page=banned&order=username">{$LNG.bo_order_username}</a> &nbsp; <a href="?page=bans&amp;order=id">{if isset($bo_order_id)}{$bo_order_id}{/if}</a> &nbsp;
	<a href="?page=banned&view=banned">{$LNG.bo_order_banned}</a>
	<script>
		var UserList = new filterlist(document.getElementsByName('ban_name')[0]);
	</script>

	<br><br>
	<a href="javascript:UserList.set('^A')" title="{$LNG.bo_select_title} A">A</A>
	<a href="javascript:UserList.set('^B')" title="{$LNG.bo_select_title} B">B</A>
	<a href="javascript:UserList.set('^C')" title="{$LNG.bo_select_title} C">C</A>
	<a href="javascript:UserList.set('^D')" title="{$LNG.bo_select_title} D">D</A>

	<a href="javascript:UserList.set('^E')" title="{$LNG.bo_select_title} E">E</A>
	<a href="javascript:UserList.set('^F')" title="{$LNG.bo_select_title} F">F</A>
	<a href="javascript:UserList.set('^G')" title="{$LNG.bo_select_title} G">G</A>
	<a href="javascript:UserList.set('^H')" title="{$LNG.bo_select_title} H">H</A>
	<a href="javascript:UserList.set('^I')" title="{$LNG.bo_select_title} I">I</A>
	<a href="javascript:UserList.set('^J')" title="{$LNG.bo_select_title} J">J</A>
	<a href="javascript:UserList.set('^K')" title="{$LNG.bo_select_title} K">K</A>
	<a href="javascript:UserList.set('^L')" title="{$LNG.bo_select_title} L">L</A>
	<a href="javascript:UserList.set('^M')" title="{$LNG.bo_select_title} M">M</A>

	<a href="javascript:UserList.set('^N')" title="{$LNG.bo_select_title} N">N</A>
	<a href="javascript:UserList.set('^O')" title="{$LNG.bo_select_title} O">O</A>
	<a href="javascript:UserList.set('^P')" title="{$LNG.bo_select_title} P">P</A>
	<a href="javascript:UserList.set('^Q')" title="{$LNG.bo_select_title} Q">Q</A>
	<a href="javascript:UserList.set('^R')" title="{$LNG.bo_select_title} R">R</A>
	<a href="javascript:UserList.set('^S')" title="{$LNG.bo_select_title} S">S</A>
	<a href="javascript:UserList.set('^T')" title="{$LNG.bo_select_title} T">T</A>
	<a href="javascript:UserList.set('^U')" title="{$LNG.bo_select_title} U">U</A>
	<a href="javascript:UserList.set('^V')" title="{$LNG.bo_select_title} V">V</A>

	<a href="javascript:UserList.set('^W')" title="{$LNG.bo_select_title} W">W</A>
	<a href="javascript:UserList.set('^X')" title="{$LNG.bo_select_title} X">X</A>
	<a href="javascript:UserList.set('^Y')" title="{$LNG.bo_select_title} Y">Y</A>
	<a href="javascript:UserList.set('^Z')" title="{$LNG.bo_select_title} Z">Z</A>
	<br>
	<input NAME="regexp" onKeyUp="UserList.set(this.value)">
	<input TYPE="button" onClick="UserList.set(this.form.regexp.value)" value="{$LNG.button_filter}">
	<input TYPE="button" onClick="UserList.reset();this.form.regexp.value=''" value="{$LNG.button_deselect}">
</td>
</tr><tr>
	<td>
	<input type="submit" value="{$LNG.bo_ban}" name="panel" style="width:20%;">&nbsp;
	<input TYPE="button" onClick="UserList.reset();this.form.regexp.value=''" value="{$LNG.button_reset}">
	</td>
</tr><tr>
	<td align="left">
		{$LNG.bo_total_users}<span style="color:lime">{$usercount}</span>
	</td>
</tr>
</table>
</form>
</td>
<td style="border:0px;width:50%;" class="transparent">
<br>
<form action="?page=banned&mode=unbanUser" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-100 my-5 mx-auto">
<tr>
	<th>{$LNG.bo_unban_player}</th>
</tr>
<tr>
	<td>
	<select name="unban_name" size="20" style="width:70%;">
	{$UserSelect.ListBan}
	</select>
	<br>
	<a href="?page=banned">{$LNG.bo_order_username}</a> &nbsp; <a href="?page=banned&amp;order2=id">{$LNG.bo_order_id}</a>
	<script TYPE="text/javascript">
		var UsersBan = new filterlist(document.getElementsByName('unban_name')[0]);
	</script>

	<br><br>
	<a href="javascript:UsersBan.set('^A')" title="{$LNG.bo_select_title} A">A</A>
	<a href="javascript:UsersBan.set('^B')" title="{$LNG.bo_select_title} B">B</A>
	<a href="javascript:UsersBan.set('^C')" title="{$LNG.bo_select_title} C">C</A>
	<a href="javascript:UsersBan.set('^D')" title="{$LNG.bo_select_title} D">D</A>

	<a href="javascript:UsersBan.set('^E')" title="{$LNG.bo_select_title} E">E</A>
	<a href="javascript:UsersBan.set('^F')" title="{$LNG.bo_select_title} F">F</A>
	<a href="javascript:UsersBan.set('^G')" title="{$LNG.bo_select_title} G">G</A>
	<a href="javascript:UsersBan.set('^H')" title="{$LNG.bo_select_title} H">H</A>
	<a href="javascript:UsersBan.set('^I')" title="{$LNG.bo_select_title} I">I</A>
	<a href="javascript:UsersBan.set('^J')" title="{$LNG.bo_select_title} J">J</A>
	<a href="javascript:UsersBan.set('^K')" title="{$LNG.bo_select_title} K">K</A>
	<a href="javascript:UsersBan.set('^L')" title="{$LNG.bo_select_title} L">L</A>
	<a href="javascript:UsersBan.set('^M')" title="{$LNG.bo_select_title} M">M</A>

	<a href="javascript:UsersBan.set('^N')" title="{$LNG.bo_select_title} N">N</A>
	<a href="javascript:UsersBan.set('^O')" title="{$LNG.bo_select_title} O">O</A>
	<a href="javascript:UsersBan.set('^P')" title="{$LNG.bo_select_title} P">P</A>
	<a href="javascript:UsersBan.set('^Q')" title="{$LNG.bo_select_title} Q">Q</A>
	<a href="javascript:UsersBan.set('^R')" title="{$LNG.bo_select_title} R">R</A>
	<a href="javascript:UsersBan.set('^S')" title="{$LNG.bo_select_title} S">S</A>
	<a href="javascript:UsersBan.set('^T')" title="{$LNG.bo_select_title} T">T</A>
	<a href="javascript:UsersBan.set('^U')" title="{$LNG.bo_select_title} U">U</A>
	<a href="javascript:UsersBan.set('^V')" title="{$LNG.bo_select_title} V">V</A>

	<a href="javascript:UsersBan.set('^W')" title="{$LNG.bo_select_title} W">W</A>
	<a href="javascript:UsersBan.set('^X')" title="{$LNG.bo_select_title} X">X</A>
	<a href="javascript:UsersBan.set('^Y')" title="{$LNG.bo_select_title} Y">Y</A>
	<a href="javascript:UsersBan.set('^Z')" title="{$LNG.bo_select_title} Z">Z</A>

	<br>
	<input NAME="regexp" onKeyUp="UsersBan.set(this.value)">
	<input TYPE="button" onClick="UsersBan.set(this.form.regexp.value)" value="{$LNG.button_filter}">
	<input TYPE="button" onClick="UsersBan.set(this.form.regexp.value)" value="{$LNG.button_deselect}">
</td>
</tr>
<tr>
	<td>
		<input value="{$LNG.bo_unban}" type="submit" >&nbsp;
	<input TYPE="button" onClick="UsersBan.reset();this.form.regexp.value=''" value="{$LNG.button_reset}"></td>
</tr><tr>
	<td align="left">
		{$LNG.bo_total_banneds}<span style="color:lime">{$bancount}</span>
	</td>
</tr>
</table>
</form>
</td>
</table>

{/block}
