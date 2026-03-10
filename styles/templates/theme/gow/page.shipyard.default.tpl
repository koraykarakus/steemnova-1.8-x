{block name="title" prepend}{if $mode == "defense"}{$LNG.lm_defenses}{else}{$LNG.lm_shipshard}{/if}{/block}
{block name="content"}
<script src="./scripts/base/avoid_submit_on_refresh.js" type="text/javascript"></script>

<script>
  function showItem(id){

    if ($('#item_big_' + id).hasClass('d-none')) {
      $('.buildItemBig').addClass('d-none');
      $('.buildItemSmall').removeClass('border-color-active').removeClass('border-color-passive');
      $('#item_big_' + id).removeClass('d-none');
      $('#item_small_' + id).addClass('border-color-active');
    }else {
      $('#item_big_' + id).addClass('d-none');
      $('#item_small_' + id).addClass('border-color-passive').removeClass('border-color-active');
    }

  }

</script>

{if !$NotBuilding}
<span class="" id="infobox">
	{$LNG.bd_building_shipyard}
</span>
{/if}


<div class="ItemsWrapper">

<div {if $mode == "defense"}style="background:url('{$dpath}images/defense.webp');"{else}style="background:url('{$dpath}images/hangar.webp');"{/if} class="">
{foreach $elementList as $ID => $Element}
<div id="item_big_{$ID}" class="">
  <div class="">
    <div class="">
        <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
    </div>
    <div class="">
      <div class="">
        <div class="">
          <span class="{if $Element.costOverflowTotal > 0}color-red hover-pointer{else}color-yellow{/if} p-0" {if $Element.costOverflowTotal > 0} {/if}>
            <div class="tooltip tooltip_top">
            <table class='table fs-12'>
              <thead>
                <tr><th colspan='2' class='text-center'>{$LNG.bd_remaining}</th></tr>
              </thead>
              <tbody>
                {foreach $Element.costOverflow as $ResType => $ResCount}
                <tr>
                  <td>{$LNG.tech.$ResType}</td>
                  <td>{$ResCount|number}</td>
                </tr>
                {/foreach}
              </tbody>
            </table>
            </div>
            {$LNG.tech.{$ID}}
          </span>
          <span class="" id="val_{$ID} p-0">
            &nbsp;( {$LNG.bd_available} {$Element.available|number} )
          </span>
        </div>
      </div>
      <div class="">
        <div class="">
          <span class="">
            {foreach $Element.costResources as $RessID => $RessAmount}
            <div class="">
              <img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'>
                <div class="tooltip tooltip_top">{$LNG.tech.$RessID}</div>
              <span class="mx-1 fs-11 {if $Element.costOverflow[$RessID] == 0}text-white{else}color-red{/if}">{$RessAmount|number}</span>
            </div>
            {/foreach}
          </span>
        </div>
        <div class="">
          {if $ID == 212}
						<span class="">&nbsp;<span class="color-green">+{$SolarEnergy}</span>&nbsp;{$LNG.tech.911}</span>
					{/if}
						<div class="">
						{if $Element.AlreadyBuild}
							<span class="">{$LNG.bd_protection_shield_only_one}</span>
						{elseif $NotBuilding && $Element.buyable}
            <form class="" action="game.php?page=shipyard&amp;mode={$mode}" method="post" id="s{$ID}">
							<input class="" type="text" name="fmenge[{$ID}]" id="input_{$ID}" size="3" maxlength="{$maxlength}" value="0" tabindex="{$smarty.foreach.FleetList.iteration}" >
							<input class="" type="button" value="{$LNG.bd_max_ships}" onclick="$('#input_{$ID}').val('{$Element.maxBuildable}')">
							<input class="b button-upgrade" type="submit" value="{$LNG.bd_build_ships}">
            </form>
						{/if}
						</div>
						<span class="">{$LNG.fgf_time}&nbsp;:&nbsp;{pretty_time($Element.elementTime)}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="">
    <p class="">{$LNG.shortDescription[$ID]}</p>
  </div>
</div>
{/foreach}
</div>

<div class="">
  <div class="">
    <span class="">{$LNG.lm_shipshard}</span>
    <span class="">&nbsp;|&nbsp;{$LNG.st_points}&nbsp;[{if $mode == "fleet"}{$userFleetPoints}{else}{$userDefensePoints}{/if}]</span>
  </div>
  <div class="">
    {foreach $elementList as $ID => $Element}
      <div class="" onclick="showItem({$ID})" id="item_small_{$ID}">
      <div class="tooltip tooltip_top">
      {$LNG.tech.{$ID}}
      {if !$Element.technologySatisfied && !empty($Element.requeriments)}
      <table class='table-tooltip'>
        <thead>
          <tr><th colspan='2' class='color-red'>{$LNG.tech_not_satisfied}</th></tr>
        </thead>
        <tbody>
          {foreach $Element.requeriments as $currentRequire}
          <tr>
            <td class='color-red'>
              <img class='mx-2 hover-pointer' src='{$dpath}gebaeude/{$currentRequire.requireID}.gif' alt='{$LNG.tech.{$currentRequire.requireID}}' width='30' height='30'>
            </td>
            <td class='color-red align-middle text-start'><span class='color-blue'>{$LNG.tech.{$currentRequire.requireID}}</span>&nbsp;({$currentRequire.neededLevel}&nbsp;/&nbsp;<span class='color-yellow'>{$currentRequire.currentLevel}</span>)</td>
          </tr>
          {/foreach}
        </tbody>
      </table>
      {/if}
      </div>
      <div class="">{shortly_number($Element.available)}</div>
      {if  !$Element.buyable || !$Element.technologySatisfied }
         <div class="black-screen"></div>
         {/if}
        <img class="hover-pointer" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="80" height="80">
      </div>
    {/foreach}
  </div>
</div>
</div>

{if !empty($BuildList)}
<div class="">
		<div class="">
			<div id="bx" class=""></div>
			<div id="timeleft" class=""></div>
			<form action="game.php?page=shipyard&mode={$mode}" method="post" class="mx-auto" style="max-width: 500px;">
				<input type="hidden" name="action" value="delete">
				<select name="auftr[]" id="auftr"
					class=""
					multiple>
					<option>&nbsp;</option>
				</select>
				<button type="submit"
					class="">
					{$LNG.bd_cancel_send}
				</button>
			</form>
			<div class="">
				{$LNG.bd_cancel_warning}
			</div>
		</div>
</div>
{/if}

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

{/block}
