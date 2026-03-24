{block name="title" prepend}{$LNG.lm_research}{/block}
{block name="content"}

  <script>
    function showItem(id) {

      if ($('#item_big_' + id).hasClass('hidden')) {
        $('.item_big').addClass('hidden');
        $('.item_small').removeClass('border-color-active').removeClass('border-color-passive');
        $('#item_big_' + id).removeClass('hidden');
        $('#item_small_' + id).addClass('border-color-active');
      } else {
        $('#item_big_' + id).addClass('hidden');
        $('#item_small_' + id).addClass('border-color-passive').removeClass('border-color-active');
      }

    }
  </script>

  {if $IsLabinBuild}
    <div class="hidden-div">{$LNG.bd_building_lab}</div>
  {/if}


  <div class="items_wrapper">
    <div class="top" style="background:url('{$dpath}images/research.jpg');" class="bg-black border-orange">
      {foreach $ResearchList as $ID => $Element}
        <div class="item_big hidden" id="item_big_{$ID}">
          <div class="top">
            <div class="left">
              <div class="img_wrapper">
                <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}elements/{$ID}.gif"
                  alt="{$LNG.tech.{$ID}}" width="203" height="203">
              </div>
            </div>
            <div class="right">
              <div class="title">
                <span
                  class="element_name{if $Element.costOverflowTotal > 0} color-red hover-pointer{else} color-yellow{/if}">
                  {if $Element.costOverflowTotal > 0}
                    <div class="tooltip tooltip_bottom">
                      <table>
                        <thead>
                          <tr>
                            <th colspan='2'>{$LNG.bd_remaining}</th>
                          </tr>
                        </thead>
                        <tbody>
                          {foreach $Element.costOverflow as $ResType => $ResCount}
                            <tr>
                              <td>{$LNG.tech.$ResType}</td>
                              <td>{$ResCount|number}</td>
                            </tr>
                          {/foreach}
                        </tbody>
                      </table>
                    </div>
                  {/if}
                  {$LNG.tech.{$ID}}
                </span>
                <span id="val_{$ID} p-0">
                  {$LNG.bd_lvl}&nbsp;{$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if}
                  <button class="button_close" onclick="showItem({$ID});">X</button>
                </span>
              </div>
              <div class="requirements">
                <div class="top">
                  <div class="left">
                    <span>{$LNG.fgf_time}&nbsp;:&nbsp;
                      {pretty_time($Element.elementTime)}
                    </span>
                  </div>
                  <div class="right">
                  </div>
                </div>
                <div class="bottom">
                  <div class="left">
                    {foreach $Element.costResources as $RessID => $RessAmount}
                      <div class="resource">
                        <div class="tooltip tooltip_top">
                          {$LNG.tech.$RessID}
                        </div>
                        <img src='{$dpath}elements/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'>
                        <span
                          class="{if $Element.costOverflow[$RessID] == 0}text-white{else}color-red{/if}">{$RessAmount|number}</span>
                      </div>
                    {/foreach}
                  </div>
                  <div class="right">
                    {if $Element.maxLevel == $Element.levelToBuild}
                      <span style="color:#ffd600">{$LNG.bd_maxlevel} || <button>End Game</button></span>
                    {elseif $IsLabinBuild || $IsFullQueue || !$Element.buyable}
                      <span style="color:#ffd600">
                        <button class="button-upgrade" type="button" name="button" disabled>
                          {$LNG.bd_build}
                          <div class="tooltip tooltip_top">
                            {$LNG.bd_build_next_level}{$Element.levelToBuild + 1}
                          </div>
                        </button>
                      </span>
                    {else}
                      <form action="game.php?page=research" method="post" class="build_form">
                        <input type="hidden" name="cmd" value="insert">
                        <input type="hidden" name="tech" value="{$ID}">
                        <button type="submit" class="button-upgrade">
                          {$LNG.bd_build}
                        </button>
                        <div class="tooltip tooltip_top">
                            {$LNG.bd_build_next_level}{$Element.levelToBuild + 1}
                          </div>
                      </form>
                    {/if}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bottom">
            <p class="text-white">{$LNG.shortDescription[$ID]}</p>
          </div>
        </div>
      {/foreach}
        <span class="page_title">{$current_pname} - {$LNG.lm_research}</span>
    </div>
    <div class="bottom">
      <div class="title">
        <span class="color-yellow">{$LNG.lm_research}</span>
        <span class="color-yellow">&nbsp;|&nbsp;{$LNG.st_points}&nbsp;[{$userTechPoints}]</span>
      </div>
      <div class="list">
        {foreach $ResearchList as $ID => $Element}
          <div class="item_small" onclick="showItem({$ID})" id="item_small_{$ID}">
            <div class="tooltip tooltip_top">
              <table>
                <thead>
                  <tr>
                    <th colspan="2">
                      {$LNG.tech.{$ID}}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  {if !$Element.technologySatisfied && !empty($Element.requirements)}
                    <tr>
                      <th colspan='2' class='color-red'>{$LNG.tech_not_satisfied}</th>
                    </tr>
                    {foreach $Element.requirements as $currentRequire}
                      <tr>
                        <td class='color-red'>
                          <img class='mx-2 hover-pointer' src='{$dpath}elements/{$currentRequire.requireID}.gif'
                            alt='{$LNG.tech.{$currentRequire.requireID}}' width='30' height='30'>
                        </td>
                        <td class='color-red align-middle text-start'><span
                            class='color-blue'>{$LNG.tech.{$currentRequire.requireID}}</span>&nbsp;({$currentRequire.neededLevel}&nbsp;/&nbsp;<span
                            class='color-yellow'>{$currentRequire.currentLevel}</span>)</td>
                      </tr>
                    {/foreach}
                  {/if}
                </tbody>
              </table>
            </div>
            <div class="level_info">
              {$Element.level}
            </div>
            {if $IsLabinBuild || $IsFullQueue || !$Element.buyable || !$Element.technologySatisfied}
              <div class="black-screen d-flex position-absolute top-0 end-0 hover-pointer"></div>
            {/if}
            <img class="hover-pointer" src="{$dpath}elements/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="90" height="90">
            {if !$IsLabinBuild && !$IsFullQueue && $Element.buyable && $Element.technologySatisfied}
              <form action="game.php?page=research" method="post">
                <input type="hidden" name="cmd" value="insert">
                <input type="hidden" name="tech" value="{$ID}">
                <button type="submit" class="button_upgrade_small">
                  <div class="tooltip tooltip_top">
                    {$LNG.bd_build_next_level}{$Element.levelToBuild + 1}
                  </div>
                </button>
              </form>
            {/if}
            <div class="name_info">
              {$LNG.tech.{$ID}}
            </div>
          </div>
        {/foreach}
      </div>
    </div>
  </div>

  {if !empty($Queue)}
    <div id="buildlist" class="queue_wrapper">
      {foreach $Queue as $List}
        {$ID = $List.element}
        <div class="queue_item{if $List@first} queue_item_first{/if}">
          <div class="queue_left">
            <div class="tooltip tooltip_top">
              {$LNG.tech.{$ID}}&nbsp;,&nbsp;<span data-time='{$List.endtime}' >{$List.display}</span>
            </div>
            <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}elements/{$ID}.gif"
              alt="{$LNG.tech.{$ID}}" width="80" height="80">
            <span class="level_info">{$List.level}</span>


            <form action="game.php?page=research" method="post">
              {if !$List@first}
                <input type="hidden" name="listid" value="{$List@iteration}">
                <input type="hidden" name="cmd" value="remove">
              {else}
                <input type="hidden" name="cmd" value="cancel">
              {/if}
              <button class="btn_cancel" type="submit" name="button">
                <div class="tooltip tooltip_top">
                  {$LNG.bd_cancel}
                </div>
              </button>
            </form>
          </div>
          {if $List@first}
            <div class="queue_right">
              <div id="progressbar" 
                data-time="{$List.resttime}"></div>
              <span class="text-yellow">{$LNG['tech'][{$ID}]}&nbsp;:&nbsp;{$List.level}</span>
              <span class="text-yellow" id="time" data-time="{$List.time}"></span>
              <span class="text-yellow">{$List.display}</span>
            </div>
          {/if}
        </div>

      {/foreach}
    </div>
  {/if}

{/block}