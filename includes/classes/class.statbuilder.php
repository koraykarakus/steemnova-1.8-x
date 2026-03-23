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

class statbuilder
{
    protected $starttime;
    protected $memory;
    protected $time;
    protected $recordData;
    protected $Unis;

    public function __construct()
    {
        $this->starttime = microtime(true);
        $this->memory = [round(memory_get_usage() / 1024, 1), round(memory_get_usage(1) / 1024, 1)];
        $this->time = TIMESTAMP;

        $this->recordData = [];
        $this->Unis = [];

        $sql = "SELECT uni FROM %%CONFIG%% ORDER BY uni ASC;";
        $uni_result = Database::get()->select($sql, []);
        foreach ($uni_result as $uni)
        {
            $this->Unis[] = $uni['uni'];
        }
    }

    private function SomeStatsInfos()
    {
        return [
            'stats_time'     => $this->time,
            'totaltime'      => round(microtime(true) - $this->starttime, 7),
            'memory_peak'    => [round(memory_get_peak_usage() / 1024, 1), round(memory_get_peak_usage(1) / 1024, 1)],
            'initial_memory' => $this->memory,
            'end_memory'     => [round(memory_get_usage() / 1024, 1), round(memory_get_usage(1) / 1024, 1)],
            'sql_count'      => Database::get()->getQueryCounter(),
        ];
    }

    private function CheckUniverseAccounts($uni_data)
    {
        $uni_data = $uni_data + array_combine($this->Unis, array_fill(1, count($this->Unis), 0));
        foreach ($uni_data as $uni => $amount)
        {
            $config = Config::get($uni);
            $config->users_amount = $amount;
            $config->save();
        }
    }

    private function GetUsersInfosFromDB()
    {
        global $RESOURCE, $RESLIST;
        $select_defenses = $select_buildings = $selected_tech = $select_fleets = $select_officers = '';

        foreach ($RESLIST['build'] as $building)
        {
            $select_buildings .= " p.".$RESOURCE[$building].",";
        }

        foreach ($RESLIST['tech'] as $techno)
        {
            $selected_tech .= " u.".$RESOURCE[$techno].",";
        }

        foreach ($RESLIST['fleet'] as $fleet)
        {
            $select_fleets .= " SUM(p.".$RESOURCE[$fleet].") as ".$RESOURCE[$fleet].",";
        }

        foreach ($RESLIST['defense'] as $defense)
        {
            $select_defenses .= " SUM(p.".$RESOURCE[$defense].") as ".$RESOURCE[$defense].",";
        }

        foreach ($RESLIST['missile'] as $defense)
        {
            $select_defenses .= " SUM(p.".$RESOURCE[$defense].") as ".$RESOURCE[$defense].",";
        }

        foreach ($RESLIST['officers'] as $officer)
        {
            $select_officers .= " u.".$RESOURCE[$officer].",";
        }

        $db = Database::get();

        $flying_fleets = [];

        $sql_fleets = $db->select('SELECT fleet_array, fleet_owner FROM %%FLEETS%%;');

        foreach ($sql_fleets as $c_fleets)
        {
            $fleet_rec = explode(";", $c_fleets['fleet_array']);

            if (!is_array($fleet_rec))
            {
                continue;
            }

            foreach ($fleet_rec as $group)
            {
                if (empty($group))
                {
                    continue;
                }

                $ship = explode(",", $group);
                if (!isset($flying_fleets[$c_fleets['fleet_owner']][$ship[0]]))
                {
                    $flying_fleets[$c_fleets['fleet_owner']][$ship[0]] = $ship[1];
                }
                else
                {
                    $flying_fleets[$c_fleets['fleet_owner']][$ship[0]] += $ship[1];
                }
            }
        }

        $return['Fleets'] = $flying_fleets;
        $return['Planets'] = $db->select('SELECT SQL_BIG_RESULT DISTINCT '.$select_buildings.' p.id, p.universe, p.id_owner, u.authlevel, u.bana, u.username FROM %%PLANETS%% as p LEFT JOIN %%USERS%% as u ON u.id = p.id_owner;');
        $return['Users'] = $db->select('SELECT SQL_BIG_RESULT DISTINCT '.$selected_tech.$select_fleets.$select_defenses.$select_officers.' u.id, u.ally_id, u.authlevel, u.bana, u.universe, u.username, s.tech_rank AS old_tech_rank, s.build_rank AS old_build_rank, s.defs_rank AS old_defs_rank, s.fleet_rank AS old_fleet_rank, s.total_rank AS old_total_rank FROM %%USERS%% as u LEFT JOIN %%USER_POINTS%% as s ON s.id_owner = u.id LEFT JOIN %%PLANETS%% as p ON u.id = p.id_owner GROUP BY s.id_owner, u.id, u.authlevel;');
        $return['Alliance'] = $db->select('SELECT SQL_BIG_RESULT DISTINCT a.id, a.ally_universe, s.tech_rank AS old_tech_rank, s.build_rank AS old_build_rank, s.defs_rank AS old_defs_rank, s.fleet_rank AS old_fleet_rank, s.total_rank AS old_total_rank FROM %%ALLIANCE%% as a LEFT JOIN %%ALLIANCE_POINTS%% as s ON s.id_owner = a.id GROUP BY a.id;');

        return $return;
    }

