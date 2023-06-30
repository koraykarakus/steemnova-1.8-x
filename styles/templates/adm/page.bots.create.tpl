{block name="content"}


<div class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12">
  <span class="fs-12 text-yellow fw-bold">Create Bots</span>
    <form class="" action="?page=bots&mode=createSend" method="post">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="bots_number">Number of bots:</label>
      <input id="bots_number" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="bots_number" value="0">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="bot_name_type">Bot Name</label>
      <select id="bot_name_type" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="bot_name_type">
        <option selected value="0">Random user name</option>
        <option value="1">Bot and number</option>
      </select>
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="target_galaxy">Target Galaxy:</label>
      <input id="target_galaxy" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="target_galaxy" value="1">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="bots_dm">Bots start dark matter:</label>
      <input id="bots_dm" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="bots_dm" value="0">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="bots_password">Bots password:</label>
      <input id="bots_password" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="password" name="bots_password" value="">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="planet_metal">Start Metal:</label>
      <input id="planet_metal" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="planet_metal" value="10000">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="planet_crystal">Start Crystal:</label>
      <input id="planet_crystal" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="planet_crystal" value="10000">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="planet_deuterium">Start Deuterium:</label>
      <input id="planet_deuterium" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="planet_deuterium" value="10000">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="planet_field_max">Start Fields:</label>
      <input id="planet_field_max" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="planet_field_max" value="163">

      <input class="btn btn-primary text-white w-100 my-2" type="submit" name="submit" value="Create">
    </form>
</div>


{/block}
