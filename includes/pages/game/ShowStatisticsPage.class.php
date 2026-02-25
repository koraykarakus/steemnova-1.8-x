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

class ShowStatisticsPage extends AbstractGamePage
{
    public static $requireModule = MODULE_STATISTICS;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $LNG;

        $who = HTTP::_GP('who', 1);
        $type = HTTP::_GP('type', 1);
        $range = HTTP::_GP('range', 1);

        switch ($type)
        {
            case 2:
                $order = "fleet_rank";
                $points = "fleet_points";
                $rank = "fleet_rank";
                $old_rank = "fleet_old_rank";
                break;
            case 3:
                $order = "tech_rank";
                $points = "tech_points";
                $rank = "tech_rank";
                $old_rank = "tech_old_rank";
                break;
            case 4:
                $order = "build_rank";
                $points = "build_points";
                $rank = "build_rank";
                $old_rank = "build_old_rank";
                break;
            case 5:
                $order = "defs_rank";
                $points = "defs_points";
                $rank = "defs_rank";
                $old_rank = "defs_old_rank";
                break;
            default:
                $order = "total_rank";
                $points = "total_points";
                $rank = "total_rank";
                $old_rank = "total_old_rank";
                break;
        }

        $range_list = [];

        $db = Database::get();
        $config = Config::get();

        switch ($who)
        {
            case 1:
                $max_users = $config->users_amount;
                $range = min($range, $max_users);
                $last_page = max(1, ceil($max_users / 100));

                for ($page = 0; $page < $last_page; $page++)
                {
                    $page_value = ($page * 100) + 1;
                    $page_range = $page_value + 99;
                    $selector['range'][$page_value] = $page_value . "-" . $page_range;
                }

                $start = max(floor(($range - 1) / 100) * 100, 0);

                if ($config->stat == 2)
                {
                    $sql = "SELECT DISTINCT s.*, u.id, u.username, u.ally_id, u.banaday, u.urlaubs_modus, u.onlinetime, a.ally_name, (a.ally_owner=u.id) as is_leader, a.ally_owner_range FROM %%USER_POINTS%% as s
					INNER JOIN %%USERS%% as u ON u.id = s.id_owner
					LEFT JOIN %%ALLIANCE%% as a ON a.id = s.id_ally
					WHERE s.universe = :universe  AND u.authlevel < :authLevel
					ORDER BY " . $order . " ASC LIMIT :offset, :limit;";
                    $query = $db->select($sql, [
                        ':universe'  => Universe::current(),
                        ':authLevel' => $config->stat_level,
                        ':offset'    => $start,
                        ':limit'     => 100,
                    ]);
                }
                else
                {

                    $sql = "SELECT DISTINCT s.*, u.id, u.username, u.ally_id, u.banaday, u.urlaubs_modus, u.onlinetime, a.ally_name, (a.ally_owner=u.id) as is_leader, a.ally_owner_range
                    FROM %%USER_POINTS%% as s
          					INNER JOIN %%USERS%% as u ON u.id = s.id_owner
          					LEFT JOIN %%ALLIANCE%% as a ON a.id = s.id_ally
          					WHERE s.universe = :universe
          					ORDER BY " . $order . " ASC LIMIT :offset, :limit;";

                    $query = $db->select($sql, [
                        ':universe' => Universe::current(),
                        ':offset'   => $start,
                        ':limit'    => 100,
                    ]);

                }

                $range_list = [];

                $total_points_user = $db->selectSingle('SELECT total_points FROM %%USER_POINTS%% WHERE id_owner = :userId;', [
                    ':userId' => $USER['id'],
                ]);

                if (!$total_points_user)
                {
                    $total_points_user = ['total_points' => 0];
                }

                $USER = array_merge($USER, $total_points_user);

                foreach ($query as $c_stat)
                {
                    $is_noob_protec = CheckNoobProtec($USER, $c_stat, $c_stat);
                    $class = userStatus($c_stat, $is_noob_protec);

                    $range_list[] = [
                        'id'               => $c_stat['id'],
                        'name'             => $c_stat['username'],
                        'class'            => $class,
                        'is_leader'        => $c_stat['is_leader'],
                        'ally_owner_range' => $c_stat['ally_owner_range'],
                        'points'           => pretty_number($c_stat[$points]),
                        'allyid'           => $c_stat['ally_id'],
                        'rank'             => $c_stat[$rank],
                        'allyname'         => $c_stat['ally_name'],
                        'ranking'          => $c_stat[$old_rank] - $c_stat[$rank],
                    ];
                }

                break;
            case 2:
                $sql = "SELECT COUNT(*) as state FROM %%ALLIANCE%% WHERE `ally_universe` = :universe;";
                $max_allys = $db->selectSingle($sql, [
                    ':universe' => Universe::current(),
                ], 'state');

                $range = min($range, $max_allys);
                $last_page = max(1, ceil($max_allys / 100));

                for ($page = 0; $page < $last_page; $page++)
                {
                    $page_value = ($page * 100) + 1;
                    $page_range = $page_value + 99;
                    $selector['range'][$page_value] = $page_value . "-" . $page_range;
                }

                $start = max(floor(($range - 1) / 100) * 100, 0);

                $sql = 'SELECT DISTINCT s.*, a.id, a.ally_members, a.ally_name 
                FROM %%ALLIANCE_POINTS%% as s
                INNER JOIN %%ALLIANCE%% as a ON a.id = s.id_owner
                WHERE universe = :universe
                ORDER BY ' . $order . ' ASC LIMIT :offset, :limit;';

                $query = $db->select($sql, [
                    ':universe' => Universe::current(),
                    ':offset'   => $start,
                    ':limit'    => 100,
                ]);

                foreach ($query as $c_stat)
                {
                    $range_list[] = [
                        'id'       => $c_stat['id'],
                        'name'     => $c_stat['ally_name'],
                        'members'  => $c_stat['ally_members'],
                        'rank'     => $c_stat[$rank],
                        'mppoints' => pretty_number(floor($c_stat[$points] / $c_stat['ally_members'])),
                        'points'   => pretty_number($c_stat[$points]),
                        'ranking'  => $c_stat[$old_rank] - $c_stat[$rank],
                    ];
                }

                break;
        }

