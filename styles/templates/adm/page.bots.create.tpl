{block name="content"}


<div class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12">
  <span class="fs-12 text-yellow fw-bold">Create Bots</span>
    <form class="" action="?page=bots&mode=createSend" method="post">
      <label class="text-start my-1 cursor-pointer hover-underline w-100" for="bots_number">Number of bots:</label>
      <input id="bots_number" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="number" name="bots_number" value="0">
      <input class="btn btn-primary text-white w-100 my-2" type="submit" name="submit" value="Create">
    </form>
</div>


{/block}
