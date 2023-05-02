{block name="title" prepend}{$LNG.lm_buildings}{/block}
{block name="content"}
<script>
$(document).ready(function(){
  $("#searchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".filter").filter(function() {
      var result = $(this).text().toLowerCase().indexOf(value) > -1;
      if (result == false) {
        $($(this).parents().eq(2)).removeClass('d-flex').addClass('d-none');
      }else {
        $($(this).parents().eq(2)).addClass('d-flex').removeClass('d-none');
      }
    });
  });
});
</script>

{if !empty($Queue)}
 <div id="buildlist" class="d-flex flex-column align-items-center justify-content-center w-100 mx-auto my-2 py-2 bg-black">
		{foreach $Queue as $List}
		{$ID = $List.element}
		<div class="d-flex flex-column">
				{if !($isBusy.research && ($ID == 6 || $ID == 31)) && !($isBusy.shipyard && ($ID == 15 || $ID == 21)) && $RoomIsOk && $CanBuildElement && $BuildInfoList[$ID].buyable}
				<form class="d-flex fs-6" action="game.php?page=buildings" method="post">
					<span>{$List@iteration}&nbsp;-&nbsp;</span>
					<input type="hidden" name="cmd" value="insert">
					<input type="hidden" name="building" value="{$ID}">
					<button type="submit" class="build_submit onlist">{$LNG.tech.{$ID}} {$List.level}{if $List.destroy} {$LNG.bd_dismantle}{/if}</button>
				</form>
				{else}
				{$LNG.tech.{$ID}} {$List.level}
					{if $List.destroy}
						{$LNG.bd_dismantle}
					{/if}
				{/if}
				{if $List@first}
			<div id="progressbar" class="d-flex align-items-center mx-auto w-100 my-2" data-time="{$List.resttime}"></div>
		</div>
			<div class="d-flex flex-column my-2">
				<div class="text-center my-2" id="time" data-time="{$List.time}"></div>
				<form class="d-flex mx-auto align-items-center justify-content-center" action="game.php?page=buildings" method="post" >
					<input type="hidden" name="cmd" value="cancel">
					<button type="submit" class="btn btn-dark px-1 py-0 fs-6 text-secondary">{$LNG.bd_cancel}</button>
				</form>
				{else}
			</div>
			<div class="d-flex flex-column my-2">
				<form action="game.php?page=buildings" method="post" class="d-flex mx-auto align-items-center justify-content-center">
					<input type="hidden" name="cmd" value="remove">
					<input type="hidden" name="listid" value="{$List@iteration}">
					<button type="submit" class="btn btn-dark px-1 py-0 fs-6 text-secondary">{$LNG.bd_cancel}</button>
				</form>
				{/if}
				<span style="color:lime" data-time="{$List.endtime}" class="my-2">{$List.display}</span>
			</div>
	 {/foreach}
</div>
{/if}

<div class="d-flex align-items-start">
	<button style="min-width:60px;" id="btn1" class="btn bg-black border border-dark px-1 py-0 text-white fs-6">Mining</button>
	<button style="min-width:60px;" id="btn2" class="btn bg-black border border-dark px-1 py-0 text-white fs-6">Other</button>
	<button style="min-width:60px;" id="btn3" class="btn bg-black border border-dark px-1 py-0 text-white fs-6">All</button>
  <input style="width:150px;" id="searchInput" type="text" class="form-control bg-dark text-white h-100 p-1 m-1 my-auto fs-14" name="" value="" placeholder="search..">
</div>