    private function setRecords($user_id, $element_id, $amount)
    {
        $this->recordData[$element_id][$amount][] = $user_id;
    }

    private function writeRecordData()
    {
        $query_data = [];
        foreach ($this->recordData as $element_id => $element_array)
        {
            krsort($element_array, SORT_NUMERIC);
            $user_winner = reset($element_array);
            $maxAmount = key($element_array);
            $user_winner = array_unique($user_winner);

            if (count($user_winner) > 3)
            {
                $keys = array_rand($user_winner, 3);

                foreach ($keys as $key)
                {
                    $query_data[] = "(".$user_winner[$key].",".$element_id.",".$maxAmount.")";
                }
            }
            else
            {
                foreach ($user_winner as $user_id)
                {
                    $query_data[] = "(".$user_id.",".$element_id.",".$maxAmount.")";
                }
            }
        }

        if (!empty($query_data))
        {
            $sql = "TRUNCATE TABLE %%RECORDS%%;";
            $sql .= "INSERT INTO %%RECORDS%% (userID, elementID, level) VALUES ".implode(', ', $query_data).";";
            $this->SaveDataIntoDB($sql);
        }
    }

    private function SaveDataIntoDB($data)
    {
        $queries = explode(';', $data);
        $queries = array_filter($queries);
        foreach ($queries as $query)
        {
            Database::get()->nativeQuery($query);
        }
    }

    private function GetTechnoPoints($user)
    {
        global $RESOURCE, $RESLIST, $PRICELIST;
        $tech_counts = 0;
        $tech_points = 0;

        foreach ($RESLIST['tech'] as $techno)
        {
            if ($user[$RESOURCE[$techno]] == 0)
            {
                continue;
            }

            $base_cost = $PRICELIST[$techno]['cost'][901] +
            $PRICELIST[$techno]['cost'][902] +
            $PRICELIST[$techno]['cost'][903];
            $level = $user[$RESOURCE[$techno]];
            $factor = $PRICELIST[$techno]['factor'];
            if ($factor == 1)
            {
                // if factor is 1 normal multiply
                $tech_points += $base_cost * $level;
            }
            else
            {
                // geometric series formula
                $tech_points += $base_cost * ((pow($factor, $level) - 1) / ($factor - 1));
            }

            $tech_counts += $user[$RESOURCE[$techno]];

            $this->setRecords($user['id'], $techno, $user[$RESOURCE[$techno]]);
        }

        return ['count' => $tech_counts, 'points' => ($tech_points / Config::get()->stat_settings)];
    }

