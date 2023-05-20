{block name="content"}

<form method="post" action="?page=stats&mode=saveSettings">
	<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
    <tr>
      <th colspan="2">{$cs_title}</th>
    </tr>
	<tr>
      <td>{$cs_point_per_resources_used} ({$cs_resources})</td>
      <td><input type="text" name="stat_settings" value="{$stat_settings}"></td>
    </tr>
    <tr>
      <td>{$cs_points_to_zero}</td>
      <td>{html_options name=stat options=$Selector selected=$stat}</td>
    </tr>
    <tr>
      <td>{$cs_access_lvl}</td>
      <td><input type="text" name="stat_level" value="{$stat_level}"></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" value="{$cs_save_changes}"></td>
    </tr>
  </table>
</form>

{/block}
