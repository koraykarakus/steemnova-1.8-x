{block name="title" prepend}{$LNG.siteTitleIndex}{/block}
{block name="content"}

	<script type="text/javascript">
		// this is the id of the form
		function loginSubmit(activeRecaptcha, use_recaptcha_on_login) {
			var recaptchaResponse = false;


			if (activeRecaptcha == 1 && use_recaptcha_on_login == 1) {
				recaptchaResponse = grecaptcha.getResponse();
			}

			$.ajax({
				type: "POST",
				url: 'index.php?page=login&mode=validate&ajax=1',
				data: {
					userEmail: $("#userEmail").val(),
					password: $("#password").val(),
					g_recaptcha_response: recaptchaResponse,
					csrfToken: $('#csrfToken').val(),
					remember_me: $('#remember_me').is(':checked'),
					universe: $('#universe option:selected').val(),
					rememberedTokenValidator: $('#rememberedTokenValidator').val(),
					rememberedTokenSelector: $('#rememberedTokenSelector').val(),
					rememberedEmail: $('#rememberedEmail').val(),
				},
				success: function(data) {

					var dataParsed = jQuery.parseJSON(data);
					$('.alert').remove();
					console.log(dataParsed);


					if (dataParsed.status == 'fail') {
						if (activeRecaptcha == 1 &&
							use_recaptcha_on_login == 1) {
							grecaptcha.reset();
						}

						$.each(dataParsed, function(typeError, errorText) {

							if (typeError == 'status') {
								return;
							}
							$('#loginButton').before("<span class='alert alert-danger fs-6 py-1 my-1'>" +
								errorText + "</span>")

						});

					} else if (dataParsed.status == 'redirect') {
						location.href = "game.php";
					}

				},
				error: function(xhr, status, error) {
					console.log(status, error, xhr.responseText);
				}

			});


		}
	</script>

	<h1 class="fs-3 my-4 w-100">{sprintf($LNG.loginWelcome, $gameName)}</h1>
	<p style="max-width:600px;" class="fs-6 my-2 w-100 mx-auto">{sprintf($LNG.loginServerDesc, $gameName)}</p>
	<div style="max-width:300px;" class="form-group contentbox container rounded mx-auto">

		<h1 class="fs-6">{$LNG.loginHeader}</h1>
		<form id="login" action="" method="post">
			<input id="csrfToken" type="hidden" name="csrfToken" value="{$csrf_token}">
			<input id="rememberedEmail" type="hidden" name="rememberedEmail" value="{$mem_email}">
			<input id="rememberedTokenSelector" type="hidden" name="rememberedTokenSelector" value="{$mem_token_sel}">
			<input id="rememberedTokenValidator" type="hidden" name="rememberedTokenValidator" value="{$mem_token_valid}">
			<div class="d-flex flex-column form-group">
				<select class="form-select my-2 w-100" name="uni" id="universe">
					{foreach $universeSelect as $universeID => $currentUniverse}
						<option class="fs-6" {if $currentUniverse == $mem_uni_id}selected{/if} value="{$universeID}">
							{$currentUniverse}</option>
					{/foreach}
				</select>
				<input class="form-control fs-6 my-2 w-100" id="userEmail" type="text" name="userEmail"
					placeholder="{$LNG.login_email}" value="{if !empty($mem_email) && $mem_email}{$mem_email}{/if}">
				<input class="form-control fs-6 my-2 w-100" id="password" type="password" name="password"
					placeholder="{$LNG.loginPassword}" value="{if $mem_pass}password{/if}">
				{if $recaptchaEnable && $use_recaptcha_on_login}
					<div style="overflow:hidden;"
						class="g-recaptcha form-group w-100 fs-6 my-2 mx-auto d-flex justify-content-start"
						data-sitekey="{$recaptchaPublicKey}"></div>
				{/if}

				<div class="form-group d-flex align-items-center justify-content-start my-2">
					<input id="remember_me" type="checkbox" name="remember_me" {if $mem_pass}checked{/if} value="">
					<span class="fs-6 px-2">Remember me</span>
				</div>

				<button id="loginButton" class="hover-bg-color-grey btn bg-dark text-white w-100" type="button"
					onclick="loginSubmit(activeRecaptcha = '{$recaptchaEnable}', use_recaptcha_on_login = '{$use_recaptcha_on_login}');">{$LNG.loginButton}
				</button>

			</div>

		</form>


		<a class="hover-bg-color-grey btn btn-block w-100 bg-dark text-white my-2 fs-6"
			href="index.php?page=register">{$LNG.buttonRegister}</a>

		{if isModuleAvailable(MODULE_AUTH_GOOGLE)}
			<a href="index.php?page=googleAuth&mode=show"
				class="btn btn-light my-2 d-flex align-items-center border text-dark w-100 fs-6">

				<img src="https://developers.google.com/identity/images/g-logo.png" alt="Google"
					style="width:18px; height:18px; margin-right:8px;">
				<span>{$LNG.auth_google_btn_text}</span>
			</a>
		{/if}

		<span class="fs-6">{$loginInfo}</span>

		{if $mailEnable}
			<a class="hover-bg-color-grey btn btn-block w-100 bg-dark text-white my-2 fs-6"
				href="index.php?page=lostPassword">{$LNG.buttonLostPassword}</a>
		{/if}

	</div>

{/block}

{if $recaptchaEnable && $use_recaptcha_on_login}
	{block name="script" append}
		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=tr"></script>
	{/block}
{/if}

{block name="script" append}
	<script type="text/javascript" src="./scripts/base/avoid_submit_on_refresh.js"></script>
{/block}