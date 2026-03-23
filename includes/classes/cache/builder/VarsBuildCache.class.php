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
            $REQUIREMENTS[$reqRow['elementID']][$reqRow['requireID']] = $reqRow['requireLevel'];
        }

        $varsResult = $db->nativeQuery('SELECT * FROM %%VARS%%;');
        foreach ($varsResult as $varsRow)
        {
            $RESOURCE[$varsRow['elementID']] = $varsRow['name'];
            $COMBATCAPS[$varsRow['elementID']] = [
                'attack' => $varsRow['attack'],
                'shield' => $varsRow['defend'],
            ];

            $PRICELIST[$varsRow['elementID']] = [
                'cost' => [
                    901 => $varsRow['cost901'],
                    902 => $varsRow['cost902'],
                    903 => $varsRow['cost903'],
                    911 => $varsRow['cost911'],
                    921 => $varsRow['cost921'],
                ],
                'factor'       => $varsRow['factor'],
                'max'          => $varsRow['maxLevel'],
                'consumption'  => $varsRow['consumption1'],
                'consumption2' => $varsRow['consumption2'],
                'speed'        => $varsRow['speed1'],
                'speed2'       => $varsRow['speed2'],
                'capacity'     => $varsRow['capacity'],
                'tech'         => $varsRow['speedTech'],
                'time'         => $varsRow['timeBonus'],
                'bonus'        => [
                    'Attack'          => [$varsRow['bonusAttack'], $varsRow['bonusAttackUnit']],
                    'Defensive'       => [$varsRow['bonusDefensive'], $varsRow['bonusDefensiveUnit']],
                    'Shield'          => [$varsRow['bonusShield'], $varsRow['bonusShieldUnit']],
                    'BuildTime'       => [$varsRow['bonusBuildTime'], $varsRow['bonusBuildTimeUnit']],
                    'ResearchTime'    => [$varsRow['bonusResearchTime'], $varsRow['bonusResearchTimeUnit']],
                    'ShipTime'        => [$varsRow['bonusShipTime'], $varsRow['bonusShipTimeUnit']],
                    'DefensiveTime'   => [$varsRow['bonusDefensiveTime'], $varsRow['bonusDefensiveTimeUnit']],
                    'Resource'        => [$varsRow['bonusResource'], $varsRow['bonusResourceUnit']],
                    'Energy'          => [$varsRow['bonusEnergy'], $varsRow['bonusEnergyUnit']],
                    'ResourceStorage' => [$varsRow['bonusResourceStorage'], $varsRow['bonusResourceStorageUnit']],
                    'ShipStorage'     => [$varsRow['bonusShipStorage'], $varsRow['bonusShipStorageUnit']],
                    'FlyTime'         => [$varsRow['bonusFlyTime'], $varsRow['bonusFlyTimeUnit']],
                    'FleetSlots'      => [$varsRow['bonusFleetSlots'], $varsRow['bonusFleetSlotsUnit']],
                    'Planets'         => [$varsRow['bonusPlanets'], $varsRow['bonusPlanetsUnit']],
                    'SpyPower'        => [$varsRow['bonusSpyPower'], $varsRow['bonusSpyPowerUnit']],
                    'Expedition'      => [$varsRow['bonusExpedition'], $varsRow['bonusExpeditionUnit']],
                    'GateCoolTime'    => [$varsRow['bonusGateCoolTime'], $varsRow['bonusGateCoolTimeUnit']],
                    'MoreFound'       => [$varsRow['bonusMoreFound'], $varsRow['bonusMoreFoundUnit']],
                ],
            ];

            $PRODGRID[$varsRow['elementID']]['production'] = [
                901 => $varsRow['production901'],
                902 => $varsRow['production902'],
                903 => $varsRow['production903'],
                911 => $varsRow['production911'],
            ];

            $PRODGRID[$varsRow['elementID']]['storage'] = [
                901 => $varsRow['storage901'],
                902 => $varsRow['storage902'],
                903 => $varsRow['storage903'],
            ];

            if (array_filter($PRODGRID[$varsRow['elementID']]['production']))
            {
                $RESLIST['prod'][] = $varsRow['elementID'];
            }

            if (array_filter($PRODGRID[$varsRow['elementID']]['storage']))
            {
                $RESLIST['storage'][] = $varsRow['elementID'];
            }

            if (($varsRow['bonusAttack'] + $varsRow['bonusDefensive'] + $varsRow['bonusShield'] + $varsRow['bonusBuildTime'] +
                $varsRow['bonusResearchTime'] + $varsRow['bonusShipTime'] + $varsRow['bonusDefensiveTime'] + $varsRow['bonusResource'] +
                $varsRow['bonusEnergy'] + $varsRow['bonusResourceStorage'] + $varsRow['bonusShipStorage'] + $varsRow['bonusFlyTime'] +
                $varsRow['bonusFleetSlots'] + $varsRow['bonusPlanets'] + $varsRow['bonusSpyPower'] + $varsRow['bonusExpedition'] +
                $varsRow['bonusGateCoolTime'] + $varsRow['bonusMoreFound']) != 0)
            {
                $RESLIST['bonus'][] = $varsRow['elementID'];
            }
            if ($varsRow['onePerPlanet'] == 1)
            {
                $RESLIST['one'][] = $varsRow['elementID'];
            }

            switch ($varsRow['class'])
            {
                case 0:
                    $RESLIST['build'][] = $varsRow['elementID'];
                    $tmp = explode(',', $varsRow['onPlanetType']);
                    foreach ($tmp as $type)
                    {
                        $RESLIST['allow'][$type][] = $varsRow['elementID'];
                    }
                    break;
                case 100:
                    $RESLIST['tech'][] = $varsRow['elementID'];
                    break;
                case 200:
                    $RESLIST['fleet'][] = $varsRow['elementID'];
                    break;
                case 400:
                    $RESLIST['defense'][] = $varsRow['elementID'];
                    break;
                case 500:
                    $RESLIST['missile'][] = $varsRow['elementID'];
                    break;
                case 600:
                    $RESLIST['officers'][] = $varsRow['elementID'];
                    break;
                case 700:
                    $RESLIST['dmfunc'][] = $varsRow['elementID'];
                    break;
            }
        }

        $rapidResult = $db->nativeQuery('SELECT * FROM %%VARS_RAPIDFIRE%%;');
        foreach ($rapidResult as $rapidRow)
        {
            $COMBATCAPS[$rapidRow['elementID']]['sd'][$rapidRow['rapidfireID']] = $rapidRow['shoots'];
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
