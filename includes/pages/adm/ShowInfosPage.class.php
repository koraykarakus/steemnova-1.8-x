<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 * @link https://github.com/jkroepke/2Moons
 */

/**
 *
 */
class ShowInfosPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG, $USER, $config;

        // @ for open_basedir
        if (@file_exists(ini_get('error_log')))
        {
            $lines = count(file(ini_get('error_log')));
        }
        else
        {
            $lines = 0;
        }

        try
        {
            $date_time_zone_server = new DateTimeZone($config->timezone);
        }
        catch (Exception $e)
        {
            $date_time_zone_server = new DateTimeZone(date_default_timezone_get());
        }

        try
        {
            $date_time_zone_user = new DateTimeZone($USER['timezone']);
        }
        catch (Exception $e)
        {
            $date_time_zone_user = new DateTimeZone(date_default_timezone_get());
        }

        try
        {
            $date_time_zone_php = new DateTimeZone(ini_get('date.timezone'));
        }
        catch (Exception $e)
        {
            $date_time_zone_php = new DateTimeZone(date_default_timezone_get());
        }

        $date_time_server = new DateTime("now", $date_time_zone_server);
        $date_time_user = new DateTime("now", $date_time_zone_user);
        $date_time_php = new DateTime("now", $date_time_zone_php);

        $sql = "SELECT dbVersion FROM %%SYSTEM%%;";

        $db_version = Database::get()->selectSingle($sql, [], 'dbVersion');

        $this->assign([
            'info_information'     => sprintf($LNG['info_information'], 'https://github.com/koraykarakus/steemnova-1.8-x/issues'),
            'info'                 => $_SERVER['SERVER_SOFTWARE'],
            'php_version'          => PHP_VERSION,
            'php_api_version'      => PHP_SAPI,
            'game_version'         => $config->VERSION.(file_exists(ROOT_PATH.'/.git/ORIG_HEAD') ? ' ('.trim(file_get_contents(ROOT_PATH.'/.git/ORIG_HEAD')).')' : ''),
            'mysql_client_version' => $GLOBALS['DATABASE']->getVersion(),
            'mysql_server_version' => $GLOBALS['DATABASE']->getServerVersion(),
            'root'                 => $_SERVER['SERVER_NAME'],
            'gameroot'             => $_SERVER['SERVER_NAME'].str_replace('/admin.php', '', $_SERVER['PHP_SELF']),
            'json'                 => function_exists('json_encode') ? $LNG['ad_infos_yes'] : $LNG['ad_infos_no'],
            'bcmath'               => extension_loaded('bcmath') ? $LNG['ad_infos_yes'] : $LNG['ad_infos_no'],
            'curl'                 => extension_loaded('curl') ? $LNG['ad_infos_yes'] : $LNG['ad_infos_no'],
            'browser'              => $_SERVER['HTTP_USER_AGENT'],
            'safemode'             => ini_get('safe_mode') ? $LNG['ad_infos_yes'] : $LNG['ad_infos_no'],
            'memory'               => ini_get('memory_limit'),
            'suhosin'              => ini_get('suhosin.request.max_value_length') ? $LNG['ad_infos_yes'] : $LNG['ad_infos_no'],
            'log_errors'           => ini_get('log_errors') ? $LNG['ad_infos_active'] : $LNG['ad_infos_inactive'],
            'errorlog'             => ini_get('error_log'),
            'errorloglines'        => $lines,
            'db_version'           => $db_version,
            'php_tz'               => $date_time_php->getOffset() / 3600,
            'conf_tz'              => $date_time_server->getOffset() / 3600,
            'user_tz'              => $date_time_user->getOffset() / 3600,
        ]);

        $this->display('page.information.default.tpl');

    }

}
