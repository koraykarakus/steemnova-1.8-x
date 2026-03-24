{block name="title" prepend}{$LNG.lm_messages}{/block}
{block name="content"}
	<table class="table-gow table_full">
		<tr>
			<th colspan="6">{$LNG.mg_overview}<span id="loading" style="display:none;">
					({$LNG.loading})</span></th>
		</tr>
		{foreach $CategoryList as $CategoryID => $CategoryRow}
			{if ($CategoryRow@iteration % 6) === 1}<tr>{/if}
				{if $CategoryRow@last && ($CategoryRow@iteration % 6) !== 0}<td>&nbsp;</td>{/if}
				<td onclick="window.location.href='game.php?page=messages&category={$CategoryID}'"
					class="{if $CategoryID == $MessID}bg-black{/if} text-center align-middle hover-pointer bg-hover-black"
					style="word-wrap: break-word;color:{$CategoryRow.color};">
					<div>
						<div>
							<span href="" style="color:{$CategoryRow.color};">{$LNG.mg_type.{$CategoryID}}</a>
						</div>
						<div>
							<span id="unread_{$CategoryID}">{$CategoryRow.unread}</span>
							<span>/</span>
							<span data-total-number="{$CategoryRow.total}" 
								id="total_{$CategoryID}">{$CategoryRow.total}</span>
						</div>
					</div>
				</td>
				{if $CategoryRow@last || ($CategoryRow@iteration % 6) === 0}
			</tr>{/if}
		{/foreach}
	</table>
	<form action="game.php?page=messages" method="post">
		<input type="hidden" name="mode" value="action">
		<input type="hidden" name="ajax" value="1">
		<input type="hidden" name="messcat" value="{$MessID}">
		<input type="hidden" name="side" value="{$messagePage}">
		<table id="messagestable" class="table-gow table_full">
			<tr>
				<th colspan="5">{$LNG.mg_message_title}</th>
			</tr>		
			<tr>
				{if $MessID != 999}
				<td style="width: 50%;" class="text_center">
					<select style="height:28px;" name="actionTop">
						<option value="readmarked">{$LNG.mg_read_marked}</option>
						<option value="readtypeall">{$LNG.mg_read_type_all}</option>
						<option value="readall">{$LNG.mg_read_all}</option>
						<option value="deletemarked">{$LNG.mg_delete_marked}</option>
						<option value="deleteunmarked">{$LNG.mg_delete_unmarked}</option>
						<option value="deletetypeall">{$LNG.mg_delete_type_all}</option>
						<option value="deleteall">{$LNG.mg_delete_all}</option>
					</select>
					<input style="height:28px;" value="{$LNG.mg_confirm}" type="submit" name="submitTop">
				</td>
				{/if}
				<td style="width: 50%;" class="text_center">
					<span>{$LNG.mg_page}:</span>
					<button style="min-width:32px;" type="button"
						onclick="window.location.href='game.php?page=messages&category={$MessID}&side=1'"
						class="text-yellow">&laquo;</button>
					{if $messagePage > 5}..&nbsp;{/if}
					{for $site=1 to $maxPage}
						{if ($site > $messagePage-5 && $site < $messagePage+5)}
							<button style="min-width:32px;" type="button"
								onclick="window.location.href='game.php?page=messages&category={$MessID}&side={$site}'"
								class="{if $site == $messagePage}{else}{/if} text-yellow">
								{$site}
							</button>
						{/if}
					{/for}
					{if $messagePage < $maxPage-4}..&nbsp;{/if}
					<button style="min-width:32px;" type="button"
						onclick="window.location.href='game.php?page=messages&category={$MessID}&side={$maxPage}'"
						class="text-yellow">&raquo;</a>
				</td>

			</tr>
			
			
		</table>
		<table id="messagestable" class="table-gow table_full">
			<tr>
				<td class="color-blue" style="width:40px;">{$LNG.mg_action}</td>
				<td class="color-blue">{$LNG.mg_date}</td>
				<td class="color-blue">{if $MessID != 999}{$LNG.mg_from}{else}{$LNG.mg_to}{/if}
				</td>
				<td class="color-blue">{$LNG.mg_subject}</td>
			</tr>
			{foreach $MessageList as $Message}
				<tr id="message_{$Message.id}"
					class="message_{$Message.id} message_head{if $MessID != 999 && $Message.unread == 1} mes_unread{/if}">
					<td>
						{if $MessID != 999}
							<input name="messageID[{$Message.id}]" value="1" type="checkbox">
						{/if}
					</td>
					<td>{$Message.time}</td>
					<td>{$Message.from}</td>
					<td>{$Message.subject}
						{if $Message.type == 1 && $MessID != 999}
							<a href="#" onclick="return Dialog.PM({$Message.sender}, Message.CreateAnswer('{$Message.subject}'));"
								title="{$LNG.mg_answer_to} {strip_tags($Message.from)}"><img src="{$dpath}img/m.gif" border="0"></a>
						{/if}
					</td>
					<td>
						{if $MessID != 999}
							<a href="#" onclick="Message.deleteMessage({$Message.id}, {$Message.type});return false;">
								<img src="{$dpath}img/deletemsg.png">
							</a>
						{/if}
					</td>
				</tr>
				<tr class="message_{$Message.id} messages_body{if $MessID != 999 && $Message.unread == 1} mes_unread{/if}">
					<td colspan="5" class="left">
						<p class="message_{$MessID}">{$Message.text}</p>
					</td>
				</tr>
			{/foreach}
			
			
		</table>
	</form>
{/block}