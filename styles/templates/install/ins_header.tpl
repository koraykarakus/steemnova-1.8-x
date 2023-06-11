<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="{$lang}" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="{$lang}" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="{$lang}" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="{$lang}" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="{$lang}" class="no-js"> <!--<![endif]-->
<head>
	<title>{$title}</title>
	{if !empty($goto)}
	<meta http-equiv="refresh" content="{$gotoinsec};URL={$goto}">
	{/if}
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">

	{assign var="REV" value="1.0.0.1" nocache}


	<!-- Bootstrap 5 - No IE support -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

	<!-- -->

	<link rel="stylesheet" type="text/css" href="../styles/resource/css/base/boilerplate.css?v={$REV}">
	<link rel="stylesheet" type="text/css" href="../styles/resource/css/base/jquery.css?v={$REV}">
	<link rel="stylesheet" type="text/css" href="../styles/resource/css/base/jquery.fancybox.css?v={$REV}">
	<link rel="stylesheet" type="text/css" href="../styles/resource/css/base/validationEngine.jquery.css?v={$REV}">
	<script type="text/javascript" src="../scripts/base/jquery.js?v={$REV}"></script>
	<script type="text/javascript" src="../scripts/base/jquery.ui.js?v={$REV}"></script>
	<script type="text/javascript" src="../scripts/base/jquery.cookie.js?v={$REV}"></script>
	<script type="text/javascript" src="../scripts/base/jquery.fancybox.js?v={$REV}"></script>
	<script type="text/javascript" src="../scripts/base/jquery.validationEngine.js?v={$REV}"></script>
	<script type="text/javascript" src="../scripts/l18n/validationEngine/jquery.validationEngine-{$lang}.js?v={$REV}"></script>


</head>
<body style="background:#000;" id="step{if isset($smarty.get.step)}{$smarty.get.step|htmlspecialchars|default:'intro'}{/if}">
<div id="tooltip" class="tip"></div>
<table class="table table-dark w-50 mx-auto my-5" width="960">
<tr>
	<th colspan="3">{$header}</th>
</tr>
