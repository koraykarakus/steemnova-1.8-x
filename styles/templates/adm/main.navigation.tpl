<script>
  $(document).ready(function() {
    $("#searchInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#menu li").filter(function() {

        if ($(this).text().toLowerCase().indexOf(value) == -1) {

          if ($(this).hasClass('d-flex')) {
            $(this).removeClass('d-flex').addClass('d-none');
          }

        } else {
          if ($(this).hasClass('d-none')) {
            $(this).removeClass('d-none').addClass('d-flex');
          }
        }


      });
    });
  });
</script>

<div id="leftmenu">
  <div style="background-image: url('./styles/theme/img/menu-top.png');height:100px;"></div>
  <input class="bg-dark  py-0 my-1 form-control" style="height:38px;width:100%;" id="searchInput" type="text"
    placeholder="search...">
  <ul class="bg-dark d-flex flex-column p-0 m-0" id="menu">
    {if allowedTo('ShowInformationPage')}
      <li class="d-flex {if $page == 'infos'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6" href="?page=infos">{$LNG.mu_game_info}</a>
      </li>
    {/if}
     {if allowedTo('ShowGameSettingsPage')}
      <li class="d-flex {if $page == 'gameSettings'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none fs-6" href="?page=gameSettings">{$LNG.mu_improved_game_settings}</a>
      </li>
    {/if}
    {if allowedTo('ShowConfigBasicPage')}
      <li class="d-flex {if $page == 'server'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=server">{$LNG.mu_settings}</a>
      </li>
    {/if}
    {if allowedTo('ShowConfigUniPage')}
      <li class="d-flex {if $page == 'universe' && $mode == ''}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6"
          href="?page=universe">{$LNG.mu_unisettings}</a>
      </li>
    {/if}
    {if allowedTo('ShowResetPage')}
      <li class="d-flex {if $page == 'reset'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6"
          href="?page=reset">{$LNG.mu_reset_universe}</a>
      </li>
    {/if}
    {if allowedTo('ShowUniversePage')}
      <li class="d-flex {if $page == 'universe' && $mode == 'create'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6"
          href="?page=universe&mode=create">{$LNG.mu_create_universe}</a>
      </li>
    {/if}
    {if allowedTo('ShowColonySettingsPage')}
      <li class="d-flex {if $page == 'colonySettings'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6" href="?page=colonySettings">
          {$LNG.mu_colony_settings}
        </a>
      </li>
    {/if}
    {if allowedTo('ShowRelocatePage')}
      <li class="d-flex {if $page == 'relocate'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6" href="?page=relocate">
          {$LNG.mu_relocate_settings}
        </a>
      </li>
    {/if}
    {if allowedTo('ShowCollectMinesPage')}
      <li class="d-flex {if $page == 'collectMines'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6" href="?page=collectMines">
          {$LNG.mu_collect_mines_settings}</a>
      </li>
    {/if}
    {if allowedTo('ShowBostPage')}
      <li class="d-flex {if $page == 'bots'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6" href="?page=bots">
          {$LNG.mu_bots}
        </a>
      </li>
    {/if}
    {if allowedTo('ShowPlanetFieldsPage')}
      <li class="d-flex {if $page == 'planetFields'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6" href="?page=planetFields">
          {$LNG.mu_planet_fields}
        </a>
      </li>
    {/if}
    {if allowedTo('ShowExpeditionSettingsPage')}
      <li class="d-flex {if $page == 'expedition'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6" href="?page=expedition">
          {$LNG.mu_expedition_settings}
        </a>
      </li>
    {/if}
    {if allowedTo('ShowChatConfigPage')}
      <li class="d-flex {if $page == 'chat'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=chat">{$LNG.mu_chat}</a>
      </li>
    {/if}
    {if allowedTo('ShowGoogleAuthPage')}
      <li class="d-flex {if $page == 'googleAuth'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6"
          href="?page=googleAuth">{$LNG.mu_google_options}</a>
      </li>
    {/if}
    {if allowedTo('ShowDiscordAuthPage')}
      <li class="d-flex {if $page == 'discordAuth'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6"
          href="?page=discordAuth">{$LNG.mu_discord_options}</a>
      </li>
    {/if}
    {if allowedTo('ShowModulePage')}
      <li class="d-flex {if $page == 'module'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=module">{$LNG.mu_module}</a>
      </li>
    {/if}
    {if allowedTo('ShowDisclaimerPage')}
      <li class="d-flex {if $page == 'disclamer'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6"
          href="?page=disclaimer">{$LNG.mu_disclaimer}</a>
      </li>
    {/if}
    {if allowedTo('ShowStatsPage')}
      <li class="d-flex {if $page == 'stats'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=stats">{$LNG.mu_stats_options}</a>
      </li>
    {/if}
    {if allowedTo('ShowVerifyPage')}
      <li class="d-flex {if $page == 'verify'}menu-active{/if}">
        <a class="d-flexw-100 h-100 p-1  text-decoration-none  fs-6" href="?page=verify">{$LNG.mu_verify}</a>
      </li>
    {/if}
    {if allowedTo('ShowCronjobPage')}
      <li class="d-flex {if $page == 'cronjob'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=cronjob">{$LNG.mu_cronjob}</a>
      </li>
    {/if}
    {if allowedTo('ShowDumpPage')}
      <li class="d-flex {if $page == 'dump'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=dump">{$LNG.mu_dump}</a>
      </li>
    {/if}
    {if allowedTo('ShowCreatorPage')}
      <li class="d-flex {if $page == 'create'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1 text-decoration-none  fs-6"
          href="?page=create">{$LNG.new_creator_title}</a>
      </li>
    {/if}
    {if allowedTo('ShowAccountEditorPage')}
      <li class="d-flex {if $page == 'accounts'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=accounts">{$LNG.mu_add_delete_resources}</a>
      </li>
    {/if}
    {if allowedTo('ShowBanPage')}
      <li class="d-flex {if $page == 'banned'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=banned">{$LNG.mu_ban_options}</a>
      </li>
    {/if}
    {if allowedTo('ShowGiveawayPage')}
      <li class="d-flex {if $page == 'giveaway'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=giveaway">{$LNG.mu_giveaway}</a>
      </li>
    {/if}
    {if allowedTo('ShowSearchPage')}
      <li class="d-flex {if $page == 'search' && $search == 'online'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=search&amp;search=online&amp;minimize=on">{$LNG.mu_connected}</a>
      </li>
    {/if}
    {if allowedTo('ShowSupportPage')}
      <li class="d-flex {if $page == 'support'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=support">{$LNG.mu_support}{if isset($supportticks)} ({$supportticks}){/if}</a>
      </li>
    {/if}
    {if allowedTo('ShowActivePage')}
      <li class="d-flex {if $page == 'active'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=active">{$LNG.mu_vaild_users}</a>
      </li>
    {/if}
    {if allowedTo('ShowSearchPage')}
      <li class="d-flex {if $page == 'search' && $search == 'p_connect'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=search&amp;search=p_connect&amp;minimize=on">{$LNG.mu_active_planets}</a>
      </li>
    {/if}
    {if allowedTo('ShowFlyingFleetPage')}
      <li class="d-flex {if $page == 'fleets'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=fleets">{$LNG.mu_flying_fleets}</a>
      </li>
    {/if}
    {if allowedTo('ShowNewsPage')}
      <li class="d-flex {if $page == 'news'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=news">{$LNG.mu_news}</a>
      </li>
    {/if}
    {if allowedTo('ShowSearchPage')}
      <li class="d-flex {if $page == 'search' && $search == 'users'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=search&amp;search=users&amp;minimize=on">{$LNG.mu_user_list}</a>
      </li>
    {/if}
    {if allowedTo('ShowSearchPage')}
      <li class="d-flex {if $page == 'search' && $search == 'planet'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=search&amp;search=planet&amp;minimize=on">{$LNG.mu_planet_list}</a>
      </li>
    {/if}
    {if allowedTo('ShowSearchPage')}
      <li class="d-flex {if $page == 'search' && $search == 'moon'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=search&amp;search=moon&amp;minimize=on">{$LNG.mu_moon_list}</a>
      </li>
    {/if}
    {if allowedTo('ShowMessageListPage')}
      <li class="d-flex {if $page == 'messagelist'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=messagelist">{$LNG.mu_mess_list}</a>
      </li>
    {/if}
    {if allowedTo('ShowAccountDataPage')}
      <li class="d-flex {if $page == 'accountData'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=accountData">{$LNG.mu_info_account_page}</a>
      </li>
    {/if}
    {if allowedTo('ShowSearchPage')}
      <li class="d-flex {if $page == 'search' && $search == ''}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=search">{$LNG.mu_search_page}</a>
      </li>
    {/if}
    {if allowedTo('ShowMultiIPPage')}
      <li class="d-flex {if $page == 'multi'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=multi">{$LNG.mu_multiip_page}</a>
      </li>
    {/if}
    {if allowedTo('ShowLogPage')}
      <li class="d-flex {if $page == 'log'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=log">{$LNG.mu_logs}</a>
      </li>
    {/if}
    {if allowedTo('ShowSendMessagesPage')}
      <li class="d-flex {if $page == 'sendMessages'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=sendMessages">{$LNG.mu_global_message}</a>
      </li>
    {/if}
    {if allowedTo('ShowPassEncripterPage')}
      <li class="d-flex {if $page == 'passEncripter'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=passEncripter">{$LNG.mu_md5_encripter}</a>
      </li>
    {/if}
    {if allowedTo('ShowStatUpdatePage')}
      <li class="d-flex {if $page == 'statsUpdate'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6" href="?page=statsUpdate"
          onClick=" return confirm('{$LNG.mu_mpu_confirmation}');">{$LNG.mu_manual_points_update}</a>
      </li>
    {/if}
    {if allowedTo('ShowClearCachePage')}
      <li class="d-flex {if $page == 'clearCache'}menu-active{/if}">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none  fs-6"
          href="?page=clearCache">{$LNG.mu_clear_cache}</a>
      </li>
    {/if}
    <li style="background-image: url('./styles/theme/img/menu-foot.png');height:30px;"></li>
  </ul>
</div>