    private function GetBuildPoints($planet)
    {
        global $RESOURCE, $RESLIST, $PRICELIST;
        $build_counts = 0;
        $build_points = 0;

        foreach ($RESLIST['build'] as $Build)
        {
            if ($planet[$RESOURCE[$Build]] == 0)
            {
                continue;
            }

            $base_cost = $PRICELIST[$Build]['cost'][901] +
            $PRICELIST[$Build]['cost'][902] +
            $PRICELIST[$Build]['cost'][903];

            $level = $planet[$RESOURCE[$Build]];
            $factor = $PRICELIST[$Build]['factor'];

            if ($factor == 1)
            {
                // if factor is 1 normal multiply
                $build_points += $base_cost * $level;
            }
            else
            {
                // geometric series formula
                $build_points += $base_cost * ((pow($factor, $level) - 1) / ($factor - 1));
            }

            $build_counts += $planet[$RESOURCE[$Build]];

            $this->setRecords($planet['id_owner'], $Build, $planet[$RESOURCE[$Build]]);
        }
        return ['count' => $build_counts, 'points' => ($build_points / Config::get()->stat_settings)];
    }

    private function GetDefensePoints($user)
    {
        global $RESOURCE, $RESLIST, $PRICELIST;
        $defense_counts = 0;
        $defense_points = 0;

        foreach (array_merge($RESLIST['defense'], $RESLIST['missile']) as $defense)
        {
            if ($user[$RESOURCE[$defense]] == 0)
            {
                continue;
            }

            $units = $PRICELIST[$defense]['cost'][901] + $PRICELIST[$defense]['cost'][902] + $PRICELIST[$defense]['cost'][903];
            $defense_points += $units * $user[$RESOURCE[$defense]];
            $defense_counts += $user[$RESOURCE[$defense]];

            $this->setRecords($user['id'], $defense, $user[$RESOURCE[$defense]]);
        }

        return ['count' => $defense_counts, 'points' => ($defense_points / Config::get()->stat_settings)];
    }

    private function GetFleetPoints($user)
    {
        global $RESOURCE, $RESLIST, $PRICELIST;
        $fleet_counts = 0;
        $fleet_points = 0;

        foreach ($RESLIST['fleet'] as $Fleet)
        {
            if ($user[$RESOURCE[$Fleet]] == 0)
            {
                continue;
            }

            $Units = $PRICELIST[$Fleet]['cost'][901] +
            $PRICELIST[$Fleet]['cost'][902] + $PRICELIST[$Fleet]['cost'][903];

            $fleet_points += $Units * $user[$RESOURCE[$Fleet]];
            $fleet_counts += $user[$RESOURCE[$Fleet]];

            $this->setRecords($user['id'], $Fleet, $user[$RESOURCE[$Fleet]]);
        }

        return ['count' => $fleet_counts, 'points' => ($fleet_points / Config::get()->stat_settings)];
    }

    private function GetOfficerPoints($user)
    {
        global $RESOURCE, $RESLIST;

        foreach ($RESLIST['officers'] as $officer)
        {
            if ($user[$RESOURCE[$officer]] == 0)
            {
                continue;
            }

            $this->setRecords($user['id'], $officer, $user[$RESOURCE[$officer]]);
        }
    }

    private function SetNewRanks()
    {
        foreach ($this->Unis as $uni)
        {
            foreach (['tech', 'build', 'defs', 'fleet', 'total'] as $type)
            {
                Database::get()->nativeQuery('SELECT @i := 0;');

                $sql = 'UPDATE %%USER_POINTS%% SET '.$type.'_rank = (SELECT @i := @i + 1)
				WHERE universe = :uni
				ORDER BY '.$type.'_points DESC, id_owner ASC;';

                Database::get()->update($sql, [
                    ':uni' => $uni,
                ]);

                Database::get()->nativeQuery('SELECT @i := 0;');

                Database::get()->update($sql, [
                    ':uni' => $uni,
                ]);
            }
        }
    }

