{block name="title" prepend}{$LNG.lm_buildings}{/block}
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

  <div class="items_wrapper">
    <div class="top" style="background:url('{$dpath}images/buildings.jpg');" class="bg-black border-orange">
      {foreach $BuildInfoList as $ID => $Element}
        <div class="item_big hidden" id="item_big_{$ID}">
          <div class="top">
            <div class="left">
              <div class="img_wrapper">
                <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}gebaeude/{$ID}.gif"
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
                    {if !empty($Element.infoEnergyLong)}
                      <span class="hover-pointer {if {$Element.infoEnergyShort} > 0}color-green{else}color-red{/if}">
                        {$LNG.bd_next_level}&nbsp;{$Element.infoEnergyLong}
                      </span>
                    {/if}
                  </div>
                  <div class="right">
                    {if $Element.level > 0}
                      {if $ID == 43}
                        <a href="#" onclick="return Dialog.info({$ID})">{$LNG.bd_jump_gate_action}</a>
                      {/if}
                      {if ($ID == 44 && !$HaveMissiles) ||  $ID != 44}
                        <form action='game.php?page=buildings' method='post' class='destroy_form'>
                          <input type='hidden' name='cmd' value='destroy'>
                          <input type='hidden' name='building' value='{$ID}'>
                          <button type='submit' class='button-downgrade'>{$LNG.bd_dismantle}</button>
                          <div class="tooltip tooltip_bottom">
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
                                    <td>
                                      <span
                                        style='color:{if empty($Element.destroyOverflow[$ResType])}lime{else}#ffd600{/if}'>{$ResCount|number}</span>
                                    </td>
                                  </tr>
                                {/foreach}
                                <tr>
                                  <td>{$LNG.bd_destroy_time}</td>
                                  <td>{$Element.destroyTime|time}</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </form>
                      {/if}
                    {/if}
                  </div>
                </div>
                <div class="bottom">
                  <div class="left">
                    {foreach $Element.costResources as $RessID => $RessAmount}
                      <div class="resource">
                        <div class="tooltip tooltip_top">
                          {$LNG.tech.$RessID}
                        </div>
                        <img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'>
                        <span
                          class="{if $Element.costOverflow[$RessID] == 0}text-white{else}color-red{/if}">{$RessAmount|number}</span>
                      </div>
                    {/foreach}
                  </div>
                  <div class="right">
                    <div class="">
                      {if $Element.maxLevel == $Element.levelToBuild}
                        <span style="color:#ffd600">{$LNG.bd_maxlevel} || <button>End Game</button></span>
                      {elseif ($isBusy.research && ($ID == 6 || $ID == 31)) || ($isBusy.shipyard && ($ID == 15 || $ID == 21))}
                        <span style="color:#ffd600">{$LNG.bd_working}</span>
                      {else}
                        {if $RoomIsOk}
                          {if $CanBuildElement && $Element.buyable && $Element.technologySatisfied}
                            <form action="game.php?page=buildings" method="post" class="build_form">
                              <input type="hidden" name="cmd" value="insert">
                              <input type="hidden" name="building" value="{$ID}">
                              <button type="submit" class="button-upgrade">
                                {$LNG.bd_build}
                              </button>
                              <div class="tooltip tooltip_top">
                                {$LNG.bd_build_next_level}{$Element.levelToBuild + 1}
                              </div>
                            </form>
                          {else}
                            <span class="fs-12" style="color:#ffd600">
                              <button data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                title="{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}" class="button-upgrade" type="button"
                                name="button" disabled>
                                {$LNG.bd_build}
                              </button>
                            </span>
                          {/if}
                        {else}
                          <span style="color:#ffd600">{$LNG.bd_no_more_fields}</span>
                        {/if}
                      {/if}
                    </div>
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
    </div>
    <div class="bottom">
      <div class="title">
        <span class="color-yellow">{$LNG.lm_buildings}&nbsp;(&nbsp;{$usedField}&nbsp;/&nbsp;{$maxField}&nbsp;)</span>
        <span class="color-yellow">&nbsp;|&nbsp;{$LNG.st_points}&nbsp;[{$userBuildPoints}]</span>
      </div>
      <div class="list">
        {foreach $BuildInfoList as $ID => $Element}
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
                  {if !$Element.technologySatisfied && !empty($Element.requeriments)}
                    <tr>
                      <th colspan='2' class='color-red'>{$LNG.tech_not_satisfied}</th>
                    </tr>
                    {foreach $Element.requeriments as $currentRequire}
                      <tr>
                        <td class='color-red'>
                          <img class='mx-2 hover-pointer' src='{$dpath}gebaeude/{$currentRequire.requireID}.gif'
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
            {if !$CanBuildElement || !$RoomIsOk || !$Element.buyable || !$Element.technologySatisfied || ($isBusy.research && ($ID == 6 || $ID == 31)) || ($isBusy.shipyard && ($ID == 15 || $ID == 21))}
              <div class="black-screen d-flex position-absolute top-0 end-0 hover-pointer"></div>
            {/if}
            <img class="hover-pointer" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="90" height="90">
            {if $CanBuildElement && $RoomIsOk && $Element.buyable && $Element.technologySatisfied && !($isBusy.research && ($ID == 6 || $ID == 31)) && !($isBusy.shipyard && ($ID == 15 || $ID == 21))}
              <form action="game.php?page=buildings" method="post" class="">
                <input type="hidden" name="cmd" value="insert">
                <input type="hidden" name="building" value="{$ID}">
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
              {$LNG.tech.{$ID}}&nbsp;,&nbsp;<span class="timer" data-time='{$List.endtime}'>{$List.display}</span>
            </div>
            <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}gebaeude/{$ID}.gif"
              alt="{$LNG.tech.{$ID}}" width="80" height="80">
            <span class="level_info">{$List.level}</span>

            <form action="game.php?page=buildings" method="post">
              {if !$List@first}
                <input type="hidden" name="listid" value="{$List@iteration}">
                <input type="hidden" name="cmd" value="remove">
              {else}
                <input type="hidden" name="cmd" value="cancel">
              {/if}
              <button class="btn_cancel" type="submit" name="button">
                BTN
                <div class="tooltip tooltip_top">{$LNG.bd_cancel}</div>
              </button>
            </form>
          </div>
          {if $List@first}
            <div class="queue_right">
              <div style="border-radius:10px;height:12px;" id="progressbar" class="" data-time="{$List.resttime}"></div>
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