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

function calculateIPMAttack(
    int $target_shield_tech,
    int $sender_military_tech,
    int $missiles,
    array $target_defensive,
    int $primary_target,
    int $defense_missiles
): array {
    global $PRICELIST, $COMBATCAPS;

    $destroyed_units = [];
    $effective_missile_count = $missiles - $defense_missiles;

    if ($effective_missile_count == 0)
    {
        return $destroyed_units;
    }

    $total_attack = $effective_missile_count * $COMBATCAPS[503]['attack'] * (1 + 0.1 * $sender_military_tech);

    // Select primary target, if exists
    if (isset($target_defensive[$primary_target]))
    {
        $first_target_data = [$primary_target => $target_defensive[$primary_target]];
        unset($target_defensive[$primary_target]);
        $target_defensive = $first_target_data + $target_defensive;
    }

    foreach ($target_defensive as $element => $count)
    {
        if ($element == 0)
        {
            throw new Exception("Unknown error. Please report this error on tracker.2moons.cc. Debuginforations:<br><br>" .
            serialize(
                [$target_shield_tech, $sender_military_tech, $missiles, $target_defensive, $primary_target, $defense_missiles]
            ));
        }
        $element_structure_points = ($PRICELIST[$element]['cost'][901] + $PRICELIST[$element]['cost'][902]) * (1 + 0.1 * $target_shield_tech) / 10;
        $destroy_count = floor($total_attack / $element_structure_points);
        $destroy_count = min($destroy_count, $count);
        $total_attack -= $destroy_count * $element_structure_points;

        $destroyed_units[$element] = $destroy_count;
        if ($total_attack <= 0)
        {
            return $destroyed_units;
        }
    }

    return $destroyed_units;
}
