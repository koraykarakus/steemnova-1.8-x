{include file="ins_header.tpl"}
<tr>
	<td colspan="2">
		<div id="lang" class="d-flex justify-content-between align-items-center">
			<span>{$LNG.intro_lang}&nbsp;</span>
			<select style="width:150px;" class="form-select bg-dark text-white text-start py-0" id="lang" name="lang" onchange="document.location = '?lang='+$(this).val();">{html_options class="py-1" options=$Selector selected=$lang}</select>
		</div>
		<div id="main" align="left">
			<h2>{$LNG.intro_welcome}</h2>
			<p>{$LNG.intro_text}</p>
		</div><br>
		<a class="d-flex w-100 btn btn-primary text-white justify-content-center p-1 my-2" href="index.php?mode=install&amp;step=2">
			{$LNG.continue}
		</a>
	</td>
</tr>
{if $canUpgrade}
<tr>
	<th colspan="3">{$LNG.menu_upgrade}</th>
</tr>
<tr>
	<td colspan="2">
		<div id="main" align="left">
			<h2>{$LNG.intro_upgrade_head}</h2>
			<p>{$LNG.intro_upgrade_text}</p>
		</div>
		<br>
		<a class="d-flex w-100 btn btn-primary text-white justify-content-center p-1 my-2" href="index.php?mode=upgrade">{$LNG.continueUpgrade}</a>
	</td>
</tr>
{/if}
{include file="ins_footer.tpl"}
