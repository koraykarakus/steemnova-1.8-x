{block name="title" prepend}{$LNG.lm_overview}{/block}
{block name="content"}



  <script>
    function showNews() {

      $.ajax({
        type: "POST",
        url: 'game.php?page=overview&mode=changeNewsVisibility&ajax=1',
        success: function(data) {

          if ($('#newsRow').hasClass('d-none')) {
            $('#newsRow').removeClass('d-none')
          } else {
            $('#newsRow').addClass('d-none')
          }

        }

      });

    }
  </script>


  <table class="table_game table_full">
    <tbody>
      <tr>
        <td>{$LNG["type_planet_{$planet_type}"]}</td>
        <td>
          <a class="planet_actions" href="#" onclick="return Dialog.PlanetAction();">
            <div class="tooltip tooltip_top">
              {$LNG.ov_planetmenu}
            </div>
            <i class="icon_settings"></i>
              {$planet_name}&nbsp;({$username})
          </a>
        </td>
      </tr>
      <tr>
        <td>{$LNG.ov_admins_online}</td>
        <td>
          {foreach $admins_online as $ID => $Name}
            {if !$Name@first}&nbsp;&bull;&nbsp;{/if}
            <a href="#" onclick="return Dialog.PM({$ID})"><a style="color:lime">{$Name}</a>
            {foreachelse}
            {/foreach}
        </td>
      </tr>
      <tr>
        <td>{$LNG.ov_players}</td>
        <td><a style="color:lime">{$users_online}</a></td>
      </tr>
      <tr>
        <td>{$LNG.ov_moving_fleets}</td>
        <td><a style="color:lime">{$fleets_online}</a></td>
      </tr>
      <tr>
        <td>{$LNG.ov_points}</td>
        <td>{$rank_info}</td>
      </tr>
      {if !empty($news)}
        <tr>
          <td>
            <button onclick="showNews();">{$LNG.ov_news}</button>
          </td>
        </tr>
        <tr id="newsRow" class="{if $show_news_active}d-none{/if}">
          <td colspan="2">
            <table class="table_game">
              <thead>
                <tr>
                  <th class="color-blue" colspan="3">{$LNG.ov_news}</th>
                </tr>
              </thead>
              <tbody>
                {foreach $news as $currentNews}
                  <tr>
                    <td class="color-blue">{$currentNews.user}</td>
                    <td class="color-blue">{$currentNews.date}</td>
                    <td class="color-blue">{$currentNews.text}</td>
                  </tr>
                {/foreach}
              </tbody>
            </table>
          </td>
        </tr>
      {/if}
      <tr>
        <td>
          <table class="table_full">
            <tbody>
              <tr>
                <td>{$planet_name}</td>
                <td>
                  <a class="hover-pointer" href="?page=overview&cp={$planet_id}">
                    <img src="{$dpath}planets/{$planet_image}.jpg" height="160" width="160" alt="{$planet_name}">
                  </a>
                </td>
              </tr>
              {if $moon}
              <tr>
                <td>{$moon.name} {if $moon.planet_type == 3}({$LNG.fcm_moon}){/if}</td>
                <td>
                  <a href="game.php?page=overview&amp;cp={$moon.id}" title="{$moon.name}">
                    <img src="{$dpath}planets/{$moon.image}.jpg" height="50" width="50"
                      alt="{$moon.name} {if $moon.planet_type == 3}({$LNG.fcm_moon}){/if}">
                  </a>
                </td>
              </tr>
              {/if}
            </tbody>
          </table>
        </td>
        <td>
          <table class="table_game">
            <thead></thead>
            <tbody>
              <tr>
                <td><span style="color:skyblue">{$LNG.ov_diameter}:</span> </td>
                <td>{$LNG.ov_distance_unit} (<a
                    title="{$LNG.ov_developed_fields}">{$planet_field_current}</a> / <a
                    title="{$LNG.ov_max_developed_fields}">{$planet_field_max}</a> {$LNG.ov_fields})</td>
              </tr>
              <tr>
                <td><span style="color:skyblue">{$LNG.ov_temperature}:</span></td>
                <td>{$LNG.ov_aprox} {$planet_temp_min}{$LNG.ov_temp_unit} {$LNG.ov_to}
                  {$planet_temp_max}{$LNG.ov_temp_unit}</td>
              </tr>
              <tr>
                <td><span style="color:skyblue">{$LNG.ov_position}:</span></td>
                <td>
                  <a class="hover-underline"
                    href="game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}">[{$galaxy}:{$system}:{$planet}]</a>
                </td>
              </tr>
              {if  isModuleAvailable($smarty.const.MODULE_RELOCATE)}
                <tr>
                  <td colspan="2">
                    <a class="text-yellow"
                      href="game.php?page=relocate">{$LNG.rl_relocate}</a>
                  </td>
                </tr>
              {/if}
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>


  <table class="table_game">
    <tr>
      <td>
        {if $build_info.buildings}
          <a href="game.php?page=buildings">{$LNG.lm_buildings}: </a>
          {$LNG.tech[$build_info.buildings['id']]} ({$build_info.buildings['level']})<br>
          <div class="timer" data-time="{$build_info.buildings['timeleft']}">{$build_info.buildings['starttime']}</div>
        {else}
          <a class="hover-underline" href="game.php?page=buildings">{$LNG.lm_buildings}: {$LNG.ov_free}</a><br>
        {/if}
      </td>
      <td>
        {if $build_info.tech}
          <a href="game.php?page=research">{$LNG.lm_research}: </a>
          {$LNG.tech[$build_info.tech['id']]} ({$build_info.tech['level']})<br>
          <div class="timer" data-time="{$build_info.tech['timeleft']}">{$build_info.tech['starttime']}</div>
        {else}
          <a class="hover-underline" href="game.php?page=research">{$LNG.lm_research}: {$LNG.ov_free}</a>
        {/if}
      </td>
      <td>
        {if $build_info.fleet}
          <a href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipyard}: </a>
          {$LNG.tech[$build_info.fleet['id']]} ({$build_info.fleet['level']})<br>
          <div class="timer" data-time="{$build_info.fleet['timeleft']}">{$build_info.fleet['starttime']}</div>
        {else}
          <a class="hover-underline" href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipyard}: {$LNG.ov_free}</a>
        {/if}
      </td>
    </tr>
  </table>





{/block}