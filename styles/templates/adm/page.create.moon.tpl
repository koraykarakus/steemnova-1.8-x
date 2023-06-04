{block name="content"}

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=create&mode=createMoon" method="post">

<div class="form-group">
	<span class="text-yellow fw-bold">{$LNG.po_add_moon}</span>
</div>
<div class="form-group">
	<label for="add_moon" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.input_id_planet}</label>
	<input id="add_moon" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="add_moon" value="" size="3">
</div>
<div class="form-group">
	<label for="name" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.mo_moon_name}</label>
	<input id="name" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" value="{$LNG.mo_moon}" name="name">
</div>
<div class="form-group">
	<label for="diameter" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.mo_diameter}</label>
	<input id="diameter" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="diameter" size="5" maxlength="5">
</div>
<div class="form-group d-flex justify-content-start">
	<label for="diameter_check" class="text-start my-1 cursor-pointer hover-underline d-flex">({$LNG.mo_moon_random})&nbsp;</label>
	<input id="diameter_check" type="checkbox" checked="checked" name="diameter_check">
</div>
<div class="form-group">
	<label for="field_max" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.mo_fields_avaibles}</label>
	<input id="field_max" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="field_max" size="5" maxlength="5" value="1">
</div>
<div class="form-group">
	<input class="btn btn-primary text-white my-2 w-100" type="submit" value="{$LNG.button_add}">
</div>
<div class="form-group d-flex justify-content-start">
	<a class="text-white" href="?page=create">{$LNG.new_creator_go_back}</a>
	<a class="text-white" href="?page=create&mode=moon">{$LNG.new_creator_refresh}</a>
</div>
</form>

{/block}
