{block name="title" prepend}{$LNG.lm_trader}{/block}
{block name="content"}
	{if $requiredDarkMatter}
		<table class="table-gow table_full">
			<tr>
				<th>{$LNG.fcm_info}</th>
			</tr>
			<tr>
				<td><span style="color:red;">{$requiredDarkMatter}</span></td>
			</tr>
		</table>
	{/if}
	<table class="table-gow table_full">
		<thead>
			<tr>
				<th>{$LNG.tr_call_trader}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					{$LNG.tr_call_trader_who_buys}
				</td>
			</tr>
			<tr>
				<td>
					{foreach $charge as $resourceID => $chageData}
						{if !$requiredDarkMatter}
							<form action="game.php?page=trader" method="post">
								<input type="hidden" name="mode" value="trade">
								<input type="hidden" name="resource" value="{$resourceID}">
								<label for="trader_metal" class="color-blue fw-bold fs-11">{$LNG.tech.$resourceID}</label>
								<input type="image" id="trader_metal" src="{$dpath}images/{$resource.$resourceID}.gif"
									title="{$LNG.tech.$resourceID}" border="0" height="32" width="52">
							</form>
						{else}
							<img src="{$dpath}images/{$resource.$resourceID}.gif" title="{$LNG.tech.$resourceID}" border="0"
								height="32" width="52" style="margin: 3px;">
							<span>{$LNG.tech.$resourceID}</span>
						{/if}
					{/foreach}
				</td>
			</tr>
			<tr>
				<td>
					<p class="text-center fs-11 fw-bold color-white">{$tr_cost_dm_trader}</p>
				</td>
			</tr>
			<tr>
				<td>
					<p class="text-center fs-11 fw-bold color-white">{$LNG.tr_exchange_quota}:
						{$charge.901.903}/{$charge.902.903}/{$charge.903.903}</p>
				</td>
			</tr>
		</tbody>
	</table>
{/block}