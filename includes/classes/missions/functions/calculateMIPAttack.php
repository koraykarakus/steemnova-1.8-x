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

function calculateMIPAttack(
    $target_def_tech,
    $owner_att_tech,
    $missiles,
    $target_defensive,
    $first_target,
    $defense_missiles
) {
    global $PRICELIST, $COMBATCAPS;

    $destroy_ships = [];
    $count_missiles = $missiles - $defense_missiles;

    if ($count_missiles == 0)
    {
        return $destroy_ships;
    }

    $total_attack = $count_missiles * $COMBATCAPS[503]['attack'] * (1 + 0.1 * $owner_att_tech);

    // Select primary target, if exists
    if (isset($target_defensive[$first_target]))
    {
        $first_target_data = [$first_target => $target_defensive[$first_target]];
        unset($target_defensive[$first_target]);
        $target_defensive = $first_target_data + $target_defensive;
    }

    foreach ($target_defensive as $element => $count)
    {
        if ($element == 0)
        {
            throw new Exception("Unknown error. Please report this error on tracker.2moons.cc. Debuginforations:<br><br>" .
            serialize(
                [$target_def_tech, $owner_att_tech, $missiles, $target_defensive, $first_target, $defense_missiles]
            ));
        }
        $element_structure_points = ($PRICELIST[$element]['cost'][901] + $PRICELIST[$element]['cost'][902]) * (1 + 0.1 * $target_def_tech) / 10;
        $destroyCount = floor($total_attack / $element_structure_points);
        $destroyCount = min($destroyCount, $count);
        $total_attack -= $destroyCount * $element_structure_points;

        $destroy_ships[$element] = $destroyCount;
        if ($total_attack <= 0)
        {
            return $destroy_ships;
        }
    }

    return $destroy_ships;
}
