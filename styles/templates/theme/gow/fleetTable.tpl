<div class="fleet_wrapper">
  {if empty($fleets)}
    <span class="fleet_text color-blue">{$LNG.fm_no_fleet_movements}</span>
  {else}
    <span class="fleet_text color-gray">{$LNG.fm_fleets}</span>
  {/if}
  {if !empty($fleets)}
    <button id="fleet_btn" onclick="showHideFleets();" class="fleet_btn {if $show_fleets_active}up{else}down{/if}" type="button" name="button">
      &#9660;
    </button>
  {/if}
</div>


<script>
  function showHideFleets() {

    $.ajax({
      type: "POST",
      url: 'game.php?page=fleetTableSettings&mode=changeVisibility&ajax=1',
      success: function(data) {

        $('.fleet_events').stop(true, true).slideToggle(200);
        $('#fleet_btn').toggleClass('up down');
      }

    });

  }
</script>