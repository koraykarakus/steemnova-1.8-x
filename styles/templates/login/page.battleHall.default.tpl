{block name="title" prepend}{$LNG.siteTitleBattleHall}{/block}
{block name="content"}
	{if $isMultiUniverse}
		<p>
			{html_options options=$universe_select selected=$UNI class="changeUni" id="universe" name="universe"}
		</p>
	{/if}
	<table>
		<tr>
			<th style="color:lime">{$LNG.tkb_platz}</th>
			<th style="color:lime">{$LNG.tkb_owners}</th>
			<th style="color:lime">{$LNG.tkb_datum}</th>
			<th style="color:lime">{$LNG.tkb_units}</th>
		</tr>
		{foreach $hall_list as $c_hall}
			<tr>
				<td>{$c_hall@iteration}</td>
				<td>
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
				</td>
				<td>{$c_hall.time}</td>
				<td>{$c_hall.units|number}</td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="4">
				<p>{$LNG.tkb_legende}
					<span style="color:#00FF00">{$LNG.tkb_gewinner}</span>
					<span style="color:#FF0000">{$LNG.tkb_verlierer}</span>
				</p>
			</td>
		</tr>
	</table>
{/block}