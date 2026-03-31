{block name="content"}
<div class="bg-black w-75 p-3 my-3 mx-auto fs-12">
  <span class="fs-12 text-yellow fw-bold">RapidFire Settings</span>
    {foreach $rapid_fire_list as $c_id => $c_val}
      <table>
        <thead>
          <tr>
            <td class="text-yellow">{$LNG.tech.{$c_id}}</td>
          </tr>
        </thead>
        <tbody>
          {foreach $c_val as $val}
            <tr>
              <td>{$LNG.tech.{$val.id}}</td>
              <td>{$val.shoots}</td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    {/foreach}
</div>
{/block}
