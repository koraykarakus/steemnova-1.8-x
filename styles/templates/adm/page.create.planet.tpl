{block name="content"}

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=create&mode=createPlanet" method="post">
<div class="form-group">
	<span class="text-yellow fw-bold">{$LNG.po_add_planet}</span>
</div>
<div class="form-group">
	<label for="id" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.input_id_user}</label>
	<input id="id" name="id" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" size="4">
</div>
<div class="form-group d-flex flex-column">
	<label for="galaxy" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.new_creator_coor}</label>
	<div class="d-flex align-items-center">
		<input id="galaxy" style="width:60px;" name="galaxy" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" size="3" maxlength="1" title="{$LNG.po_galaxy}">
		 <span>:</span>
		<input style="width:60px;" name="system" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" size="3" maxlength="3"  title="{$LNG.po_system}">
		<span>:</span>
		<input style="width:60px;" name="planet" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" size="3" maxlength="2"  title="{$LNG.po_planet}">
	</div>
</div>
<div class="form-group">
	<label for="name" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.po_name_planet}</label>
	<input id="name" name="name" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" size="15" maxlength="25" value="{$LNG.po_colony}">
</div>
<div class="form-group">
	<label for="field_max" class="text-start my-1 cursor-pointer hover-underline d-flex w-100">{$LNG.po_fields_max}</label>
	<input id="field_max" name="field_max" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" size="6" maxlength="10">
</div>
<div class="form-group">
	<input class="btn btn-primary text-white my-2 w-100" type="submit" value="{$LNG.button_add}">
</div>
<div class="form-group d-flex justify-content-start">
	<a class="text-white" href="?page=create">{$LNG.new_creator_go_back}</a>&nbsp;
	<a class="text-white" href="?page=create&amp;mode=planet">{$LNG.new_creator_refresh}</a>
</div>
</form>

{/block}
