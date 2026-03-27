{block name="title" prepend}{$LNG.fcm_info}{/block}
{block name="content"}
	<table class="table-gow table_full">
		<tr>
			<th>{$LNG.fcm_info}</th>
		</tr>
		<tr>
			<td class="text_center">
				{$message}
			</td>
		</tr>
		{if !empty($redirect_buttons)}
		<tr>
			<td class="text_center">
				{foreach $redirect_buttons as $button}
					{if isset($button.url) && $button.label}
						<a href="{$button.url}">
							<button class="text-yellow">{$button.label}</button>
						</a>
					{/if}
				{/foreach}	
			</td>
		</tr>
		{/if}
	</table>
{/block}