{foreach $BuildInfoList as $ID => $Element}
{if ($ID == 1 || $ID == 2 || $ID == 3 || $ID == 4 || $ID == 12 || $ID == 22 || $ID == 23 || $ID == 24)}
<div class="infos d-flex my-1 rounded bg-black border border-1 border-dark py-2">
	<div class="d-flex align-items-center justify-content-center">
				<img class="mx-2 hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
	</div>
	<div class="d-flex flex-column w-100">
		<div class="bg-blue d-flex justify-content-start m-2 p-2 text-white fw-bold">
				<a href="#" class="fs-12 text-yellow hover-underline filter" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
				{if $Element.level > 0}
				<span class="fs-12 text-white px-2" id="val_{$ID}">
				({$LNG.bd_lvl} {$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if})
				</span>
				{/if}
		</div>
		<div class="d-flex mx-2 justify-content-between">
			<div class="m-0 p-0">
				<span class="d-flex">
					{foreach $Element.costResources as $RessID => $RessAmount}
						<a class="fs-12" href='#' onclick="return Dialog.info({$RessID});" data-bs-toggle="tooltip"
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
						</table>">{$LNG.tech.{$RessID}}:&nbsp;</a><span class="mx-1 fs-12 {if $Element.costOverflow[$RessID] == 0}color-green{else}color-red{/if}">{$RessAmount|number}</span>
					{/foreach}
				</span>
				<div class="d-flex flex-column">
				{if $Element.costOverflowTotal > 0}
				<span class="d-flex my-1 fs-12">{$LNG.bd_remaining}</span>
				{foreach $Element.costOverflow as $ResType => $ResCount}
				<div class="d-flex my-1 align-items-center">
					<a class="fs-12" onclick="return Dialog.info({$ResType});"
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
					>{$LNG.tech.{$ResType}}</a>
				<span class="fw-bold fs-12">:&nbsp;{$ResCount|number}</span>
			</div>
			{/foreach}
			{/if}
				</div>

			{if !empty($Element.infoEnergy)}
			<span class="d-flex my-1 fs-12">{$LNG.bd_next_level}&nbsp;{$Element.infoEnergy}</span>
			{/if}

			</div>
			<div class="infos_inner_right">
				{if $Element.maxLevel == $Element.levelToBuild}
					<span class="fs-12" style="color:#ffd600">{$LNG.bd_maxlevel} || <button>End Game</button></span>
				{elseif ($isBusy.research && ($ID == 6 || $ID == 31)) || ($isBusy.shipyard && ($ID == 15 || $ID == 21))}
					<span class="fs-12" style="color:#ffd600">{$LNG.bd_working}</span>
				{else}
					{if $RoomIsOk}
						{if $CanBuildElement && $Element.buyable}
						<form action="game.php?page=buildings" method="post" class="build_form">
							<input type="hidden" name="cmd" value="insert">
							<input type="hidden" name="building" value="{$ID}">
							<button type="submit" class="button-upgrade">{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_build}{else}{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}{/if}</button>
						</form>
						{else}
						<span class="fs-12" style="color:#ffd600">{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_build}{else}{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}{/if}</span>
						{/if}
					{else}
					<span class="fs-12" style="color:#ffd600">{$LNG.bd_no_more_fields}</span>
					{/if}
				{/if}
				<br>
        <span class="fs-12 my-1">{$LNG.fgf_time}:{$Element.elementTime|time}</span>

					 {if $Element.level > 0}
								{if $ID == 43}<a href="#" onclick="return Dialog.info({$ID})">{$LNG.bd_jump_gate_action}</a>{/if}
								{if ($ID == 44 && !$HaveMissiles) ||  $ID != 44}<br><button class="button-downgrade" data-bs-toggle="popover" data-bs-placement="top"  data-bs-content="
                <table>
                  <thead>
                    <tr>
                      <th colspan='2'>{$LNG.bd_price_for_destroy} {$LNG.tech.{$ID}} {$Element.level}</th>
                    </tr>
                  </thead>
                  <tbody>
                    {foreach $Element.destroyResources as $ResType => $ResCount}
                    <tr>
                      <td>{$LNG.tech.{$ResType}}</td>
                      <td><span style='color:{if empty($Element.destroyOverflow[$RessID])}lime{else}#ffd600{/if}'>{$ResCount|number}</span></td>
                    </tr>
                    {/foreach}
                    <tr>
                      <td>{$LNG.bd_destroy_time}</td>
                      <td>{$Element.destroyTime|time}</td>
                    </tr>
                    <tr>
                      <td colspan='2'>
                        <form action='game.php?page=buildings' method='post' class='build_form'>
                          <input type='hidden' name='cmd' value='destroy'>
                          <input type='hidden' name='building' value='{$ID}'>
                          <button type='submit' class='build_submit onlist'>{$LNG.bd_dismantle}</button>
                        </form>
                      </td>
                    </tr>
                  </tbody>
                </table>
                ">{$LNG.bd_dismantle}</button>{/if}
							{else}
								&nbsp;
							{/if}
			</div>
		</div>
	</div>
