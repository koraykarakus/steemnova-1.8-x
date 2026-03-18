<ul id="menu">
  <li class="menu-button">
    <a class="long{if $page == 'overview'} menuActive{/if}" href="game.php?page=overview">{$LNG.lm_overview}</a>
    <a class="menu_icon icon_1{if $page == 'overview'} active{/if}" href="game.php?page=overview"></a>
  </li>
  {if isModuleAvailable($smarty.const.MODULE_BUILDING)}
    <li class="menu-button">
      <a class="long{if $page == 'buildings'} menuActive{/if}" href="game.php?page=buildings">{$LNG.lm_buildings}</a>
      <a class="menu_icon icon_2{if $page == 'resources'} active{/if}" href="game.php?page=resources"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_BUILDING)}
    <li class="menu-button">
      <a class="long{if $page == 'facilities'} menuActive{/if}" href="game.php?page=facilities">{$LNG.lm_facilities}</a>
      <a class="menu_icon icon_3{if $page == 'facilities'} active{/if}" href="game.php?page=facilities"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_SHIPYARD_FLEET)}
    <li class="menu-button">
      <a class="long{if $page == 'shipyard' && $mode == 'fleet'} menuActive{/if}"
        href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipyard}</a>
        <a class="menu_icon icon_4{if $page == 'shipyard' && $mode == 'fleet'} active{/if}" href="game.php?page=shipyard&amp;mode=fleet"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_SHIPYARD_DEFENSIVE)}
    <li class="menu-button">
      <a class="long{if $page == 'shipyard' && $mode == 'defense'} menuActive{/if}"
        href="game.php?page=shipyard&amp;mode=defense">{$LNG.lm_defenses}</a>
      <a class="menu_icon icon_5{if $page == 'shipyard' && $mode == 'defense'} active{/if}" href="game.php?page=shipyard&amp;mode=defense"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_RESEARCH)}
    <li class="menu-button">
      <a class="long{if $page == 'research'} menuActive{/if}" href="game.php?page=research">{$LNG.lm_research}</a>
      <a class="menu_icon icon_6{if $page == 'research'} active{/if}" href="game.php?page=research"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_TRADER)}
    <li class="menu-button">
      <a class="long{if $page == 'fleetTable'} menuActive{/if}" href="game.php?page=fleetTable">{$LNG.lm_fleet}</a>
      <a class="menu_icon icon_7{if $page == 'fleetDispatch'} active{/if}" href="game.php?page=fleetDispatch"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_GALAXY)}
    <li class="menu-button">
      <a class="long{if $page == 'galaxy'} menuActive{/if}" href="game.php?page=galaxy">{$LNG.lm_galaxy}</a>
      <a class="menu_icon icon_8{if $page == 'galaxy'} active{/if}" href="game.php?page=galaxy"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_IMPERIUM)}
    <li class="menu-button">
      <a target="_blank" class="long{if $page == 'imperium'} menuActive{/if}"
        href="game.php?page=imperium">{$LNG.lm_empire}</a>
      <a target="_blank" class="menu_icon icon_9{if $page == 'imperium'} active{/if}" href="game.php?page=imperium"></a>
    </li>
  {/if}

  {if isModuleAvailable($smarty.const.MODULE_TECHTREE)}
    <li class="menu-button">
      <a class="long{if $page == 'techtree'} menuActive{/if}" href="game.php?page=techtree">{$LNG.lm_technology}</a>
      <a class="menu_icon icon_14{if $page == 'techtree'} active{/if}" href="game.php?page=techtree"></a>
    </li>
  {/if}
  
  {if isModuleAvailable($smarty.const.MODULE_OFFICERS) || isModuleAvailable($smarty.const.MODULE_DMEXTRAS)}
    <li class="menu-button">
      <a class="long{if $page == 'officers'} menuActive{/if}" href="game.php?page=officers">{$LNG.lm_officers}</a>
      <a class="menu_icon icon_10{if $page == 'officers'} active{/if}" href="game.php?page=officers"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_TRADER)}
    <li class="menu-button">
      <a class="long{if $page == 'trader'} menuActive{/if}" href="game.php?page=trader">{$LNG.lm_trader}</a>
      <a class="menu_icon icon_11{if $page == 'trader'} active{/if}" href="game.php?page=trader"></a>
    </li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_FLEET_TRADER)}
    <li class="menu-button">
      <a class="long{if $page == 'fleetDealer'} menuActive{/if}" href="game.php?page=fleetDealer">{$LNG.lm_fleettrader}</a>
      <a class="menu_icon icon_12{if $page == 'fleetDealer'} active{/if}" href="game.php?page=fleetDealer"></a>
    </li>
  {/if}

  {if isModuleAvailable($smarty.const.MODULE_ALLIANCE)}
    <li class="menu-button">
      <a class="long{if $page == 'alliance'} menuActive{/if}" href="game.php?page=alliance">{$LNG.lm_alliance}</a>
      <a class="menu_icon icon_13{if $page == 'alliance'} active{/if}" href="game.php?page=alliance"></a>
    </li>
  {/if}
  {if $authlevel > 0}
    <li class="menu-button">
      <a href="./admin.php" class="long" style="color:lime">{$LNG.lm_administration}</a>
    </li>
    <li class="menu-button">
      <a href="?page=testBattle" class="long" style="color:lime" target="_blank">Test Battle</a>
    </li>
  {/if}
  {if !empty($commit)}
    <li class="menu-button">
      <a href="https://github.com/koraykarakus/steemnova-1.8-x/tree/{$commit}" class="long" target="copy">
        {$commitShort}
      </a>
    </li>
  {/if}
</ul>