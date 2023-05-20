{block name="content"}

<center>
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
    <th colspan="3">{$LNG.mod_module}</th>
</tr>
<tr>
    <td colspan="3"><strong>{$LNG.mod_info}</strong></td>
</tr>
{foreach key=ID item=Info from=$Modules}
<tr>
	<td>{$Info.name}</td>
	{if $Info.state == 1}
		<td style="color:green"><b>{$LNG.mod_active}</b></td>
		<td><a href="?page=module&mode=change&type=deaktivate&id={$ID}">{$LNG.mod_change_deactive}</a></td>
	{else}
		<td style="color:red"><b>{$LNG.mod_deactive}</b></td>
		<td><a href="?page=module&mode=change&type=activate&id={$ID}">{$LNG.mod_change_active}</a></td>
	{/if}
	</tr>
{/foreach}
</table>
</center>

{/block}
