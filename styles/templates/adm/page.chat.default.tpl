{block name="content"}

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=chat&mode=saveSettings" method="post">
	<input type="hidden" name="opt_save" value="1">
	<div class="form-gorup d-flex justify-content-between">
		<span>{$se_server_parameters}</span>
	</div>
	<div class="form-gorup d-flex flex-column my-1 p-2 ">
		<label class="text-start my-1 cursor-pointer hover-underline" for="chat_channelname">{$ch_channelname}</label>
		<input id="chat_channelname" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="chat_channelname" value="{$chat_channelname}" type="text">
	</div>
	<div class="form-gorup d-flex flex-column my-1 p-2 ">
		<label class="text-start my-1 cursor-pointer hover-underline" for="chat_botname">{$ch_botname}</label>
		<input id="chat_botname" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="chat_botname" value="{$chat_botname}" type="text">
	</div>
	<div class="form-gorup d-flex my-1 p-2 ">
		<label class="text-start my-1 cursor-pointer hover-underline" for="chat_nickchange">{$ch_nickchange}</label>
		<input id="chat_nickchange" class="mx-2" name="chat_nickchange"{if $chat_nickchange == '1'} checked="checked"{/if} type="checkbox">
	</div>
	<div class="form-gorup d-flex my-1 p-2 ">
		<label class="text-start my-1 cursor-pointer hover-underline" for="chat_logmessage">{$ch_logmessage}</label>
		<input id="chat_logmessage" class="mx-2" name="chat_logmessage"{if $chat_logmessage == '1'} checked="checked"{/if} type="checkbox">
	</div>
	<div class="form-gorup d-flex my-1 p-2 ">
		<label class="text-start my-1 cursor-pointer hover-underline" for="chat_allowmes">{$ch_allowmes}</label>
		<input id="chat_allowmes" class="mx-2" name="chat_allowmes"{if $chat_allowmes == '1'} checked="checked"{/if} type="checkbox">
	</div>
	<div class="form-gorup d-flex my-1 p-2 ">
		<label class="text-start my-1 cursor-pointer hover-underline" for="chat_allowchan">{$ch_allowchan}</label>
		<input id="chat_allowchan" class="mx-2" name="chat_allowchan"{if $chat_allowchan == '1'} checked="checked"{/if} type="checkbox">
	</div>
	<div class="form-gorup d-flex my-1 p-2 ">
		<label class="text-start my-1 cursor-pointer hover-underline" for="chat_closed">{$ch_closed}</label>
		<input id="chat_closed" class="mx-2" name="chat_closed"{if $chat_closed == '1'} checked="checked"{/if} type="checkbox">
	</div>
	<div class="form-gorup d-flex flex-column my-1 p-2 ">
		<input class="btn btn-primary text-white" value="{$se_save_parameters}" type="submit">
	</div>
</form>

{/block}
