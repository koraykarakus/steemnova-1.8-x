{block name="title" prepend}{$LNG.fcm_info}{/block}
{block name="content"}
	<table class="table-gow table_full">
		<tr>
			<th>{$LNG.fcm_info}</th>
		</tr>
		<tr>
			<td>
				<p class="text-center">{$message}</p>
				{if !empty($redirectButtons)}
					<p>
						{foreach $redirectButtons as $button}
							{if isset($button.url) && $button.label}
								<a href="{$button.url}">
									<button class="text-yellow fs-12">{$button.label}</button>
								</a>
							{/if}
						{/foreach}
					</p>
				{/if}
			</td>
		</tr>
	</table>
{/block}