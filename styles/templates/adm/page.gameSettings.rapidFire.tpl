{block name="content"}
<div class="bg-black w-75 p-3 my-3 mx-auto fs-12">
  <span class="fs-12 text-yellow fw-bold">RapidFire Settings</span>

  <ul class="nav nav-tabs" id="" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_1" type="button">
          Current Rapidfire
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_2" type="button">
          Add new rapidfire rule
        </button>
      </li>
  </ul>

  <div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="tab_1">
      <div class="d-flex w-100 justify-content-start p-2 mx-auto">
        <a href="?page=gameSettings&mode=restoreRapidFire" class="btn btn-primary text-white">Turn back to default settings</a>
      </div>
      <div class="p-2 w-100 mx-auto">
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
              <form action="?page=gameSettings&mode=updateRapidFire" method="post">
              <input type="hidden" name="element_id" value="{$c_id}">
              {foreach $c_val as $val}
                <tr>
                  <td class="text-start">{$LNG.tech.{$val.id}} [ID: {$val.id}]</td>
                  <td class="text-center">
                  <input name="shoots[{$val.id}]" class="text-center" type="number" value="{$val.shoots}">
                  </td>
                  <td>
                    <a href="?page=gameSettings&mode=removeRapidFire&element_id={$c_id}&rapidfire_id={$val.id}" class="text-center btn bg-secondary text-danger">x</a>
                  </td>
                </tr>
              {/foreach}
              <tr>
                <td class="text-start" colspan="3">
                  <button type="submit" class="btn btn-primary text-white w-25">Modify</button>
                </td>
              </tr>
              </form>
            </tbody>
          </table>
        {/foreach}
      </div>
    </div>
    <div class="tab-pane fade" id="tab_2">
        <table class="table table-dark">
          <thead>
            <tr>
              <td colspan="3">Add new rule</td>
            </tr>
            <tr>
              <td>From</td>
              <td>To</td>
              <td>shoots</td>
            </tr>
          </thead>
          <tbody>
            <form action="?page=gameSettings&mode=addRapidFire" method="post">
              <tr>
                <td>
                  <select class="form-select bg-dark text-white" name="element_id" id="">
                    {foreach $elements as $c_element}
                      <option value="{$c_element}">
                        {$LNG.tech.{$c_element}}
                      </option>
                    {/foreach}
                  </select>
                </td>
                <td>
                  <select class="form-select bg-dark text-white" name="rapidfire_id" id="">
                    {foreach $elements as $c_element}
                      <option value="{$c_element}">
                        {$LNG.tech.{$c_element}}
                      </option>
                    {/foreach}
                  </select>
                </td>
                <td>
                  <input class="form-control bg-dark text-white" name="shoots" type="number" value="0">
                </td>
              </tr>
              <tr>
                <td colspan="3" class="text-start">
                    <button class="btn btn-primary text-white w-25" type="submit">Save</button>
                </td>
              </tr>
            </form>
          </tbody>
          <tr></tr>
        </table>
    </div>
  </div>

  
    
</div>
{/block}
