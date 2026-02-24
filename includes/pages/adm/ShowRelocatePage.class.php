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
class ShowRelocatePage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {

        global $config;

        $this->assign([
            'relocate_price'               => $config->relocate_price,
            'relocate_next_time'           => $config->relocate_next_time,
            'relocate_jump_gate_active'    => $config->relocate_jump_gate_active,
            'relocate_move_fleet_directly' => $config->relocate_move_fleet_directly,
        ]);

        $this->display('page.relocate.default.tpl');

    }

    public function saveSettings(): void
    {
        global $LNG, $config;

        $config_before = [
            'relocate_price'               => $config->relocate_price,
            'relocate_next_time'           => $config->relocate_next_time,
            'relocate_jump_gate_active'    => $config->relocate_jump_gate_active,
            'relocate_move_fleet_directly' => $config->relocate_move_fleet_directly,
        ];

        $relocate_price = HTTP::_GP('relocate_price', 50000);
        $relocate_next_time = HTTP::_GP('relocate_next_time', 24);
        $relocate_jump_gate_active = HTTP::_GP('relocate_jump_gate_active', 24);
        $relocate_move_fleet_directly = (HTTP::_GP('relocate_move_fleet_directly', 'off') == 'on') ? 1 : 0;

        $config_after = [
            'relocate_price'               => $relocate_price,
            'relocate_next_time'           => $relocate_next_time,
            'relocate_jump_gate_active'    => $relocate_jump_gate_active,
            'relocate_move_fleet_directly' => $relocate_move_fleet_directly,
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
            'url'   => 'admin.php?page=relocate&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirect_button);

    }

}
