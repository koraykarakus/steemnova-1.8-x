
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

<table class="table519" style="min-width:560px; width:560px">

  {if $messages}
  <tr class="message">
    <td></td>
    <td colspan="2"><a href="?page=messages">{$messages}</a></td>
  </tr>
  {/if}
  <tr>
    <td>{$LNG["type_planet_{$planet_type}"]}</td>
    <td colspan="2">
      <a href="#" onclick="return Dialog.PlanetAction();" title="{$LNG.ov_planetmenu}"> {$planetname} ({$username})</a>
    </td>
  </tr>
	<tr>
    <td>{$LNG.ov_server_time}</td>
    <td colspan="2" class="servertime">{$servertime}</td>
  </tr>
  <tr>
    <td>{$LNG.ov_admins_online}:&nbsp;</td>
    <td colspan="2">
      {foreach $AdminsOnline as $ID => $Name}
      {if !$Name@first}&nbsp;&bull;&nbsp;{/if}
      <a href="#" onclick="return Dialog.PM({$ID})"><a style="color:lime">{$Name}</a>
      {foreachelse}
      {/foreach}
    </td>
    {$LNG.ov_online}
  </tr>
  <tr>
    <td>{$LNG.ov_players}</td>
    <td colspan="2"><a style="color:lime">{$usersOnline}</a></td>
  </tr>
  <tr>
    <td>{$LNG.ov_moving_fleets}</td>
    <td colspan="2"><a style="color:lime">{$fleetsOnline}</a></td>
  </tr>
  <tr>
    <td>{$LNG.ov_points}</td>
    <td colspan="2">{$rankInfo}</td>
  </tr>
  {if !empty($news)}
    <tr>
      <td class="text-center" colspan="3">
        <button class="text-yellow" onclick="showNews();">Check News</button>
      </td>
    </tr>
    <tr id="newsRow" class="d-none">
      <td colspan="2">
        <table class="table table-gow fs-12">
          <thead>
            <tr>
              <th class="color-blue text-center" colspan="3">{$LNG.ov_news}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $news as $currentNews}
            <tr>
              <td class="text-center color-blue">{$currentNews.user}</td>
              <td class="text-center color-blue">{$currentNews.date}</td>
              <td class="text-center color-blue">{$currentNews.text}</td>
            </tr>
            {/foreach}
          </tbody>
        </table>
      </td>

    </tr>
  {/if}


<tr>
	{$LNG.ov_events} <button id="chkbtn1">Hide fleets</button>

	<ul style="list-style-type:none;" id="hidden-div2">
	{foreach $fleets as $index => $fleet}


		<li style=" padding: 3px; "><span id="fleettime_{$index}" class="fleets" data-fleet-end-time="{$fleet.returntime}" data-fleet-time="{$fleet.resttime}">{pretty_fly_time({$fleet.resttime})}
		</span> <td id="fleettime_{$index}">{$fleet.text}</td></li>

	{/foreach}
</ul>
 &nbsp;<span style="display:none" id="tn3"><button id="chkbtn3">Show fleets</button></span>
</tr>
<tr>
  <td>
    <br>
    <table>
      <tr>
        <td><span style="color:skyblue">{$LNG.ov_diameter}:</span> </td>
        <td>{$LNG.ov_distance_unit} (<a title="{$LNG.ov_developed_fields}">{$planet_field_current}</a> / <a title="{$LNG.ov_max_developed_fields}">{$planet_field_max}</a> {$LNG.ov_fields})</td>
      </tr>
      <tr>
        <td><span style="color:skyblue">{$LNG.ov_temperature}:</span></td>
        <td>{$LNG.ov_aprox} {$planet_temp_min}{$LNG.ov_temp_unit} {$LNG.ov_to} {$planet_temp_max}{$LNG.ov_temp_unit}</td>
      </tr>
      <tr>
        <td><span style="color:skyblue">{$LNG.ov_position}:</span></td>
        <td>     <a href="game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}">[{$galaxy}:{$system}:{$planet}]</a></td>
      </tr>
    </table>
    <br>
    {$planetname}<br>
    <img src="{$dpath}planeten/{$planetimage}.jpg" height="160" width="160" alt="{$planetname}">
    {if $Moon}
    <br>
    <br>
    <a href="game.php?page=overview&amp;cp={$Moon.id}&amp;re=0" title="{$Moon.name}"><img src="{$dpath}planeten/mond.jpg" height="50" width="50" alt="{$Moon.name} ({$LNG.fcm_moon})"></a><br>
    {$Moon.name} ({$LNG.fcm_moon})
    {/if}
  </td>
  <td colspan="2">
    {if $AllPlanets}
    {$LNG.lv_planet}<br>
    {foreach $AllPlanets as $PlanetRow}
        <a href="game.php?page=overview&amp;cp={$PlanetRow.id}" title="{$PlanetRow.name}"><img style="margin: 5px;" src="{$dpath}planeten/{$PlanetRow.image}.jpg" width="100" height="100" alt="{$PlanetRow.name}"></a>
        <br>
        {$PlanetRow.name}
        <br>
        {$PlanetRow.build}
        <br>
    {/foreach}

    {else}&nbsp;

    {/if}
  </td>
</tr>
</table>

<table class="table519" style="min-width:560px; width:560px">
  <tr>
    <td>
      {if $buildInfo.buildings}
      <a href="game.php?page=buildings">{$LNG.lm_buildings}: </a>
      {$LNG.tech[$buildInfo.buildings['id']]} ({$buildInfo.buildings['level']})<br>
      <div class="timer" data-time="{$buildInfo.buildings['timeleft']}">{$buildInfo.buildings['starttime']}</div>
      {else}
      <a href="game.php?page=buildings">{$LNG.lm_buildings}: {$LNG.ov_free}</a><br>
      {/if}
    </td>
    <td>
      {if $buildInfo.tech}
      <a href="game.php?page=research">{$LNG.lm_research}: </a>
      {$LNG.tech[$buildInfo.tech['id']]} ({$buildInfo.tech['level']})<br>
      <div class="timer" data-time="{$buildInfo.tech['timeleft']}">{$buildInfo.tech['starttime']}</div>
      {else}
      <a href="game.php?page=research">{$LNG.lm_research}: {$LNG.ov_free}</a>
      {/if}
    </td>
    <td>
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
