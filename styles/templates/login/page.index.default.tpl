{block name="title" prepend}{$LNG.siteTitleIndex}{/block}
{block name="content"}
<section>
	<h1>{sprintf($LNG.loginWelcome, $gameName)}</h1>
	<p class="desc">{sprintf($LNG.loginServerDesc, $gameName)}</p>
	<p class="desc"><ul id="desc_list">{foreach $LNG.gameDescription as $info}<li>{$info}</li>{/foreach}</ul></p>
	</p>
</section>
<section>
	<div class="contentbox">

				<h1>{$LNG.loginHeader}</h1>
				<form id="login" name="login" action="index.php?page=login&mode=validate" data-action="index.php?page=login" method="post">
					<div class="row">
						<select name="uni" id="universe" class="changeAction">{html_options options=$universeSelect selected=$UNI}</select>
						<input name="userEmail" id="userEmail" type="text" placeholder="{$LNG.login_email}">

						{if isset($error)}
							{if isset($error['email'])}
								{foreach $error['email'] as $emailError}
									<br>
									<span class="errorText colorError specialFont" style="margin:5px auto;color:red;">{$emailError}</span>
								{/foreach}
							{/if}
						{/if}

						<input name="password" id="password" type="password" placeholder="{$LNG.loginPassword}">

						{if isset($error)}
							{if isset($error['password'])}
								{foreach $error['password'] as $passwordError}
									<br>
									<span class="errorText colorError specialFont" style="margin:5px auto;color:red;">{$passwordError}</span>
								{/foreach}
							{/if}
						{/if}

						{if $recaptchaEnable}
								<div style="margin:0 auto;" class="g-recaptcha" data-sitekey="{$recaptchaPublicKey}"></div>
								{if isset($error)}
									{if isset($error['recaptcha'])}
										{foreach $error['recaptcha'] as $recaptchaError}
											<span class="errorText colorError specialFont" style="margin:5px auto;color:red;">{$recaptchaError}</span>
										{/foreach}
									{/if}
								{/if}
						{/if}

						<input type="submit" value="{$LNG.loginButton}">

					</div>

				</form>
				{if $facebookEnable}<a href="#" data-href="index.php?page=externalAuth&method=facebook" class="fb_login"><img src="styles/resource/images/facebook/fb-connect-large.png" alt=""></a>{/if}

				<a href="index.php?page=register"><input value="{$LNG.buttonRegister}"></a>
				<br><span class="small">{$loginInfo}</span>

	</div>
</section>
<section>
<div class="button-box">
		<div class="button-box-inner">
			<div class="button-important">
				<a href="index.php?page=register">
					<span class="button-left"></span>
					<span class="button-center">{$LNG.buttonRegister}</span>
					<span class="button-right"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="button-box">
		<div class="button-box-inner">
			{if $mailEnable}
			<div class="button multi">
				<a href="index.php?page=lostPassword">
					<span class="button-left"></span>
					<span class="button-center">{$LNG.buttonLostPassword}</span>
					<span class="button-right"></span>
				</a>
			</div>
			<div class="button multi">
			{else}
			<div class="button">
			{/if}
				<a href="index.php?page=screens">
					<span class="button-left"></span>
					<span class="button-center">{$LNG.buttonScreenshot}</span>
					<span class="button-right"></span>
				</a>
			</div>
		</div>
	</div>
</section>
{/block}

{if $recaptchaEnable}
{block name="script" append}
<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=tr"></script>
{/block}
{/if}
