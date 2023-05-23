{block name="content"}

<form action="?page=chat&mode=saveSettings" method="post">
<input type="hidden" name="opt_save" value="1">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="2">{$se_server_parameters}</th>
	<th>(?)</th>
</tr><tr>
	<td>{$ch_channelname}</td>
	<td><input name="chat_channelname" value="{$chat_channelname}" type="text"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$ch_botname}</td>
	<td><input name="chat_botname" value="{$chat_botname}" type="text"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$ch_nickchange}</td>
	<td><input name="chat_nickchange"{if $chat_nickchange == '1'} checked="checked"{/if} type="checkbox"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$ch_logmessage}</td>
	<td><input name="chat_logmessage"{if $chat_logmessage == '1'} checked="checked"{/if} type="checkbox"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$ch_allowmes}</td>
	<td><input name="chat_allowmes"{if $chat_allowmes == '1'} checked="checked"{/if} type="checkbox"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$ch_allowchan}</td>
	<td><input name="chat_allowchan"{if $chat_allowchan == '1'} checked="checked"{/if} type="checkbox"></td>
	<td>&nbsp;</td>
</tr><tr>
	<td>{$ch_closed}</td>
	<td><input name="chat_closed"{if $chat_closed == '1'} checked="checked"{/if} type="checkbox"></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan="3"><input value="{$se_save_parameters}" type="submit"></td>
</tr>
</table>
</form>

{/block}