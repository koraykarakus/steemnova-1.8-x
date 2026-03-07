{block name="content"}

	<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=disclaimer&mode=saveSettings" method="post">
		<div class="form-group">
			<span>{$LNG.se_server_parameters}</span>
			<span>(?)</span>
		</div>
		<div class="form-group d-flex flex-column">
			<label for="disclaimer_address"
				class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerAddress}</label>
			<textarea id="disclaimer_address" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
				name="disclaimer_address" cols="80" rows="5">{$disclaimer_address}</textarea>
		</div>
		<div class="form-group d-flex flex-column">
			<label for="disclaimer_phone"
				class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerPhone}</label>
			<input id="disclaimer_phone" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
				name="disclaimer_phone" size="40" value="{$disclaimer_phone}" type="text">
		</div>
		<div class="form-group d-flex flex-column">
			<label for="disclaimer_mail"
				class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerMail}</label>
			<input id="disclaimer_mail" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
				name="disclaimer_mail" size="40" value="{$disclaimer_mail}" type="text">
		</div>
		<div class="form-group d-flex flex-column">
			<label for="disclaimer_notice"
				class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerNotice}</label>
			<textarea id="disclaimer_notice" class="form-control py-1 bg-dark text-white my-1 border border-secondary"
				name="disclaimer_notice" cols="80" rows="5">{$disclaimer_notice}</textarea>
		</div>
		<div class="form-group d-flex flex-column">
			<input class="btn btn-primary text-white my-1" value="{$LNG.se_save_parameters}" type="submit">
		</div>

	</form>

{/block}