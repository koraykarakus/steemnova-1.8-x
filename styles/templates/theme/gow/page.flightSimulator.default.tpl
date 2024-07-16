{extends file="layout.full.tpl"}
{block name="title" prepend}{$LNG.lm_battlesim}{/block}
{block name="content"}

<script type="text/javascript">

  function clearCoordinates(){
    $('.coordinate').val(1);
  }

  function clearShips(){
    $('.ship').val(0);
  }

  function clearTech(){
    $('.tech').val(0);
  }

  $(document).ready(function(){

  getResult();

  });

  function getResult(){
    $.post( "game.php?page=flightSimulator&mode=calcFleetSpeed&ajax=1", $('#flightSimForm').serialize(), function(data) {
      console.log(data);
      data = JSON.parse(data);

      var constant = 10;

      var i = 0;
      $.each(data,function(index,seconds){

        var readableTime = getTime(seconds);



        if (i == 0) {
          $('.resultInfo').remove();
        }
        $('#result').append('<tr class="resultInfo"><td>'+ index*constant +'</td><td>'+seconds+'</td><td>'+readableTime+'</td></tr>');
        i++;
      });
    });
  }

  function getTime(seconds){

    var minutes = 0, hours = 0,timeString = "";

    hours = Math.floor(seconds / 3600);
    seconds -= hours * 3600;
    minutes = Math.floor(seconds / 60);
    seconds -= minutes * 60;

    const h = $('#data_time').data('hour');
    const m = $('#data_time').data('minute');
    const s = $('#data_time').data('second');

    timeString = hours + " " + h + " " + minutes + " " + m + " " + seconds + " " + s;


    return timeString;


  }

</script>
<div id="data_time" style="display:none;" data-hour="{$LNG.fs_time_hours}" data-second="{$LNG.fs_time_seconds}" data-minute="{$LNG.fs_time_minutes}"></div>

<div class="ItemsWrapper">
  <table class="table table-sm fs-12 table-gow">
    <thead>
        <tr>
          <th class="text-center" colspan="4">
           {$LNG.fs_flight_sim}
          </th>
        </tr>
    </thead>
    <tbody>
      <form id="flightSimForm" class="" action="" method="post">
        <input type="hidden" name="mode" value="calcFleetSpeed">
        <tr>
          <td colspan="2"><span>{$LNG.tech.115}</span></td>
          <td colspan="2"><input class="form-control tech bg-dark text-white text-center p-0 my-auto mx-auto fs-12" onkeyup="getResult();" name="combustionTech" value="{$combustionTech}"></td>
        </tr>
        <tr>
          <td colspan="2"><span>{$LNG.tech.117}</span></td>
          <td colspan="2"><input class="form-control tech bg-dark text-white text-center p-0 my-auto mx-auto fs-12" onkeyup="getResult();" name="impulseTech" value="{$impulseTech}"></td>
        </tr>
        <tr>
          <td colspan="2"><span>{$LNG.tech.118}</span></td>
          <td colspan="2"><input class="form-control tech bg-dark text-white text-center p-0 my-auto mx-auto fs-12" onkeyup="getResult();" name="hyperspaceTech" value="{$hyperspaceTech}"></td>
        </tr>
        {foreach $ships as $key => $ship}
        <tr>
          <td colspan="2"><span>{$LNG['tech'][$ship.id]}:</span></td>
          <td colspan="2"><input onkeyup="getResult();" class="form-control ship bg-dark text-white text-center p-0 my-auto mx-auto fs-12"  name="ship_{$ship.id}" value="{$ship.count}"></td>
        </tr>
        {/foreach}
        <tr>
          <td><span>{$LNG.fl_beginning}:</span></td>
          <td><input onkeyup="getResult();" class="form-control coordinate bg-dark text-white text-center p-0 my-auto mx-auto fs-12" name="startGalaxy" value="{$startGalaxy}"></td>
          <td><input onkeyup="getResult();" class="form-control coordinate bg-dark text-white text-center p-0 my-auto mx-auto fs-12" name="startSystem" value="{$startSystem}"></td>
          <td><input onkeyup="getResult();" class="form-control coordinate bg-dark text-white text-center p-0 my-auto mx-auto fs-12" name="startPlanet" value="{$startPlanet}"></td>
        </tr>
        <tr>
          <td><span>{$LNG.fl_destiny}:</span></td>
          <td><input onkeyup="getResult();" class="form-control coordinate bg-dark text-white text-center p-0 my-auto mx-auto fs-12" name="endGalaxy" value="1"></td>
          <td><input onkeyup="getResult();" class="form-control coordinate bg-dark text-white text-center p-0 my-auto mx-auto fs-12" name="endSystem" value="1"></td>
          <td><input onkeyup="getResult();" class="form-control coordinate bg-dark text-white text-center p-0 my-auto mx-auto fs-12" name="endPlanet" value="1"></td>
        </tr>
        <tr>
          <td class="text-center" colspan="4">
            <button class="button_blue" onclick="clearShips();getResult();" type="button" name="button">Clear Ships</button>
            <button class="button_blue" onclick="clearTech();getResult();" type="button" name="button">Clear Tech</button>
            <button class="button_blue" onclick="clearCoordinates();getResult();" type="button" name="button">Clear Coordinates</button>
          </td>
        </tr>
      </form>
    </tbody>
  </table>
</div>

  <div class="table-responsive my-2">
    <table id="result" class="table table-sm fs-12 table-gow">
      <thead>

      </thead>
      <tbody>
        <tr>
          <td><span class="fsresultPercentage blue thick">{$LNG.fs_percantage}</span></td>
          <td><span class="fsresultSeconds blue thick">{$LNG.fs_time_seconds}</span></td>
          <td><span class="fsresultNormalTime blue thick">{$LNG.fs_time}</span></td>
        </tr>
      </tbody>
    </table>
  </div>






{/block}
