{include file="overall_header.tpl"}

<script>
$(document).ready(function(){
  $("#searchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#menu li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>

<div id="leftmenu">
	<div style="background-image: url('./styles/theme/gow/img/menu-top.png');height:100px;"></div>
	<input class="bg-dark text-white" style="height:38px;width:100%;" id="searchInput" type="text"  placeholder="search...">
	<ul class="bg-dark" id="menu">
		{if allowedTo('ShowInformationPage')}
			<li class="d-flex">
				<a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=infos" target="Hauptframe">{$LNG.mu_game_info}</a>
			</li>
		{/if}
		{if allowedTo('ShowConfigBasicPage')}
			<li>
				<a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=config" target="Hauptframe">{$LNG.mu_settings}</a>
			</li>
		{/if}
		{if allowedTo('ShowConfigUniPage')}
			<li>
				<a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=configuni" target="Hauptframe">{$LNG.mu_unisettings}</a>
			</li>
		{/if}
		{if allowedTo('ShowChatConfigPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=chat" target="Hauptframe">{$LNG.mu_chat}</a></li>{/if}
		{if allowedTo('ShowTeamspeakPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=teamspeak" target="Hauptframe">{$LNG.mu_ts_options}</a></li>{/if}
		{if allowedTo('ShowFacebookPage')}<li><a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=facebook" target="Hauptframe">{$LNG.mu_fb_options}</a></li>{/if}
		{if allowedTo('ShowModulePage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=module" target="Hauptframe">{$LNG.mu_module}</a></li>{/if}
		{if allowedTo('ShowDisclamerPage')}<li><a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=disclamer" target="Hauptframe">{$LNG.mu_disclaimer}</a></li>{/if}
		{if allowedTo('ShowStatsPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=statsconf" target="Hauptframe">{$LNG.mu_stats_options}</a></li>{/if}
		{* if allowedTo('ShowVertifyPage')}<li><a class="d-flexw-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=vertify" target="Hauptframe">{$LNG.mu_vertify}</a></li>{/if *}
		{if allowedTo('ShowCronjobPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=cronjob" target="Hauptframe">{$LNG.mu_cronjob}</a></li>{/if}
		{if allowedTo('ShowDumpPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=dump" target="Hauptframe">{$LNG.mu_dump}</a></li>{/if}
		{if allowedTo('ShowCreatorPage')}<li><a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=create" target="Hauptframe">{$LNG.new_creator_title}</a></li>{/if}
		{if allowedTo('ShowAccountEditorPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=accounteditor" target="Hauptframe">{$LNG.mu_add_delete_resources}</a></li>{/if}
		{if allowedTo('ShowBanPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=bans" target="Hauptframe">{$LNG.mu_ban_options}</a></li>{/if}
		{if allowedTo('ShowGiveawayPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=giveaway" target="Hauptframe">{$LNG.mu_giveaway}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=online&amp;minimize=on" target="Hauptframe">{$LNG.mu_connected}</a></li>{/if}
		{if allowedTo('ShowSupportPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=support" target="Hauptframe">{$LNG.mu_support}{if $supportticks != 0} ({$supportticks}){/if}</a></li>{/if}
		{if allowedTo('ShowActivePage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=active" target="Hauptframe">{$LNG.mu_vaild_users}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=p_connect&amp;minimize=on" target="Hauptframe">{$LNG.mu_active_planets}</a></li>{/if}
		{if allowedTo('ShowFlyingFleetPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=fleets" target="Hauptframe">{$LNG.mu_flying_fleets}</a></li>{/if}
		{if allowedTo('ShowNewsPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=news" target="Hauptframe">{$LNG.mu_news}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=users&amp;minimize=on" target="Hauptframe">{$LNG.mu_user_list}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=planet&amp;minimize=on" target="Hauptframe">{$LNG.mu_planet_list}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=moon&amp;minimize=on" target="Hauptframe">{$LNG.mu_moon_list}</a></li>{/if}
		{if allowedTo('ShowMessageListPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=messagelist" target="Hauptframe">{$LNG.mu_mess_list}</a></li>{/if}
		{if allowedTo('ShowAccountDataPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=accountdata" target="Hauptframe">{$LNG.mu_info_account_page}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search" target="Hauptframe">{$LNG.mu_search_page}</a></li>{/if}
		{if allowedTo('ShowMultiIPPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=multiips" target="Hauptframe">{$LNG.mu_multiip_page}</a></li>{/if}
		{if allowedTo('ShowLogPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=log" target="Hauptframe">{$LNG.mu_logs}</a></li>{/if}
		{if allowedTo('ShowSendMessagesPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=globalmessage" target="Hauptframe">{$LNG.mu_global_message}</a></li>{/if}
		{if allowedTo('ShowPassEncripterPage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=password" target="Hauptframe">{$LNG.mu_md5_encripter}</a></li>{/if}
		{if allowedTo('ShowStatUpdatePage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=statsupdate" target="Hauptframe" onClick=" return confirm('{$LNG.mu_mpu_confirmation}');">{$LNG.mu_manual_points_update}</a></li>{/if}
		{if allowedTo('ShowClearCachePage')}<li><a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=clearcache" target="Hauptframe">{$LNG.mu_clear_cache}</a></li>{/if}
		<li style="background-image: url('./styles/theme/gow/img/menu-foot.png');height:30px;"></li>
	</ul>
</div>
{include file="overall_footer.tpl"}
