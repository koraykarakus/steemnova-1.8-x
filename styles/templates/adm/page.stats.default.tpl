{block name="content"}

<form  class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" method="post" action="?page=stats&mode=saveSettings">
	<div class="form-group">
		<span class="text-yellow fs-12 fw-bold">{$cs_title}</span>
	</div>
	<div class="form-group d-flex flex-column">
		<label for="stat_settings" class="text-start my-1 cursor-pointer hover-underline text-white">{$cs_point_per_resources_used} ({$cs_resources})</label>
		<input id="stat_settings" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="stat_settings" value="{$stat_settings}">
	</div>
	<div class="form-group d-flex flex-column">
		<label for="stat" class="text-start my-1 cursor-pointer hover-underline text-white">{$cs_points_to_zero}</label>
		<select id="stat" class="form-select py-1 bg-dark text-white my-1 border border-secondary" name="stat">
			{foreach $Selector as $key => $optionText}
			<option value="{$key}" {if $key == $stat}selected{/if}>{$optionText}</option>
			{/foreach}
		</select>
	</div>
	<div class="form-group d-flex flex-column">
		<label for="stat_level" class="text-start my-1 cursor-pointer hover-underline text-white">{$cs_access_lvl}</label>
		<input id="stat_level" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="stat_level" value="{$stat_level}">
	</div>
	<div class="form-group">
		<input class="btn btn-primary text-white my-1 w-100" type="submit" value="{$cs_save_changes}">
	</div>
</form>

{/block}
