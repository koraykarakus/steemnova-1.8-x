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

class ShowTestBattlePage extends AbstractGamePage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
        $this->window = 'popup';
    }

    public function show(): void
    {
        global $reslist, $LNG;

        $ships_list = $defense_list = [];

        foreach ($reslist['fleet'] as $c_ship_id)
        {
            $ships_list[] = [
                'id'   => $c_ship_id,
                'name' => $LNG['tech'][$c_ship_id],
            ];
        }

        foreach ($reslist['defense'] as $c_def_id)
        {
            $defense_list[] = [
                'id'   => $c_def_id,
                'name' => $LNG['tech'][$c_def_id],
            ];
        }

        $this->assign([
            'ships_list'   => $ships_list,
            'defense_list' => $defense_list,
        ]);

        $this->display('page.testbattle.default.tpl');
    }

    public function send(): void
    {
        global $PLANET, $USER, $reslist, $pricelist, $resource;

        if ($USER['authlevel'] !== 3)
        {
            $this->printMessage('No rights to use feature');
            return;
        }

        // Coordinates of test planet
        $target_galaxy = 1;
        $target_system = 1;
        $target_planet = 1;

        ///////////////////////
        // UPDATE TARGET PLANET START
        ///////////////////////

        $sql_update = $sql_reset = "UPDATE %%PLANETS%% SET ";
        $query_ships = $query_reset = [];
        foreach ($reslist['fleet'] as $id)
        {
            $query_reset[] = $resource[$id] . " = 0";
            $amount = max(0, round(HTTP::_GP('def_ship_'.$id, 0.0, 0.0)));

            if ($amount < 1)
            {
                continue;
            }
            $query_ships[] = $resource[$id] . " = " . $amount;
        }

        foreach ($reslist['defense'] as $id)
        {
            $query_reset[] = $resource[$id] . " = 0";
            $amount = max(0, round(HTTP::_GP('def_def_'.$id, 0.0, 0.0)));

            if ($amount < 1)
            {
                continue;
            }

            if (in_array($id, $reslist['one']))
            {
                $amount = 1;
            }

            $query_ships[] = $resource[$id] . " = " . $amount;
        }

        if (!empty($query_ships))
        {
            $sql_reset .= implode(', ', $query_reset);
            $sql_reset .= " WHERE galaxy = :galaxy AND system = :system AND planet = :planet 
            AND universe = :universe";
            Database::get()->update($sql_reset, [
                ':galaxy'   => $target_galaxy,
                ':system'   => $target_system,
                ':planet'   => $target_planet,
                ':universe' => Universe::current(),
            ]);

            $sql_update .= implode(', ', $query_ships);
            $sql_update .= " WHERE galaxy = :galaxy AND system = :system AND planet = :planet 
            AND universe = :universe";
            Database::get()->update($sql_update, [
                ':galaxy'   => $target_galaxy,
                ':system'   => $target_system,
                ':planet'   => $target_planet,
                ':universe' => Universe::current(),
            ]);
        }

        ///////////////////////
        // UPDATE TARGET PLANET END
        ///////////////////////

        $fleet_array = [];
        $fleet_room = 0;
        foreach ($reslist['fleet'] as $ship_id)
        {
            $amount = max(0, round(HTTP::_GP('atk_ship_'.$ship_id, 0.0, 0.0)));

            if ($amount < 1
                || $ship_id == 212)
            {
                continue;
            }

            $fleet_array[$ship_id] = $amount;
            $fleet_room += $pricelist[$ship_id]['capacity'] * $amount;
        }

        $fleet_room *= 1 + $USER['factor']['ShipStorage'];

        $target_type = 1; // for now
        $fleet_start_time = TIMESTAMP + 20;
        $fleet_stay_time = 0; // ?
        $fleet_end_time = TIMESTAMP + 40;
        $fleet_resource = [
            901 => 0,
            902 => 0,
            903 => 0,
        ];
        $fleet_group = 0; // ACS related.
        $target_mission = 1;

        $sql = "SELECT id, id_owner FROM %%PLANETS%% 
        WHERE galaxy = :galaxy AND system = :system AND planet = :planet 
        AND universe = :universe";

        $data_planet = Database::get()->selectSingle($sql, [
            ':galaxy'   => $target_galaxy,
            ':system'   => $target_system,
            ':planet'   => $target_planet,
            ':universe' => Universe::current(),
        ]);

        if ($data_planet === false)
        {
            $this->printMessage('wrong planet coordinates');
        }

        FleetFunctions::sendFleetTest(
            $fleet_array,
            $target_mission,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $data_planet['id_owner'],
            $data_planet['id'],
            $target_galaxy,
            $target_system,
            $target_planet,
            $target_type,
            $fleet_resource,
            $fleet_start_time,
            $fleet_stay_time,
            $fleet_end_time,
            $fleet_group,
            0
        );

        $this->printMessage('success');
    }
}
