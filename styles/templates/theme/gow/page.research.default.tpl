{block name="title" prepend}{$LNG.lm_research}{/block}
{block name="content"}

{if !empty($Queue)}
<div id="buildlist" class="d-flex flex-column align-items-center justify-content-center w-100 mx-auto my-2 py-2 bg-black">
		{foreach $Queue as $List}
		{$ID = $List.element}
		<div class="d-flex flex-column">

				{if isset($ResearchList[$List.element])}
				{$CQueue = $ResearchList[$List.element]}
				{/if}
				{if isset($CQueue) && $CQueue.maxLevel != $CQueue.level && !$IsFullQueue && $CQueue.buyable}
				<form action="game.php?page=research" method="post" class="d-flex fs-6">
					<span>{$List@iteration}&nbsp;-&nbsp;</span>
					<input type="hidden" name="cmd" value="insert">
					<input type="hidden" name="tech" value="{$ID}">
					<button type="submit" class="build_submit onlist">{$LNG.tech.{$ID}} {$List.level}{if !empty($List.planet)} @ {$List.planet}{/if}</button>
				</form>
				{else}
				{$LNG.tech.{$ID}} {$List.level}{if !empty($List.planet)} @ {$List.planet}{/if}
				{/if}
				{if $List@first}
				<div id="progressbar" class="d-flex align-items-center mx-auto w-100 my-2" data-time="{$List.resttime}"></div>
			</div>
			<div class="d-flex flex-column my-2">
				<div class="text-center my-2" id="time" data-time="{$List.time}"></div>
				<form action="game.php?page=research" method="post"class="d-flex mx-auto align-items-center justify-content-center">
					<input type="hidden" name="cmd" value="cancel">
					<button type="submit" class="btn btn-dark px-1 py-0 fs-6 text-secondary">{$LNG.bd_cancel}</button>
				</form>
				{else}
			</div>
			<div class="d-flex flex-column my-2">
				<form action="game.php?page=research" method="post" class="d-flex mx-auto align-items-center justify-content-center">
					<input type="hidden" name="cmd" value="remove">
					<input type="hidden" name="listid" value="{$List@iteration}">
					<button type="submit" class="btn btn-dark px-1 py-0 fs-6 text-secondary">{$LNG.bd_cancel}</button>
				</form>
				{/if}
				<span style="color:lime" data-time="{$List.endtime}" class="timer">{$List.display}</span>
			</div>
	{/foreach}
</div>
{/if}

{if $IsLabinBuild}
<div class="hidden-div">{$LNG.bd_building_lab}</div>
{/if}

<div class="planeto">
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="lab1">Imperial</button>
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="lab2">Military</button>
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="lab3">Engins</button>
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="lab4">Mining</button>
	<button style="min-width:60px;" class="btn bg-black border border-dark px-1 py-0 text-white fs-6" id="lab5">All</button>
</div>

	{foreach $ResearchList as $ID => $Element}
<div class="infos" id="t{$ID}">
	<div class="infos_left">
		<a href="#" onclick="return Dialog.info({$ID})">
			<img style="float: left;" src="{$dpath}gebaeude/{$ID}.gif" width="120" height="120">
		</a>
	</div>
	<div class="infos_right">
		<div class="infos_right_top">
			<a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
			{if $Element.level != 0}
			<span class="yellow">({$LNG.bd_lvl} {$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if})</span>
			{/if}
		</div>
		<div class="infos_right_bottom">
			<div class="infos_inner_left">
				<span>
					{foreach $Element.costResources as $RessID => $RessAmount}
		        <a href='#' onclick='return Dialog.info({$RessID})' data-bs-toggle="tooltip"
						data-bs-placement="left"
						data-bs-html="true" title="<table>
							<thead>
								<tr><th>{$LNG.tech.{$RessID}}</th></tr>
							</thead>
							<tbody>
								<tr>
									<td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td>
								</tr>
								<tr>
									<td>{$LNG.shortDescription.$RessID}</td>
								</tr>
							</tbody>
						</table>">{$LNG.tech.{$RessID}}:</a><b><span style="color:{if $Element.costOverflow[$RessID] == 0}lime{else}red{/if}">{$RessAmount|number}</span></b>
					{/foreach}
				</span>
				<span>{$LNG.bd_remaining}</span>
				{foreach $Element.costOverflow as $ResType => $ResCount}
		  	<a href='#' onclick='return Dialog.info({$ResType})' data-bs-toggle="tooltip"
				data-bs-placement="left"
				data-bs-html="true" title="<table>
					<thead>
						<tr><th>{$LNG.tech.{$RessID}}</th></tr>
					</thead>
					<tbody>
						<tr>
							<td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td>
						</tr>
						<tr>
							<td>{$LNG.shortDescription.$RessID}</td>
						</tr>
					</tbody>
				</table>">{$LNG.tech.{$ResType}}&nbsp;:&nbsp;{$ResCount|number}</a>
				{/foreach}
			</div>
			<div class="infos_inner_right">
				{if $Element.maxLevel == $Element.levelToBuild}
						<span style="color:#ffd600">{$LNG.bd_maxlevel}</span>
					{elseif $IsLabinBuild || $IsFullQueue || !$Element.buyable}
						<span style="color:#ffd600">{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_tech}{else}{$LNG.bd_tech_next_level}{$Element.levelToBuild + 1}{/if}</span>
					{else}
						<form action="game.php?page=research" method="post" class="build_form">
							<input type="hidden" name="cmd" value="insert">
							<input type="hidden" name="tech" value="{$ID}">
							<button type="submit" class="build_submit">{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_tech}{else}{$LNG.bd_tech_next_level}{$Element.levelToBuild + 1}{/if}</button>
						</form>
					{/if}
					</br>
						{$LNG.fgf_time}
						{$Element.elementTime|time}
			</div>

		</div>

	</div>
</div>
	{/foreach}
{/block}


{block name="script" append}
    {if !empty($Queue)}
        <script src="scripts/game/research.js"></script>
    {/if}
{/block}
