{block name="title" prepend}{$LNG.lm_buildings}{/block}
{block name="content"}

  <script>
    function showItem(id) {

      if ($('#item_big_' + id).hasClass('hidden')) {
        $('.item_big').addClass('hidden');
        $('.buildItemSmall').removeClass('border-color-active').removeClass('border-color-passive');
        $('#item_big_' + id).removeClass('hidden');
        $('#item_small_' + id).addClass('border-color-active');
      } else {
        $('#item_big_' + id).addClass('hidden');
        $('#item_small_' + id).addClass('border-color-passive').removeClass('border-color-active');
      }

    }
  </script>

  <div class="items_wrapper">
    <div class="top" style="background:url('{$dpath}images/buildings.webp');" class="bg-black border-orange">
      {foreach $BuildInfoList as $ID => $Element}
        <div class="item_big hidden" id="item_big_{$ID}">
          <img class="hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}gebaeude/{$ID}.gif"
            alt="{$LNG.tech.{$ID}}" width="120" height="120">
          <div>
            <span class="{if $Element.costOverflowTotal > 0}color-red hover-pointer{else}color-yellow{/if}">
              {if $Element.costOverflowTotal > 0}
                <div class="tooltip">
                  <table class='table-tooltip fs-11'>
                    <thead>
                      <tr>
                        <th colspan='2' class='text-center'>{$LNG.bd_remaining}</th>
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
            <span class="text-white" id="val_{$ID} p-0">
              &nbsp;({$LNG.bd_lvl}&nbsp;{$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if})
            </span>
            {if !empty($Element.infoEnergyLong)}
              <span class="hover-pointer {if {$Element.infoEnergyShort} > 0}color-green{else}color-red{/if}">
                <div class="tooltip">
                  <span class="fs-12">{$LNG.bd_next_level}&nbsp;{$Element.infoEnergyLong}</span>
                </div>
                <i class="bi bi-activity"></i>&nbsp;{$Element.infoEnergyShort}
              </span>
            {/if}
          </div>
          <div class="">
            <div class="">
              {foreach $Element.costResources as $RessID => $RessAmount}
                <div class="">
                  <img data-bs-toggle="tooltip" data-bs-placement="left" data-bs-html="true" title="{$LNG.tech.$RessID}"
                    src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'>
                  <span
                    class="{if $Element.costOverflow[$RessID] == 0}text-white{else}color-red{/if}">{$RessAmount|number}</span>
                </div>
              {/foreach}
            </div>
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
                      <button type="submit" class="button-upgrade" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-html="true" title="{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}">
                        {$LNG.bd_build}
                      </button>
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

              {if $Element.level > 0}
                {if $ID == 43}<a href="#" onclick="return Dialog.info({$ID})">{$LNG.bd_jump_gate_action}</a>{/if}
                {if ($ID == 44 && !$HaveMissiles) ||  $ID != 44}
                  <form action='game.php?page=buildings' method='post' class='build_form'>
                    <input type='hidden' name='cmd' value='destroy'>
                    <input type='hidden' name='building' value='{$ID}'>
                    <button type='submit' class='button-downgrade'>{$LNG.bd_dismantle}</button>
                    <div class="tooltip">
                      <table class='table-tooltip fs-11'>
                        <thead>
                          <tr>
                            <th colspan='2'>{$LNG.bd_price_for_destroy} {$LNG.tech.{$ID}} {$Element.level}</th>
                          </tr>
                        </thead>
                        <tbody>
                          {foreach $Element.destroyResources as $ResType => $ResCount}
                            <tr>
                              <td>{$LNG.tech.{$ResType}}</td>
                              <td><span style='color:

                              {if empty($Element.destroyOverflow[$RessID])}lime

                              {else}#ffd600

                              {/if}'>{$ResCount|number}</span></td>
                            </tr>

                          {/foreach}
                          <tr>
                            <td>{$LNG.bd_destroy_time}</td>
                            <td>{$Element.destroyTime|time}</td>
                          </tr>
                          <tr>
                            <td colspan='2'>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </form>
                {/if}
              {/if}
            </div>
          </div>
          <div>
            <span>{$LNG.fgf_time}&nbsp;:&nbsp;
              {pretty_time($Element.elementTime)}
            </span>
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
            <img class="hover-pointer" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="80" height="80">
            {if $CanBuildElement && $RoomIsOk && $Element.buyable && $Element.technologySatisfied && !($isBusy.research && ($ID == 6 || $ID == 31)) && !($isBusy.shipyard && ($ID == 15 || $ID == 21))}
              <form action="game.php?page=buildings" method="post" class="position-absolute top-0 left-0">
                <input type="hidden" name="cmd" value="insert">
                <input type="hidden" name="building" value="{$ID}">
                <button type="submit" class="button-upgrade-small" data-bs-toggle="tooltip" data-bs-placement="top"
                  data-bs-html="true" title="{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}">
                </button>
              </form>
            {/if}
          </div>
        {/foreach}
      </div>
    </div>
  </div>

  {if !empty($Queue)}
    <div id="buildlist"
      class="ItemsWrapper d-flex flex-wrap justify-content-start w-100 mx-auto my-2 py-2 bg-black border-orange">
      {foreach $Queue as $List}
        {$ID = $List.element}
        <div class="d-flex align-items-center {if $List@first}w-100{/if}">
          <div class="queueItemFirst position-relative d-flex flex-column" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-html="true"
            title="{$LNG.tech.{$ID}}&nbsp;,&nbsp;<span data-time='{$List.endtime}' >{$List.display}</span>">
            <img class="m-0 hover-pointer" onclick="return Dialog.info({$ID})" src="{$dpath}gebaeude/{$ID}.gif"
              alt="{$LNG.tech.{$ID}}" width="80" height="80">
            <span
              class="position-absolute d-flex align-items-center justify-content-center top-0 start-0 levelInfo fs-11 bg-dark text-yellow py-1">{$List.level}</span>

            <form class="d-flex mx-auto align-items-center justify-content-center" action="game.php?page=buildings"
              method="post">
              {if !$List@first}
                <input type="hidden" name="listid" value="{$List@iteration}">
                <input type="hidden" name="cmd" value="remove">
              {else}
                <input type="hidden" name="cmd" value="cancel">
              {/if}
              <button class="btn btn-dark d-flex position-absolute bottom-0 rounded end-0 p-0 bi bi-x-square color-red"
                type="submit" name="button" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"
                title="{$LNG.bd_cancel}">
              </button>
            </form>
          </div>
          {if $List@first}
            <div class="d-flex flex-column align-items-start justify-content-start w-100 mx-2">
              <div style="border-radius:10px;height:12px;" id="progressbar" class="d-flex align-items-center my-2"
                data-time="{$List.resttime}"></div>
              <span class="fs-12 text-yellow">{$LNG['tech'][{$ID}]}&nbsp;:&nbsp;{$List.level}</span>
              <span class="text-center my-2 text-yellow fs-12" id="time" data-time="{$List.time}"></span>
              <span class="fs-12 text-yellow">{$List.display}</span>
            </div>
          {/if}
        </div>
      {/foreach}
    </div>
  {/if}

{/block}