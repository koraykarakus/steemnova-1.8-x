{block name="title" prepend}{$LNG.siteTitleBattleHall}{/block}
{block name="content"}
	{if $isMultiUniverse}
		<p>
			{html_options options=$universe_select selected=$UNI class="changeUni" id="universe" name="universe"}
		</p>
	{/if}
	{if !empty($hall_list)}
		<table class="table" style="table-layout:fixed; width:100%; margin:0;">
			<thead>
				<tr>
					<th class="text-center" style="color:lime; padding:8px;">{$LNG.tkb_platz}</th>
					<th class="text-center" style="color:lime; padding:8px;">{$LNG.tkb_owners}</th>
					<th class="text-center" style="color:lime; padding:8px;">{$LNG.tkb_datum}</th>
					<th class="text-center" style="color:lime; padding:8px;">{$LNG.tkb_units}</th>
				</tr>
			</thead>

			<tbody>
				{foreach $hall_list as $c_hall}
					<tr style="margin:0;">
						<td style="padding:6px;">#{$c_hall@iteration}</td>

						<td class="text-center" style="padding:6px; white-space:nowrap;">
							<a href="report.php?page=report&id={$c_hall.rid}" style="text-decoration:none;">
								{if $c_hall.result == "a"}
									<span style="color:#00FF00">{$c_hall.attacker}</span>
									<span style="color:#FFFFFF"><b> VS </b></span>
									<span style="color:#FF0000">{$c_hall.defender}</span>
								{elseif $c_hall.result == "r"}
									<span style="color:#FF0000">{$c_hall.attacker}</span>
									<span style="color:#FFFFFF"><b> VS </b></span>
									<span style="color:#00FF00">{$c_hall.defender}</span>
								{else}
									{$c_hall.attacker}<b> VS </b>{$c_hall.defender}
								{/if}
							</a>
						</td>

						<td class="text-center" style="padding:6px;">{$c_hall.time}</td>
						<td class="text-center" style="padding:6px;">{$c_hall.units|number}</td>
					</tr>
				{/foreach}

				<tr>
					<td colspan="4" style="padding:6px; text-align:left; font-size:12px; line-height:1.4;">
						{$LNG.tkb_legende}
						<span style="color:#00FF00; margin-left:8px;">{$LNG.tkb_gewinner}</span>
						<span style="color:#FF0000; margin-left:8px;">{$LNG.tkb_verlierer}</span>
					</td>
				</tr>
			</tbody>
		</table>
	{else}
		<span>{$LNG.bh_no_battles}</span>
	{/if}

{/block}