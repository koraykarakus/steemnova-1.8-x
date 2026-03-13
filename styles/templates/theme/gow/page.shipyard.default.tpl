{block name="title" prepend}{if $mode == "defense"}{$LNG.lm_defenses}{else}{$LNG.lm_shipshard}{/if}{/block}
{block name="content"}
<script src="./scripts/base/avoid_submit_on_refresh.js" type="text/javascript"></script>
<script>
  function showItem(id){
    if ($('#item_big_' + id).hasClass('hidden')) {
        $('.item_big').addClass('hidden');
        $('.item_small').removeClass('border-color-active').removeClass('border-color-passive');
        $('#item_big_' + id).removeClass('hidden');
        $('#item_small_' + id).addClass('border-color-active');
      } else {
        $('#item_big_' + id).addClass('hidden');
        $('#item_small_' + id).addClass('border-color-passive').removeClass('border-color-active');
      }
  }
</script>

{if !$NotBuilding}
  <span id="infobox">
    {$LNG.bd_building_shipyard}
  </span>
{/if}

<div class="items_wrapper">
  <div class="top" {if $mode == "defense"}style="background:url('{$dpath}images/defense.jpg');"{else}style="background:url('{$dpath}images/hangar.jpg');"{/if} >
      {foreach $elementList as $ID => $Element}
        <div class="item_big hidden" id="item_big_{$ID}">
          <div class="top">
            <div class="left">
              <div class="img_wrapper">
                  <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="203" height="203">
              </div>
            </div>
            <div class="right">
              <div class="title">
                {if $ID == 212}
                  <span class="">&nbsp;<span class="color-green">+{$SolarEnergy}</span>&nbsp;{$LNG.tech.911}</span>
                {/if}
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
                <span id="val_{$ID} p-0">
                  {$LNG.bd_available} {$Element.available|number}
                  <button class="button_close" onclick="showItem({$ID});">X</button>
                </span>
              </div>
              <div class="requirements">
                <div class="top">
                  <div class="left">
                      <span class="">{$LNG.fgf_time}&nbsp;:&nbsp;{pretty_time($Element.elementTime)}</span>
                  </div>
                  <div class="right">
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
                </div>
              <div class="bottom">
                <div class="left">
                {foreach $Element.costResources as $RessID => $RessAmount}
                      <div class="resource">
                        <div class="tooltip tooltip_top">
                          {$LNG.tech.$RessID}
                        </div>
                        <img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'>
                        <span class="mx-1 fs-11 {if $Element.costOverflow[$RessID] == 0}text-white{else}color-red{/if}">{$RessAmount|number}</span>
                      </div>
                    {/foreach}
                </div>
                <div class="right"></div>
              </div>
            </div>
          </div>
          </div>
            

                 

            
            <div class="bottom">
              <p class="">{$LNG.shortDescription[$ID]}</p>
            </div>
        </div>
      {/foreach}
  </div>
  <div class="bottom">
    <div class="title">
        <span class="color-yellow">{$LNG.lm_shipshard}</span>
        <span class="color-yellow">&nbsp;|&nbsp;{$LNG.st_points}&nbsp;[{if $mode == "fleet"}{$userFleetPoints}{else}{$userDefensePoints}{/if}]</span>
    </div>
    <div class="list">
      {foreach $elementList as $ID => $Element}
        <div class="item_small" onclick="showItem({$ID})" id="item_small_{$ID}">
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
        <div class="level_info">{shortly_number($Element.available)}</div>
        {if !$Element.buyable || !$Element.technologySatisfied }
          <div class="black-screen"></div>
        {/if}
        <img class="hover-pointer" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="80" height="80">
          <div class="name_info">
              {$LNG.tech.{$ID}}
          </div>
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
