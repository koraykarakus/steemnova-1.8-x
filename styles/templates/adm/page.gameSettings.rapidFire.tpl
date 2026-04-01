{block name="content"}
<div class="bg-black w-75 p-3 my-3 mx-auto fs-12">
  <span class="fs-12 text-yellow fw-bold">RapidFire Settings</span>
  <div class="d-flex w-75 justify-content-start p-2 mx-auto">
    <a href="?page=gameSettings&mode=restoreRapidFire" class="btn btn-primary text-white">Turn back to default settings</a>
  </div>
  <div class="p-2 w-75 mx-auto">
        {foreach $rapid_fire_list as $c_id => $c_val}
          <table class="table table-dark w-100">
            <thead>
              <tr>
                <td colspan="3" class="text-yellow text-center">{$LNG.tech.{$c_id}} - ID: [{$c_id}]</td>
              </tr>
              <tr>
                <td class="text-yellow text-center">Against</td>
                <td class="text-yellow text-center">Shoots</td>
                <td class="text-yellow text-center">Action</td>
              </tr>
            </thead>
            <tbody>
              {foreach $c_val as $val}
                <tr>
                  <td class="text-start">{$LNG.tech.{$val.id}}</td>
                  <td class="text-center">
                  <input class="text-center" type="text" value="{$val.shoots}">
                  </td>
                  <td>
                    <a href="?page=gameSettings&mode=removeRapidFire&element_id={$c_id}&rapidfire_id={$val.id}" class="text-center btn bg-secondary text-danger">x</a>
                  </td>
                </tr>
              {/foreach}
            </tbody>
          </table>
        {/foreach}
  </div>
    
</div>
{/block}
