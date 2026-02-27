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

class ShowBattleHallPage extends AbstractLoginPage
{
    public static $require_module = MODULE_LOGIN_BATTLEHALL;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;
        $db = Database::get();

        $sql = "SELECT *, (
			SELECT DISTINCT
			IF(%%TOPKB_USERS%%.username = '', GROUP_CONCAT(%%USERS%%.username SEPARATOR ' & '), GROUP_CONCAT(%%TOPKB_USERS%%.username SEPARATOR ' & '))
			FROM %%TOPKB_USERS%%
			LEFT JOIN %%USERS%% ON uid = %%USERS%%.id
			WHERE %%TOPKB_USERS%%.`rid` = %%TOPKB%%.`rid` AND `role` = 1
		) as `attacker`,
		(
			SELECT DISTINCT
			IF(%%TOPKB_USERS%%.username = '', GROUP_CONCAT(%%USERS%%.username SEPARATOR ' & '), GROUP_CONCAT(%%TOPKB_USERS%%.username SEPARATOR ' & '))
			FROM %%TOPKB_USERS%% INNER JOIN %%USERS%% ON uid = id
			WHERE %%TOPKB_USERS%%.`rid` = %%TOPKB%%.`rid` AND `role` = 2
		) as `defender`
		FROM %%TOPKB%% WHERE `universe` = :universe ORDER BY units DESC LIMIT 100;";

        $hall_res = $db->select($sql, [
            ':universe' => Universe::current(),
        ]);

        $hall_list = [];
        foreach ($hall_res as $c_hall)
        {
            $hall_list[] = [
                'result'   => $c_hall['result'],
                'time'     => _date($LNG['php_tdformat'], $c_hall['time']),
                'units'    => $c_hall['units'],
                'rid'      => $c_hall['rid'],
                'attacker' => $c_hall['attacker'],
                'defender' => $c_hall['defender'],
            ];
        }

        $universe_select = $this->getUniverseSelector();

        $this->assign([
            'universe_select' => $universe_select,
            'hall_list'       => $hall_list,
        ]);

        $this->display('page.battleHall.default.tpl');
    }
}
