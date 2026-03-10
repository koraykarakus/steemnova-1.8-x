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


  <table class="table-gow table_overview">
    <tbody>
      <tr>
        <td>{$LNG["type_planet_{$planet_type}"]}</td>
        <td>
          <a href="#" onclick="return Dialog.PlanetAction();"
            title="{$LNG.ov_planetmenu}">{$planetname}&nbsp;({$username})</a>
        </td>
      </tr>
      <tr>
        <td>{$LNG.ov_admins_online}</td>
        <td>
          {foreach $AdminsOnline as $ID => $Name}
            {if !$Name@first}&nbsp;&bull;&nbsp;{/if}
            <a href="#" onclick="return Dialog.PM({$ID})"><a style="color:lime">{$Name}</a>
            {foreachelse}
            {/foreach}
        </td>
      </tr>
      <tr>
        <td>{$LNG.ov_players}</td>
        <td><a style="color:lime">{$usersOnline}</a></td>
      </tr>
      <tr>
        <td>{$LNG.ov_moving_fleets}</td>
        <td><a style="color:lime">{$fleetsOnline}</a></td>
      </tr>
      <tr>
        <td>{$LNG.ov_points}</td>
        <td>{$rankInfo}</td>
      </tr>
      {if !empty($news)}
        <tr>
          <td>
            <button onclick="showNews();">{$LNG.ov_news}</button>
          </td>
        </tr>
        <tr id="newsRow" class="{if $show_news_active}d-none{/if}">
          <td colspan="2">
            <table class="table-gow">
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
          <div>
            <div>
              <span>{$planetname}</span>
              <a class="hover-pointer" href="?page=overview&cp={$planet_id}">
                <img src="{$dpath}planeten/{$planetimage}.jpg" height="160" width="160" alt="{$planetname}">
              </a>
            </div>
            {if $Moon}
              <div>
                <a href="game.php?page=overview&amp;cp={$Moon.id}" title="{$Moon.name}">
                  <img src="{$dpath}planeten/{$Moon.image}.jpg" height="50" width="50"
                    alt="{$Moon.name} {if $Moon.planet_type == 3}({$LNG.fcm_moon}){/if}">
                </a>
                <span>{$Moon.name} {if $Moon.planet_type == 3}({$LNG.fcm_moon}){/if}</span>
              </div>
            {/if}
          </div>
        </td>
        <td>
          <table class="table-gow">
            <thead></thead>
            <tbody>
              <tr>
                <td><span style="color:skyblue">{$LNG.ov_diameter}:</span> </td>
                <td class="text-center">{$LNG.ov_distance_unit} (<a
                    title="{$LNG.ov_developed_fields}">{$planet_field_current}</a> / <a
                    title="{$LNG.ov_max_developed_fields}">{$planet_field_max}</a> {$LNG.ov_fields})</td>
              </tr>
              <tr>
                <td><span style="color:skyblue">{$LNG.ov_temperature}:</span></td>
                <td class="text-center">{$LNG.ov_aprox} {$planet_temp_min}{$LNG.ov_temp_unit} {$LNG.ov_to}
                  {$planet_temp_max}{$LNG.ov_temp_unit}</td>
              </tr>
              <tr>
                <td><span style="color:skyblue">{$LNG.ov_position}:</span></td>
                <td class="text-center">
                  <a class="hover-underline"
                    href="game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}">[{$galaxy}:{$system}:{$planet}]</a>
                </td>
              </tr>
              {if  isModuleAvailable($smarty.const.MODULE_RELOCATE)}
                <tr>
                  <td colspan="2" class="text-center">
                    <a class="btn btn-dark py-0 px-1 border border-secondary fs-12 text-yellow"
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


  <table class="table-gow">
    <tr>
      <td>
        {if $buildInfo.buildings}
          <a href="game.php?page=buildings">{$LNG.lm_buildings}: </a>
          {$LNG.tech[$buildInfo.buildings['id']]} ({$buildInfo.buildings['level']})<br>
          <div class="timer" data-time="{$buildInfo.buildings['timeleft']}">{$buildInfo.buildings['starttime']}</div>
        {else}
          <a class="hover-underline" href="game.php?page=buildings">{$LNG.lm_buildings}: {$LNG.ov_free}</a><br>
        {/if}
      </td>
      <td>
        {if $buildInfo.tech}
          <a href="game.php?page=research">{$LNG.lm_research}: </a>
          {$LNG.tech[$buildInfo.tech['id']]} ({$buildInfo.tech['level']})<br>
          <div class="timer" data-time="{$buildInfo.tech['timeleft']}">{$buildInfo.tech['starttime']}</div>
        {else}
          <a class="hover-underline" href="game.php?page=research">{$LNG.lm_research}: {$LNG.ov_free}</a>
        {/if}
      </td>
      <td>
        {if $buildInfo.fleet}
          <a href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipshard}: </a>
          {$LNG.tech[$buildInfo.fleet['id']]} ({$buildInfo.fleet['level']})<br>
          <div class="timer" data-time="{$buildInfo.fleet['timeleft']}">{$buildInfo.fleet['starttime']}</div>
        {else}
          <a class="hover-underline" href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipshard}: {$LNG.ov_free}</a>
        {/if}
      </td>
    </tr>
  </table>





{/block}