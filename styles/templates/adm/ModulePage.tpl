{include file="overall_header.tpl"}
<center>
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
    <th colspan="3">{$mod_module}</th>
</tr>
<tr>
    <td colspan="3"><strong>{$mod_info}</strong></td>
</tr>
{foreach key=ID item=Info from=$Modules}
<tr>
	<td>{$Info.name}</td>
	{if $Info.state == 1}
		<td style="color:green"><b>{$mod_active}</b></td>
		<td><a href="?page=module&amp;mode=deaktiv&amp;id={$ID}">{$mod_change_deactive}</a></td>
	{else}
		<td style="color:red"><b>{$mod_deactive}</b></td>
		<td><a href="?page=module&amp;mode=aktiv&amp;id={$ID}">{$mod_change_active}</a></td>
	{/if}
	</tr>
{/foreach}
</table>
</center>
{include file="overall_footer.tpl"}
