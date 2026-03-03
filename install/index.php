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

$THEME = new Theme();
$THEME->setUserTheme('gow');

$LNG = new Language();
$LNG->getUserAgentLanguage();
$LNG->includeData(['L18N', 'INGAME', 'INSTALL', 'CUSTOM']);

$language = HTTP::_GP('lang', '');
if (!empty($language)
    && in_array($language, $LNG->getAllowedLangs()))
{
    setcookie('lang', $language);
}

// check if install related files exist

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

$page = HTTP::_GP('page', 'default');

if (!is_file($path_install_file))
{
    switch ($page)
    {
        case 'upgrade':
            $message = $LNG->getTemplate('locked_upgrade');
            break;
        default:
            $message = $LNG->getTemplate('locked_install');
            break;
    }
    $template = new template();
    $template->setCaching(false);
    $template->assign([
        'lang'       => $LNG->getLanguage(),
        'Selector'   => $LNG->getAllowedLangs(false),
        'title'      => $LNG['title_install'] . ' &bull; 2Moons',
        'header'     => $LNG['menu_install'],
        'canUpgrade' => file_exists('includes/config.php') && filesize('includes/config.php') !== 0,
    ]);
    $template->message($message, false, 0, true);
    exit;
}

// add
require 'includes/pages/install/AbstractInstallPage.class.php';
require 'includes/pages/install/ShowErrorPage.class.php';

$mode = HTTP::_GP('mode', 'show');
$page = str_replace(['_', '\\', '/', '.', "\0"], '', $page);
$page_class = 'Show'.ucwords($page).'Page';

$path = 'includes/pages/install/'.$page_class.'.class.php';
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