    final public function MakeStats()
    {
        global $RESOURCE;
        $ally_points = $user_points = [];
        $total_data = $this->GetUsersInfosFromDB();

        $final_sql = $save_sql = "INSERT INTO %%USER_POINTS%% 
        (id_owner, id_ally, universe, tech_old_rank, tech_points, 
        tech_count, build_old_rank, build_points, build_count, defs_old_rank, 
        defs_points, defs_count, fleet_old_rank, fleet_points, fleet_count, 
        total_old_rank, total_points, total_count) VALUES ";

        $sql_end = " ON DUPLICATE KEY UPDATE
		id_owner = VALUES(id_owner),
		id_ally = VALUES(id_ally),
		universe = VALUES(universe),
		tech_old_rank = VALUES(tech_old_rank),
		tech_points = VALUES(tech_points),
		tech_count = VALUES(tech_count),
		build_old_rank = VALUES(build_old_rank),
		build_points = VALUES(build_points),
		build_count = VALUES(build_count),
		defs_old_rank = VALUES(defs_old_rank),
		defs_points = VALUES(defs_points),
		defs_count = VALUES(defs_count),
		fleet_old_rank = VALUES(fleet_old_rank),
		fleet_points = VALUES(fleet_points),
		fleet_count = VALUES(fleet_count),
		total_old_rank = VALUES(total_old_rank),
		total_points = VALUES(total_points),
		total_count = VALUES(total_count);";

        foreach ($total_data['Planets'] as $planet_data)
        {
            if ((in_array(Config::get()->stat, [1, 2])
                && $planet_data['authlevel'] >= Config::get()->stat_level)
                || !empty($planet_data['bana']))
            {
                continue;
            }

            if (!isset($user_points[$planet_data['id_owner']]))
            {
                $user_points[$planet_data['id_owner']]['build']['count'] = $user_points[$planet_data['id_owner']]['build']['points'] = 0;
            }

            $build_points = $this->GetBuildPoints($planet_data);
            $user_points[$planet_data['id_owner']]['build']['count'] += $build_points['count'];
            $user_points[$planet_data['id_owner']]['build']['points'] += $build_points['points'];
        }

        $uni_data = [];

        $i = 0;
        foreach ($total_data['Users'] as $user_data)
        {
            $i++;
            if (!isset($uni_data[$user_data['universe']]))
            {
                $uni_data[$user_data['universe']] = 0;
            }

            $uni_data[$user_data['universe']]++;

            if ((in_array(Config::get()->stat, [1, 2])
                && $user_data['authlevel'] >= Config::get()->stat_level)
                || !empty($user_data['bana']))
            {
                $final_sql .= "(".$user_data['id'].",".$user_data['ally_id'].",".$user_data['universe'].",0,0,0,0,0,0,0,0,0,0,0,0,0,0,0), ";
                continue;
            }

            if (isset($total_data['Fleets'][$user_data['id']]))
            {
                foreach ($total_data['Fleets'][$user_data['id']] as $ID => $Amount)
                {
                    $user_data[$RESOURCE[$ID]] += $Amount;
                }
            }

            $TechnoPoints = $this->GetTechnoPoints($user_data);
            $FleetPoints = $this->GetFleetPoints($user_data);
            $DefensePoints = $this->GetDefensePoints($user_data);
            $this->GetOfficerPoints($user_data);

            $user_points[$user_data['id']]['fleet']['count'] = $FleetPoints['count'];
            $user_points[$user_data['id']]['fleet']['points'] = $FleetPoints['points'];
            $user_points[$user_data['id']]['defense']['count'] = $DefensePoints['count'];
            $user_points[$user_data['id']]['defense']['points'] = $DefensePoints['points'];
            $user_points[$user_data['id']]['techno']['count'] = $TechnoPoints['count'];
            $user_points[$user_data['id']]['techno']['points'] = $TechnoPoints['points'];

            if (!isset($user_points[$user_data['id']]['build'])) //user don't have any planets ( user id changed manually)
            {
                continue;
            }

            $user_points[$user_data['id']]['total']['count'] = $user_points[$user_data['id']]['techno']['count']
                                                                + $user_points[$user_data['id']]['build']['count']
                                                                + $user_points[$user_data['id']]['defense']['count']
                                                                + $user_points[$user_data['id']]['fleet']['count'];

            $user_points[$user_data['id']]['total']['points'] = $user_points[$user_data['id']]['techno']['points']
                                                                + $user_points[$user_data['id']]['build']['points']
                                                                + $user_points[$user_data['id']]['defense']['points']
                                                                + $user_points[$user_data['id']]['fleet']['points'];

            if ($user_data['ally_id'] != 0)
            {
                if (!isset($ally_points[$user_data['ally_id']]))
                {
                    $ally_points[$user_data['ally_id']]['build']['count'] = 0;
                    $ally_points[$user_data['ally_id']]['build']['points'] = 0;
                    $ally_points[$user_data['ally_id']]['fleet']['count'] = 0;
                    $ally_points[$user_data['ally_id']]['fleet']['points'] = 0;
                    $ally_points[$user_data['ally_id']]['defense']['count'] = 0;
                    $ally_points[$user_data['ally_id']]['defense']['points'] = 0;
                    $ally_points[$user_data['ally_id']]['techno']['count'] = 0;
                    $ally_points[$user_data['ally_id']]['techno']['points'] = 0;
                    $ally_points[$user_data['ally_id']]['total']['count'] = 0;
                    $ally_points[$user_data['ally_id']]['total']['points'] = 0;
                }

                $ally_points[$user_data['ally_id']]['build']['count'] += $user_points[$user_data['id']]['build']['count'];
                $ally_points[$user_data['ally_id']]['build']['points'] += $user_points[$user_data['id']]['build']['points'];
                $ally_points[$user_data['ally_id']]['fleet']['count'] += $user_points[$user_data['id']]['fleet']['count'];
                $ally_points[$user_data['ally_id']]['fleet']['points'] += $user_points[$user_data['id']]['fleet']['points'];
                $ally_points[$user_data['ally_id']]['defense']['count'] += $user_points[$user_data['id']]['defense']['count'];
                $ally_points[$user_data['ally_id']]['defense']['points'] += $user_points[$user_data['id']]['defense']['points'];
                $ally_points[$user_data['ally_id']]['techno']['count'] += $user_points[$user_data['id']]['techno']['count'];
                $ally_points[$user_data['ally_id']]['techno']['points'] += $user_points[$user_data['id']]['techno']['points'];
                $ally_points[$user_data['ally_id']]['total']['count'] += $user_points[$user_data['id']]['total']['count'];
                $ally_points[$user_data['ally_id']]['total']['points'] += $user_points[$user_data['id']]['total']['points'];
            }
            $final_sql .= "(".
            $user_data['id'].", ".
            $user_data['ally_id'].", ".
            $user_data['universe'].", ".
            ($user_data['old_tech_rank'] ?? 0) . ", ".
            min($user_points[$user_data['id']]['techno']['points'] ?? 0, 1E50) . ", ".
            ($user_points[$user_data['id']]['techno']['count'] ?? 0) .", ".
            ($user_data['old_build_rank'] ?? 0). ", " .
            min($user_points[$user_data['id']]['build']['points'] ?? 0, 1E50) .", ".
            ($user_points[$user_data['id']]['build']['count'] ?? 0) .", ".
            ($user_data['old_defs_rank'] ?? 0) . ", " .
            min($user_points[$user_data['id']]['defense']['points'] ?? 0, 1E50) . ", ".
            ($user_points[$user_data['id']]['defense']['count'] ?? 0) .", ".
            ($user_data['old_fleet_rank'] ?? 0) . ", ".
            min($user_points[$user_data['id']]['fleet']['points'] ?? 0, 1E50) . ", ".
            ($user_points[$user_data['id']]['fleet']['count'] ?? 0) .", ".
            ($user_data['old_total_rank'] ?? 0) . ", ".
            min($user_points[$user_data['id']]['total']['points'] ?? 0, 1E50) .", ".
            ($user_points[$user_data['id']]['total']['count'] ?? 0). "), ";

            if ($i == 50)
            {
                $final_sql = substr($final_sql, 0, -2) . $sql_end;
                $this->SaveDataIntoDB($final_sql);
                $final_sql = $save_sql;
                $i = 0;
            }
        }

        //for example $i < 50 ,
        if ($final_sql != $save_sql)
        {
            $final_sql = substr($final_sql, 0, -2) . $sql_end;
            var_dump($final_sql);
            $this->SaveDataIntoDB($final_sql);
            unset($user_points);
        }

        if (count($ally_points) != 0)
        {
            $ally_sql = $save_ally_sql = "INSERT INTO %%ALLIANCE_POINTS%% 
            (id_owner, id_ally, universe, tech_old_rank, tech_points, 
            tech_count, build_old_rank, build_points, build_count, 
            defs_old_rank, defs_points, defs_count, fleet_old_rank, 
            fleet_points, fleet_count, total_old_rank, total_points, total_count) 
            VALUES ";

            $sql_end_alliance = " ON DUPLICATE KEY UPDATE
			id_ally = VALUES(id_ally),
			id_owner = VALUES(id_owner),
			universe = VALUES(universe),
			tech_old_rank = VALUES(tech_old_rank),
			tech_points = VALUES(tech_points),
			tech_count = VALUES(tech_count),
			build_old_rank = VALUES(build_old_rank),
			build_points = VALUES(build_points),
			build_count = VALUES(build_count),
			defs_old_rank = VALUES(defs_old_rank),
			defs_points = VALUES(defs_points),
			defs_count = VALUES(defs_count),
			fleet_old_rank = VALUES(fleet_old_rank),
			fleet_points = VALUES(fleet_points),
			fleet_count = VALUES(fleet_count),
			total_old_rank = VALUES(total_old_rank),
			total_points = VALUES(total_points),
			total_count = VALUES(total_count);";

            $i = 0;
            foreach ($total_data['Alliance'] as $alliance_data)
            {
                $i++;
                $ally_sql .= "(".
                $alliance_data['id'].", 0, ".
                $alliance_data['ally_universe'].", ".
                ($ally_points['old_tech_rank'] ?? 0) .", ".
                min($ally_points[$alliance_data['id']]['techno']['points'] ?? 0, 1E50) .", ".
                ($ally_points[$alliance_data['id']]['techno']['count'] ?? 0) . ", " .
                ($alliance_data['old_build_rank'] ?? 0) . ", ".
                min($ally_points[$alliance_data['id']]['build']['points'] ?? 0, 1E50) .", ".
                ($ally_points[$alliance_data['id']]['build']['count'] ?? 0) . ", " .
                ($alliance_data['old_defs_rank'] ?? 0) . ", ".
                min($ally_points[$alliance_data['id']]['defense']['points'] ?? 0, 1E50) .", ".
                ($ally_points[$alliance_data['id']]['defense']['count'] ?? 0). ", " .
                ($alliance_data['old_fleet_rank'] ?? 0) . ", ".
                min($ally_points[$alliance_data['id']]['fleet']['points'] ?? 0, 1E50) . ", ".
                ($ally_points[$alliance_data['id']]['fleet']['count'] ?? 0) . ", " .
                ($alliance_data['old_total_rank'] ?? 0) . ", " .
                min($ally_points[$alliance_data['id']]['total']['points'] ?? 0, 1E50) . ", ".
                ($ally_points[$alliance_data['id']]['total']['count'] ?? 0). "), ";

                if ($i == 50)
                {
                    $ally_sql = substr($ally_sql, 0, -2) . $sql_end_alliance;
                    $this->SaveDataIntoDB($ally_sql);
                    $ally_sql = $save_ally_sql;
                    $i = 0;
                }

            }

            // for example i < 50
            if ($ally_sql != $save_sql)
            {
                $ally_sql = substr($ally_sql, 0, -2) . $sql_end_alliance;
                $this->SaveDataIntoDB($ally_sql);
            }

            unset($ally_points);

        }

        $this->SetNewRanks();

        $this->CheckUniverseAccounts($uni_data);
        $this->writeRecordData();

        return $this->SomeStatsInfos();
    }
}
