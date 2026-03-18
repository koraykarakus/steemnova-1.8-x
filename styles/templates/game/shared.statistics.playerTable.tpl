<tr>
	<th style="width:20px;">{$LNG.st_position}</th>
	<th>{$LNG.st_player}</th>
	<th>&nbsp;</th>
	<th>{$LNG.st_alliance}</th>
	<th>{$LNG.st_points}</th>
</tr>
{foreach name=RangeList item=RangeInfo from=$RangeList}
<tr>
	<td>
		<a class="ranking">
			<div class="tooltip tooltip_top">
				{if $RangeInfo.ranking == 0}
					<span style='color:#87CEEB'>*</span>
				{elseif $RangeInfo.ranking < 0}
					<span style='color:red'>-{$RangeInfo.ranking}</span>
				{elseif $RangeInfo.ranking > 0}
					<span style='color:green'>+{$RangeInfo.ranking}</span>
				{/if}
			</div>
			{$RangeInfo@iteration + $range - 1}
		</a>
	</td>
	<td>
		<a class="hover-underline hover-pointer color-white
		{if $RangeInfo.id != $CUser_id && !empty($RangeInfo.class)}
		{foreach $RangeInfo.class as $class}
		galaxy-short-{$class} galaxy-short
		{break}
		{/foreach}
		{/if}" href="#" onclick="return Dialog.Playercard({$RangeInfo.id}, '{$RangeInfo.name}');"{if $RangeInfo.id == $CUser_id} style="color:lime"{/if}>{$RangeInfo.name}&nbsp;</a>
		{if $RangeInfo.is_leader}
			<a class="leader" style="color:yellow">
				<div class="tooltip tooltip_top">
					<table>
						<thead>
							<tr>
								<th colspan='2' style='text-align:center;'>{$RangeInfo.ally_owner_range}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class='transparent'>{$RangeInfo.allyname}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<i class="fas fa-trophy">A</i>
			</a>
		{/if}
	{if $RangeInfo.id != $CUser_id && !empty($RangeInfo.class)}
		{foreach $RangeInfo.class as $class}
		<span class="galaxy-short-{$class} galaxy-short">({$ShortStatus.$class})</span>
		{break}
		{/foreach}
	{/if}
</td>
	<td class="text_center">
		{if $RangeInfo.id != $CUser_id}
			<a class="msg" href="#" onclick="return Dialog.PM({$RangeInfo.id});">
				<img src="{$dpath}img/m.gif" alt="{$LNG.st_write_message}">
				<div class="tooltip tooltip_top">
					{$LNG.st_write_message}
				</div>
			</a>
		{/if}
	</td>
	<td class="text_center color-blue">
		{if $RangeInfo.allyid != 0}
		<a class="hover-underline hover-pointer" href="game.php?page=alliance&amp;mode=info&amp;id={$RangeInfo.allyid}">
			{if $RangeInfo.allyid == $CUser_ally}
			<span class="color-blue">{$RangeInfo.allyname}</span>
			{else}
			{$RangeInfo.allyname}
			{/if}
		</a>
		{else}-{/if}
	</td>
	<td class="text_right">
		{$RangeInfo.points}
	</td>
</tr>
{/foreach}
