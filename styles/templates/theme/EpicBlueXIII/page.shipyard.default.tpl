{block name="title" prepend}{if $mode == "defense"}{$LNG.lm_defenses}{else}{$LNG.lm_shipshard}{/if}{/block}
{block name="content"}

{if $messages}
	<div class="message"><a href="?page=messages">{$messages}</a></div>
{/if}
{if !$NotBuilding}
<table width="70%" id="infobox" style="border: 2px solid red; text-align:center;background:transparent"><tr><td>{$LNG.bd_building_shipyard}</td></tr></table>
<br><br>
{/if}
{if !empty($BuildList)}
<div style="text-align: center;">
		<div id="bx" class="z"></div>
		<div class="ship">
		<form action="game.php?page=shipyard&amp;mode={$mode}" method="post" >
			<input type="hidden" name="action" value="delete">
			<div >

			<select name="auftr[]" id="auftr" onchange="this.form.myText.setAttribute('size', this.value);" multiple class="shipl"><option>&nbsp;</option></select><br><br>{$LNG.bd_cancel_warning}<br><input class="z" type="submit" value="{$LNG.bd_cancel_send}" />

			</div>
			</form>
			<br><span id="timeleft"></span><br><br>

	</div>
</div>
<br>
{/if}

{if $mode != "defense"}
<div class="planeto">
	<button id="ship1">Civil</button> |
	<button id="ship2">Military</button> |
	<button id="ship3">All</button>
</div>
{/if}

{foreach $elementList as $ID => $Element}
<div class="infos" id="s{$ID}">
	<div class="infos_left">
		<a href="#" onclick="return Dialog.info({$ID})">
			<img style="float: left;" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
		</a>
	</div>
	<div class="infos_right">
		<div class="infos_right_top">
			<a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
			<span class="yellow" id="val_{$ID}">
				{if $Element.available != 0}
					({$LNG.bd_available} {$Element.available|number})
				{/if}
			</span>
		</div>
		<div class="infos_right_bottom">
			<div class="infos_inner_left">
				<span>{$LNG.bd_remaining}</span>
				{foreach $Element.costOverflow as $ResType => $ResCount}
				<span>
					<a href='#' onclick='return Dialog.info({$ResType})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$ResType}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$ResType}.{if $ResType >=600 && $ResType <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$ResType}</td></tr></table></tr></table>">{$LNG.tech.{$ResType}}</a><span style="font-weight:700">&nbsp;:&nbsp;{$ResCount|number}</span>
				</span>
				{/foreach}
					<p>{$LNG.bd_max_ships_long}:<span style="font-weight:700">{$Element.maxBuildable|number}</p>

			</div>
			<div class="infos_inner_right">
				<form action="game.php?page=shipyard&amp;mode={$mode}" method="post" id="s{$ID}">
						<div class="buildl">
						<span>{foreach $Element.costResources as $RessID => $RessAmount}
						<a href='#' onclick='return Dialog.info({$RessID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$RessID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$RessID}</td></tr></table></tr></table>">{$LNG.tech.{$RessID}}</a>: <b><span style="color:{if $Element.costOverflow[$RessID] == 0}lime{else}red{/if}">{$RessAmount|number}</span></b>
						{/foreach}</span></br>
						{if $ID==212} +{$SolarEnergy} {$LNG.tech.911}<br>{/if}
						<span>{if $Element.AlreadyBuild}<span style="color:red">{$LNG.bd_protection_shield_only_one}</span>{elseif $NotBuilding && $Element.buyable}<input type="text" name="fmenge[{$ID}]" id="input_{$ID}" size="3" maxlength="{$maxlength}" value="0" tabindex="{$smarty.foreach.FleetList.iteration}" >
						<input type="button" value="{$LNG.bd_max_ships}" onclick="$('#input_{$ID}').val('{$Element.maxBuildable}')"> <input class="b" type="submit" value="{$LNG.bd_build_ships}">
						{/if}

						</p>

						<span>{$LNG.fgf_time}:{$Element.elementTime|time}</span>		
						</div>
				</form>
			</div>

		</div>

	</div>

</div>
{/foreach}

{if $NotBuilding}
<div class="planeto"></div>
{/if}

{/block}
{block name="script" append}
<script type="text/javascript">
data			= {$BuildList|json};
bd_operating	= '{$LNG.bd_operating}';
bd_available	= '{$LNG.bd_available}';
</script>

{if !empty($BuildList)}
<script src="scripts/base/bcmath.js"></script>
<script src="scripts/game/shipyard.js"></script>
<script type="text/javascript">
$(function() {
    ShipyardInit();
});

</script>
{/if}


{/block}
