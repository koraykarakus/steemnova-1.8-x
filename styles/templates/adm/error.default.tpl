{block name="title" prepend}{$LNG.fcm_info}{/block}
{block name="content"}
<table class="table table-dark fs-12 w-100 my-5">
	<tr>
		<th>{$LNG.fcm_info}</th>
	</tr>
	<tr>
		<td>
			<p>{$message}</p>
			{if !empty($redirectButtons)}
			<p>
				{foreach $redirectButtons as $button}
				{if isset($button.url) && $button.label}
					<a href="{$button.url}">
						<button class="btn bg-secondary text-white py-0 px-2 fs-11">{$button.label}</button>
					</a>
				{/if}
				{/foreach}
			</p>
			{/if}
		</td>
	</tr>
</table>
{/block}
