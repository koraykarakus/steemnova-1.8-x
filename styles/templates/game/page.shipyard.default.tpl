{block name="title" prepend}{if $mode == "defense"}{$LNG.lm_defenses}{else}{$LNG.lm_shipyard}{/if}{/block}
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
                  <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}elements/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="203" height="203">
              </div>
            </div>
            <div class="right">
              <div class="title">
                {if $ID == 212}
                  <span>&nbsp;<span class="color-green">+{$SolarEnergy}</span>&nbsp;{$LNG.tech.911}</span>
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
                      <span>{$LNG.fgf_time}&nbsp;:&nbsp;{pretty_time($Element.elementTime)}</span>
                  </div>
                  <div class="right">
                   {if $Element.AlreadyBuild}
                    <span>{$LNG.bd_protection_shield_only_one}</span>
                  {elseif $NotBuilding && $Element.buyable}
                  <form action="game.php?page=shipyard&amp;mode={$mode}" method="post" id="s{$ID}">
                    <input class="shipyard_input" type="text" name="fmenge[{$ID}]" id="input_{$ID}" size="3" maxlength="{$maxlength}" value="0" tabindex="{$smarty.foreach.FleetList.iteration}" >
                    <button class="button_max" type="button" onclick="$('#input_{$ID}').val('{$Element.maxBuildable}')">
                      >>
                    </button>
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
                        <img src='{$dpath}elements/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'>
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
              <p>{$LNG.shortDescription[$ID]}</p>
            </div>
        </div>
      {/foreach}
    <span class="page_title">{$current_pname} - {if $mode == "defense"}{$LNG.lm_defenses}{else}{$LNG.lm_shipyard}{/if}</span>
  </div>
  <div class="bottom">
    <div class="title">
        <span class="color-yellow">{$LNG.lm_shipyard}</span>
        <span class="color-yellow">&nbsp;|&nbsp;{$LNG.st_points}&nbsp;[{if $mode == "fleet"}{$userFleetPoints}{else}{$userDefensePoints}{/if}]</span>
    </div>
    <div class="list">
      {foreach $elementList as $ID => $Element}
        <div class="item_small" onclick="showItem({$ID})" id="item_small_{$ID}">
        <div class="tooltip tooltip_top">
          <table class='table-tooltip'>
            <thead>
              <th colspan="2">{$LNG.tech.{$ID}}</th> 
            </thead>
          {if !$Element.technologySatisfied && !empty($Element.requirements)}
            <tbody>
              <tr class='color-red'>
                <td colspan='2'>{$LNG.tech_not_satisfied}</td>
              </tr>
              {foreach $Element.requirements as $currentRequire}
              <tr>
                <td class='color-red'>
                  <img src='{$dpath}elements/{$currentRequire.requireID}.gif' alt='{$LNG.tech.{$currentRequire.requireID}}' width='30' height='30'>
                </td>
                <td>
                  <span class='color-blue'>{$LNG.tech.{$currentRequire.requireID}}</span>&nbsp;({$currentRequire.neededLevel}&nbsp;/&nbsp;<span class='color-yellow'>{$currentRequire.currentLevel}</span>)
                </td>
              </tr>
              {/foreach}
            </tbody>
          {/if}
          </table>
        </div>
        <div class="level_info">{shortly_number($Element.available)}</div>
        {if !$Element.buyable || !$Element.technologySatisfied }
          <div class="black-screen"></div>
        {/if}
        <img class="hover-pointer" src="{$dpath}elements/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="90" height="90">
          <div class="name_info">
              {$LNG.tech.{$ID}}
          </div>
        </div>
      {/foreach}
    </div>
  </div>
</div>

{if !empty($BuildList)}
  <div class="queue_wrapper">
      <div>
        <div id="bx"></div>
        <div id="timeleft"></div>
        <form action="game.php?page=shipyard&mode={$mode}" method="post">
          <input type="hidden" name="action" value="delete">
          <select name="auftr[]" id="auftr" multiple>
            <option>&nbsp;</option>
          </select>
          <button type="submit">
            {$LNG.bd_cancel_send}
          </button>
        </form>
      </div>
      <div>
          {$LNG.bd_cancel_warning}
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
