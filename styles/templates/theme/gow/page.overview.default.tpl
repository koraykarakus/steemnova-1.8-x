
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
<script>
  function showHideFleets(){

    if ($('.fleetRow').hasClass('d-none')) {
      $('.fleetRow').removeClass('d-none')
    }else {
      $('.fleetRow').addClass('d-none')
    }

  }
</script>

{/block}
{block name="content"}

<table class="table table-dark my-4" style="width:560px;border-collapse:collapse;">
  <thead></thead>
  <tbody>
    {if $messages}
    <tr>
      <td></td>
      <td colspan="2"><a href="?page=messages">{$messages}</a></td>
    </tr>
    {/if}
    <tr class="fs-14">
      <td class="text-start px-3 w-50 bg-black border border-dark">{$LNG["type_planet_{$planet_type}"]}</td>
      <td class="text-center px-3 w-50 bg-black border border-dark">
        <a href="#" onclick="return Dialog.PlanetAction();" title="{$LNG.ov_planetmenu}"> {$planetname} ({$username})</a>
      </td>
    </tr>
  	<tr class="fs-14">
      <td class="text-start px-3 bg-black border border-dark">{$LNG.ov_server_time}</td>
      <td class="servertime text-center px-3 w-50 bg-black border border-dark">{$servertime}</td>
    </tr>
    <tr class="fs-14">
      <td class="text-start px-3 bg-black border border-dark">{$LNG.ov_admins_online}</td>
      <td class="text-center px-3 w-50 bg-black border border-dark">
        {foreach $AdminsOnline as $ID => $Name}
        {if !$Name@first}&nbsp;&bull;&nbsp;{/if}
        <a href="#" onclick="return Dialog.PM({$ID})"><a style="color:lime">{$Name}</a>
        {foreachelse}
        {/foreach}
      </td>
    </tr>
    <tr class="fs-14">
      <td class="text-start px-3 bg-black border border-dark">{$LNG.ov_players}</td>
      <td class="text-center px-3 w-50 bg-black border border-dark"><a style="color:lime">{$usersOnline}</a></td>
    </tr>
    <tr class="fs-14">
      <td class="text-start px-3 bg-black border border-dark">{$LNG.ov_moving_fleets}</td>
      <td class="text-center px-3 w-50 bg-black border border-dark"><a style="color:lime">{$fleetsOnline}</a></td>
    </tr>
    <tr class="fs-14">
      <td class="text-start px-3 bg-black border border-dark">{$LNG.ov_points}</td>
      <td class="text-center px-3 w-50 bg-black border border-dark">{$rankInfo}</td>
    </tr>
  {if $is_news}
    <tr class="fs-14">
      <td class="text-start px-3 bg-black border border-dark">{$LNG.ov_news}</td>
      <td class="text-center px-3 w-50 bg-black border border-dark">{$news}</td>
    </tr>
    <tr>
      <td class="bg-black" colspan="3"><button id="chkbtn">Check News</button></td>
    </tr>
  {/if}

  <tr class="fs-14">
    <td>{$LNG.ov_events}</td>
    <td> <button onclick="showHideFleets();">Hide fleets</button></td>
  </tr>

  	{foreach $fleets as $index => $fleet}
    <tr class="fs-12 fleetRow">
      <td>
        <span id="fleettime_{$index}" class="fleets" data-fleet-end-time="{$fleet.returntime}" data-fleet-time="{$fleet.resttime}">
        {pretty_fly_time({$fleet.resttime})}
       </span>
     </td>
      <td id="fleettime_{$index}">{$fleet.text}</td>
    </tr>
  	{/foreach}
  <tr>
    <td>
      <table class="table table-dark">
        <thead></thead>
        <tbody class="fs-12">
          <tr>
            <td class="bg-black"><span style="color:skyblue">{$LNG.ov_diameter}:</span> </td>
            <td class="bg-black">{$LNG.ov_distance_unit} (<a title="{$LNG.ov_developed_fields}">{$planet_field_current}</a> / <a title="{$LNG.ov_max_developed_fields}">{$planet_field_max}</a> {$LNG.ov_fields})</td>
          </tr>
          <tr>
            <td class="bg-black"><span style="color:skyblue">{$LNG.ov_temperature}:</span></td>
            <td class="bg-black">{$LNG.ov_aprox} {$planet_temp_min}{$LNG.ov_temp_unit} {$LNG.ov_to} {$planet_temp_max}{$LNG.ov_temp_unit}</td>
          </tr>
          <tr>
            <td class="bg-black"><span style="color:skyblue">{$LNG.ov_position}:</span></td>
            <td class="bg-black">     <a href="game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}">[{$galaxy}:{$system}:{$planet}]</a></td>
          </tr>
        </tbody>
      </table>
      <div class="d-flex flex-row align-items-center justify-content-center bg-black fs-12">
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
    <td class="bg-black">
      {if $AllPlanets}
        {foreach $AllPlanets as $PlanetRow}
        <div class="d-flex flex-column align-items-center justify-content-start">
            <span>{$LNG.lv_planet}</span>
            <a href="game.php?page=overview&amp;cp={$PlanetRow.id}" title="{$PlanetRow.name}"><img style="margin: 5px;" src="{$dpath}planeten/{$PlanetRow.image}.jpg" width="100" height="100" alt="{$PlanetRow.name}"></a>
            <span>{$PlanetRow.name}</span>
            <span>{$PlanetRow.build}</span>
        </div>
        {/foreach}
      {/if}
    </td>
  </tr>
  </tbody>
</table>

<table class="table table-dark" style="width:560px">
  <tr>
    <td class="bg-black">
      {if $buildInfo.buildings}
      <a href="game.php?page=buildings">{$LNG.lm_buildings}: </a>
      {$LNG.tech[$buildInfo.buildings['id']]} ({$buildInfo.buildings['level']})<br>
      <div class="timer" data-time="{$buildInfo.buildings['timeleft']}">{$buildInfo.buildings['starttime']}</div>
      {else}
      <a href="game.php?page=buildings">{$LNG.lm_buildings}: {$LNG.ov_free}</a><br>
      {/if}
    </td>
    <td class="bg-black">
      {if $buildInfo.tech}
      <a href="game.php?page=research">{$LNG.lm_research}: </a>
      {$LNG.tech[$buildInfo.tech['id']]} ({$buildInfo.tech['level']})<br>
      <div class="timer" data-time="{$buildInfo.tech['timeleft']}">{$buildInfo.tech['starttime']}</div>
      {else}
      <a href="game.php?page=research">{$LNG.lm_research}: {$LNG.ov_free}</a>
      {/if}
    </td>
    <td class="bg-black">
      {if $buildInfo.fleet}
      <a href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipshard}: </a>
      {$LNG.tech[$buildInfo.fleet['id']]} ({$buildInfo.fleet['level']})<br>
      <div class="timer" data-time="{$buildInfo.fleet['timeleft']}">{$buildInfo.fleet['starttime']}</div>
      {else}
      <a href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipshard}: {$LNG.ov_free}</a>
      {/if}
    </td>
  </tr>
</table>





{/block}
{block name="script" append}
    <script src="scripts/game/overview.js"></script>
{/block}
