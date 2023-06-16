<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="{$lang}" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="{$lang}" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="{$lang}" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="{$lang}" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="{$lang}" class="no-js"> <!--<![endif]-->
<head>
	<title>{block name="title"} - {$uni_name} - {$game_name}{/block}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

	<!-- Bootstrap 5 - No IE support -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">




	{if !empty($goto)}
	<meta http-equiv="refresh" content="{$gotoinsec};URL={$goto}">
	{/if}
	{assign var="REV" value="1.0.0.153" nocache}

	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/boilerplate.css?v={$REV}">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.css?v={$REV}">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/validationEngine.jquery.css?v={$REV}">
	<link rel="stylesheet" type="text/css" href="{$dpath}formate.css?v={$REV}">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.12/css/all.css">
	<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
	<script type="text/javascript">
	var ServerTimezoneOffset = {$Offset};
	var serverTime 	= new Date({$date.0}, {$date.1 - 1}, {$date.2}, {$date.3}, {$date.4}, {$date.5});
	var startTime	= serverTime.getTime();
	var localTime 	= serverTime;
	var localTS 	= startTime;
	var Gamename	= document.title;
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

	setInterval(function() {
		if(relativeTime < Math.floor(Date.now() / 1000)) {
		serverTime.setSeconds(serverTime.getSeconds()+1);
		relativeTime++;
		}
	}, 1);
	</script>
	<script type="text/javascript" src="./scripts/base/jquery.js?v={$REV}"></script>
	<script type="text/javascript" src="./scripts/base/jquery.ui.js?v={$REV}"></script>
	<script type="text/javascript" src="./scripts/base/jquery.cookie.js?v={$REV}"></script>
	<script type="text/javascript" src="./scripts/base/jquery.validationEngine.js?v={$REV}"></script>
	<script type="text/javascript" src="./scripts/l18n/validationEngine/jquery.validationEngine-{$lang}.js?v={$REV}"></script>
	<script type="text/javascript" src="./scripts/base/tooltip.js?v={$REV}"></script>
	<script type="text/javascript" src="./scripts/game/base.js?v={$REV}"></script>
	{foreach item=scriptname from=$scripts}
	<script type="text/javascript" src="./scripts/game/{$scriptname}.js?v={$REV}"></script>
	{/foreach}
	{block name="script"}
	<!-- fancybox 5.0 -->
	<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
  <script>
   $(function(){
            		$("#btn1").on('click',function() {
                $(".infoso").hide();
                $(".infos").show();
								$("#btn2, #btn3").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 		$(function(){
            $("#btn2").on('click',function() {
                $(".infos").toggle();
                $(".infoso").show();
								$("#btn1, #btn3").removeClass("selected");
								$(this).addClass("selected");
            });
        });
 $(function(){
            $("#btn3").on('click',function() {
                $(".infos").show();
                $(".infoso").show();
								$("#btn2, #btn1").removeClass("selected");
								$(this).addClass("selected");
            });
        });

         $(function(){
            		$("#gl1").on('click',function() {
              	$(".planetb").hide();
                $(".planetb1").show();
								$("#gl2, #gl3").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 $(function(){
            		$("#gl2").on('click',function() {
                $(".planetb1").toggle();
                $(".planetb").show();
								$("#gl1, #gl3").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 $(function(){
            		$("#gl3").on('click',function() {
                $(".planetb").show();
                $(".planetb1").show();
								$("#gl2, #gl1").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 $(function(){
            		$("#lab1").on('click',function() {
                $(".infos").hide();
                $("#t108, #t113, #t114, #t123, #t124").show();
								$("#lab2, #lab3, #lab4, #lab5").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 $(function(){
            		$("#lab2").on('click',function() {
                $(".infos").hide();
                $("#t109, #t106, #t110, #t111, #t120, #t121, #t122, #t199").show();
								$("#lab1, #lab3, #lab4, #lab5").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 $(function(){
            		$("#lab3").on('click',function() {
                $(".infos").hide();
                $("#t114, #t115, #t117, #t118").show();
								$("#lab2, #lab1, #lab4, #lab5").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 $(function(){
            		$("#lab4").on('click',function() {
                $(".infos").hide();
                $("#t131, #t132, #t133").show();
								$("#lab2, #lab1, #lab3, #lab5").removeClass("selected");
								$(this).addClass("selected");
            });
        });

 $(function(){
            $("#lab5").on('click',function() {
            $(".infos").show();
						$("#lab2, #lab1, #lab4, #lab3").removeClass("selected");
						$(this).addClass("selected");
            });
        });

        $(function(){
            $("#ship1").on('click',function() {
                $(".infos").addClass('d-none').removeClass('d-flex');
                $("#s202, #s203, #s208, #s209, #s212").addClass('d-flex').removeClass('d-none');
								$("#ship2, #ship3").removeClass("selected");
								$(this).addClass("selected");
            });
        });

				$(function(){
            $("#ship2").on('click',function() {
                $(".infos").addClass('d-none').removeClass('d-flex');
                $("#s204, #s205, #s206, #s207, #s210, #s211, #s213, #s214").addClass('d-flex').removeClass('d-none');
								$("#ship1, #ship3").removeClass("selected");

 				$(function(){
            $("#ship3").on('click',function() {
                $(".infos").addClass('d-flex').removeClass('d-none');

								$("#ship1, #ship2").removeClass("selected");

$(this).addClass("selected");
            });
        });



$(this).addClass("selected");
            });
        });


    </script>
{/block}

	<script>

	var myDefaultWhiteList = bootstrap.Tooltip.Default.allowList
myDefaultWhiteList.table = ['class','style'];
myDefaultWhiteList.tbody = [];
myDefaultWhiteList.thead = [];
myDefaultWhiteList.th = ['colspan'];
myDefaultWhiteList.tr = [];
myDefaultWhiteList.td = ['colspan','style'];
myDefaultWhiteList.span = ['class','onclick'];
myDefaultWhiteList.img = ['src','alt','width','height'];
myDefaultWhiteList.form = ['class','action','method'];
myDefaultWhiteList.input = ['type','name','value'];
myDefaultWhiteList.button = ['type','class'];
myDefaultWhiteList.font = ['color'];
myDefaultWhiteList.a = ['href','class','onclick'];
myDefaultWhiteList.br = [];

	//initialize bootstrap tooltips
	$(document).ready(function(){
	  $('[data-bs-toggle="tooltip"]').tooltip({
			container: 'body',
		  html: true,
		  whiteList: myDefaultWhiteList
		});
	});

	// To allow elements
	//popovers

	$(document).ready(function(){
		$('[data-bs-toggle="popover"]').popover({
			container: 'body',
		  html: true,
			whiteList: myDefaultWhiteList
		});

	});

	</script>

</head>
<body id="{if isset($smarty.get.page)}{$smarty.get.page|htmlspecialchars|default:'overview'}{/if}" class="{$bodyclass}">
	<div id="tooltip" class="tip"></div>
