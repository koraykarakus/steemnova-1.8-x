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

function calculateSteal($attack_fleets, $defender_planet, $simulate = false)
{
    // See: http://www.owiki.de/Beute
    global $pricelist, $resource;

    $metal = 901;
    $crystal = 902;
    $deu = 903;

    $sort_fleets = [];
    $capacity = 0;

    $steal_res = [
        $metal   => 0,
        $crystal => 0,
        $deu     => 0,
    ];

    foreach ($attack_fleets as $fleet_id => $attacker)
    {
        $sort_fleets[$fleet_id] = 0;

        foreach ($attacker['unit'] as $element => $amount)
        {
            $sort_fleets[$fleet_id] += $pricelist[$element]['capacity'] * $amount;
        }

        $sort_fleets[$fleet_id] *= (1 + $attacker['player']['factor']['ShipStorage']);

        $sort_fleets[$fleet_id] -= $attacker['fleetDetail']['fleet_resource_metal'];
        $sort_fleets[$fleet_id] -= $attacker['fleetDetail']['fleet_resource_crystal'];
        $sort_fleets[$fleet_id] -= $attacker['fleetDetail']['fleet_resource_deuterium'];
        $capacity += $sort_fleets[$fleet_id];
    }

    $all_capacity = $capacity;
    if ($all_capacity <= 0)
    {
        return $steal_res;
    }

    // Step 1
    $steal_res[$metal] = min($capacity / 3, $defender_planet[$resource[$metal]] / 2);
    $capacity -= $steal_res[$metal];

    // Step 2
    $steal_res[$crystal] = min($capacity / 2, $defender_planet[$resource[$crystal]] / 2);
    $capacity -= $steal_res[$crystal];

    // Step 3
    $steal_res[$deu] = min($capacity, $defender_planet[$resource[$deu]] / 2);
    $capacity -= $steal_res[$deu];

    // Step 4
    $old_metal_booty = $steal_res[$metal];
    $steal_res[$metal] += min($capacity / 2, $defender_planet[$resource[$metal]] / 2 - $steal_res[$metal]);
    $capacity -= $steal_res[$metal] - $old_metal_booty;

    // Step 5
    $steal_res[$crystal] += min($capacity, $defender_planet[$resource[$crystal]] / 2 - $steal_res[$crystal]);

    if ($simulate)
    {
        return $steal_res;
    }

    $db = Database::get();

    foreach ($sort_fleets as $fleet_id => $fleet_capacity)
    {
        $slot_factor = $fleet_capacity / $all_capacity;

        $sql = "UPDATE %%FLEETS%% SET
		`fleet_resource_metal` = `fleet_resource_metal` + '".($steal_res[$metal] * $slot_factor)."',
		`fleet_resource_crystal` = `fleet_resource_crystal` + '".($steal_res[$crystal] * $slot_factor)."',
		`fleet_resource_deuterium` = `fleet_resource_deuterium` + '".($steal_res[$deu] * $slot_factor)."'
		WHERE fleet_id = :fleet_id;";

        $db->update($sql, [
            ':fleet_id' => $fleet_id,
        ]);
    }

    return $steal_res;
}
