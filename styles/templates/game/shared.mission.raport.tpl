{block name="title" prepend}{$pageTitle}{/block}
{block name="content"}
	{if isset($Info)}
		<table class="table_game center_x">
			<tr>
				<td class="text_center">
					<span style="color:{if $Raport.result == "a"}lime{elseif $Raport.result == "r"}red{else}white{/if}">{$Info.0}</span>
					<span>VS</span>
					<span style="color:{if $Raport.result == "r"}lime{elseif $Raport.result == "a"}red{else}white{/if}">{$Info.1}</span>
				</td>
			</tr>
		</table>
	{/if}
	<div style="width:100%;text-align:center">
		{if $Raport.mode == 1}{$LNG.sys_destruc_title}{else}{$LNG.sys_attack_title}{/if}
		{$Raport.time}:<br><br>
		{foreach $Raport.rounds as $Round => $RoundInfo}
			<table style="width:auto;" class="table_game center_x">
				<tr>
					{foreach $RoundInfo.attacker as $Player}
						{$PlayerInfo = $Raport.players[$Player.userID]}
						<td class="transparent">
							<table class="mx-auto">
								<tr>
									<td>
										{$LNG.sys_attack_attacker_pos} {$PlayerInfo.name}
										{if isset($Info)}([XX:XX:XX])
										{else}([{$PlayerInfo.koords[0]}:{$PlayerInfo.koords[1]}:{$PlayerInfo.koords[2]}]
											{if isset($PlayerInfo.koords[3])}
											({$LNG["type_planet_short_{$PlayerInfo.koords[3]}"]}){/if})
										{/if}<br>
										{$LNG.sys_ship_weapon} {$PlayerInfo.tech[0]}% - {$LNG.sys_ship_shield}
										{$PlayerInfo.tech[1]}% - {$LNG.sys_ship_armour} {$PlayerInfo.tech[2]}%
										<table class="table_game center_x">
											{if !empty($Player.ships)}
												<tr>
													<td class="transparent">{$LNG.sys_ship_type}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$LNG.shortNames.{$ShipID}}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_count}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[0]|number}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_weapon}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[1]|number}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_shield}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[2]|number}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_armour}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[3]|number}</td>
													{/foreach}
												</tr>
											{else}
												<tr>
													<td class="transparent">
														<br>{$LNG.sys_destroyed}<br><br>
													</td>
												</tr>
											{/if}
										</table>
									</td>
								</tr>
							</table>
						</td>
					{/foreach}
				</tr>
			</table>
			<table style="width:auto;" class="table_game center_x">
				<tr>
					{foreach $RoundInfo.defender as $Player}
						{$PlayerInfo = $Raport.players[$Player.userID]}
						<td class="transparent">
							<table>
								<tr>
									<td>
										{$LNG.sys_attack_defender_pos} {$PlayerInfo.name}
										{if isset($Info)}([XX:XX:XX])
										{else}([{$PlayerInfo.koords[0]}:{$PlayerInfo.koords[1]}:{$PlayerInfo.koords[2]}]
											{if isset($PlayerInfo.koords[3])}
											({$LNG.type_planet_short[$PlayerInfo.koords[3]]}){/if})
										{/if}<br>
										{$LNG.sys_ship_weapon} {$PlayerInfo.tech[0]}% - {$LNG.sys_ship_shield}
										{$PlayerInfo.tech[1]}% - {$LNG.sys_ship_armour} {$PlayerInfo.tech[2]}%
										<table class="table_game center_x">
											{if !empty($Player.ships)}
												<tr>
													<td class="transparent">{$LNG.sys_ship_type}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$LNG.shortNames.{$ShipID}}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_count}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[0]|number}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_weapon}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[1]|number}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_shield}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[2]|number}</td>
													{/foreach}
												</tr>
												<tr>
													<td class="transparent">{$LNG.sys_ship_armour}</td>
													{foreach $Player.ships as $ShipID => $ShipData}
														<td class="transparent">{$ShipData[3]|number}</td>
													{/foreach}
												</tr>
											{else}
												<tr>
													<td class="transparent">
														<br>{$LNG.sys_destroyed}<br><br>
													</td>
												</tr>
											{/if}
										</table>
									</td>
								</tr>
							</table>
						</td>
					{/foreach}
				</tr>
			</table>
			{if !$RoundInfo@last}
				{$LNG.fleet_attack_1} {$RoundInfo.info[0]|number} {$LNG.fleet_attack_2} {$RoundInfo.info[3]|number}
				{$LNG.damage}<br>
				{$LNG.fleet_defs_1} {$RoundInfo.info[2]|number} {$LNG.fleet_defs_2} {$RoundInfo.info[1]|number} {$LNG.damage}<br>
				<hr>
			{/if}
		{/foreach}
		<br><br>
		{if $Raport.result == "a"}

			<span class="">{$LNG.sys_attacker_won}</span><br><br>

			<span class="">
				{$LNG.sys_stealed_ressources} {foreach $Raport.steal as $elementID => $amount}{$amount|number}
					{$LNG.tech.$elementID}{if ($amount@index + 2) == count($Raport.steal)} {$LNG.sys_and}
					{elseif !$amount@last},
					{/if}
				{/foreach}
			</span>

		{elseif $Raport.result == "r"}
			<span class="">{$LNG.sys_defender_won}</span>
		{else}
			<span class="">{$LNG.sys_both_won}</span>
		{/if}
		<br><br>
		<span class="">{$LNG.sys_attacker_lostunits} {$Raport['units'][0]|number} {$LNG.sys_units}</span>
		<br>
		<span class="">{$LNG.sys_defender_lostunits} {$Raport['units'][1]|number} {$LNG.sys_units}</span>
		<br>
		<span class="">{$LNG.debree_field_1}
			{foreach $Raport.debris as $elementID => $amount}{$amount|number}
				{$LNG.tech.$elementID}{if ($amount@index + 2) == count($Raport.debris)} {$LNG.sys_and}
				{elseif !$amount@last},
				{/if}
			{/foreach}{$LNG.debree_field_2}</span>
		<br><br>
		{if $Raport.mode == 1}
			{* Destruction *}
			{if $Raport.moon.moonDestroySuccess == -1}
				{* Attack not win *}
				{$LNG.sys_destruc_stop}<br>
			{else}
				{* Attack win *}
				{sprintf($LNG.sys_destruc_lune, "{$Raport.moon.moonDestroyChance}")}<br>{$LNG.sys_destruc_mess1}
				{if $Raport.moon.moonDestroySuccess == 1}
					{* Destroy success *}
					{$LNG.sys_destruc_reussi}
				{elseif $Raport.moon.moonDestroySuccess == 0}
					{* Destroy failed *}
					{$LNG.sys_destruc_null}
				{/if}
				<br>
				{sprintf($LNG.sys_destruc_rip, "{$Raport.moon.fleetDestroyChance}")}
				{if $Raport.moon.fleetDestroySuccess == 1}
					{* Fleet destroyed *}
					<br>{$LNG.sys_destruc_echec}
				{/if}
			{/if}
		{else}
			{* Normal Attack *}
			<span class="">{$LNG.sys_moonproba} {$Raport.moon.moonChance} %</span>
			<br>
			{if !empty($Raport.moon.moonName)}
				{if isset($Info)}
					{* Moon created (HoF Mode) *}
					{sprintf($LNG.sys_moonbuilt, "{$Raport.moon.moonName}", "XX", "XX", "XX")}
				{else}
					{* Moon created *}
					{sprintf($LNG.sys_moonbuilt, "{$Raport.moon.moonName}", "{$Raport.koords[0]}", "{$Raport.koords[1]}",
					"{$Raport.koords[2]}")}
				{/if}
			{/if}
		{/if}

		<span class="">{$Raport.additionalInfo}</span>

	</div>
{/block}