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

class ShowInstallPage extends AbstractInstallPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display_step_1()
    {
        if (isset($_POST['post']))
        {
            if (isset($_POST['accept']))
            {
                HTTP::redirectTo('index.php?page=install&step=2');
            }
            else
            {
                $this->assign([
                    'accept' => false,
                ]);
            }
        }

        $this->display('ins_license.tpl');
    }

    public function display_step_2()
    {
        global $LNG;
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
						<a class="btn btn-primary  w-100 my-2 p-1" href="index.php?page=install&step=3">
							' . $LNG['continue'] . '
						</a>
					</td>
					</tr>';
        }
        else
        {
            $done = '';
        }

        $this->assign([
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

        $this->display('ins_req.tpl');
    }

    public function display_step_3()
    {
        $this->assign([
            'host'     => getenv('DB_HOST'),
            'user'     => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'dbname'   => getenv('DB_NAME'),
        ]);

        $this->display('ins_form.tpl');
    }

    public function display_step_4()
    {
        global $LNG;
        $host = HTTP::_GP('host', '');
        $port = HTTP::_GP('port', 3306);
        $user = HTTP::_GP('user', '', true);
        $userpw = HTTP::_GP('passwort', '', true);
        $dbname = HTTP::_GP('dbname', '', true);
        $prefix = HTTP::_GP('prefix', 'uni1_');

        $this->assign([
            'host'   => $host,
            'port'   => $port,
            'user'   => $user,
            'dbname' => $dbname,
            'prefix' => $prefix,
        ]);

        if (empty($dbname))
        {

            $this->assign([
                'class'   => 'fatalerror',
                'message' => $LNG['step2_db_no_dbname'], ]);

            $this->display('ins_step4.tpl');
            exit;
        }

        if (strlen($prefix) > 36)
        {

            $this->assign([
                'class'   => 'fatalerror',
                'message' => $LNG['step2_db_too_long'],
            ]);

            $this->display('ins_step4.tpl');
            exit;
        }

        if (strspn($prefix, '-./\\') !== 0)
        {

            $this->assign([
                'class'   => 'fatalerror',
                'message' => $LNG['step2_prefix_invalid'],
            ]);

            $this->display('ins_step4.tpl');

            exit;
        }

        if (preg_match('!^[0-9]!', $prefix) !== 0)
        {

            $this->assign([
                'class'   => 'fatalerror',
                'message' => $LNG['step2_prefix_invalid'],
            ]);

            $this->display('ins_step4.tpl');
            exit;
        }

        if (is_file(ROOT_PATH . "includes/config.php")
            && filesize(ROOT_PATH . "includes/config.php") != 0)
        {

            $this->assign([
                'class'   => 'fatalerror',
                'message' => $LNG['step2_config_exists'],
            ]);

            $this->display('ins_step4.tpl');

            exit;
        }
        @touch(ROOT_PATH . "includes/config.php");
        if (!is_writable(ROOT_PATH . "includes/config.php"))
        {

            $this->assign([
                'class'   => 'fatalerror',
                'message' => $LNG['step2_conf_op_fail'],
            ]);

            $this->display('ins_step4.tpl');
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

            $this->assign([
                'class'   => 'fatalerror',
                'message' => $LNG['step2_db_con_fail'] . '</p><p>' . $e->getMessage(),
            ]);

            $this->display('ins_step4.tpl');

            unlink(ROOT_PATH . 'includes/config.php');
            exit;
        }
        @touch(ROOT_PATH . "includes/error.log");

        $this->assign([
            'class'   => 'noerror',
            'message' => $LNG['step2_db_done'],
        ]);

        $this->display('ins_step4.tpl');
    }

    public function display_step_5()
    {
        $this->display('ins_step5.tpl');
    }

    public function display_step_6()
    {
        global $LNG;
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
            $config->announcement_text = $LNG['sql_welcome'] . $install_version;
            $config->uni_name = $LNG['fcm_universe'] . ' ' . Universe::current();
            $config->close_reason = $LNG['sql_close_reason'];
            $config->moduls = implode(';', array_fill(0, MODULE_AMOUNT - 1, 1));

            unset($install_sql, $install_revision, $install_version);

            $config->save();

            HTTP::redirectTo('index.php?page=install&step=7');
        }
        catch (Exception $e)
        {
            require 'includes/config.php';
            @unlink('includes/config.php');
            $error = $e->getMessage();
            $this->assign([
                'host'    => $database['host'],
                'port'    => $database['port'],
                'user'    => $database['user'],
                'dbname'  => $database['databasename'],
                'prefix'  => $database['tableprefix'],
                'class'   => 'fatalerror',
                'message' => $LNG['step3_db_error'] . '</p><p>' . $error,
            ]);
            $this->display('ins_step4.tpl');
            exit;
        }
    }

    public function display_step_7()
    {
        $this->assign([
            'name'     => getenv('ADMIN_NAME'),
            'password' => getenv('ADMIN_PASSWORD'),
            'mail'     => getenv('ADMIN_MAIL'),
        ]);

        $this->display('ins_acc.tpl');
    }

    public function display_step_8()
    {
        global $LNG;
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
        $this->display('ins_step8.tpl');
    }

    public function show(): void
    {
        $step = HTTP::_GP('step', 0);
        switch ($step)
        {
            case 1:
                $this->display_step_1();
                break;
            case 2:
                $this->display_step_2();
                break;
            case 3:
                $this->display_step_3();
                break;
            case 4:
                $this->display_step_4();
                exit;
                break;
            case 5:
                $this->display_step_5();
                break;
            case 6:
                $this->display_step_6();
                break;
            case 7:
                $this->display_step_7();
                break;
            case 8:
                $this->display_step_8();
                break;
        }
    }
}
