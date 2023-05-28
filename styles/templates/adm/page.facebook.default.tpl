{block name="content"}

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=facebook&mode=saveSettings" method="post">

	<div class="form-group">
		<span class="text-center">{$LNG.fb_settings}</span>
	</div>
	<div class="form-group">
		<span class="text-center">{$fb_info}</span>
	</div>
	<div class="form-group">
		<span>{$fb_curl_info}</span>
	</div>
	<div class="form-group d-flex justify-content-start">
		<label for="fb_on"  class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.fb_active}</label>
		<input id="fb_on" class="mx-2" name="fb_on"{if $fb_on == 1 && $fb_curl == 1} checked="checked"{/if} type="checkbox" {if $fb_curl == 0}disabled{/if}>
	</div>
	<div class="form-group d-flex flex-column">
		<label for="fb_apikey"  class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.fb_api_key}</label>
		<input id="fb_apikey"  class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="fb_apikey" size="40" value="{$fb_apikey}" type="text" {if $fb_curl == 0}disabled{/if}>
	</div>
	<div class="form-group d-flex flex-column">
		<label for="fb_skey" class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.fb_secrectkey}</label>
		<input id="fb_skey" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="fb_skey" size="40" value="{$fb_skey}" type="text" {if $fb_curl == 0}disabled{/if}>
	</div>
	<div class="form-group d-flex justify-content-between">
		<input class="btn btn-primary text-white my-1" value="{$LNG.se_save_parameters}" type="submit" {if $fb_curl == 0}disabled{/if}>
	</div>

</form>

{/block}
