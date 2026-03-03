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

class ShowAjaxPage extends AbstractInstallPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
        $this->window = 'ajax';
        $this->initTemplate();
    }

    public function show(): void
    {
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
        $this->sendJSON('');
    }
}
