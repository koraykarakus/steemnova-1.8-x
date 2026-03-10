{include file="main.header.tpl" bodyclass="full"}

<div class="site_header">
	{if isModuleAvailable($smarty.const.MODULE_COLLECT_MINES)}
		<form action="game.php?page=collectMines" method="post">
			<input type="hidden" name="from" value="{$page}">
			<button class="button_dark" type="submit">{$LNG.cm_collect_mines_submit}</button>
		</form>
	{/if}
</div>

<div class="page_content">
	{if $page != "imperium"}
		<div id="content_top">
			{include file="main.topnav.tpl"}
		</div>
		<div id="content_left">
			{include file="main.navigation.tpl"}
		</div>
	{/if}
	<div id="content_mid" class="{if $page!='imperium'}contentMiddle{else}w-100{/if}">
		<content class="content-wrapper">
			{if $hasAdminAccess}
				<div class="alert alert-danger my-2 p-2 fs-12 globalWarning">
					{$LNG.admin_access_1} <a class="fw-bold hover-underline hover-pointer text-danger"
						id="drop-admin">{$LNG.admin_access_link}</a>{$LNG.admin_access_2}
				</div>
			{/if}
			{if $closed}
				<div class="infobox">{$LNG.ov_closed}</div>
			{elseif $delete}
				<div class="infobox">{$delete}</div>
			{elseif $vacation}
				<div class="infobox">{$LNG.tn_vacation_mode} {$vacation}</div>
			{/if}
			{if $page !== "imperium"}
				{include file="fleet.events.tpl"}
			{/if}
			{block name="content"}{/block}
			<table class="hack"></table>
		</content>
	</div>
	{if $page != "imperium"}
		<div id="content_right">
			{include file="main.planetmenu.tpl"}
		</div>
	{/if}
	{foreach $execscript as $currentScript}
		<script type="text/javascript">
			{$currentScript}
		</script>
	{/foreach}
</div>

{include file="main.footer.tpl" nocache}
</body>

</html>