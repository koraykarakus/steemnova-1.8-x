{block name="content"}

	<form action="admin.php?page=messagelist" method="post" id="form">
		<input type="hidden" name="side" value="{$c_page}" id="side">
		<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
			<tr>
				<th colspan="5">{$LNG.ml_message_list}</th>
			</tr>
			<tr>
				<td style="width:15%">{$LNG.ml_type}</td>
				<td style="width:35%">{html_options options=$categories selected=$type name="type"}</td>
				<td style="width:15%">{$LNG.ml_date_range}</td>
				<td style="width:17.5%"><input value="{$date_start.day|default:''}" type="text" id="dateStartDay"
						name="dateStart[day]" style="width:25px" maxlength="2" placeholder="dd">.<input
						value="{$date_start.month|default:''}" type="text" id="dateStartMonth" name="dateStart[month]"
						style="width:25px" maxlength="2" placeholder="mm">.<input value="{$date_start.year|default:''}"
						type="text" id="dateStartYear" name="dateStart[year]" style="width:35px" maxlength="4"
						placeholder="yyyy"></td>
				<td style="width:17.5%"><input value="{$date_end.day|default:''}" type="text" id="dateEndDay"
						name="dateEnd[day]" style="width:25px" maxlength="2" placeholder="dd">.<input
						value="{$date_end.month|default:''}" type="text" id="dateEndMonth" name="dateEnd[month]"
						style="width:25px" maxlength="2" placeholder="mm">.<input value="{$date_end.year|default:''}"
						type="text" id="dateEndYear" name="dateEnd[year]" style="width:35px" maxlength="4"
						placeholder="yyyy"></td>
			</tr>
			<tr>
				<td style="width:15%"><label for="sender">{$LNG.ml_sender}</label></td>
				<td style="width:35%"><input type="text" id="sender" name="sender" value="{$sender}" length="11"></td>
				<td style="width:15%"><label for="receiver">{$LNG.ml_receiver}</label></td>
				<td style="width:35%" colspan="2"><input type="text" id="receiver" name="receiver" value="{$receiver}"
						length="11"></td>
			</tr>
			<tr>
				<th colspan="5" class="center">
					<input type="submit" value="{$LNG.ml_type_submit}">
				</th>
			</tr>
		</table>
		<table width="90%" class="table table-dark table-striped table-sm fs-12 my-5 mx-auto">
			<tr>
				<th colspan="{if $type == 100}6{else}5{/if}">{$LNG.ml_message_list}</th>
			</tr>
			<tr style="height: 20px;">
				<td class="right" colspan="{if $type == 100}6{else}5{/if}">{$LNG.mg_page}: {if $c_page != 1}<a href="#"
					onclick="gotoPage({$c_page - 1});return false;">&laquo;</a> {/if}
				{for $site=1 to $max_page}<a href="#" onclick="gotoPage({$site});return false;">
						{if $site == $c_page}<span
								style="color:orange"><b>[{$site}]</b></span>{else}[{$site}]{/if}</a>{if $site != $max_page}
						{/if}{/for}{if $c_page != $max_page} <a href="#"
						onclick="gotoPage({$c_page + 1});return false;">&raquo;</a>{/if}</td>
			</tr>
			<tr>
				<th style="width:4%">{$LNG.ml_id}</th>
				{if $type == 100}<th>{$LNG.ml_type}</th>{/if}
				<th style="width:15%">{$LNG.ml_date}</th>
				<th>{$LNG.ml_sender}</th>
				<th>{$LNG.ml_receiver}</th>
				<th>{$LNG.ml_subject}</th>
			</tr>
			{foreach $message_list as $messageID => $messageRow}
				<tr data-messageID="{$messageID}">
					<td><a href="#" class="toggle">{$messageID}</a></td>
					{if $type == 100}<td><a href="#" class="toggle">{$LNG.mg_type[$messageRow.type]}</a></td>{/if}
					<td><a href="#" class="toggle">{$messageRow.time}</a></td>
					<td><a href="#" class="toggle">{$messageRow.sender}</a></td>
					<td><a href="#" class="toggle">{$messageRow.receiver}</a></td>
					<td><a href="#" class="toggle">{$messageRow.subject}{if $messageRow.deleted}&nbsp;<img
									src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip"
								data-tooltip-content="{$LNG.ml_deleted}">{/if}</a></td>
				</tr>
				<tr id="contentID{$messageID}" style="display:none;">
					<td class="left" colspan="{if $type == 100}6{else}5{/if}" style="padding:5px 8px;">{$messageRow.text}</td>
				</tr>
			{/foreach}
		</table>
	</form>
	<script src="scripts/admin/messageList.js"></script>

{/block}