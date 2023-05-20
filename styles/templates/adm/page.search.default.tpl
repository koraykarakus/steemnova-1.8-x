{block name="content"}

<form action="" method="POST">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
<td class="transparent left">
<input type="checkbox" {if isset($minimize)}{$minimize}{/if} name="minimize"><input type="submit" value="{$se_contrac}" class="button">
<img src="./styles/resource/images/admin/GO.png" onClick="javascript:$('#seeker').slideToggle();" style="cursor:pointer;padding-right:60px;" class="tooltip" data-tooltip-content="{$ac_minimize_maximize}">
</td>
</tr>
</table>
<div id="seeker"{if isset($diisplaay)}{$diisplaay}{/if}>
<table width="90%" class="table table-dark table-striped table-sm fs-12">
	<tr>
		<th colspan="8">
			{$se_search_title}
		</td>
	</tr>
	<tr>
		<td>
			{$se_intro}
		</td>
		<td>
			{$se_type_typee}
		</td>
		<td>
			{$se_search_in}
		</td>
		<td>
			{$se_filter_title}
		</td>
		<td>
			{$se_limit}
		</td>
		<td>
			{$se_asc_desc}
		</td>
		{if isset($OrderBYParse)}
		<td>
			{$se_search_order}
		</td>
		{/if}
		<td>
			&nbsp;
		</td>
	</tr>
<tr>
	<td>
		<input type="text" name="key_user" value="{$search}">
	</td>
	<td>
		{html_options name=search options=$Selector.list selected=$SearchFile}
	</td>
	<td>
		{html_options name=search_in options=$Selector.search selected=$SearchFor}
	</td>
	<td>
		{html_options name=fucki options=$Selector.filter selected=$SearchMethod}
	</td>
	<td>
		{html_options name=limit options=$Selector.limit selected=$limit}
	</td>
	<td>
		{html_options name=key_acc options=$Selector.order selected=$OrderBY}
	</td>
	{if isset($OrderBYParse)}
	<td>
		{html_options name=key_order options=$OrderBYParse selected=$Order}
	</td>
	{/if}
	<td>
		<input type="submit" value="{$se_search}">
	</td>
</tr>
{if !empty($error)}
<tr>
	<td colspan="8">
		<span style="color:red">{$error}</span>
	</td>
</tr>
{/if}
</table>
</div>
<table class="table table-dark table-striped table-sm fs-12">
{$PAGES}
</table>
{$LIST}
<table class="table table-dark table-striped table-sm fs-12">
{$PAGES}
</table>
</form>

{/block}
