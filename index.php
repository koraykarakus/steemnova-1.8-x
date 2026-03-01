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

define('MODE', 'LOGIN');
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)).'/');
set_include_path(ROOT_PATH);

require 'includes/pages/login/AbstractLoginPage.class.php';
require 'includes/pages/login/ShowErrorPage.class.php';
require 'includes/common.php';
require_once 'includes/classes/LoginService.class.php';
/** @var $LNG Language */

$page = HTTP::_GP('page', 'index');
$mode = HTTP::_GP('mode', 'show');
$page = str_replace(['_', '\\', '/', '.', "\0"], '', $page);
$page_class = 'Show'.ucfirst($page).'Page';


$path = 'includes/pages/login/'.$page_class.'.class.php';

if (!file_exists($path))
{
    ShowErrorPage::printError($LNG['page_doesnt_exist']);
}

// Added Autoload in feature Versions
require($path);

$page_obj = new $page_class();
// PHP 5.2 FIX
// can't use $pageObj::$require_module
$page_props = get_class_vars(get_class($page_obj));

if (isset($page_props['require_module'])
    && $page_props['require_module'] !== 0
    && !isModuleAvailable($page_props['require_module']))
{
    ShowErrorPage::printError($LNG['sys_module_inactive']);
}

if (!is_callable([$page_obj, $mode]))
{
    if (!isset($page_props['defaultController'])
        || !is_callable([$page_obj, $page_props['defaultController']]))
    {
        ShowErrorPage::printError($LNG['page_doesnt_exist']);
    }
    $mode = $page_props['defaultController'];
}

$page_obj->{$mode}();
