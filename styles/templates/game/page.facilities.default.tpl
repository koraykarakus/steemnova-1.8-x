{block name="title" prepend}{$LNG.lm_facilities}{/block}
{block name="content"}

  <script>
    function showItem(id) {

      if ($('#item_big_' + id).hasClass('hidden')) {
        $('.item_big').addClass('hidden');
        $('.link_resources').addClass('hidden');
        $('.item_small').removeClass('border-color-active').removeClass('border-color-passive');
        $('#item_big_' + id).removeClass('hidden');
        $('#item_small_' + id).addClass('border-color-active');
      } else {
        $('.link_resources').removeClass('hidden');
        $('#item_big_' + id).addClass('hidden');
        $('#item_small_' + id).addClass('border-color-passive').removeClass('border-color-active');
      }

    }
  </script>

  <div class="items_wrapper">
    <div class="top" style="background:url('{$dpath}images/facilities_2.jpg');" class="bg-black border-orange">
      {foreach $build_info_list as $id => $element}
        <div class="item_big hidden" id="item_big_{$id}">
          <div class="top">
            <div class="left">
              <div class="img_wrapper">
                <img class="hover-pointer" onclick="return Dialog.info({$id})" src="{$dpath}elements/{$id}.gif"
                  alt="{$LNG.tech.{$id}}" width="203" height="203">
              </div>
            </div>
            <div class="right">
              <div class="title">
                <span
                  class="element_name{if $element.cost_overflow_total > 0} color-red hover-pointer{else} color-yellow{/if}">
                  {if $element.cost_overflow_total > 0}
                    <div class="tooltip tooltip_bottom">
                      <table>
                        <thead>
                          <tr>
                            <th colspan='2'>{$LNG.bd_remaining}</th>
                          </tr>
                        </thead>
                        <tbody>
                          {foreach $element.cost_overflow as $res_type => $res_count}
                            <tr>
                              <td>{$LNG.tech.$res_type}</td>
                              <td>{$res_count|number}</td>
                            </tr>
                          {/foreach}
                        </tbody>
                      </table>
                    </div>
                  {/if}
                  {$LNG.tech.{$id}}
                </span>
                <span id="val_{$id} p-0">
                  {$LNG.bd_lvl}&nbsp;{$element.level}{if $element.max_level != 255}/{$element.max_level}{/if}
                  <button class="button_close" onclick="showItem({$id});">X</button>
                </span>
              </div>
              <div class="requirements">
                <div class="top">
                  <div class="left">
                    <span>{$LNG.fgf_time}&nbsp;:&nbsp;
                      {pretty_time($element.element_time)}
                    </span>
                    {if !empty($element.info_energy_long)}
                      <span class="hover-pointer {if {$element.info_energy_short} > 0}color-green{else}color-red{/if}">
                        {$LNG.bd_next_level}&nbsp;{$element.info_energy_long}
                      </span>
                    {/if}
                  </div>
                  <div class="right">
                    {if $element.level > 0}
                      {if $id == 43}
                        <a href="#" onclick="return Dialog.info({$id})">{$LNG.bd_jump_gate_action}</a>
                      {/if}
                      {if ($id == 44 && !$have_missiles) ||  $id != 44}
                        <form action='game.php?page=facilities' method='post' class='destroy_form'>
                          <input type='hidden' name='cmd' value='destroy'>
                          <input type='hidden' name='building' value='{$id}'>
                          <button type='submit' class='button-downgrade'>{$LNG.bd_dismantle}</button>
                          <div class="tooltip tooltip_bottom">
                            <table>
                              <thead>
                                <tr>
                                  <th colspan='2'>{$LNG.bd_price_for_destroy} {$LNG.tech.{$id}} {$element.level}</th>
                                </tr>
                              </thead>
                              <tbody>
                                {foreach $element.destroy_resources as $res_type => $res_count}
                                  <tr>
                                    <td>{$LNG.tech.{$res_type}}</td>
                                    <td>
                                      <span
                                        style='color:{if empty($element.destroy_overflow[$res_type])}lime{else}#ffd600{/if}'>{$res_count|number}</span>
                                    </td>
                                  </tr>
                                {/foreach}
                                <tr>
                                  <td>{$LNG.bd_destroy_time}</td>
                                  <td>{$element.destroy_time|time}</td>
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
                    {foreach $element.cost_resources as $ress_id => $ress_amount}
                      <div class="resource">
                        <div class="tooltip tooltip_top">
                          {$LNG.tech.$ress_id}
                        </div>
                        <img src='{$dpath}elements/{$ress_id}.{if $ress_id >=600 && $ress_id <= 699}jpg{else}gif{/if}'>
                        <span
                          class="{if $element.cost_overflow[$ress_id] == 0}text-white{else}color-red{/if}">{$ress_amount|number}</span>
                      </div>
                    {/foreach}
                  </div>
                  <div class="right">
                    <div class="">
                      {if $element.max_level == $element.level_to_build}
                        <span style="color:#ffd600">{$LNG.bd_maxlevel} || <button>End Game</button></span>
                      {elseif ($is_busy.research && ($id == 6 || $id == 31)) || ($is_busy.shipyard && ($id == 15 || $id == 21))}
                        <span style="color:#ffd600">{$LNG.bd_working}</span>
                      {else}
                        {if $is_room_ok}
                          {if $can_build_element && $element.buyable && $element.technology_satisfied}
                            <form action="game.php?page=facilities" method="post" class="build_form">
                              <input type="hidden" name="cmd" value="insert">
                              <input type="hidden" name="building" value="{$id}">
                              <button type="submit" class="button-upgrade">
                                {$LNG.bd_build}
                              </button>
                              <div class="tooltip tooltip_top">
                                {$LNG.bd_build_next_level}{$element.level_to_build + 1}
                              </div>
                            </form>
                          {else}
                            <span class="fs-12" style="color:#ffd600">
                              <button data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                title="{$LNG.bd_build_next_level}{$element.level_to_build + 1}" class="button-upgrade" type="button"
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
            <p class="text-white">{$LNG.shortDescription[$id]}</p>
          </div>
        </div>
      {/foreach}
      <span class="page_title">{$current_pname} - {$LNG.lm_facilities}</span>
      <a class="link_resources" href="?page=resources">
        {$LNG.bd_resource_settings}
      </a>
    </div>
    <div class="bottom">
      <div class="title">
        <span class="color-yellow">{$LNG.lm_buildings}&nbsp;(&nbsp;{$used_field}&nbsp;/&nbsp;{$max_field}&nbsp;)</span>
        <span class="color-yellow">&nbsp;|&nbsp;{$LNG.st_points}&nbsp;[{$build_points}]</span>
      </div>
      <div class="list">
        {foreach $build_info_list as $id => $element}
          <div class="item_small" onclick="showItem({$id})" id="item_small_{$id}">
            <div class="tooltip tooltip_top">
              <table>
                <thead>
                  <tr>
                    <th colspan="2">
                      {$LNG.tech.{$id}}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  {if !$element.technology_satisfied && !empty($element.requeriments)}
                    <tr>
                      <th colspan='2' class='color-red'>{$LNG.tech_not_satisfied}</th>
                    </tr>
                    {foreach $element.requeriments as $c_require}
                      <tr>
                        <td class='color-red'>
                          <img class='mx-2 hover-pointer' src='{$dpath}elements/{$c_require.require_id}.gif'
                            alt='{$LNG.tech.{$c_require.require_id}}' width='30' height='30'>
                        </td>
                        <td class='color-red align-middle text-start'><span
                            class='color-blue'>{$LNG.tech.{$c_require.require_id}}</span>&nbsp;({$c_require.needed_level}&nbsp;/&nbsp;<span
                            class='color-yellow'>{$c_require.current_level}</span>)</td>
                      </tr>
                    {/foreach}
                  {/if}
                </tbody>
              </table>
            </div>
            <div class="level_info">
              {$element.level}
            </div>
            {if !$can_build_element || !$is_room_ok || !$element.buyable || !$element.technology_satisfied || ($is_busy.research && ($id == 6 || $id == 31)) || ($is_busy.shipyard && ($id == 15 || $id == 21))}
              <div class="black-screen"></div>
            {/if}
            <img class="hover-pointer" src="{$dpath}elements/{$id}.gif" alt="{$LNG.tech.{$id}}" width="90" height="90">
            {if $can_build_element && $is_room_ok && $element.buyable && $element.technology_satisfied && !($is_busy.research && ($id == 6 || $id == 31)) && !($is_busy.shipyard && ($id == 15 || $id == 21))}
              <form action="game.php?page=facilities" method="post" class="">
                <input type="hidden" name="cmd" value="insert">
                <input type="hidden" name="building" value="{$id}">
                <button type="submit" class="button_upgrade_small">
                  <div class="tooltip tooltip_top">
                    {$LNG.bd_build_next_level}{$element.level_to_build + 1}
                  </div>
                </button>
              </form>
            {/if}
            <div class="name_info">
              {$LNG.tech.{$id}}
            </div>
          </div>
        {/foreach}
      </div>
    </div>
  </div>

  {if !empty($queue)}
    <div id="buildlist" class="queue_wrapper">
      {foreach $queue as $list}
        {$id = $list.element}
        <div class="queue_item{if $list@first} queue_item_first{/if}">
          <div class="queue_left">
            <div class="tooltip tooltip_top">
              {$LNG.tech.{$id}}&nbsp;,&nbsp;<span data-time='{$list.endtime}'>{$list.display}</span>
            </div>
            <img class="hover-pointer" onclick="return Dialog.info({$id})" src="{$dpath}elements/{$id}.gif"
              alt="{$LNG.tech.{$id}}" width="80" height="80">
            <span class="level_info">{$list.level}</span>

            <form action="game.php?page=facilities" method="post">
              {if !$list@first}
                <input type="hidden" name="listid" value="{$list@iteration}">
                <input type="hidden" name="cmd" value="remove">
              {else}
                <input type="hidden" name="cmd" value="cancel">
              {/if}
              <button class="btn_cancel" type="submit" name="button">
                <div class="tooltip tooltip_top">{$LNG.bd_cancel}</div>
              </button>
            </form>
          </div>
          {if $list@first}
            <div class="queue_right">
              <div style="border-radius:10px;height:12px;" id="progressbar" class="" data-time="{$list.resttime}"></div>
              <span class="text-yellow">{$LNG['tech'][{$id}]}&nbsp;:&nbsp;{$list.level}</span>
              <span class="text-yellow" id="time" data-time="{$list.time}"></span>
              <span class="text-yellow">{$list.display}</span>
            </div>
          {/if}
        </div>
      {/foreach}
    </div>
  {/if}

{/block}