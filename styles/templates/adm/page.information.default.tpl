{block name="content"}

<div class="d-flex flex-column bg-black text-white w-75 mx-auto p-3 my-3">
  <span>{$info_information}</span>
  <div style="word-break: break-word" class="d-flex flex-column py-2">
    <span class="fs-14 fw-bold">-- {$LNG.ad_infos_server} --</span>
    <span>{$LNG.ad_infos_server_infos}: {$info}</span>
    <span>{$LNG.ad_infos_php_version}: {$vPHP} ({$vAPI})</span>
    <span>{$LNG.ad_infos_jason_active}: {$json}</span>
    <span>{$LNG.ad_infos_bc_math_active}: {$bcmath}</span>
    <span>{$LNG.ad_infos_curl_active}: {$curl}</span>
    <span>{$LNG.ad_infos_safe_mode}: {$safemode}</span>
    <span>{$LNG.ad_infos_memory_limit}: {$memory}</span>
    <span>{$LNG.ad_infos_mysql_cli_version}: {$vMySQLc}</span>
    <span>{$LNG.ad_infos_mysql_server_version}: {$vMySQLs}</span>
    <span>{$LNG.ad_infos_error_log}: {$errorlog} ({$errorloglines}, {$log_errors})</span>
    <span>{$LNG.ad_infos_timezone}: {$php_tz} / {$conf_tz} / {$user_tz}</span>
    <span>{$LNG.ad_infos_db_suhosin}: {$suhosin}</span>
    <span>{$LNG.ad_infos_db_version}: {$dbVersion}</span>
  </div>
  <div style="word-break: break-word" class="d-flex flex-column py-2">
    <span class="fs-14 fw-bold">-- {$LNG.ad_infos_game} --</span>
    <span>{$LNG.ad_infos_game_version}: 2Moons {$vGame}</span>
    <span>{$LNG.ad_infos_game_address}: http://{$root}/</span>
    <span>{$LNG.ad_infos_game_pfad}: http://{$gameroot}/index.php</span>
    <span>{$LNG.ad_infos_browser}: {$browser}</span>
  </div>


</div>


{/block}
