{block name="title" prepend}{$LNG.fcm_info}{/block}
{block name="content"}
<div class="container">
	<div class="bg-black mx-auto text-white p-3 my-3 d-flex flex-column align-items-center">
		<span class="fs-14 text-yellow my-2">{$LNG.fcm_info}</span>
		<span class="my-2 fs-14">{$message}</span>
		{if !empty($redirectButtons)}
		<p class="my-2 fs-14">
			{foreach $redirectButtons as $button}
			{if isset($button.url) && $button.label}
				<a href="{$button.url}">
					<button class="btn btn-primary text-white py-0 px-2 fs-11">{$button.label}</button>
				</a>
			{/if}
			{/foreach}
		</p>
		{/if}
	</div>
</div>
{/block}
