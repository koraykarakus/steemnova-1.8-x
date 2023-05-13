
{block name="title" prepend}{$LNG.lm_overview}{/block}
{block name="script" append}
    <script>

              $(function(){
            $("#chkbtn").on('click',function() {
                $(this).hide();
                $("#hidden-div").show();
            });
        });

 $(function(){
            $("#chkbtn2").on('click',function() {
                $("#chkbtn").show();
                $("#hidden-div").hide();
            });
        });
 $(function(){
            $("#chkbtn1").on('click',function() {
                $(this).hide();
                $("#hidden-div2").hide()
$("#tn3").show();
            });
        });
 $(function(){
            $("#chkbtn3").on('click',function() {
                $("#chkbtn1").show();
                $("#hidden-div2").show();
$("#tn3").hide();

            });
        });
</script>


{/block}
{block name="content"}



<div class="table-responsive">
  <table class="table table-sm fs-12 table-gow">
    <thead></thead>
    <tbody>
      <tr>
        <td class="text-start px-3 w-50">{$LNG["type_planet_{$planet_type}"]}</td>
        <td class="text-center px-3 w-50">
          <a href="#"  onclick="return Dialog.PlanetAction();" title="{$LNG.ov_planetmenu}">{$planetname}&nbsp;({$username})</a>
        </td>
      </tr>

      <tr>
        <td class="text-start px-3">{$LNG.ov_admins_online}</td>
        <td class="text-center px-3 w-50">
          {foreach $AdminsOnline as $ID => $Name}
          {if !$Name@first}&nbsp;&bull;&nbsp;{/if}
          <a href="#" onclick="return Dialog.PM({$ID})"><a style="color:lime">{$Name}</a>
          {foreachelse}
          {/foreach}
        </td>
      </tr>
      <tr>
        <td class="text-start px-3">{$LNG.ov_players}</td>
        <td class="text-center px-3 w-50"><a style="color:lime">{$usersOnline}</a></td>
      </tr>
      <tr>
        <td class="text-start px-3">{$LNG.ov_moving_fleets}</td>
        <td class="text-center px-3 w-50"><a style="color:lime">{$fleetsOnline}</a></td>
      </tr>
      <tr>
        <td class="text-start px-3">{$LNG.ov_points}</td>
        <td class="text-center px-3 w-50">{$rankInfo}</td>
      </tr>
    {if $is_news}
      <tr>
        <td class="text-start px-3">{$LNG.ov_news}</td>
        <td class="text-center px-3 w-50">{$news}</td>
      </tr>
      <tr>
        <td class="text-center" colspan="3"><button class="text-yellow" id="chkbtn">Check News</button></td>
      </tr>
    {/if}


    <tr>
      <td>
        <table class="table table-dark">
          <thead></thead>
          <tbody class="fs-12">
            <tr>
              <td ><span style="color:skyblue">{$LNG.ov_diameter}:</span> </td>
              <td class="text-center">{$LNG.ov_distance_unit} (<a title="{$LNG.ov_developed_fields}">{$planet_field_current}</a> / <a title="{$LNG.ov_max_developed_fields}">{$planet_field_max}</a> {$LNG.ov_fields})</td>
            </tr>
            <tr>
              <td ><span style="color:skyblue">{$LNG.ov_temperature}:</span></td>
              <td class="text-center">{$LNG.ov_aprox} {$planet_temp_min}{$LNG.ov_temp_unit} {$LNG.ov_to} {$planet_temp_max}{$LNG.ov_temp_unit}</td>
            </tr>
            <tr>
              <td><span style="color:skyblue">{$LNG.ov_position}:</span></td>
              <td class="text-center">
                <a class="hover-underline" href="game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}">[{$galaxy}:{$system}:{$planet}]</a>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="d-flex flex-row align-items-center justify-content-center  fs-12">
          <div class="d-flex flex-column align-items-center justify-content-center w-100">
            <span>{$planetname}</span>
            <img src="{$dpath}planeten/{$planetimage}.jpg" height="160" width="160" alt="{$planetname}">
          </div>
          {if $Moon}
          <div class="d-flex flex-column align-items-center justify-content-center w-100">
            <a href="game.php?page=overview&amp;cp={$Moon.id}&amp;re=0" title="{$Moon.name}"><img src="{$dpath}planeten/mond.jpg" height="50" width="50" alt="{$Moon.name} ({$LNG.fcm_moon})"></a>
            <span>{$Moon.name} ({$LNG.fcm_moon})</span>
          </div>
          {/if}


        </div>

      </td>
    </tr>
    </tbody>
  </table>
</div>


<table class="table table-sm table-gow">
  <tr>
    <td class="text-center">
      {if $buildInfo.buildings}
      <a href="game.php?page=buildings">{$LNG.lm_buildings}: </a>
      {$LNG.tech[$buildInfo.buildings['id']]} ({$buildInfo.buildings['level']})<br>
      <div class="timer" data-time="{$buildInfo.buildings['timeleft']}">{$buildInfo.buildings['starttime']}</div>
      {else}
      <a class="hover-underline" href="game.php?page=buildings">{$LNG.lm_buildings}: {$LNG.ov_free}</a><br>
      {/if}
    </td>
    <td class="text-center">
      {if $buildInfo.tech}
      <a href="game.php?page=research">{$LNG.lm_research}: </a>
      {$LNG.tech[$buildInfo.tech['id']]} ({$buildInfo.tech['level']})<br>
      <div class="timer" data-time="{$buildInfo.tech['timeleft']}">{$buildInfo.tech['starttime']}</div>
      {else}
      <a class="hover-underline" href="game.php?page=research">{$LNG.lm_research}: {$LNG.ov_free}</a>
      {/if}
    </td>
    <td class="text-center">
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
{block name="script" append}
    <script src="scripts/game/overview.js"></script>
{/block}
