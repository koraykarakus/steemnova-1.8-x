{block name="title" prepend}{$LNG.lm_officers}{/block}
{block name="content"}

{if !empty($darkmatterList)}
	<table class="table-gow table_full">
	<tr>
		<th colspan="2">{$of_dm_trade}</th>
	</tr>
	{foreach $darkmatterList as $ID => $Element}
	<tr>
		<td rowspan="2" style="width:120px;">
			<a href="#" onclick="return Dialog.info({$ID});">
				<img src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
			</a>
		</td>
		<th>
			<a href="#" onclick="return Dialog.info({$ID});">{$LNG.tech.{$ID}}</a>
		</th>
	</tr>
	<tr>
		<td>
			<table>
				<tbody>
					<tr>
						<td class="left" style="width:90%;padding:10px;">
							<p>{$LNG.shortDescription.{$ID}}</p>
							{foreach $Element.elementBonus as $BonusName => $Bonus}{if $Bonus@iteration % 3 === 1}<p>{/if}{if $Bonus[0] < 0}-{else}+{/if}{if $Bonus[1] == 0}{abs($Bonus[0] * 100)}%{else}{$Bonus[0]}{/if} {$LNG.bonus.$BonusName}{if $Bonus@iteration % 3 === 0 || $Bonus@last}</p>{else}&nbsp;{/if}{/foreach}
                            <p>{foreach $Element.costResources as $RessID => $RessAmount}
															<a href='#' onclick='return Dialog.info({$RessID})' data-bs-toggle="tooltip" data-bs-placement="top"
										          data-bs-html="true" title="<table class='table-tooltip fs-11'><tr><th>{$LNG.tech.{$RessID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$RessID}</td></tr></table></tr></table>">{$LNG.tech.{$RessID}}</a>: <b><span style="color:{if $Element.costOverflow[$RessID] == 0}lime{else}red{/if}">{$RessAmount|number}</span></b>{/foreach} | {$LNG.in_dest_durati}: <span style="color:lime">{$Element.time|time}</span></p>
						</td>
						<td style="vertical-align:middle;width:100px">
						{if $Element.timeLeft > 0}
							{$LNG.of_still}<br>
							<span class="color-red" id="time_{$ID}">-</span>
							<span class="color-green">({$LNG.of_active})</span>
							{if $Element.buyable}
							<form action="game.php?page=officers" method="post" class="build_form">
								<input type="hidden" name="id" value="{$ID}">
								<button type="submit" class="build_submit">{$LNG.of_recruit}</button>
							</form>
							{/if}
						{elseif $Element.buyable}
						<form action="game.php?page=officers" method="post" class="build_form">
							<input type="hidden" name="id" value="{$ID}">
							<button type="submit" class="build_submit">{$LNG.of_recruit}</button>
						</form>
						{else}
						<span style="color:#FF0000">{$LNG.of_recruit}</span>
						{/if}
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	{/foreach}
</table>
{/if}
{if $officer_list}
<table class="table-gow table_full">
	<tr>
		<th colspan="2">{$LNG.of_offi}</th>
	</tr>
	{foreach $officer_list as $ID => $Element}
	<tr>
		<td rowspan="2" style="width:120px;">
			<a href="#" onclick="return Dialog.info({$ID})">
				<img src="{$dpath}gebaeude/{$ID}.jpg" alt="{$LNG.tech.{$ID}}" width="120" height="120">
			</a>
		</td>
		<th>
			<a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a> ({$LNG.of_lvl} {$Element.level}/{$Element.maxLevel})
		</th>
	</tr>
	<tr>
		<td>
			<table>
				<tbody>
					<tr>
						<td style="width:90%;padding:0px 10px 10px 10px;">
							<p>{$LNG.shortDescription.{$ID}}</p>
							<p>{foreach $Element.elementBonus as $BonusName => $Bonus}{if $Bonus[0] < 0}-{else}+{/if}{if $Bonus[1] == 0}{abs($Bonus[0] * 100)}%{else}{floatval($Bonus[0])}{/if} {$LNG.bonus.$BonusName}<br>{/foreach}</p>
                            <p>{foreach $Element.costResources as $RessID => $RessAmount}<a href='#' onclick='return Dialog.info({$RessID})' data-bs-toggle="tooltip"
				          					data-bs-placement="top"
				          					data-bs-html="true" title="<table  class='table-tooltip fs-11'><tr><th>{$LNG.tech.{$RessID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$RessID}</td></tr></table></tr></table>">{$LNG.tech.{$RessID}}</a>: <b><span style="color:{if $Element.costOverflow[$RessID] == 0}lime{else}red{/if}">{$RessAmount|number}</span></b>{/foreach}</p>
						</td>
						<td style="vertical-align:middle;width:100px">
						{if $Element.maxLevel <= $Element.level}
							<span style="color:red">{$LNG.bd_maxlevel}</span>
						{elseif $Element.buyable}
							<form action="game.php?page=officers" method="post" class="build_form">
								<input type="hidden" name="id" value="{$ID}">
								<button type="submit" class="build_submit">{$LNG.of_recruit}</button>
							</form>
						{else}
							<span style="color:red">{$LNG.of_recruit}</span>
						{/if}
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	{/foreach}
</table>
{/if}

<script src="scripts/game/officers.js"></script>


{/block}
