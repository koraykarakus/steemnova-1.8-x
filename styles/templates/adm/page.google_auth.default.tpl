{block name="content"}

	<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=googleAuth&mode=saveSettings" method="post">

		<div class="form-group">
			<span class="text-center">{$LNG.google_auth_settings}</span>
		</div>
		<div class="form-group">
			<span class="text-center">{$LNG.google_auth_info}</span>
		</div>
		<div class="form-group d-flex flex-column">
			<label for="client_id" class="text-start my-1 cursor-pointer hover-underline text-white">
				{$LNG.google_auth_client_id}
			</label>
			<input id="client_id" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="client_id"
				size="40" value="{$client_id}" type="text">
		</div>
		<div class="form-group d-flex flex-column">
			<label for="client_secret"
				class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.google_auth_client_secret}</label>
			<input id="client_secret" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
				name="client_secret" size="40" value="{$client_secret}" type="text">
		</div>
		<div class="form-group d-flex flex-column">
			<label for="callback_url"
				class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.google_auth_callback_url}</label>
			<input id="callback_url" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
				name="callback_url" size="40" value="{$callback_url}" type="text">
		</div>
		<div class="form-group d-flex justify-content-between">
			<input class="btn btn-primary text-white my-1" value="{$LNG.se_save_parameters}" type="submit">
		</div>

	</form>

{/block}