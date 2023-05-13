{include file="overall_header.tpl"}
<center>
<form action="" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="2">{$fb_settings}</th>
</tr><tr>
	<td colspan="2">{$fb_info}</td>
</tr><tr>
	<td colspan="2">{$fb_curl_info}</td>
</tr><tr>
	<td>{$fb_active}</td>
	<td><input name="fb_on"{if $fb_on == 1 && $fb_curl == 1} checked="checked"{/if} type="checkbox"{if $fb_curl == 0} disabled{/if}></td>
</tr><tr>
	<td>{$fb_api_key}</td>
	<td><input name="fb_apikey" size="40" value="{$fb_apikey}" type="text"{if $fb_curl == 0} disabled{/if}></td>
</tr><tr>
	<td>{$fb_secrectkey}</td>
	<td><input name="fb_skey" size="40" value="{$fb_skey}" type="text"{if $fb_curl == 0} disabled{/if}></td>
</tr></tr>
	<td colspan="3"><input value="{$se_save_parameters}" type="submit"{if $fb_curl == 0} disabled{/if}></td>
</tr>
</table>
</form>
</center>
{include file="overall_footer.tpl"}
