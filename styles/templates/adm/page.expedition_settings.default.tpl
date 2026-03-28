{block name="content"}

<form class="bg-black w-75  p-3 my-3 mx-auto fs-12" action="?page=expedition&mode=send" method="post">
<input type="hidden" name="opt_save" value="1">
  <div class="form-gorup d-flex my-1 p-2">
  	<label class="text-start my-1 cursor-pointer hover-underline" for="expedition_allow_fleet_loss">{$LNG.es_allow_fleet_loss}</label>
  	<input class="mx-2" id="expedition_allow_fleet_loss" name="expedition_allow_fleet_loss" {if $expedition_allow_fleet_loss} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_allow_fleet_delay">{$LNG.es_allow_fleet_delay}</label>
    <input class="mx-2" id="expedition_allow_fleet_delay" name="expedition_allow_fleet_delay" {if $expedition_allow_fleet_delay} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_allow_fleet_speedup">{$LNG.es_allow_fleet_speedup}</label>
    <input class="mx-2" id="expedition_allow_fleet_speedup" name="expedition_allow_fleet_speedup" {if $expedition_allow_fleet_speedup} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_allow_expedition_war">{$LNG.es_allow_wars}</label>
    <input class="mx-2" id="expedition_allow_expedition_war" name="expedition_allow_expedition_war" {if $expedition_allow_expedition_war} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_allow_darkmatter_find">{$LNG.es_allow_dm_finding}</label>
    <input class="mx-2" id="expedition_allow_darkmatter_find" name="expedition_allow_darkmatter_find" {if $expedition_allow_darkmatter_find} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_allow_resources_find">{$LNG.es_allow_resources_finding}</label>
    <input class="mx-2" id="expedition_allow_resources_find" name="expedition_allow_resources_find" {if $expedition_allow_resources_find} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_allow_ships_find">{$LNG.es_allow_ships_finding}</label>
    <input class="mx-2" id="expedition_allow_ships_find" name="expedition_allow_ships_find" {if $expedition_allow_ships_find} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_consider_holdtime">{$LNG.es_consider_hold_time}</label>
    <input class="mx-2" id="expedition_consider_holdtime" name="expedition_consider_holdtime" {if $expedition_consider_holdtime} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_consider_same_coordinate">{$LNG.es_consider_same_coordinate}</label>
    <input class="mx-2" id="expedition_consider_same_coordinate" name="expedition_consider_same_coordinate" {if $expedition_consider_same_coordinate} checked="checked"{/if} type="checkbox">
  </div>
  <div class="form-gorup d-flex flex-column my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_min_darkmatter_small_min">{$LNG.es_min_dm_small_event}</label>
    <input class="form-control py-1 bg-dark  my-1 border border-secondary" id="expedition_min_darkmatter_small_min" name="expedition_min_darkmatter_small_min" value="{$expedition_min_darkmatter_small_min}" type="text">
  </div>
  <div class="form-gorup d-flex flex-column my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_min_darkmatter_small_max">{$LNG.es_max_dm_small_event}</label>
    <input class="form-control py-1 bg-dark  my-1 border border-secondary" id="expedition_min_darkmatter_small_max" name="expedition_min_darkmatter_small_max" value="{$expedition_min_darkmatter_small_max}" type="text">
  </div>
  <div class="form-gorup d-flex flex-column my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_min_darkmatter_large_min">{$LNG.es_min_dm_large_event}</label>
    <input class="form-control py-1 bg-dark  my-1 border border-secondary" id="expedition_min_darkmatter_large_min" name="expedition_min_darkmatter_large_min" value="{$expedition_min_darkmatter_large_min}" type="text">
  </div>
  <div class="form-gorup d-flex flex-column my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_min_darkmatter_large_max">{$LNG.es_max_dm_large_event}</label>
    <input class="form-control py-1 bg-dark  my-1 border border-secondary" id="expedition_min_darkmatter_large_max" name="expedition_min_darkmatter_large_max" value="{$expedition_min_darkmatter_large_max}" type="text">
  </div>
  <div class="form-gorup d-flex flex-column my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_min_darkmatter_vlarge_min">{$LNG.es_min_dm_very_large_event}</label>
    <input class="form-control py-1 bg-dark  my-1 border border-secondary" id="expedition_min_darkmatter_vlarge_min" name="expedition_min_darkmatter_vlarge_min" value="{$expedition_min_darkmatter_vlarge_min}" type="text">
  </div>
  <div class="form-gorup d-flex flex-column my-1 p-2">
    <label class="text-start my-1 cursor-pointer hover-underline" for="expedition_min_darkmatter_vlarge_max">{$LNG.es_max_dm_very_large_event}</label>
    <input class="form-control py-1 bg-dark  my-1 border border-secondary" id="expedition_min_darkmatter_vlarge_max" name="expedition_min_darkmatter_vlarge_max" value="{$expedition_min_darkmatter_vlarge_max}" type="text">
  </div>

<div class="form-gorup d-flex flex-column my-1 p-2 ">
	<input  class="btn btn-primary " value="{$LNG.se_save_parameters}" type="submit">
</div>
</form>

<form class="bg-black w-75  p-3 my-3 mx-auto fs-12" action="?page=expedition&mode=default" method="post">
  <div class="form-gorup d-flex flex-column my-1 p-2 ">
  	<input  class="btn btn-primary " value="return default" type="submit">
  </div>
</form>

{/block}
