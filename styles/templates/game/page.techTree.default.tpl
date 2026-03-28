
{block name="title" prepend}
{$LNG.lm_technology}
{/block}

{block name="content"}

<script>
  function toggleTechDiv(id){
    if($('.h' + id).hasClass('d-none')){
      $('.h' + id).addClass('d-flex').removeClass('d-none');
      $('#button_icon_' + id ).removeClass('bi-plus-circle').addClass('bi-dash-circle');
    }else {
      $('.h' + id).addClass('d-none').removeClass('d-flex');
      $('#button_icon_' + id).removeClass('bi-dash-circle').addClass('bi-plus-circle');
    }
  }
</script>

<div class="techWrapper">
{foreach $tech_tree_list as $elementID => $requireList}
	{if !is_array($requireList)}
	<div class="techb" id="{$requireList}">
		<button class="" onclick="toggleTechDiv(id = {$elementID});">
			<i id="button_icon_{$requireList}" class="bi bi-plus-circle"></i>
		</button>
		<span class="">{$LNG.tech.$requireList}</span>
	</div>
	{else}
		{if $requireList}
			<table class="table-gow table_full">
				<thead>
					<tr>
						<th style="text-align: left;" colspan="2">
							<a class="color-yellow" href="#" onclick="return Dialog.info({$elementID})">{$LNG.tech.$elementID}</a>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 100px;">
							<a href="#" onclick="return Dialog.info({$elementID})">
								<img src="{$dpath}elements/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}" width="100" height="100">
							</a>
						</td>
						<td>
							<table class="table-gow center_x table_full">
								<thead>
									<tr>
										<th>{$LNG.tt_requirements}:</th>
									</tr>
								</thead>
								<tbody>
								{foreach $requireList as $requireID => $NeedLevel}
									<tr>
										<td class="text_center">
											<a href="#" onclick="return Dialog.info({$requireID})">
												<span class="{if $NeedLevel.own < $NeedLevel.count}color-yellow{else}color-green{/if}">{$LNG.tech.$requireID} ({$LNG.tt_lvl} {$NeedLevel.own}/{$NeedLevel.count})</span>
											</a>
										</td>
									</tr>
								{/foreach}
								</tbody>
							</table>	
						</td>
					</tr>
				</tbody>
			</table>
		<div class="d-none {if ($elementID < 100)}h0{elseif ($elementID < 200)}h100{elseif ($elementID < 300)}h200{elseif ($elementID < 500)}h400{elseif ($elementID < 600)}h500{elseif ($elementID < 700)}h600{/if}">
		
		
		
		</div>
		{/if}
	{/if}
{/foreach}
</div>

{/block}
