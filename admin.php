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

define('MODE', 'ADMIN');
define('DATABASE_VERSION', 'OLD');

define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)).'/');
set_include_path(ROOT_PATH.'includes/libs/BBCodeParser2/'.':'.ROOT_PATH.':'.get_include_path());
require_once('includes/libs/BBCodeParser2/HTML/BBCodeParser2.php');

require 'includes/pages/adm/AbstractAdminPage.php';
require 'includes/pages/adm/ShowErrorPage.php';
require 'includes/common.php';
require 'includes/classes/class.Log.php';

if ($USER['authlevel'] == AUTH_USR)
{
    HTTP::redirectTo('game.php');
}

$uni = HTTP::_GP('uni', 0);

if ($USER['authlevel'] == AUTH_ADM && !empty($uni))
{
    Universe::setEmulated($uni);
}

$page = HTTP::_GP('page', 'overview');
$mode = HTTP::_GP('mode', 'show');

$page = str_replace(['_', '\\', '/', '.', "\0"], '', $page);
$pageClass = 'Show'.ucwords($page).'Page';

$path = 'includes/pages/adm/'.$pageClass.'.php';

$session = Session::create();

if ($session->adminAccess != 1)
{
    $path = 'includes/pages/adm/ShowLoginPage.php';
    $pageClass = "ShowLoginPage";
}

if (!file_exists($path))
{
    ShowErrorPage::printError($LNG['page_doesnt_exist']);
}

// Added Autoload in feature Versions
require $path;

$pageObj = new $pageClass();
$pageProps = get_class_vars(get_class($pageObj));

if (!is_callable([$pageObj, $mode]))
{
    if (!isset($pageProps['default_controller'])
        || !is_callable([$pageObj, $pageProps['default_controller']]))
    {
        ShowErrorPage::printError($LNG['page_doesnt_exist']);
    }
    $mode = $pageProps['default_controller'];
}

$pageObj->{$mode}();
