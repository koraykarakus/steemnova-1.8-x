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
$pageClass = 'Show'.ucwords($page).'Page';

$path = 'includes/pages/report/'.$pageClass.'.class.php';

if (!file_exists($path))
{
    ShowErrorPage::printError($LNG['page_doesnt_exist']);
}

require $path;

$pageObj = new $pageClass();

$pageProps = get_class_vars(get_class($pageObj));

if (!is_callable([$pageObj, $mode]))
{
    if (!isset($pageProps['defaultController'])
        || !is_callable([$pageObj, $pageProps['defaultController']]))
    {
        ShowErrorPage::printError($LNG['page_doesnt_exist']);
    }
    $mode = $pageProps['defaultController'];
}

$pageObj->{$mode}();
