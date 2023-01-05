{block name="title" prepend}{$LNG.siteTitleRegister}{/block}
{block name="content"}
<div id="registerFormWrapper">
<form id="registerForm" method="post" action="index.php?page=register" data-action="index.php?page=register">
<input type="hidden" value="send" name="mode">
<input type="hidden" value="{$externalAuth.account}" name="externalAuth[account]">
<input type="hidden" value="{$externalAuth.method}" name="externalAuth[method]">
<input type="hidden" value="{$referralData.id}" name="referralID">
	<div class="rowForm">
		<label for="universe">{$LNG.universe}</label>
		<select name="uni" id="universe" class="changeAction">{html_options options=$universeSelect selected=$UNI}</select>
	</div>
	{if !empty($error.uni)}<span class="error errorUni"></span>{/if}

	{if !empty($externalAuth.account)}
	{if $facebookEnable}
	<div class="rowForm">
		<label>{$LNG.registerFacebookAccount}</label>
		<span class="text fbname">{$accountName}</span>
	</div>
	{/if}
	{elseif empty($referralData.id)}
	{if $facebookEnable}
	<div class="rowForm">
		<label>{$LNG.registerFacebookAccount}</label>
		<a href="#" data-href="index.php?page=externalAuth&method=facebook" class="fb_login"><img src="styles/resource/images/facebook/fb-connect-large.png" alt=""></a>
	</div>
	{/if}
	{/if}
	<div class="rowForm">
		<label for="username">{$LNG.registerUsername}</label>
		<input type="text" class="input" name="username" id="username" maxlenght="32">
	</div>
	<div class="rowForm">
		{if !empty($error.username)}<span class="error errorUsername"></span>{/if}
		<span class="inputDesc">{$LNG.registerUsernameDesc}</span>
	</div>

	<div class="rowForm">
		<label for="password">{$LNG.registerPassword}</label>
		<input type="password" class="input" name="password" id="password">
	</div>
	<div class="rowForm">
		{if !empty($error.password)}<span class="error errorPassword"></span>{/if}
		<span class="inputDesc">{$registerPasswordDesc}</span>
	</div>
	<div class="rowForm">
		<label for="email">{$LNG.registerEmail}</label>
		<input type="email" class="input" name="email" id="email">
	</div>
	<div class="rowForm">
		{if !empty($error.email)}<span class="error errorEmail"></span>{/if}
		<span class="inputDesc">{$LNG.registerEmailDesc}</span>
	</div>

	{if count($languages) > 1}
	<div class="rowForm">
		<label for="language">{$LNG.registerLanguage}</label>
		<select name="lang" id="language">{html_options options=$languages selected=$lang}</select>
	</div>
	<div class="rowForm">
		{if !empty($error.language)}<span class="error errorLanguage"></span>{/if}
		<div class="clear"></div>
	</div>
	{/if}
	{if !empty($referralData.name)}
	<div class="rowForm">
		<label for="language">{$LNG.registerReferral}</label>
		<span class="text">{$referralData.name}</span>
	</div>
	<div class="rowForm">
		{if !empty($error.language)}<span class="error errorLanguage"></span>{/if}
		<div class="clear"></div>
	</div>
	{/if}
	{if $recaptchaEnable}
	<div class="rowForm">
		<label>{$LNG.registerCaptcha}</label>
	</div>
	<div class="rowForm" id="captchaRow">
			<div class="g-recaptcha" data-sitekey="{$recaptchaPublicKey}"></div>
			<div class="clear"></div>
	</div>
	{/if}
	<div class="rowForm">
		<label for="rules">{$LNG.registerRules}</label>
	</div>
	<div class="rowForm rowFormRules">
		<input type="checkbox" name="rules" id="rules" value="1">
		{if !empty($error.rules)}<span class="error errorRules"></span>{/if}
		<span class="inputDesc">{$registerRulesDesc}</span>
	</div>
	<div class="rowForm">
		<input type="submit" class="submitButton" value="{$LNG.buttonRegister}">
	</div>
</form>
{/block}
{block name="script" append}
<link rel="stylesheet" type="text/css" href="styles/resource/css/login/register.css?v={$REV}">
{if $recaptchaEnable}
<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl={$lang}"></script>
{/if}
<script type="text/javascript" src="scripts/login/register.js"></script>
{/block}

{block name="script" append}
<script type="text/javascript" src="./scripts/base/avoid_submit_on_refresh.js"></script>
{/block}
