{block name="content"}

<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
	<tr>
		<th colspan=2>{$log_info}</th>
	</tr>
	<tr>
		<td>{$log_admin}:</td>
		<td>{$admin}</td>
	</tr>
	<tr>
		<td>{$log_target}:</td>
		<td>{$target}</td>
	</tr>
	<tr>
		<td>{$log_time}:</td>
		<td>{$time}</td>
	</tr>
</table>
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th>{$log_element}</th>
	<th>{$log_old}</th>
	<th>{$log_new}</th>
</tr>
{foreach item=LogInfo from=$LogArray}
{if ($LogInfo.old <> $LogInfo.new)}
<tr>
	<td>{$LogInfo.Element}</td>
	<td>{$LogInfo.old}</td>
	<td>{$LogInfo.new}</td>
</tr>
{/if}
{/foreach}
</table>
</body>

{/block}
