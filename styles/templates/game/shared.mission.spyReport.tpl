<div class="spyRaport">
	<div class="text-white">
		<a class="text-decoration-none" href="game.php?page=galaxy&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}">{$title}</a>
	</div>
	{foreach $spyData as $Class => $elementIDs}
	<div class="">
	<span class="">{$LNG.tech.$Class}</span>
	<div class="">
		{foreach $elementIDs as $elementID => $amount}
		<div class="">
			<a class="hover-underline" href='#' onclick='return Dialog.info({$elementID})' data-bs-toggle="tooltip"
		data-bs-placement="left"
		data-bs-html="true" title="
		<table class='table-tooltip'>
			<thead>
				<tr>
					<th colspan='2'>{$LNG.tech.{$elementID}}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><img src='./styles/theme/gow/elements/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td>
				</tr>
				<tr>
					<td>{$LNG.shortDescription.$elementID}</td>
				</tr>
			</tbody>
		</table>
		">{$LNG.tech.$elementID}:</a>
		<span class="">{$amount|number}</span>
	</div>
	{/foreach}
</div>
	</div>
	{/foreach}
	<div class="">
		<a class="color-blue hover-underline" href="game.php?page=fleetTable&amp;galaxy={$targetPlanet.galaxy}&amp;system={$targetPlanet.system}&amp;planet={$targetPlanet.planet}&amp;planettype={$targetPlanet.planet_type}&amp;target_mission=1">{$LNG.type_mission_1}</a>
		<span class="color-blue text-center">
			{if $targetChance >= $spyChance}
			 {$LNG.sys_mess_spy_destroyed}
			{else}
			 {sprintf($LNG.sys_mess_spy_lostproba, $targetChance)}
			{/if}
		</span>
		{if $isBattleSim}
		<a class="color-blue hover-underline" href="game.php?page=battleSimulator{foreach $spyData as $Class => $elementIDs}{foreach $elementIDs as $elementID => $amount}&amp;im[{$elementID}]={$amount}{/foreach}{/foreach}">{$LNG.fl_simulate}</a>
		{/if}
	</div>
</div>
