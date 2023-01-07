{include file="overall_header.tpl"}

<script type="text/javascript">
	function searchPages(){
		var inputText = $('#searchInput').val().toLowerCase().replace(/\s/g, '');

		var pageNames = $('.pageName');

		for (var i = 0; i < pageNames.length; i++) {

			if ($(pageNames[i]).html().toLowerCase().replace(/\s/g, '').indexOf(inputText) >= 0) {
				$(pageNames[i]).css('display','flex');
			}else {
				$(pageNames[i]).css('display','none');
			}


		}

	}
</script>

<div id="leftmenu">
	<ul id="menu">
		<li style="background-image: url('./styles/theme/gow/img/menu-top.png');height:100px;"></li>
		<li style="height:40px;display:flex;align-items:center;width:100%;"> <input style="height:38px;width:100%;" id="searchInput" type="text" onkeyup="searchPages();" placeholder="search..."> </li>
		{if allowedTo('ShowInformationPage')}<li><a class="pageName" href="?page=infos" target="Hauptframe">{$LNG.mu_game_info}</a></li>{/if}
		{if allowedTo('ShowConfigBasicPage')}<li><a class="pageName" href="?page=config" target="Hauptframe">{$LNG.mu_settings}</a></li>{/if}
		{if allowedTo('ShowConfigUniPage')}<li><a class="pageName" href="?page=configuni" target="Hauptframe">{$LNG.mu_unisettings}</a></li>{/if}
		{if allowedTo('ShowChatConfigPage')}<li><a class="pageName" href="?page=chat" target="Hauptframe">{$LNG.mu_chat}</a></li>{/if}
		{if allowedTo('ShowTeamspeakPage')}<li><a class="pageName" href="?page=teamspeak" target="Hauptframe">{$LNG.mu_ts_options}</a></li>{/if}
		{if allowedTo('ShowFacebookPage')}<li><a class="pageName" href="?page=facebook" target="Hauptframe">{$LNG.mu_fb_options}</a></li>{/if}
		{if allowedTo('ShowModulePage')}<li><a class="pageName" href="?page=module" target="Hauptframe">{$LNG.mu_module}</a></li>{/if}
		{if allowedTo('ShowDisclamerPage')}<li><a class="pageName" href="?page=disclamer" target="Hauptframe">{$LNG.mu_disclaimer}</a></li>{/if}
		{if allowedTo('ShowStatsPage')}<li><a class="pageName" href="?page=statsconf" target="Hauptframe">{$LNG.mu_stats_options}</a></li>{/if}
		{* if allowedTo('ShowVertifyPage')}<li><a class="pageName" href="?page=vertify" target="Hauptframe">{$LNG.mu_vertify}</a></li>{/if *}
		{if allowedTo('ShowCronjobPage')}<li><a class="pageName" href="?page=cronjob" target="Hauptframe">{$LNG.mu_cronjob}</a></li>{/if}
		{if allowedTo('ShowDumpPage')}<li><a class="pageName" href="?page=dump" target="Hauptframe">{$LNG.mu_dump}</a></li>{/if}
		{if allowedTo('ShowCreatorPage')}<li><a class="pageName" href="?page=create" target="Hauptframe">{$LNG.new_creator_title}</a></li>{/if}
		{if allowedTo('ShowAccountEditorPage')}<li><a class="pageName" href="?page=accounteditor" target="Hauptframe">{$LNG.mu_add_delete_resources}</a></li>{/if}
		{if allowedTo('ShowBanPage')}<li><a class="pageName" href="?page=bans" target="Hauptframe">{$LNG.mu_ban_options}</a></li>{/if}
		{if allowedTo('ShowGiveawayPage')}<li><a class="pageName" href="?page=giveaway" target="Hauptframe">{$LNG.mu_giveaway}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="pageName" href="?page=search&amp;search=online&amp;minimize=on" target="Hauptframe">{$LNG.mu_connected}</a></li>{/if}
		{if allowedTo('ShowSupportPage')}<li><a class="pageName" href="?page=support" target="Hauptframe">{$LNG.mu_support}{if $supportticks != 0} ({$supportticks}){/if}</a></li>{/if}
		{if allowedTo('ShowActivePage')}<li><a class="pageName" href="?page=active" target="Hauptframe">{$LNG.mu_vaild_users}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="pageName" href="?page=search&amp;search=p_connect&amp;minimize=on" target="Hauptframe">{$LNG.mu_active_planets}</a></li>{/if}
		{if allowedTo('ShowFlyingFleetPage')}<li><a class="pageName" href="?page=fleets" target="Hauptframe">{$LNG.mu_flying_fleets}</a></li>{/if}
		{if allowedTo('ShowNewsPage')}<li><a class="pageName" href="?page=news" target="Hauptframe">{$LNG.mu_news}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="pageName" href="?page=search&amp;search=users&amp;minimize=on" target="Hauptframe">{$LNG.mu_user_list}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="pageName" href="?page=search&amp;search=planet&amp;minimize=on" target="Hauptframe">{$LNG.mu_planet_list}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="pageName" href="?page=search&amp;search=moon&amp;minimize=on" target="Hauptframe">{$LNG.mu_moon_list}</a></li>{/if}
		{if allowedTo('ShowMessageListPage')}<li><a class="pageName" href="?page=messagelist" target="Hauptframe">{$LNG.mu_mess_list}</a></li>{/if}
		{if allowedTo('ShowAccountDataPage')}<li><a class="pageName" href="?page=accountdata" target="Hauptframe">{$LNG.mu_info_account_page}</a></li>{/if}
		{if allowedTo('ShowSearchPage')}<li><a class="pageName" href="?page=search" target="Hauptframe">{$LNG.mu_search_page}</a></li>{/if}
		{if allowedTo('ShowMultiIPPage')}<li><a class="pageName" href="?page=multiips" target="Hauptframe">{$LNG.mu_multiip_page}</a></li>{/if}
		{if allowedTo('ShowLogPage')}<li><a class="pageName" href="?page=log" target="Hauptframe">{$LNG.mu_logs}</a></li>{/if}
		{if allowedTo('ShowSendMessagesPage')}<li><a class="pageName" href="?page=globalmessage" target="Hauptframe">{$LNG.mu_global_message}</a></li>{/if}
		{if allowedTo('ShowPassEncripterPage')}<li><a class="pageName" href="?page=password" target="Hauptframe">{$LNG.mu_md5_encripter}</a></li>{/if}
		{if allowedTo('ShowStatUpdatePage')}<li><a class="pageName" href="?page=statsupdate" target="Hauptframe" onClick=" return confirm('{$LNG.mu_mpu_confirmation}');">{$LNG.mu_manual_points_update}</a></li>{/if}
		{if allowedTo('ShowClearCachePage')}<li><a class="pageName" href="?page=clearcache" target="Hauptframe">{$LNG.mu_clear_cache}</a></li>{/if}
		<li style="background-image: url('./styles/theme/gow/img/menu-foot.png');height:30px;"></li>
	</ul>
</div>
{include file="overall_footer.tpl"}
