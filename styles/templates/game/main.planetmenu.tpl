{if $AllPlanets}
  {foreach $AllPlanets as $PlanetRow}
    <div class="planet 
      {if isset($PlanetRow.moonInfo)}
        {if $PlanetRow.moonInfo[0].selected || $PlanetRow.selected}
        border-danger
        {/if}
      {else}
        {if $PlanetRow.selected}
        border-danger
        {/if}
      {/if}">
    <div class="left">
    <a class="type_planet" href="game.php?page={if empty($page)}overview{else}{$page}{/if}&amp;cp={$PlanetRow.id}">
        <div class="tooltip tooltip_right"> 
          <table class=''>
            <thead>
              <tr>
                <th class='color-yellow' colspan='2'>{$PlanetRow.name}&nbsp;[{$PlanetRow.galaxy}:{$PlanetRow.system}:{$PlanetRow.planet}]</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class='color-blue'>{$LNG.pm_fields}:</td>
                <td class='text_right'>({$PlanetRow.field_current}&nbsp;/&nbsp;{$PlanetRow.field_max})</td>
              </tr>
              <tr>
                <td class='color-blue'>{$LNG.pm_diameter}:</td>
                <td class='text_right'>{$PlanetRow.diameter}</td>
              </tr>
              <tr>
                <td class='color-blue'>{$LNG.pm_min_temperature}:</td>
                <td class='text_right'>{$PlanetRow.temp_min}</td>
              </tr>
              <tr>
                <td class='color-blue'>{$LNG.pm_max_temperature}:</td>
                <td class='text_right'>{$PlanetRow.temp_max}</td>
              </tr>
            </tbody>
          </table>
        </div>
        {if !empty($PlanetRow)}
        <img class="{if $PlanetRow.selected}border-yellow{/if}" src="{$dpath}planets/small/s_{$PlanetRow.image}.jpg" width="33" height="33" alt="{$PlanetRow.name}">
        {/if}
      </a>
      {if isset($PlanetRow.moonInfo)}
      <a class="type_moon" href="game.php?page={if empty($page)}overview{else}{$page}{/if}&amp;cp={$PlanetRow.moonInfo[0].id}">
        <div class="tooltip tooltip_right">  
          <table class=''>
            <thead>
              <tr>
                <th class='color-yellow' colspan='2'>{$PlanetRow.moonInfo[0].name}&nbsp;[{$PlanetRow.moonInfo[0].galaxy}:{$PlanetRow.moonInfo[0].system}:{$PlanetRow.moonInfo[0].planet}]</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class='color-blue'>{$LNG.pm_fields}:</td>
                <td class='text_right'>({$PlanetRow.moonInfo[0].field_current}&nbsp;/&nbsp;{$PlanetRow.moonInfo[0].field_max})</td>
              </tr>
              <tr>
                <td class='color-blue'>{$LNG.pm_diameter}:</td>
                <td class='text_right'>{$PlanetRow.moonInfo[0].diameter}</td>
              </tr>
              <tr>
                <td class='color-blue'>{$LNG.pm_min_temperature}:</td>
                <td class='text_right'>{$PlanetRow.moonInfo[0].temp_min}</td>
              </tr>
              <tr>
                <td class='color-blue'>{$LNG.pm_max_temperature}:</td>
                <td class='text_right'>{$PlanetRow.moonInfo[0].temp_max}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <img class="{if $PlanetRow.moonInfo[0].selected}border-yellow{else}hover-border-yellow{/if}" src="{$dpath}planets/small/s_{$PlanetRow.moonInfo[0].image}.jpg" width="20" height="20" alt="{$PlanetRow.moonInfo[0].name}">
      </a>
      {/if}
    </div>
    <div class="right">
      <span>{$PlanetRow.name}</span>
      <span>[{$PlanetRow.galaxy}:{$PlanetRow.system}:{$PlanetRow.planet}]</span>
    </div>
    </div>
  {/foreach}
{/if}
