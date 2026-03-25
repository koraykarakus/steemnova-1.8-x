{block name="title" prepend}{$LNG.lm_overview}{/block}
{block name="content"}

	<script>
		function changeContent(type) {

			if (type == 1) {
				$('#rename_planet').removeClass('hidden');
				$('#abondon_planet').addClass('hidden');
			} else {
				$('#rename_planet').addClass('hidden');
				$('#abondon_planet').removeClass('hidden');
			}

		}

		function setActive(el) {
			$('.menu-btn').removeClass('active');
			$(el).addClass('active');
		}
	</script>

	<div>
		<div class="planet_menu">
			<div class="menu_wrapper">
				<button class="menu-btn active" onclick="changeContent(1); setActive(this);">
					{$LNG.ov_planet_rename}
				</button>
				<button class="menu-btn" onclick="changeContent(2); setActive(this);">
					{$LNG.ov_delete_planet}
				</button>
			</div>
		</div>
		<div id="rename_planet">
			<div class="actions_card">
				<form>
					<span class="rename-title">
						{$LNG.ov_rename_label}
					</span>
					<input type="text" name="name" id="name" placeholder="" maxlength="20"
						autocomplete="off">
					<input class="button-blue" type="button" onclick="checkrename();" class="button-blue"
						value="{$LNG.ov_planet_rename}">
				</form>
			</div>
		</div>
		<div id="abondon_planet" class="hidden">
			<div class="actions_card">
				<form>
					<div>
						{$LNG.ov_security_request}
					</div>
					<div>
						{$ov_security_confirm}
					</div>
					<input id="password" type="password" name="password"
						placeholder="{$LNG.ov_ac_password}" autocomplete="new-password">
					<input class="button-blue" type="button" onclick="checkcancel();"
						value="{$LNG.ov_delete_planet}">
				</form>
			</div>
		</div>
	</div>

	{block name="script" append}
		<script src="scripts/game/overview.actions.js?v={$REV}"></script>
	{/block}
{/block}