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

class VarsBuildCache implements BuildCache
{
    public function buildCache()
    {
        $RESOURCE = [];
        $REQUIREMENTS = [];
        $PRICELIST = [];
        $COMBATCAPS = [];
        $RESLIST = [];
        $PRODGRID = [];

        $RESLIST['prod'] = [];
        $RESLIST['storage'] = [];
        $RESLIST['bonus'] = [];
        $RESLIST['one'] = [];
        $RESLIST['build'] = [];
        $RESLIST['allow'][1] = [];
        $RESLIST['allow'][3] = [];
        $RESLIST['tech'] = [];
        $RESLIST['fleet'] = [];
        $RESLIST['defense'] = [];
        $RESLIST['missile'] = [];
        $RESLIST['officers'] = [];
        $RESLIST['dmfunc'] = [];

        $db = Database::get();

        $reqResult = $db->nativeQuery('SELECT * FROM %%VARS_REQUIRE%%;');
        foreach ($reqResult as $reqRow)
        {
            $REQUIREMENTS[$reqRow['element_id']][$reqRow['require_id']] = $reqRow['require_level'];
        }

        $varsResult = $db->nativeQuery('SELECT * FROM %%VARS%%;');
        foreach ($varsResult as $varsRow)
        {
            $RESOURCE[$varsRow['element_id']] = $varsRow['name'];
            $COMBATCAPS[$varsRow['element_id']] = [
                'attack' => $varsRow['attack'],
                'shield' => $varsRow['defend'],
            ];

            $PRICELIST[$varsRow['element_id']] = [
                'cost' => [
                    901 => $varsRow['cost901'],
                    902 => $varsRow['cost902'],
                    903 => $varsRow['cost903'],
                    911 => $varsRow['cost911'],
                    921 => $varsRow['cost921'],
                ],
                'factor'       => $varsRow['factor'],
                'max'          => $varsRow['max_level'],
                'consumption'  => $varsRow['consumption1'],
                'consumption2' => $varsRow['consumption2'],
                'speed'        => $varsRow['speed1'],
                'speed2'       => $varsRow['speed2'],
                'capacity'     => $varsRow['capacity'],
                'tech'         => $varsRow['speed_tech'],
                'time'         => $varsRow['time_bonus'],
                'bonus'        => [
                    'Attack'          => [$varsRow['bonus_attack'], $varsRow['bonus_attack_unit']],
                    'Defensive'       => [$varsRow['bonus_defensive'], $varsRow['bonus_defensive_unit']],
                    'Shield'          => [$varsRow['bonus_shield'], $varsRow['bonus_shield_unit']],
                    'BuildTime'       => [$varsRow['bonus_build_time'], $varsRow['bonus_build_time_unit']],
                    'ResearchTime'    => [$varsRow['bonus_research_time'], $varsRow['bonus_research_time_unit']],
                    'ShipTime'        => [$varsRow['bonus_ship_time'], $varsRow['bonus_ship_time_unit']],
                    'DefensiveTime'   => [$varsRow['bonus_defensive_time'], $varsRow['bonus_defensive_time_unit']],
                    'Resource'        => [$varsRow['bonus_resource'], $varsRow['bonus_resource_unit']],
                    'Energy'          => [$varsRow['bonus_energy'], $varsRow['bonus_energy_unit']],
                    'ResourceStorage' => [$varsRow['bonus_resource_storage'], $varsRow['bonus_resource_storage_unit']],
                    'ShipStorage'     => [$varsRow['bonus_ship_storage'], $varsRow['bonus_ship_storage_unit']],
                    'FlyTime'         => [$varsRow['bonus_fly_time'], $varsRow['bonus_fly_time_unit']],
                    'FleetSlots'      => [$varsRow['bonus_fleet_slots'], $varsRow['bonus_fleet_slots_unit']],
                    'Planets'         => [$varsRow['bonus_planets'], $varsRow['bonus_planets_unit']],
                    'SpyPower'        => [$varsRow['bonus_spy_power'], $varsRow['bonus_spy_power_unit']],
                    'Expedition'      => [$varsRow['bonus_expedition'], $varsRow['bonus_expedition_unit']],
                    'GateCoolTime'    => [$varsRow['bonus_gate_cool_time'], $varsRow['bonus_gate_cool_time_unit']],
                    'MoreFound'       => [$varsRow['bonus_more_found'], $varsRow['bonus_more_found_unit']],
                ],
            ];

            $PRODGRID[$varsRow['element_id']]['production'] = [
                901 => $varsRow['production901'],
                902 => $varsRow['production902'],
                903 => $varsRow['production903'],
                911 => $varsRow['production911'],
            ];

            $PRODGRID[$varsRow['element_id']]['storage'] = [
                901 => $varsRow['storage901'],
                902 => $varsRow['storage902'],
                903 => $varsRow['storage903'],
            ];

            if (array_filter($PRODGRID[$varsRow['element_id']]['production']))
            {
                $RESLIST['prod'][] = $varsRow['element_id'];
            }

            if (array_filter($PRODGRID[$varsRow['element_id']]['storage']))
            {
                $RESLIST['storage'][] = $varsRow['element_id'];
            }

            if (($varsRow['bonus_attack'] + $varsRow['bonus_defensive'] + $varsRow['bonus_shield'] + $varsRow['bonus_build_time'] +
                $varsRow['bonus_research_time'] + $varsRow['bonus_ship_time'] + $varsRow['bonus_defensive_time'] + $varsRow['bonus_resource'] +
                $varsRow['bonus_energy'] + $varsRow['bonus_resource_storage'] + $varsRow['bonus_ship_storage'] + $varsRow['bonus_fly_time'] +
                $varsRow['bonus_fleet_slots'] + $varsRow['bonus_planets'] + $varsRow['bonus_spy_power'] + $varsRow['bonus_expedition'] +
                $varsRow['bonus_gate_cool_time'] + $varsRow['bonus_more_found']) != 0)
            {
                $RESLIST['bonus'][] = $varsRow['element_id'];
            }
            if ($varsRow['one_per_planet'] == 1)
            {
                $RESLIST['one'][] = $varsRow['element_id'];
            }

            switch ($varsRow['class'])
            {
                case 0:
                    $RESLIST['build'][] = $varsRow['element_id'];
                    $tmp = explode(',', $varsRow['on_planet_type']);
                    foreach ($tmp as $type)
                    {
                        $RESLIST['allow'][$type][] = $varsRow['element_id'];
                    }
                    break;
                case 100:
                    $RESLIST['tech'][] = $varsRow['element_id'];
                    break;
                case 200:
                    $RESLIST['fleet'][] = $varsRow['element_id'];
                    break;
                case 400:
                    $RESLIST['defense'][] = $varsRow['element_id'];
                    break;
                case 500:
                    $RESLIST['missile'][] = $varsRow['element_id'];
                    break;
                case 600:
                    $RESLIST['officers'][] = $varsRow['element_id'];
                    break;
                case 700:
                    $RESLIST['dmfunc'][] = $varsRow['element_id'];
                    break;
            }
        }

        $rapidResult = $db->nativeQuery('SELECT * FROM %%VARS_RAPIDFIRE%%;');
        foreach ($rapidResult as $rapidRow)
        {
            $COMBATCAPS[$rapidRow['element_id']]['sd'][$rapidRow['rapidfire_id']] = $rapidRow['shoots'];
        }

        return [
            'RESLIST'      => $RESLIST,
            'PRODGRID'     => $PRODGRID,
            'COMBATCAPS'   => $COMBATCAPS,
            'RESOURCE'     => $RESOURCE,
            'PRICELIST'    => $PRICELIST,
            'REQUIREMENTS' => $REQUIREMENTS,
        ];
    }
}
