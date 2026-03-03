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

define('MODE', 'REPORT');
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)).'/');

require 'includes/pages/report/AbstractReportPage.class.php';
require 'includes/pages/report/ShowErrorPage.class.php';
require 'includes/common.php';

$page = HTTP::_GP('page', 'report');
$mode = HTTP::_GP('mode', 'show');
$page = str_replace(['_', '\\', '/', '.', "\0"], '', $page);
$page_class = 'Show'.ucwords($page).'Page';

$path = 'includes/pages/report/'.$page_class.'.class.php';

if (!file_exists($path))
{
    ShowErrorPage::printError($LNG['page_doesnt_exist']);
}

require $path;

$page_obj = new $page_class();

$page_props = get_class_vars(get_class($page_obj));

if (!is_callable([$page_obj, $mode]))
{
    if (!isset($page_props['default_controller'])
        || !is_callable([$page_obj, $page_props['default_controller']]))
    {
        ShowErrorPage::printError($LNG['page_doesnt_exist']);
    }
    $mode = $page_props['default_controller'];
}

$page_obj->{$mode}();
