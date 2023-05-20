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
              <pre class="left">```-- Server Info --
              Server Infos: {$info}
              PHP-Version: {$vPHP} ({$vAPI})
              JSON Verfügbar: {$json}
              BCMath Verfügbar: {$bcmath}
              cURL Verfügbar: {$curl}
              SafeMode: {$safemode}
              MemoryLimit: {$memory}
              MySQL-Client-Version: {$vMySQLc}
              MySQL-Server-Version: {$vMySQLs}
              ErrorLog: {$errorlog} ({$errorloglines}, {$log_errors})
              Timezone(PHP/CONF/USER): {$php_tz} / {$conf_tz} / {$user_tz}
              Suhosin: {$suhosin}
              DB Version: {$dbVersion}

              -- Game --
              Game Version: 2Moons {$vGame}
              Game Addresse: http://{$root}/
              Game Pfad: http://{$gameroot}/index.php

              Browser: {$browser}
              ```</pre>
  		</td>
    </tr>
  </tbody>

</table>

{/block}
