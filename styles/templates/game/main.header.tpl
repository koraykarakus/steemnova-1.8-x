<!DOCTYPE html>
<html lang="{$lang}" class="no-js">

	<head>
		<title>{block name="title"} - {$uni_name} - {$game_name}{/block}</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		{if !empty($goto)}
			<meta http-equiv="refresh" content="{$gotoinsec};URL={$goto}">
		{/if}
		{assign var="REV" value="1.0.0.43" nocache}
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<!-- jquery.css required for progress bar -->
		<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.css?v={$REV}">
		<link rel="stylesheet" type="text/css" href="{$dpath}formate.css?v={$REV}">
		<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
		<script type="text/javascript">
			var ServerTimezoneOffset = {$Offset};
			var serverTime 	= new Date({$date.0}, {$date.1 - 1}, {$date.2}, {$date.3}, {$date.4}, {$date.5});
			var startTime = serverTime.getTime();
			var localTime = serverTime;
			var localTS = startTime;
			var Gamename = document.title;
			var Ready		= "{$LNG.ready}";
			var Skin		= "{$dpath}";
			var Lang		= "{$lang}";
			var head_info	= "{$LNG.fcm_info}";
			var auth		= {$authlevel|default:'0'};
			var days 		= {$LNG.week_day|json|default:'[]'}
			var months 		= {$LNG.months|json|default:'[]'} ;
			var tdformat	= "{$LNG.js_tdformat}";
			var queryString	= "{$queryString|escape:'javascript'}";
			var isPlayerCardActive	= "{$isPlayerCardActive|json}";
			var relativeTime = Math.floor(Date.now() / 1000);
			var attackListenTime = {$attackListenTime};

			setInterval(function() {
				if (relativeTime < Math.floor(Date.now() / 1000)) {
					serverTime.setSeconds(serverTime.getSeconds() + 1);
					relativeTime++;
				}
			}, 1);
		</script>

		<script type="text/javascript" src="./scripts/base/jquery.js?v={$REV}"></script>
		<script type="text/javascript" src="./scripts/base/jquery.ui.js?v={$REV}"></script>
		<script type="text/javascript" src="./scripts/base/jquery.cookie.js?v={$REV}"></script>
		<script type="text/javascript" src="./scripts/base/jquery.validationEngine.js?v={$REV}"></script>
		<script type="text/javascript"
			src="./scripts/l18n/validationEngine/jquery.validationEngine-{$lang}.js?v={$REV}"></script>
		<script type="text/javascript" src="./scripts/base/tooltip.js?v={$REV}"></script>
		<script type="text/javascript" src="./scripts/game/base.js?v={$REV}"></script>
		{foreach item=scriptname from=$scripts}
			<script type="text/javascript" src="./scripts/game/{$scriptname}.js?v={$REV}"></script>
		{/foreach}
		{if isModuleAvailable($smarty.const.MODULE_ATTACK_ALERT)}
			<script type="text/javascript">
				var attackListenTime = {$attackListenTime};
			</script>
			<script type="text/javascript" src="./scripts/game/attackAlert.js?v={$REV}"></script>
		{/if}
		<!-- fancybox 5.0 -->
		<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
		<script src="scripts/game/overview.js"></script>
	</head>

	<body id="{if isset($smarty.get.page)}{$smarty.get.page|htmlspecialchars|default:'overview'}{/if}"
		class="{$bodyclass}">
<div id="tooltipNotify" class="tip"></div>