{block name="content"}
<div class="bg-black w-75 p-3 my-3 mx-auto fs-12">
  <span class="fs-12 text-yellow fw-bold">{$LNG.gsb_title}</span>

  <ul class="nav nav-tabs" id="" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_1" type="button">
          {$LNG.gsb_settings}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_2" type="button">
          {$LNG.gsb_add_new_building}
        </button>
      </li>
  </ul>

  <div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="tab_1">
      <div class="d-flex w-100 justify-content-start p-2 mx-auto">
        <a href="?page=gameSettings&mode=restoreBuildings" class="btn btn-primary text-white">
          {$LNG.gsb_default_settings}
        </a>
      </div>
      <div class="p-2 w-100 mx-auto">
        {foreach $buildings_list as $c_building}
          <table class="table table-dark w-100">
            <thead>
              <tr>
                <td colspan="6" class="text-yellow text-center">{$LNG.tech.{$c_building.element_id}} - ID: [{$c_building.element_id}]</td>
              </tr>
            </thead>
            <tbody>
              <form action="?page=gameSettings&mode=updateBuildings" method="post">
              <input type="hidden" name="element_id" value="{$c_building.element_id}">
              <tr>
                <td class="text-yellow text-center">{$LNG.gsb_cost_grow_factor}</td>
                <td class="text-yellow text-center">{$LNG.gsb_base_cost_metal}</td>
                <td class="text-yellow text-center">{$LNG.gsb_base_cost_crystal}</td>
              </tr>
              <tr>
                <td class="text-center">
                  <input step="0.01" type="number" name="factor" value="{$c_building.factor}" class="form-control text-center">
                </td>
                <td class="text-center">
                  <input type="number" name="cost_metal" value="{$c_building.cost901}" class="form-control text-center">
                </td>
                <td class="text-center">
                  <input type="number" name="cost_crystal" value="{$c_building.cost902}" class="form-control text-center">
                </td>
              </tr>
              <tr>
                <td class="text-yellow text-center">{$LNG.gsb_base_cost_deu}</td>
                <td class="text-yellow text-center">{$LNG.gsb_base_cost_energy}</td>
                <td class="text-yellow text-center">{$LNG.gsb_base_cost_dm}</td>
              </tr>
              <tr>
                <td class="text-center">
                  <input type="number" name="cost_deu" value="{$c_building.cost903}" class="form-control text-center">
                </td>
                <td class="text-center">
                  <input type="number" name="cost_energy" value="{$c_building.cost911}" class="form-control text-center">
                </td>
                <td class="text-center">
                  <input type="number" name="cost_dm" value="{$c_building.cost921}" class="form-control text-center">
                </td>
              </tr>
              <tr>
                <td class="text-start" colspan="6">
                  <button type="submit" class="btn btn-primary text-white w-25">{$LNG.gsb_modify}</button>
                </td>
              </tr>
              </form>
            </tbody>
          </table>
        {/foreach}
      </div>
    </div>
    <div class="tab-pane fade" id="tab_2">
        
    </div>
  </div>

  
    
</div>
{/block}
