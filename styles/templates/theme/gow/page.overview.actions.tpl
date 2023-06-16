{block name="title" prepend}{$LNG.lm_overview}{/block}
{block name="content"}
<div id="tabs" class="fs-12">
	<ul>
		<li><a href="#tabs-1">{$LNG.ov_planet_rename}</a></li>
		<li><a href="#tabs-2">{$LNG.ov_delete_planet}</a></li>
	</ul>
	<div id="tabs-1">
		<form>
			<div class="form-group py-2 my-2 d-flex justify-content-center align-items-center">
				<input class="text-center form-control p-0 m-0 w-50" placeholder="{$LNG.ov_rename_label}" type="text" name="name" id="name" size="25" maxlength="20" autocomplete="off">
			</div>
			<div class="form-group py-2 my-2 d-flex justify-content-center">
				<input class="mx-auto button-blue" type="button" onclick="checkrename();" value="{$LNG.ov_planet_rename}">
			</div>
		</form>
	</div>
	<div id="tabs-2">
		<form>
			<div class="form-group d-flex justify-content-center align-items-center py-2 my-2">
				{$LNG.ov_security_request}
			</div>
			<div class="form-group d-flex justify-content-center align-items-center py-2 my-2">
				{$ov_security_confirm}
			</div>
			<div class="form-group d-flex justify-content-center align-items-center py-2 my-2">
				<input id="password" class="text-center form-control p-0 m-0 w-50" type="password" name="password" placeholder="{$LNG.ov_ac_password}" autocomplete="new-password">
			</div>
			<div class="form-group d-flex justify-content-center align-items-center py-2 my-2">
				<input class="mx-auto button-blue" type="button" onclick="checkcancel()" value="{$LNG.ov_delete_planet}">
			</div>
		</form>
	</div>
</div>
{/block}
{block name="script" append}
    <script src="scripts/game/overview.actions.js?v={$REV}"></script>
{/block}
