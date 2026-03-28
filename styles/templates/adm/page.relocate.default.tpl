{block name="content"}

  <form id="colonySettings" class="bg-black w-75  p-3 my-3 mx-auto fs-12"
    action="?page=relocate&mode=saveSettings" method="post">
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="relocate_price">{$LNG.rl_price_dm}</label>
      <input id="relocate_price" class="form-control py-1 bg-dark  my-1 border border-secondary"
        name="relocate_price" value="{$relocate_price}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="relocate_next_time">{$LNG.rl_next_time}</label>
      <input id="relocate_next_time" class="form-control py-1 bg-dark  my-1 border border-secondary"
        name="relocate_next_time" value="{$relocate_next_time}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline" for="relocate_jump_gate_active">{$LNG.rl_jump_gate_cd_after_relocate}</label>
      <input id="relocate_jump_gate_active" class="form-control py-1 bg-dark  my-1 border border-secondary"
        name="relocate_jump_gate_active" value="{$relocate_jump_gate_active}" type="text" maxlength="5">
    </div>
    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <label class="text-start my-1 cursor-pointer hover-underline"
        for="relocate_move_fleet_directly">{$LNG.rl_move_fleet_directly}</label>
      <input id="relocate_move_fleet_directly" type="checkbox" {if $relocate_move_fleet_directly}checked="checked" {/if}
        name="relocate_move_fleet_directly">
    </div>

    <div class="form-gorup d-flex flex-column my-1 p-2 ">
      <input class="btn btn-primary " value="{$LNG.se_save_parameters}" type="submit">
    </div>
  </form>
{/block}