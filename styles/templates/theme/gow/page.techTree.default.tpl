
{block name="title" prepend}
{$LNG.lm_technology}{/block}

{block name="content"}

<style>
.techi {
        display:none;	}
.minus {
        display:none;	}
</style>

{if $messages}
	<div class="message"><a href="?page=messages">{$messages}</a></div>
{/if}

<div class="techWrapper">

{foreach $TechTreeList as $elementID => $requireList}

{if !is_array($requireList)}
<div class="techb" id="{$requireList}">
	<button>
		<span class="plus" id="{$requireList}s"><i class="fa fa-plus"></i></span>
  	<span class="minus" id="{$requireList}h"><i class="fa fa-minus"></i></span>
	</button>
	<span>{$LNG.tech.$requireList}</span>
</div>
{else}
	{if $requireList}
	<div class="techi" id="h{$elementID}">
	<span>
		<a href="#" onclick="return Dialog.info({$elementID})">{$LNG.tech.$elementID}</a>
	</span><br>
	<a href="#" onclick="return Dialog.info({$elementID})">
		<img src="{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}" width="89">
	</a><br>
	<span>{$LNG.tt_requirements}:</span><br>
		{foreach $requireList as $requireID => $NeedLevel}
			<a href="#" onclick="return Dialog.info({$requireID})">
				<span style="color:{if $NeedLevel.own < $NeedLevel.count}#ffd600{else}lime{/if};">{$LNG.tech.$requireID} ({$LNG.tt_lvl} {$NeedLevel.own}/{$NeedLevel.count})</span>
			</a><br>
			{if !$NeedLevel@last}
			{/if}
		{/foreach}
	</div>
	{/if}
{/if}
{/foreach}
</div>

{/block}
