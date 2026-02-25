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

class ShowRecordsPage extends AbstractGamePage
{
    public static $requireModule = MODULE_RECORDS;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $LNG, $reslist;

        $db = Database::get();

        $sql = "SELECT elementID, level, userID, username
		FROM %%USERS%%
		INNER JOIN %%RECORDS%% ON userID = id
		WHERE universe = :universe;";

        $records = $db->select($sql, [
            ':universe' => Universe::current(),
        ]);

        $defense_list = array_fill_keys($reslist['defense'], []);
        $fleet_list = array_fill_keys($reslist['fleet'], []);
        $research_list = array_fill_keys($reslist['tech'], []);
        $build_list = array_fill_keys($reslist['build'], []);
        $officer_list = array_fill_keys($reslist['officier'], []);

        foreach ($records as $c_record)
        {
            if (in_array($c_record['elementID'], $reslist['defense']))
            {
                $defense_list[$c_record['elementID']][] = $c_record;
            }
            elseif (in_array($c_record['elementID'], $reslist['fleet']))
            {
                $fleet_list[$c_record['elementID']][] = $c_record;
            }
            elseif (in_array($c_record['elementID'], $reslist['tech']))
            {
                $research_list[$c_record['elementID']][] = $c_record;
            }
            elseif (in_array($c_record['elementID'], $reslist['build']))
            {
                $build_list[$c_record['elementID']][] = $c_record;
            }
            elseif (in_array($c_record['elementID'], $reslist['officier']))
            {
                $officer_list[$c_record['elementID']][] = $c_record;
            }
            elseif (in_array($c_record['elementID'], $reslist['missile']))
            {
                $defense_list[$c_record['elementID']][] = $c_record;
            }
        }

        require_once 'includes/classes/Cronjob.class.php';

        $this->assign([
            'defenseList'  => $defense_list,
            'fleetList'    => $fleet_list,
            'researchList' => $research_list,
            'buildList'    => $build_list,
            'officerList'  => $officer_list,
            'update'       => _date(
                $LNG['php_tdformat'],
                Cronjob::getLastExecutionTime('statistic'),
                $USER['timezone']
            ),
        ]);

        $this->display('page.records.default.tpl');
    }
}
