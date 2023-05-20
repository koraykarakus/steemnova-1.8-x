{block name="content"}

<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
  <thead>
    <tr>
      <td class="text-center">{$info_information}</td>
    </tr>
  </thead>
  <tbody>
    <tr>
  		<td>
              <pre class="left">```-- {$LNG.ad_infos_server} --
              {$LNG.ad_infos_server_infos}: {$info}
              {$LNG.ad_infos_php_version}: {$vPHP} ({$vAPI})
              {$LNG.ad_infos_jason_active}: {$json}
              {$LNG.ad_infos_bc_math_active}: {$bcmath}
              {$LNG.ad_infos_curl_active}: {$curl}
              {$LNG.ad_infos_safe_mode}: {$safemode}
              {$LNG.ad_infos_memory_limit}: {$memory}
              {$LNG.ad_infos_mysql_cli_version}: {$vMySQLc}
              {$LNG.ad_infos_mysql_server_version}: {$vMySQLs}
              {$LNG.ad_infos_error_log}: {$errorlog} ({$errorloglines}, {$log_errors})
              {$LNG.ad_infos_timezone}: {$php_tz} / {$conf_tz} / {$user_tz}
              {$LNG.ad_infos_db_suhosin}: {$suhosin}
              {$LNG.ad_infos_db_version}: {$dbVersion}

              -- {$LNG.ad_infos_game} --
              {$LNG.ad_infos_game_version}: 2Moons {$vGame}
              {$LNG.ad_infos_game_address}: http://{$root}/
              {$LNG.ad_infos_game_pfad}: http://{$gameroot}/index.php

              {$LNG.ad_infos_browser}: {$browser}
              ```</pre>
  		</td>
    </tr>
  </tbody>

</table>

{/block}
