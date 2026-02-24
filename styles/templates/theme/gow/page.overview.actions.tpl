{block name="title" prepend}{$LNG.lm_overview}{/block}
{block name="content"}

	<script>
		function changeContent(type) {

			if (type == 1) {
				$('#rename_planet').removeClass('d-none').addClass('d-flex');
				$('#abondon_planet').removeClass('d-flex').addClass('d-none');
			} else {
				$('#rename_planet').removeClass('d-flex').addClass('d-none');
				$('#abondon_planet').removeClass('d-none').addClass('d-flex');
			}

		}

		function setActive(el) {
			$('.menu-btn').removeClass('active');
			$(el).addClass('active');
		}
	</script>

	<div class="d-flex flex-column">
		<div class="planet-menu d-flex justify-content-center align-items-center">
			<div class="menu-wrapper">

				<button class="menu-btn active" onclick="changeContent(1); setActive(this);">
					{$LNG.ov_planet_rename}
				</button>

				<button class="menu-btn" onclick="changeContent(2); setActive(this);">
					{$LNG.ov_delete_planet}
				</button>

			</div>
		</div>
		<div id="rename_planet" class="d-flex align-items-center justify-content-center my-5">
			<div class="actions-card d-flex align-items-center">
				<form class="w-100 text-center">
					<h6 class="rename-title mb-3">
						{$LNG.ov_rename_label}
					</h6>
					<input type="text" name="name" id="name" class="text-center my-2 w-100" placeholder="" maxlength="20"
						autocomplete="off">
					<input class="button-blue my-2 w-100" type="button" onclick="checkrename();" class="mx-auto button-blue"
						value="{$LNG.ov_planet_rename}">
				</form>
			</div>
		</div>
		<div id="abondon_planet" class="d-none text-white align-items-center justify-content-center my-5">
			<div class="actions-card d-flex align-items-center">
				<form class="d-flex flex-column w-100 align-items-center h-100 justify-content-center">
					<div class="form-group d-flex justify-content-center align-items-center my-2">
						{$LNG.ov_security_request}
					</div>
					<div class="form-group d-flex justify-content-center align-items-center my-2">
						{$ov_security_confirm}
					</div>
					<input id="password" class="text-center my-2 w-100" type="password" name="password"
						placeholder="{$LNG.ov_ac_password}" autocomplete="new-password">
					<input class="button-blue my-2 w-100" type="button" onclick="checkcancel()"
						value="{$LNG.ov_delete_planet}">
				</form>
			</div>

		</div>
	</div>

	<style>
		.actions-card {
			width: 320px;
			padding: 30px 17px;
			border-radius: 12px;
			background: rgba(255, 255, 255, 0.05);
			backdrop-filter: blur(10px);
			box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
			border: 1px solid rgba(255, 255, 255, 0.08);
		}

		.actions-card input {
			height: 38px;
			border-radius: 8px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			background: rgba(255, 255, 255, 0.08);
			color: #fff;
		}

		.actions-card input:focus {
			border-color: #3b82f6;
			box-shadow: 0 0 0 0.15rem rgba(59, 130, 246, 0.25);
			background: rgba(255, 255, 255, 0.12);
		}

		.planet-menu {
			height: 60px;
			background: linear-gradient(145deg, #0f172a, #1e293b);
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
		}

		.menu-wrapper {
			display: flex;
			gap: 10px;
			background: rgba(255, 255, 255, 0.04);
			padding: 6px;
			border-radius: 10px;
		}

		.menu-btn {
			min-width: 140px;
			padding: 8px 18px;
			border: none;
			border-radius: 8px;
			background: transparent;
			color: #cbd5e1;
			font-weight: 500;
			transition: all 0.2s ease;
		}

		.menu-btn:hover {
			background: rgba(59, 130, 246, 0.15);
			color: #fff;
		}

		.menu-btn.active {
			background: #3b82f6;
			color: #fff;
			box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
		}
	</style>

	{block name="script" append}
		<script src="scripts/game/overview.actions.js?v={$REV}"></script>
	{/block}
{/block}