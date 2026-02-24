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
class ShowCollectMinesPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $config;

        $this->assign([
            'collect_mines_under_attack' => $config->collect_mines_under_attack,
            'collect_mine_time_minutes'  => $config->collect_mine_time_minutes,
        ]);

        $this->display('page.collect_mines.default.tpl');
    }

    public function saveSettings(): void
    {
        global $LNG, $config;

        $config_before = [
            'collect_mines_under_attack' => $config->collect_mines_under_attack,
            'collect_mine_time_minutes'  => $config->collect_mine_time_minutes,
        ];

        $collect_mines_under_attack = (HTTP::_GP('collect_mines_under_attack', 'off') == 'on') ? 1 : 0;
        $collect_mine_time_minutes = HTTP::_GP('collect_mine_time_minutes', 30);

        $config_after = [
            'collect_mines_under_attack' => $collect_mines_under_attack,
            'collect_mine_time_minutes'  => $collect_mine_time_minutes,
        ];

        foreach ($config_after as $key => $value)
        {
            $config->$key = $value;
        }

        $config->save();

        $log = new Log(3);
        $log->target = 1;
        $log->old = $config_before;
        $log->new = $config_after;
        $log->save();

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=collectMines&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirect_button);
    }
}
