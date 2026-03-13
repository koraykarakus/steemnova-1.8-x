
{block name="title" prepend}
{$LNG.lm_technology}{/block}

{block name="content"}



<script>
  function toggleTechDiv(id){
    console.log($('#h' + id).hasClass('flex-column'));
    if($('.h' + id).hasClass('d-none')){
      $('.h' + id).addClass('d-flex').removeClass('d-none');
      $('#button_icon_' + id ).removeClass('bi-plus-circle').addClass('bi-dash-circle');
    }else {
      $('.h' + id).addClass('d-none').removeClass('d-flex');
      $('#button_icon_' + id).removeClass('bi-dash-circle').addClass('bi-plus-circle');
    }
    console.log(id);
  }
</script>


<div class="techWrapper">

{foreach $TechTreeList as $elementID => $requireList}

{if !is_array($requireList)}
<div class="techb" id="{$requireList}">
	<button class="text-white" onclick="toggleTechDiv(id = {$elementID});">
		<i id="button_icon_{$requireList}" class="bi bi-plus-circle"></i>
	</button>
	<span class="text-white">{$LNG.tech.$requireList}</span>
</div>
{else}
	{if $requireList}
	<div class="d-none {if ($elementID < 100)}h0{elseif ($elementID < 200)}h100{elseif ($elementID < 300)}h200{elseif ($elementID < 500)}h400{elseif ($elementID < 600)}h500{elseif ($elementID < 700)}h600{/if}">
	<span>
		<a class="color-yellow" href="#" onclick="return Dialog.info({$elementID})">{$LNG.tech.$elementID}</a>
	</span>
	<a href="#" onclick="return Dialog.info({$elementID})">
		<img src="{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}" width="89" height="89">
	</a>
	<span>{$LNG.tt_requirements}:</span>
		{foreach $requireList as $requireID => $NeedLevel}
			<a class="" href="#" onclick="return Dialog.info({$requireID})">
				<span class="{if $NeedLevel.own < $NeedLevel.count}color-yellow{else}color-green{/if}">{$LNG.tech.$requireID} ({$LNG.tt_lvl} {$NeedLevel.own}/{$NeedLevel.count})</span>
			</a>
			{if !$NeedLevel@last}
			{/if}
		{/foreach}
	</div>
	{/if}
{/if}
{/foreach}
</div>

{/block}
