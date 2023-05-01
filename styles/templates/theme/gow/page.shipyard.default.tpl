{block name="title" prepend}{if $mode == "defense"}{$LNG.lm_defenses}{else}{$LNG.lm_shipshard}{/if}{/block}
{block name="content"}
<script src="./scripts/base/avoid_submit_on_refresh.js" type="text/javascript"></script>

{if !$NotBuilding}
<span class="d-flex justify-content-center rounded p-2 text-danger fw-bold bg-dark border border-2 border-danger mx-auto my-2 w-100" id="infobox">
	{$LNG.bd_building_shipyard}
</span>
{/if}
{if !empty($BuildList)}
<div style="width:auto;" class="d-flex flex-column mx-auto w-50 bg-black align-items-center my-2 rounded">
		<div id="bx" class="z my-2 text-center w-100"></div>
		<form class="d-flex flex-column" action="game.php?page=shipyard&amp;mode={$mode}" method="post" >
			<input type="hidden" name="action" value="delete">
			<select class="mx-2 p-2 rounded" name="auftr[]" id="auftr" onchange="this.form.myText.setAttribute('size', this.value);" multiple class="shipl">
				<option>&nbsp;</option>
			</select>
			<span class="text-center text-danger fw-bold my-2">{$LNG.bd_cancel_warning}</span>
			<button style="width:auto;" class="btn btn-secondary text-white fw-bold" type="submit"/>{$LNG.bd_cancel_send}</button>
		</form>
		<span class="text-center my-2" id="timeleft"></span>
</div>
{/if}

{if $mode != "defense"}
<div class="d-flex align-items-start">
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="ship1">Civil</button>
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="ship2">Military</button>
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="ship3">All</button>
</div>
{/if}

{foreach $elementList as $ID => $Element}
<div class="infos d-flex my-1 rounded bg-black border border-1 border-dark py-2" id="s{$ID}">
	<div class="d-flex align-items-center justify-content-center">
			<img onclick="return Dialog.info({$ID})" class="mx-2 hover-pointer" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
	</div>
	<div class="d-flex flex-column w-100">
		<div class="bg-blue d-flex justify-content-start m-2 p-2 text-white fw-bold">
			<a href="#" class="fs-12 text-yellow" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
			{if $Element.available != 0}
			<span class="fs-12 text-white px-2" id="val_{$ID}">
					( {$LNG.bd_available} {$Element.available|number} )
			</span>
			{/if}
		</div>
		<div class="d-flex mx-2 justify-content-between">
			<div class="m-0 p-0">
				{if $Element.costOverflowTotal > 0}
				<span class="d-flex my-1 fs-12">{$LNG.bd_remaining}</span>
				{foreach $Element.costOverflow as $ResType => $ResCount}
				<span class="d-flex justify-content-start align-items-center my-1 fs-12">
					<a class="d-flex align-items-center" onclick="return Dialog.info({$ResType});"
					data-bs-toggle="tooltip"
					data-bs-placement="left"
					data-bs-html="true"
					title = '<table class="table table-dark table-striped p-0 m-0">
						<thead>
							<tr>
								<th>{$LNG.tech.{$ResType}}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
								 <img src="{$dpath}gebaeude/{$ResType}.{if $ResType >=600 && $ResType <= 699}jpg{else}gif{/if}">
							  </td>
							</tr>
							<tr>
								<td>
									<span class="d-flex text-start">{$LNG.shortDescription.$ResType}</span>
							  </td>
							</tr>
						</tbody>
					</table>'
					>
						{$LNG.tech.{$ResType}}
						<span class="fw-bold fs-12">:&nbsp;{$ResCount|number}</span>
					</a>

				</span>
				{/foreach}
				{/if}

				<span class="d-flex flex-column my-1 fs-12 justify-content-start">
					<span class="fs-12 text-yellow my-1">{$LNG.bd_max_ships_long}:</span>
					<span class="fw-bold fs-12 my-1">({$Element.maxBuildable|number})</span>
				</span>
			</div>
				<form class="d-flex flex-column align-items-end" action="game.php?page=shipyard&amp;mode={$mode}" method="post" id="s{$ID}">
						<span>
							{foreach $Element.costResources as $RessID => $RessAmount}
						<a class="my-1 fs-12" href='#' onclick='return Dialog.info({$RessID})' data-bs-toggle="tooltip"
						data-bs-placement="top"
						data-bs-html="true"
						title = '<table class="table table-dark table-striped p-0 m-0">
							<thead>
								<tr>
									<th>{$LNG.tech.{$RessID}}</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
									 <img src="{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}">
								  </td>
								</tr>
								<tr>
									<td>
										<span class="d-flex text-start">{$LNG.shortDescription.$RessID}</span>
								  </td>
								</tr>
							</tbody>
						</table>'>
						{$LNG.tech.{$RessID}}
					</a>:
						<span class="my-1 fs-12 {if $Element.costOverflow.$RessID == 0}color-green{else}color-red{/if}">
							{$RessAmount|number}
						</span>
					{/foreach}
					</span>
					{if $ID==212}
						<span class="fs-12"> +{$SolarEnergy} {$LNG.tech.911}</span>
					{/if}
						<div class="my-1">
						{if $Element.AlreadyBuild}
							<span class="fs-12 color-red">{$LNG.bd_protection_shield_only_one}</span>
						{elseif $NotBuilding && $Element.buyable}
							<input class="p-1 fs-12" type="text" name="fmenge[{$ID}]" id="input_{$ID}" size="3" maxlength="{$maxlength}" value="0" tabindex="{$smarty.foreach.FleetList.iteration}" >
							<input class="p-1 fs-12" type="button" value="{$LNG.bd_max_ships}" onclick="$('#input_{$ID}').val('{$Element.maxBuildable}')">
							<input class="b p-1 fs-12" type="submit" value="{$LNG.bd_build_ships}">
						{/if}
						</div>
						<span class="my-1 fs-12 text-right">{$LNG.fgf_time}&nbsp;:&nbsp;{$Element.elementTime|time}</span>
				</form>

		</div>

	</div>

</div>
{/foreach}

{if $NotBuilding}
<div class="planeto"></div>
{/if}

{/block}
{block name="script" append}
<script type="text/javascript">
data			= {$BuildList|json};
bd_operating	= '{$LNG.bd_operating}';
bd_available	= '{$LNG.bd_available}';
</script>

{if !empty($BuildList)}
<script src="scripts/base/bcmath.js"></script>
<script src="scripts/game/shipyard.js"></script>
<script type="text/javascript">
$(function() {
    ShipyardInit();
});

</script>
{/if}



{/block}
