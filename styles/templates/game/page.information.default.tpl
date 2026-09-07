{block name="title" prepend}{$LNG.lm_info}{/block}
{block name="content"}
	<table class="table_game table_full">
		<tbody>
			<tr>
				<th>{$LNG.tech.$element_id}</th>
			</tr>
			<tr>
				<td>
					<table class="table_full">
						<tr>

							<td class="transparent">
								<p>{$LNG.longDescription.$element_id}</p>
								{if !empty($bonus)}<p>
										<b>{$LNG.in_bonus}</b><br>
										{foreach $bonus as $BonusName => $elementBouns}
											{if $elementBouns[0] < 0}-
											{else}+
											{/if}
											{if $elementBouns[1] == 0}
												{abs($elementBouns[0] * 100)}%
											{else}
												{floatval($elementBouns[0])}
											{/if}
										{$LNG.bonus.$BonusName}<br>{/foreach}
								</p>{/if}
								{if !empty($fleet_info)}
									{if !empty($fleet_info.rapidfire.to)}<p>
											{foreach $fleet_info.rapidfire.to as $rapidfireID => $shoots}
												{$LNG.in_rf_again} {$LNG.tech.$rapidfireID}: <span
													style="color:#00ff00">{$shoots|number}</span><br>
											{/foreach}
									</p>{/if}
									{if !empty($fleet_info.rapidfire.from)}<p>
											{foreach $fleet_info.rapidfire.from as $rapidfireID => $shoots}
												{$LNG.in_rf_from} {$LNG.tech.$rapidfireID}: <span
													style="color:#ff0000">{$shoots|number}</span><br>
											{/foreach}
									</p>{/if}
								{/if}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	{if !empty($fleet_info)}
		{include file="shared.information.shipInfo.tpl"}
	{/if}
	{if !empty($gate_data)}
		{include file="shared.information.gate.tpl"}
	{/if}
	{if !empty($missile_list)}
		{include file="shared.information.missiles.tpl"}
	{/if}
	{if !empty($production_table.production)}
		{include file="shared.information.production.tpl"}
	{/if}
	{if !empty($production_table.storage)}
		{include file="shared.information.storage.tpl"}
	{/if}
{/block}