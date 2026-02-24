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
class ShowStatsPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;

        $config = Config::get(Universe::getEmulated());

        $this->assign([
            'stat_level'                  => $config->stat_level,
            'stat'                        => $config->stat,
            'stat_settings'               => $config->stat_settings,
            'cs_access_lvl'               => $LNG['cs_access_lvl'],
            'cs_points_to_zero'           => $LNG['cs_points_to_zero'],
            'cs_point_per_resources_used' => $LNG['cs_point_per_resources_used'],
            'cs_title'                    => $LNG['cs_title'],
            'cs_resources'                => $LNG['cs_resources'],
            'cs_save_changes'             => $LNG['cs_save_changes'],
            'Selector'                    => [1 => $LNG['cs_yes'], 2 => $LNG['cs_no_view'], 0 => $LNG['cs_no']],
        ]);

        $this->display('page.stats.default.tpl');

    }

    public function saveSettings(): void
    {
        global $LNG;
        $config = Config::get(Universe::getEmulated());

        $config_before = [
            'stat_settings' => $config->stat_settings,
            'stat'          => $config->stat,
            'stat_level'    => $config->stat_level,
        ];

        $stat_settings = HTTP::_GP('stat_settings', 0);
        $stat = HTTP::_GP('stat', 0);
        $stat_level = HTTP::_GP('stat_level', 0);

        $config_after = [
            'stat_settings' => $stat_settings,
            'stat'          => $stat,
            'stat_level'    => $stat_level,
        ];

        foreach ($config_after as $key => $value)
        {
            $config->$key = $value;
        }
        $config->save();

        $log = new Log(3);
        $log->target = 2;
        $log->old = $config_before;
        $log->new = $config_after;
        $log->save();

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=stats&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirect_button);

    }

}

function ShowStatsPage()
{
    global $LNG;

    $config = Config::get(Universe::getEmulated());

    $template = new template();

    $template->assign_vars([
        'stat_level'                  => $config->stat_level,
        'stat'                        => $config->stat,
        'stat_settings'               => $config->stat_settings,
        'cs_access_lvl'               => $LNG['cs_access_lvl'],
        'cs_points_to_zero'           => $LNG['cs_points_to_zero'],
        'cs_point_per_resources_used' => $LNG['cs_point_per_resources_used'],
        'cs_title'                    => $LNG['cs_title'],
        'cs_resources'                => $LNG['cs_resources'],
        'cs_save_changes'             => $LNG['cs_save_changes'],
        'Selector'                    => [1 => $LNG['cs_yes'], 2 => $LNG['cs_no_view'], 0 => $LNG['cs_no']],
    ]);

    $template->show('StatsPage.tpl');
}