        $selector['who'] = [1 => $LNG['st_player'], 2 => $LNG['st_alliance']];
        $selector['type'] = [1 => $LNG['st_points'], 2 => $LNG['st_fleets'], 3 => $LNG['st_researh'], 4 => $LNG['st_buildings'], 5 => $LNG['st_defenses']];

        require_once 'includes/classes/Cronjob.class.php';

        $this->assign([
            'Selectors'  => $selector,
            'who'        => $who,
            'type'       => $type,
            'range'      => floor(($range - 1) / 100) * 100 + 1,
            'RangeList'  => $range_list,
            'CUser_ally' => $USER['ally_id'],
            'CUser_id'   => $USER['id'],
            'stat_date'  => _date(
                $LNG['php_tdformat'],
                Cronjob::getLastExecutionTime('statistic'),
                $USER['timezone']
            ),
            'ShortStatus' => [
                'vacation'     => $LNG['gl_short_vacation'],
                'banned'       => $LNG['gl_short_ban'],
                'inactive'     => $LNG['gl_short_inactive'],
                'longinactive' => $LNG['gl_short_long_inactive'],
                'noob'         => $LNG['gl_short_newbie'],
                'strong'       => $LNG['gl_short_strong'],
                'enemy'        => $LNG['gl_short_enemy'],
                'friend'       => $LNG['gl_short_friend'],
                'member'       => $LNG['gl_short_member'],
            ],
        ]);

        $this->display('page.statistics.default.tpl');
    }
}
