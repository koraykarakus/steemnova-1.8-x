{block name="content"}

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=disclamer&mode=saveSettings" method="post">
	<div class="form-group">
		<span>{$LNG.se_server_parameters}</span>
		<span>(?)</span>
	</div>
	<div class="form-group d-flex flex-column">
		<label for="disclaimerAddress" class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerAddress}</label>
		<textarea id="disclaimerAddress"class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="disclaimerAddress" cols="80" rows="5">{$disclaimerAddress}</textarea>
	</div>
	<div class="form-group d-flex flex-column">
		<label for="disclaimerPhone" class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerPhone}</label>
		<input id="disclaimerPhone" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="disclaimerPhone" size="40" value="{$disclaimerPhone}" type="text">
	</div>
	<div class="form-group d-flex flex-column">
		<label for="disclaimerMail" class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerMail}</label>
		<input id="disclaimerMail" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="disclaimerMail" size="40" value="{$disclaimerMail}" type="text">
	</div>
	<div class="form-group d-flex flex-column">
		<label for="disclaimerNotice" class="text-start my-1 cursor-pointer hover-underline text-white">{$LNG.se_disclaimerNotice}</label>
		<textarea id="disclaimerNotice" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="disclaimerNotice" cols="80" rows="5">{$disclaimerNotice}</textarea>
	</div>
	<div class="form-group d-flex flex-column">
		<input class="btn btn-primary text-white my-1" value="{$LNG.se_save_parameters}" type="submit">
	</div>

</form>

{/block}
