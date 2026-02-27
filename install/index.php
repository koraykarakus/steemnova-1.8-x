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
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

define('MODE', 'INSTALL');
define('ROOT_PATH', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/');
set_include_path(ROOT_PATH);
chdir(ROOT_PATH);

require 'includes/common.php';
$THEME->setUserTheme('gow');
$LNG = new Language();
$LNG->getUserAgentLanguage();
$LNG->includeData(['L18N', 'INGAME', 'INSTALL', 'CUSTOM']);

$mode = HTTP::_GP('mode', '');

$template = new template();
$template->setCaching(false);
$template->assign([
    'lang'       => $LNG->getLanguage(),
    'Selector'   => $LNG->getAllowedLangs(false),
    'title'      => $LNG['title_install'] . ' &bull; 2Moons',
    'header'     => $LNG['menu_install'],
    'canUpgrade' => file_exists('includes/config.php') && filesize('includes/config.php') !== 0,
]);

$path_install_file = 'includes/ENABLE_INSTALL_TOOL';
$path_quick_start_file = 'includes/FIRST_INSTALL';

// If include/FIRST_INSTALL is present and can be deleted, automatically create include/ENABLE_INSTALL_TOOL
if (is_file($path_quick_start_file)
    && is_writeable($path_quick_start_file)
    && unlink($path_quick_start_file))
{
    @touch($path_install_file);
}

// Only allow Install Tool access if the file "include/ENABLE_INSTALL_TOOL" is found
if (is_file($path_install_file)
    && (time() - filemtime($path_install_file) > 3600))
{
    $content = file_get_contents($path_install_file);
    $verify_string = 'KEEP_FILE';
    if (trim($content) !== $verify_string)
    {
        // Delete the file if it is older than 3600s (1 hour)
        unlink($path_install_file);
    }
}

if (!is_file($path_install_file))
{
    switch ($mode)
    {
        case 'upgrade':
            $message = $LNG->getTemplate('locked_upgrade');
            break;
        default:
            $message = $LNG->getTemplate('locked_install');
            break;
    }
    $template->message($message, false, 0, true);
    exit;
}

$language = HTTP::_GP('lang', '');

if (!empty($language)
    && in_array($language, $LNG->getAllowedLangs()))
{
    setcookie('lang', $language);
}

switch ($mode)
{
    case 'ajax':
        require 'includes/libs/ftp/ftp.class.php';
        require 'includes/libs/ftp/ftpexception.class.php';
        $LNG->includeData(['ADMIN']);
        $connection_config = [
            "host"     => $_GET['host'],
            "username" => $_GET['user'],
            "password" => $_GET['pass'],
            "port"     => 21,
        ];

        try
        {
            $ftp = FTP::getInstance();
            $ftp->connect($connection_config);
        }
        catch (FTPException $error)
        {
            exit($LNG['req_ftp_error_data']);
        }
        if (!$ftp->changeDir($_GET['path']))
        {
            exit($LNG['req_ftp_error_dir']);
        }

        $CHMOD = (php_sapi_name() == 'apache2handler') ? 0777 : 0755;
        $ftp->chmod('cache', $CHMOD);
        $ftp->chmod('includes', $CHMOD);
        $ftp->chmod('install', $CHMOD);
        break;
    case 'upgrade':
        // Willkommen zum Update page. Anzeige, von und zu geupdatet wird. Informationen, dass ein backup erstellt wird.

        try
        {
            $sql = "SELECT dbVersion FROM %%SYSTEM%%;";

            $db_version = Database::get()->selectSingle($sql, [], 'dbVersion');
        }
        catch (Exception $e)
        {
            $db_version = 0;
        }

        $updates = [];

        $file_revision = 0;

        $directory_iterator = new DirectoryIterator(ROOT_PATH . 'install/migrations/');
        /** @var DirectoryIterator $file_info */
        foreach ($directory_iterator as $file_info)
        {
            if (!$file_info->isFile()
                || !preg_match('/^migration_\d+/', $file_info->getFilename()))
            {
                continue;
            }

            $file_revision = substr($file_info->getFilename(), 10, -4);

            if ($file_revision <= $db_version
                || $file_revision > DB_VERSION_REQUIRED)
            {
                continue;
            }

            $updates[$file_info->getPathname()] = makebr(
                str_replace(
                    '%PREFIX%',
                    DB_PREFIX,
                    file_get_contents($file_info->getPathname())
                )
            );
        }

        $template->assign_vars([
            'file_revision' => min(DB_VERSION_REQUIRED, $file_revision),
            'sql_revision'  => $db_version,
            'updates'       => $updates,
            'header'        => $LNG['menu_upgrade'],
        ]);

        $template->show('ins_update.tpl');
        break;
    case 'doupgrade':
        // TODO:Need a rewrite!
        require 'includes/config.php';

        // Create a Backup
        $sql_table_raw = Database::get()->nativeQuery("SHOW TABLE STATUS FROM `" . DB_NAME . "`;");
        $prefix_counts = strlen(DB_PREFIX);
        $db_tables = [];
        foreach ($sql_table_raw as $table)
        {
            if (DB_PREFIX == substr($table['Name'], 0, $prefix_counts))
            {
                $db_tables[] = $table['Name'];
            }
        }

        if (empty($db_tables))
        {
            throw new Exception('No tables found for dump.');
        }

        @set_time_limit(600);

        $file_name = '2MoonsBackup_' . date('Y_m_d_H_i_s', TIMESTAMP) . '.sql';
        $file_path = 'includes/backups/' . $file_name;
        require 'includes/classes/SQLDumper.class.php';
        $dump = new SQLDumper();
        $dump->dumpTablesToFile($db_tables, $file_path);

        try
        {
            $sql = "SELECT dbVersion FROM %%SYSTEM%%;";
            $db_version = Database::get()->selectSingle($sql, [], 'dbVersion');
        }
        catch (Exception $e)
        {
            $db_version = 0;
        }

        $http_root = PROTOCOL . HTTP_HOST . str_replace(
            ['\\', '//'],
            '/',
            dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'
        );

        $revision = $db_version;
        $file_list = [];
        $directory_iterator = new DirectoryIterator(ROOT_PATH . 'install/migrations/');
        /** @var DirectoryIterator $file_info */
        foreach ($directory_iterator as $file_info)
        {
            if (!$file_info->isFile())
            {
                continue;
            }
            $file_revision = substr($file_info->getFilename(), 10, -4);
            if ($file_revision > $revision
                && $file_revision <= DB_VERSION_REQUIRED)
            {
                $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                $key = $file_revision . ((int)$file_extension === 'php');
                $file_list[$key] = [
                    'fileName'       => $file_info->getFilename(),
                    'fileRevision'   => $file_revision,
                    'file_extension' => $file_extension,
                ];
            }
        }
        ksort($file_list);
        foreach ($file_list as $file_info)
        {
            switch ($file_info['file_extension'])
            {
                case 'php':
                    copy(
                        ROOT_PATH.'install/migrations/' . $file_info['fileName'],
                        ROOT_PATH.$file_info['fileName']
                    );
                    $ch = curl_init($http_root . $file_info['fileName']);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_setopt($ch, CURLOPT_MUTE, true);
                    curl_exec($ch);
                    if (curl_errno($ch))
                    {
                        $error_msg = 'CURL-Error on update ' . basename($file_info['filePath']) . ':' . curl_error($ch);
                        try
                        {
                            $dump->restoreDatabase($file_path);
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Backup restored.</i></b>';
                        }
                        catch (Exception $e)
                        {
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Can not restore backup. Your game is maybe broken right now.</i></b><br><br>Restore error:<br>' . $e->getMessage();
                        }
                        throw new Exception($message);
                    }
                    curl_close($ch);
                    unlink($file_info['fileName']);
                    break;
                case 'sql':
                    $data = file_get_contents(ROOT_PATH . 'install/migrations/' . $file_info['fileName']);
                    try
                    {
                        $queries = explode(";\n", str_replace('%PREFIX%', DB_PREFIX, $data));
                        $queries = array_filter($queries);
                        foreach ($queries as $query)
                        {
                            try
                            {
                                // alter table IF NOT EXISTS
                                Database::get()->nativeQuery(trim($query));
                            }
                            catch (Exception $e)
                            {
                                error_log('Query: [' . $query . '] failed. Error: ' . $e->getMessage() . '. Skipped');
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        $error_msg = $e->getMessage();
                        try
                        {
                            $dump->restoreDatabase($file_path);
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Backup restored.</i></b>';
                        }
                        catch (Exception $e)
                        {
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Can not restore backup. Your game is maybe broken right now.</i></b><br><br>Restore error:<br>' . $e->getMessage();
                        }
                        throw new Exception($message);
                    }
                    break;
            }
        }
        $revision = end($file_list);
        $revision = $revision['fileRevision'];

        Database::get()->update("UPDATE %%SYSTEM%% SET dbVersion = " . DB_VERSION_REQUIRED . ";");

        ClearCache();

        $template->assign_vars([
            'update'   => !empty($file_list),
            'revision' => $revision,
            'header'   => $LNG['menu_upgrade'],
        ]);
        $template->show('ins_doupdate.tpl');
        unlink($path_install_file);
        break;
    case 'install':
        $step = HTTP::_GP('step', 0);
        switch ($step)
        {
            case 1:
                if (isset($_POST['post']))
                {
                    if (isset($_POST['accept']))
                    {
                        HTTP::redirectTo('index.php?mode=install&step=2');
                    }
                    else
                    {
                        $template->assign([
                            'accept' => false,
                        ]);
                    }
                }
                $template->show('ins_license.tpl');
                break;
            case 2:
                $error = $ftp = false;

                if (version_compare(PHP_VERSION, "8.0.0", ">="))
                {
                    $PHP = "<span class=\"text-success\">" . $LNG['reg_yes'] . ", v" . PHP_VERSION . "</span>";
                }
                else
                {
                    $PHP = "<span class=\"text-danger\">" . $LNG['reg_no'] . ", v" . PHP_VERSION . "</span>";
                    $error = true;
                }

                if (class_exists('PDO')
                    && in_array('mysql', PDO::getAvailableDrivers()))
                {
                    $pdo = "<span class=\"text-success\">" . $LNG['reg_yes'] . "</span>";
                }
                else
                {
                    $pdo = "<span class=\"text-danger\">" . $LNG['reg_no'] . "</span>";
                    $error = true;
                }

                if (function_exists('json_encode'))
                {
                    $json = "<span class=\"text-success\">" . $LNG['reg_yes'] . "</span>";
                }
                else
                {
                    $json = "<span class=\"text-danger\">" . $LNG['reg_no'] . "</span>";
                    $error = true;
                }

                if (function_exists('ini_set'))
                {
                    $iniset = "<span class=\"text-success\">" . $LNG['reg_yes'] . "</span>";
                }
                else
                {
                    $iniset = "<span class=\"text-danger\">" . $LNG['reg_no'] . "</span>";
                    $error = true;
                }

                if (!ini_get('register_globals'))
                {
                    $global = "<span class=\"text-success\">" . $LNG['reg_yes'] . "</span>";
                }
                else
                {
                    $global = "<span class=\"text-danger\">" . $LNG['reg_no'] . "</span>";
                    $error = true;
                }

                if (!extension_loaded('gd'))
                {
                    $gdlib = "<span class=\"text-danger\">" . $LNG['reg_no'] . "</span>";
                }
                else
                {
                    $gd_version = '0.0.0';
                    if (function_exists('gd_info'))
                    {
                        $temp = gd_info();
                        $match = [];
                        if (preg_match('!([0-9]+\.[0-9]+(?:\.[0-9]+)?)!', $temp['GD Version'], $match))
                        {
                            if (preg_match('/^[0-9]+\.[0-9]+$/', $match[1]))
                            {
                                $match[1] .= '.0';
                            }
                            $gd_version = $match[1];
                        }
                    }
                    $gdlib = "<span class=\"text-success\">" . $LNG['reg_yes'] . ", v" . $gd_version . "</span>";
                }

                clearstatcache();

                if (file_exists(ROOT_PATH . "includes/config.php")
                    || @touch(ROOT_PATH . "includes/config.php"))
                {

                    if (is_writable(ROOT_PATH . "includes/config.php"))
                    {
                        $chmod = "<span class=\"text-success\"> - " . $LNG['reg_writable'] . "</span>";
                    }
                    else
                    {
                        $chmod = " - <span class=\"text-danger\">" . $LNG['reg_not_writable'] . "</span>";
                        $error = true;
                        $ftp = true;
                    }

                    $config = "<tr><td class=\"transparent left\"><p>" . sprintf($LNG['reg_file'], 'includes/config.php') . "</p></td><td class=\"transparent\"><span class=\"text-success\">" . $LNG['reg_found'] . "</span>" . $chmod . "</td></tr>";
                }
                else
                {
                    $config = "<tr><td class=\"transparent left\"><p>" . sprintf($LNG['reg_file'], 'includes/config.php') . "</p></td><td class=\"transparent\"><span class=\"text-danger\">" . $LNG['reg_not_found'] . "</span></td></tr>";
                    $error = true;
                    $ftp = true;
                }

                $directories = ['cache/', 'cache/templates/', 'cache/sessions/', 'includes/'];
                $dirs = "";
                foreach ($directories as $dir)
                {
                    if (file_exists(ROOT_PATH . $dir)
                        || @mkdir(ROOT_PATH . $dir))
                    {
                        if (is_writable(ROOT_PATH . $dir))
                        {
                            $chmod = "<span class=\"text-success\"> - " . $LNG['reg_writable'] . "</span>";
                        }
                        else
                        {
                            $chmod = " - <span class=\"text-danger\">" . $LNG['reg_not_writable'] . "</span>";
                            $error = true;
                            $ftp = true;
                        }

                        $dirs .= "<tr><td class=\"transparent left\"><p>" . sprintf($LNG['reg_dir'], $dir) . "</p></td><td class=\"transparent\"><span class=\"text-success\">" . $LNG['reg_found'] . "</span>" . $chmod . "</td></tr>";

                    }
                    else
                    {
                        $dirs .= "<tr><td class=\"transparent left\"><p>" . sprintf($LNG['reg_dir'], $dir) . "</p></td><td class=\"transparent\"><span class=\"text-danger\">" . $LNG['reg_not_found'] . "</span></td></tr>";
                    }

                }

                if ($error == false)
                {
                    $done = '
					<tr class="noborder"><td colspan="2" class="transparent">
						<a class="btn btn-primary text-white w-100 my-2 p-1" href="index.php?mode=install&step=3">
							' . $LNG['continue'] . '
						</a>
					</td>
					</tr>';
                }
                else
                {
                    $done = '';
                }

                $template->assign([
                    'dir'    => $dirs,
                    'json'   => $json,
                    'done'   => $done,
                    'config' => $config,
                    'gdlib'  => $gdlib,
                    'PHP'    => $PHP,
                    'pdo'    => $pdo,
                    'ftp'    => $ftp,
                    'iniset' => $iniset,
                    'global' => $global,
                ]);

                $template->show('ins_req.tpl');
                break;
            case 3:

                $template->assign([
                    'host'     => getenv('DB_HOST'),
                    'user'     => getenv('DB_USER'),
                    'password' => getenv('DB_PASSWORD'),
                    'dbname'   => getenv('DB_NAME'),
                ]);

                $template->show('ins_form.tpl');

                break;
            case 4:
                $host = HTTP::_GP('host', '');
                $port = HTTP::_GP('port', 3306);
                $user = HTTP::_GP('user', '', true);
                $userpw = HTTP::_GP('passwort', '', true);
                $dbname = HTTP::_GP('dbname', '', true);
                $prefix = HTTP::_GP('prefix', 'uni1_');

                $template->assign([
                    'host'   => $host,
                    'port'   => $port,
                    'user'   => $user,
                    'dbname' => $dbname,
                    'prefix' => $prefix,
                ]);

                if (empty($dbname))
                {

                    $template->assign([
                        'class'   => 'fatalerror',
                        'message' => $LNG['step2_db_no_dbname'], ]);

                    $template->show('ins_step4.tpl');
                    exit;
                }

                if (strlen($prefix) > 36)
                {

                    $template->assign([
                        'class'   => 'fatalerror',
                        'message' => $LNG['step2_db_too_long'],
                    ]);

                    $template->show('ins_step4.tpl');
                    exit;
                }

                if (strspn($prefix, '-./\\') !== 0)
                {

                    $template->assign([
                        'class'   => 'fatalerror',
                        'message' => $LNG['step2_prefix_invalid'],
                    ]);

                    $template->show('ins_step4.tpl');

                    exit;
                }

                if (preg_match('!^[0-9]!', $prefix) !== 0)
                {

                    $template->assign([
                        'class'   => 'fatalerror',
                        'message' => $LNG['step2_prefix_invalid'],
                    ]);

                    $template->show('ins_step4.tpl');
                    exit;
                }

                if (is_file(ROOT_PATH . "includes/config.php")
                    && filesize(ROOT_PATH . "includes/config.php") != 0)
                {

                    $template->assign([
                        'class'   => 'fatalerror',
                        'message' => $LNG['step2_config_exists'],
                    ]);

                    $template->show('ins_step4.tpl');

                    exit;
                }
                @touch(ROOT_PATH . "includes/config.php");
                if (!is_writable(ROOT_PATH . "includes/config.php"))
                {

                    $template->assign([
                        'class'   => 'fatalerror',
                        'message' => $LNG['step2_conf_op_fail'],
                    ]);

                    $template->show('ins_step4.tpl');
                    exit;
                }
                $database = [];
                $database['host'] = $host;
                $database['port'] = $port;
                $database['user'] = $user;
                $database['userpw'] = $userpw;
                $database['databasename'] = $dbname;
                $database['tableprefix'] = $prefix;
                $blowfish = substr(str_shuffle('./0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 22);
                file_put_contents(ROOT_PATH . 'includes/config.php', sprintf(file_get_contents('includes/config.sample.php'), $host, $port, $user, $userpw, $dbname, $prefix, $blowfish));
                try
                {
                    Database::get();
                }
                catch (Exception $e)
                {

                    $template->assign([
                        'class'   => 'fatalerror',
                        'message' => $LNG['step2_db_con_fail'] . '</p><p>' . $e->getMessage(),
                    ]);

                    $template->show('ins_step4.tpl');

                    unlink(ROOT_PATH . 'includes/config.php');
                    exit;
                }
                @touch(ROOT_PATH . "includes/error.log");

                $template->assign([
                    'class'   => 'noerror',
                    'message' => $LNG['step2_db_done'],
                ]);

                $template->show('ins_step4.tpl');
                exit;
                break;
            case 5:
                $template->show('ins_step5.tpl');
                break;
            case 6:
                $db = Database::get();
                $install_sql = file_get_contents('install/install.sql');
                $install_version = file_get_contents('install/VERSION');
                $install_revision = 0;
                preg_match('!\$' . 'Id: install.sql ([0-9]+)!', $install_sql, $match);
                $install_version = explode('.', $install_version);

                if (isset($match[1]))
                {
                    $install_revision = (int)$match[1];
                    $install_version[2] = $install_revision;
                }
                else
                {
                    $install_revision = (int)$install_version[2];
                }

                $install_version = implode('.', $install_version);
                try
                {
                    $db->query(str_replace([
                        '%PREFIX%',
                        '%VERSION%',
                        '%REVISION%',
                        '%DB_VERSION%',
                    ], [
                        DB_PREFIX,
                        $install_version,
                        $install_revision,
                        DB_VERSION_REQUIRED,
                    ], $install_sql));

                    $config = Config::get(Universe::current());
                    $config->timezone = @date_default_timezone_get();
                    $config->lang = $LNG->getLanguage();
                    $config->OverviewNewsText = $LNG['sql_welcome'] . $install_version;
                    $config->uni_name = $LNG['fcm_universe'] . ' ' . Universe::current();
                    $config->close_reason = $LNG['sql_close_reason'];
                    $config->moduls = implode(';', array_fill(0, MODULE_AMOUNT - 1, 1));

                    unset($install_sql, $install_revision, $install_version);

                    $config->save();

                    HTTP::redirectTo('index.php?mode=install&step=7');
                }
                catch (Exception $e)
                {
                    require 'includes/config.php';
                    @unlink('includes/config.php');
                    $error = $e->getMessage();
                    $template->assign([
                        'host'    => $database['host'],
                        'port'    => $database['port'],
                        'user'    => $database['user'],
                        'dbname'  => $database['databasename'],
                        'prefix'  => $database['tableprefix'],
                        'class'   => 'fatalerror',
                        'message' => $LNG['step3_db_error'] . '</p><p>' . $error,
                    ]);
                    $template->show('ins_step4.tpl');
                    exit;
                }
                break;
            case 7:

                $template->assign([
                    'name'     => getenv('ADMIN_NAME'),
                    'password' => getenv('ADMIN_PASSWORD'),
                    'mail'     => getenv('ADMIN_MAIL'),
                ]);

                $template->show('ins_acc.tpl');
                break;
            case 8:
                $username = HTTP::_GP('username', '', UTF8_SUPPORT);
                $password = HTTP::_GP('password', '', true);
                $mail = HTTP::_GP('email', '');
                // Get Salt.
                require 'includes/config.php';
                require 'includes/vars.php';

                $hash_password = PlayerUtil::cryptPassword($password);

                if (empty($username)
                    || empty($password)
                    || empty($mail))
                {
                    $template->assign([
                        'message'  => $LNG['step8_need_fields'],
                        'username' => $username,
                        'mail'     => $mail,
                    ]);
                    $template->show('ins_step8error.tpl');
                    exit;
                }

                list($user_id, $planet_id) = PlayerUtil::createPlayer(
                    Universe::current(),
                    $username,
                    $hash_password,
                    $mail,
                    $LNG->getLanguage(),
                    1,
                    1,
                    2,
                    null,
                    AUTH_ADM
                );

                $session = Session::create();
                $session->userId = $user_id;
                $session->adminAccess = 1;

                @unlink($path_install_file);
                $template->show('ins_step8.tpl');
                break;
        }
        break;
    default:
        $template->assign([
            'intro_text'    => $LNG['intro_text'],
            'intro_welcome' => $LNG['intro_welcome'],
            'intro_install' => $LNG['intro_install'],
        ]);
        $template->show('ins_intro.tpl');
        break;
}
