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

class ShowPlayerCardPage extends AbstractGamePage
{
    public static $require_module = MODULE_PLAYERCARD;

    protected $disable_eco_system = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $LNG;

        $this->setWindow('popup');
        $this->initTemplate();

        $db = Database::get();

        $uid = HTTP::_GP('id', 0);

        $sql = "SELECT
				u.username, u.galaxy, u.system, u.planet, u.wons, u.loos, u.draws, 
                u.kbmetal, u.kbcrystal, u.lostunits, u.desunits, u.ally_id,
				p.name,
				s.tech_rank, s.tech_points, s.build_rank, s.build_points, 
                s.defs_rank, s.defs_points, s.fleet_rank, s.fleet_points, 
                s.total_rank, s.total_points,
				a.ally_name
				FROM %%USERS%% u
				INNER JOIN %%PLANETS%% p ON p.id = u.id_planet
				LEFT JOIN %%USER_POINTS%% s ON s.id_owner = u.id 
				LEFT JOIN %%ALLIANCE%% a ON a.id = u.ally_id
				WHERE u.id = :uid AND u.universe = :universe;";

        $query = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':uid'      => $uid,
        ]);

        if (!$query)
        {
            // TODO : create new language key
            $this->printMessage('wrong user id');
            return;
        }

        $total_fights = $query['wons'] + $query['loos'] + $query['draws'];

        $win_percent = $lose_percent = $draw_percent = 0;

        if ($total_fights > 0)
        {
            $win_percent = ($query['wons'] / $total_fights) * 100;
            $lose_percent = ($query['loos'] / $total_fights) * 100;
            $draw_percent = ($query['draws'] / $total_fights) * 100;
        }

        $this->assign([
            'id'            => $uid,
            'yourid'        => $USER['id'],
            'name'          => $query['username'],
            'homeplanet'    => $query['name'],
            'galaxy'        => $query['galaxy'],
            'system'        => $query['system'],
            'planet'        => $query['planet'],
            'allyid'        => $query['ally_id'],
            'tech_rank'     => pretty_number($query['tech_rank']),
            'tech_points'   => pretty_number($query['tech_points']),
            'build_rank'    => pretty_number($query['build_rank']),
            'build_points'  => pretty_number($query['build_points']),
            'defs_rank'     => pretty_number($query['defs_rank']),
            'defs_points'   => pretty_number($query['defs_points']),
            'fleet_rank'    => pretty_number($query['fleet_rank']),
            'fleet_points'  => pretty_number($query['fleet_points']),
            'total_rank'    => pretty_number($query['total_rank']),
            'total_points'  => pretty_number($query['total_points']),
            'allyname'      => $query['ally_name'],
            'playerdestory' => sprintf($LNG['pl_destroy'], $query['username']),
            'wons'          => pretty_number($query['wons']),
            'loos'          => pretty_number($query['loos']),
            'draws'         => pretty_number($query['draws']),
            'kbmetal'       => pretty_number($query['kbmetal']),
            'kbcrystal'     => pretty_number($query['kbcrystal']),
            'lostunits'     => pretty_number($query['lostunits']),
            'desunits'      => pretty_number($query['desunits']),
            'totalfights'   => pretty_number($total_fights),
            'siegprozent'   => round($win_percent, 2),
            'loosprozent'   => round($lose_percent, 2),
            'drawsprozent'  => round($draw_percent, 2),
        ]);

        $this->display('page.playerCard.default.tpl');
    }
}
