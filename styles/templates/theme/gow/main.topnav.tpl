  <div class="bar">
    <div class="left">
      <img class="user_icon" src="{$avatar}" width="25" height="25"></a>
      <a href="game.php?page=settings">{$LNG.tech.615}&nbsp;{$username}</a>
      {if isModuleAvailable($smarty.const.MODULE_ATTACK_ALERT)}
        <img style="min-width: 15px;" id="attack_alert" src="" alt="">
      {/if}
      {if isModuleAvailable($smarty.const.MODULE_COLLECT_MINES)}
      <form action="game.php?page=collectMines" method="post">
        <input type="hidden" name="from" value="{$page}">
        <button class="button_dark" type="submit">{$LNG.cm_collect_mines_submit}</button>
      </form>
	    {/if}
    </div>
    <div class="mid">
      {if isModuleAvailable($smarty.const.MODULE_STATISTICS)}
        <a href="game.php?page=statistics" class="{if $page === "statistics"}active{/if}">
          {$LNG.lm_statistics}
        </a>
      {/if}
      {if isModuleAvailable($smarty.const.MODULE_NOTICE)}
        <a href="javascript:OpenPopup('?page=notes', 'notes', 720, 300);">
          {$LNG.lm_notes}
        </a>
      {/if}
      {if isModuleAvailable($smarty.const.MODULE_BUDDYLIST)}
        <a href="game.php?page=buddyList" class="{if $page === "buddyList"}active{/if}">
          {$LNG.lm_buddylist}
        </a>
      {/if}
      <a href="game.php?page=settings" class="{if $page === "settings"}active{/if}">
        {$LNG.lm_options}
      </a>
      {if isModuleAvailable($smarty.const.MODULE_SEARCH)}
        <a href="game.php?page=search" class="{if $page === "search"}active{/if}">
          {$LNG.lm_search}
        </a>
      {/if}
      {if isModuleAvailable($smarty.const.MODULE_SUPPORT)}
        <a href="game.php?page=ticket" class="{if $page === "ticket"}active{/if}">
          {$LNG.lm_support}
        </a>
      {/if}
      {if isModuleAvailable($smarty.const.MODULE_QUESTIONS)}
      <a href="game.php?page=questions" class="{if $page === "questions"}active{/if}">
        {$LNG.lm_faq}
      </a>
      {/if}
      <a href="game.php?page=logout">
        {$LNG.lm_logout}
      </a>
    </div>
    <div class="right">
      <div class="servertime">
        {$servertime}
      </div>
    </div>
  </div>
  <div class="page_refresher">
    <a href="game.php?page=overview">
      <img width="130" height="70" class="" src="styles/resource/images/meta.png" />
    </a>
  </div>
  <div class="resources">
    {foreach $resourceTable as $resourceID => $resourceData}
      <div class="resource">
        <div class="tooltip tooltip_bottom">
          <table class='table-gow'>
            {if in_array($resourceID,array(901,902,903))}
              <tr>
                <td>{$LNG.resource_available}:</td>
                <td class="text_right">{$resourceData.current|number}</td>
              </tr>
              <tr>
                <td>{$LNG.resource_capacity}:</td>
                <td class="text_right">{$resourceData.max|number}</td>
              </tr>
              <tr>
                <td>{$LNG.resource_production}:</td>
                <td class='text_right {if $resourceData.current < $resourceData.max}color-green{else}color-red{/if}'>
                  {if $resourceData.current < $resourceData.max}
                    {$resourceData.production|number}&nbsp;/&nbsp;{$LNG.short_hour}
                  {else}
                    0
                  {/if}
                </td>
              </tr>
            {elseif $resourceID == 911}
              <tr>
                <td>{$LNG.energy_available}:</td>
                <td class='text_right {if  ($resourceData.max + $resourceData.used) > 0}color-green{else}color-red{/if}'>
                  {($resourceData.max + $resourceData.used)|number}&nbsp;/&nbsp;{$LNG.short_hour}</td>
              </tr>
              <tr>
                <td>{$LNG.energy_used}:</td>
                <td class="text_right">{$resourceData.used|number}&nbsp;/&nbsp;{$LNG.short_hour}</td>
              </tr>
              <tr>
                <td>{$LNG.energy_produced}:</td>
                <td class="text_right">{$resourceData.max|number}&nbsp;/&nbsp;{$LNG.short_hour}</td>
              </tr>
            {elseif $resourceID == 921}
              <tr>
                <td>{$LNG.darkmatter_available}:</td>
                <td class="text_right">{$resourceData.current|number}</td>
              </tr>
            {/if}
          </table>
        </div>
        <img class="user-select-none" onclick="return Dialog.info({$resourceID});"
          src="{$dpath}images/{$resourceData.name}.gif">
        <span class="resource_name">{$LNG.tech.$resourceID}</span>
        {if !isset($resourceData.current)}
          {$resourceData.currentt = $resourceData.max + $resourceData.used}
          <div class="res_current user-select-none fs-10 {if $resourceData.currentt > 0}color-green{else}color-red{/if}">
            {$resourceData.currentt|number}
          </div>
        {else}
          <div class="res_current user-select-none fs-10" id="current_{$resourceData.name}"
            data-real="{$resourceData.current}">
            {$resourceData.current|number}
          </div>
        {/if}
      </div>
    {/foreach}
  </div>
  {if isModuleAvailable($smarty.const.MODULE_MESSAGES)}
    <a href="?page=messages" class="messages">
      <div class="tooltip tooltip_top">
        {$LNG.lm_messages}
      </div>
      {nocache}
      {if $new_message > 0}
        <span id="newmes">
          <span id="newmesnum">{$new_message}</span>
        </span>
      {/if}
      {/nocache}
    </a>
  {/if}
  {include file="fleetTable.tpl"}

  {if !$vmode}
    <script type="text/javascript">
      var viewShortlyNumber	= {$shortlyNumber|json};
      var vacation			= {$vmode};
      $(function() {
        {foreach $resourceTable as $resourceID => $resourceData}
          {if isset($resourceData.production)}
            resourceTicker({
              available: {$resourceData.current|json},
              limit: [0, {$resourceData.max|json}],
              production: {$resourceData.production|json},
              valueElem: "current_{$resourceData.name}"
            }, true);
          {/if}
        {/foreach}
      });
    </script>
    <script src="scripts/game/topnav.js"></script>
    {if $hasGate}<script src="scripts/game/gate.js"></script>{/if}
{/if}