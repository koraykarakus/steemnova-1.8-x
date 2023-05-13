{if $AllPlanets}
  {foreach $AllPlanets as $PlanetRow}
    <a class="d-flex align-items-center justify-content-start hover-pointer text-decoration-none my-1 mx-auto p-1 border border-1 {if $PlanetRow.selected}border-danger{else}border-secondary{/if} w-75" href="game.php?page={$page}&amp;cp={$PlanetRow.id}" data-bs-toggle="tooltip"
    data-bs-placement="left"
    data-bs-html="true"
    title="
    <table class='table-tooltip fs-11'>
      <thead>
        <tr>
          <th class='text-start color-yellow' colspan='2'>{$PlanetRow.name}&nbsp;[{$PlanetRow.galaxy}:{$PlanetRow.system}:{$PlanetRow.planet}]</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class='text-start color-blue fw-bold'>{$LNG.pm_fields}:</td>
          <td class='text-end'>({$PlanetRow.field_current}&nbsp;/&nbsp;{$PlanetRow.field_max})</td>
        </tr>
        <tr>
          <td class='text-start color-blue fw-bold'>{$LNG.pm_diameter}:</td>
          <td class='text-end'>{$PlanetRow.diameter}</td>
        </tr>
        <tr>
          <td class='text-start color-blue fw-bold'>{$LNG.pm_min_temperature}:</td>
          <td class='text-end'>{$PlanetRow.temp_min}</td>
        </tr>
        <tr>
          <td class='text-start color-blue fw-bold'>{$LNG.pm_max_temperature}:</td>
          <td class='text-end'>{$PlanetRow.temp_max}</td>
        </tr>
      </tbody>
    </table>
    ">
      <img class="mx-2" src="{$dpath}planeten/{$PlanetRow.image}.jpg" width="50" height="50" alt="{$PlanetRow.name}">
    <div class="d-flex flex-column text-yellow align-items-start justify-content-start fs-11">
      <span>{$PlanetRow.name}</span>
      <span>[{$PlanetRow.galaxy}:{$PlanetRow.system}:{$PlanetRow.planet}]</span>
    </div>
    </a>
  {/foreach}
{/if}