</div>
{else}
<div class="infos d-flex my-1 rounded bg-black border border-1 border-dark py-2">
	<div class="d-flex align-items-center justify-content-center">
			<img onclick="return Dialog.info({$ID})" class="mx-2 hover-pointer" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
	</div>
	<div class="d-flex flex-column w-100">
		<div class="bg-blue d-flex justify-content-start m-2 p-2 text-white fw-bold">
			<a href="#" class="fs-12 text-yellow hover-underline filter" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
				{if $Element.level > 0}
				<span class="fs-12 text-white px-2">
				 	({$LNG.bd_lvl} {$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if})
				</span>
				{/if}
		</div>
		<div class="d-flex mx-2 justify-content-between">
			<div class="m-0 p-0">
				<div class="d-flex flex-column">
						{foreach $Element.costResources as $RessID => $RessAmount}
						<div class="d-flex my-1">
						<a class="fs-12" href='#' onclick="return Dialog.info({$RessID});" data-bs-toggle="tooltip"
						data-bs-placement="left"
						data-bs-html="true" title="
						<table>
							<thead>
								<tr><th>{$LNG.tech.{$RessID}}</th></tr>
							</thead>
							<tbody>
								<tr>
									<td>
									 <img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'>
								  </td>
								</tr>
								<tr>
									<td>{$LNG.shortDescription.$RessID}</td>
								</tr>
							</tbody>">{$LNG.tech.{$RessID}}:&nbsp;</a>
							<span class="fs-12 {if $Element.costOverflow[$RessID] == 0}color-green{else}color-red{/if}">{$RessAmount|number}</span>
						</div>
					{/foreach}
				</div>
				{if $Element.costOverflowTotal > 0}
				<span  class="d-flex my-1 fs-12">{$LNG.bd_remaining}</span>
				<div class="d-flex flex-column align-items-start">
					{foreach $Element.costOverflow as $ResType => $ResCount}
					 <a class="my-1 fs-12" href='#' onclick="return Dialog.info({$ResType});" data-bs-toggle="tooltip"
					 data-bs-placement="left"
					 data-bs-html="true" title="
					 <table>
						 <thead>
							 <tr><th>{$LNG.tech.{$ResType}}</th></tr>
						 </thead>
						 <tbody>
							 <tr><td>{$LNG.shortDescription.$ResType}</td></tr>
						 </tbody>
					 </table>
						 ">{$LNG.tech.{$ResType}}&nbsp;:&nbsp;<span class="text-red fs-12">{$ResCount|number}</span></a>
					{/foreach}
				</div>

				{/if}
					{if !empty($Element.infoEnergy)}
					<span>{$LNG.bd_next_level}{$Element.infoEnergy}</span>
					{/if}
			</div>
			<div class="infos_inner_right">
				{if $Element.maxLevel == $Element.levelToBuild}
					<span style="color:#ffd600">{$LNG.bd_maxlevel}</span>
				{elseif ($isBusy.research && ($ID == 6 || $ID == 31)) || ($isBusy.shipyard && ($ID == 15 || $ID == 21))}
					<span style="color:#ffd600">{$LNG.bd_working}</span>
				{else}
					{if $RoomIsOk}
						{if $CanBuildElement && $Element.buyable}
						<form action="game.php?page=buildings" method="post" class="build_form">
							<input type="hidden" name="cmd" value="insert">
							<input type="hidden" name="building" value="{$ID}">
							<button type="submit" class="button-upgrade">{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_build}{else}{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}{/if}</button>
						</form>
						{else}
						<span style="color:#ffd600">{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_build}{else}{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}{/if}</span>
						{/if}
					{else}
					<span style="color:#ffd600">{$LNG.bd_no_more_fields}</span>
					{/if}
				{/if}
				<br>
        <span class="fs-12 my-1">{$LNG.fgf_time}:{$Element.elementTime|time}</span>
					{if $Element.level > 0}
										{if $ID == 43}
										<a href="#" onclick="return Dialog.info({$ID})">{$LNG.bd_jump_gate_action}</a>
										{/if}
										{if ($ID == 44 && !$HaveMissiles) ||  $ID != 44}
										<br><button class="button-downgrade my-1" data-bs-toggle="popover" data-bs-placement="top"  data-bs-content="
                    <table>
                      <thead>
                        <tr>
                          <th colspan='2'>{$LNG.bd_price_for_destroy} {$LNG.tech.{$ID}} {$Element.level}</th>
                        </tr>
                      </thead>
                      <tbody>
                        {foreach $Element.destroyResources as $ResType => $ResCount}
                        <tr>
                          <td>{$LNG.tech.{$ResType}}</td>
                          <td><span style='color:{if empty($Element.destroyOverflow[$RessID])}lime{else}#ffd600{/if}'>{$ResCount|number}</span></td>
                        </tr>
                        {/foreach}
                        <tr>
                          <td>{$LNG.bd_destroy_time}</td>
                          <td>{$Element.destroyTime|time}</td>
                        </tr>
                        <tr>
                          <td colspan='2'>
                            <form action='game.php?page=buildings' method='post' class='build_form'>
                              <input type='hidden' name='cmd' value='destroy'>
                              <input type='hidden' name='building' value='{$ID}'>
                              <button type='submit' class='build_submit onlist'>{$LNG.bd_dismantle}</button>
                            </form>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    ">{$LNG.bd_dismantle}</button>
							{/if}
							{else}
								&nbsp;
							{/if}
			</div>
		</div>
	</div>
</div>
{/if}
{/foreach}
{/block}
