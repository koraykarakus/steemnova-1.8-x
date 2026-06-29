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

class ShowShipyardPage extends AbstractGamePage
{
    public static int $require_module = 0;

    public static string $default_controller = 'show';

    public function __construct()
    {
        parent::__construct();
    }

    private function CancelAuftr(): bool
    {
        global $USER, $PLANET, $RESOURCE;
        $element_queue = unserialize($PLANET['b_shipyard_id']);

        $cancel_array = HTTP::_GP('auftr', []);

        if (!is_array($cancel_array))
        {
            return false;
        }

        foreach ($cancel_array as $auftr)
        {
            if (!isset($element_queue[$auftr]))
            {
                continue;
            }

            if ($auftr == 0)
            {
                $PLANET['b_shipyard'] = 0;
            }

            $element = $element_queue[$auftr][0];
            $count = $element_queue[$auftr][1];

            $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $element, false, $count);

            if (isset($cost_resources[901]))
            {
                $PLANET[$RESOURCE[901]] += $cost_resources[901] * FACTOR_CANCEL_SHIPYARD;
            }
            if (isset($cost_resources[902]))
            {
                $PLANET[$RESOURCE[902]] += $cost_resources[902] * FACTOR_CANCEL_SHIPYARD;
            }
            if (isset($cost_resources[903]))
            {
                $PLANET[$RESOURCE[903]] += $cost_resources[903] * FACTOR_CANCEL_SHIPYARD;
            }
            if (isset($cost_resources[921]))
            {
                $USER[$RESOURCE[921]] += $cost_resources[921] * FACTOR_CANCEL_SHIPYARD;
            }

            unset($element_queue[$auftr]);
        }

        if (empty($element_queue))
        {
            $PLANET['b_shipyard_id'] = '';
        }
        else
        {
            $PLANET['b_shipyard_id'] = serialize(array_values($element_queue));
        }

        return true;
    }

    private function BuildAuftr(array $fmenge): void
    {
        global $USER, $PLANET, $RESLIST, $RESOURCE;

        $missiles = [
            502 => $PLANET[$RESOURCE[502]],
            503 => $PLANET[$RESOURCE[503]],
        ];

        foreach ($fmenge as $element => $count)
        {
            if (empty($count)
                || !in_array($element, array_merge($RESLIST['fleet'], $RESLIST['defense'], $RESLIST['missile']))
                || !BuildFunctions::isTechnologieAccessible($USER, $PLANET, $element)
            ) {
                continue;
            }

            $max_elements = BuildFunctions::getMaxConstructibleElements($USER, $PLANET, $element);
            $count = is_numeric($count) ? round($count) : 0;
            $count = max(min($count, Config::get()->max_fleet_per_build), 0);
            $count = min($count, $max_elements);

            $build_array = !empty($PLANET['b_shipyard_id']) ? unserialize($PLANET['b_shipyard_id']) : [];
            if (in_array($element, $RESLIST['missile']))
            {
                $max_missiles = BuildFunctions::getMaxConstructibleRockets($USER, $PLANET, $missiles);
                $count = min($count, $max_missiles[$element]);

                $missiles[$element] += $count;
            }
            elseif (in_array($element, $RESLIST['one']))
            {
                $in_build = false;
                foreach ($build_array as $element_array)
                {
                    if ($element_array[0] == $element)
                    {
                        $in_build = true;
                        break;
                    }
                }

                if ($in_build)
                {
                    continue;
                }

                if ($count != 0 && $PLANET[$RESOURCE[$element]] == 0 && $in_build === false)
                {
                    $count = 1;
                }
            }

            if (empty($count))
            {
                continue;
            }

            $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $element, false, $count);

            if (isset($cost_resources[901]))
            {
                $PLANET[$RESOURCE[901]] -= $cost_resources[901];
            }
            if (isset($cost_resources[902]))
            {
                $PLANET[$RESOURCE[902]] -= $cost_resources[902];
            }
            if (isset($cost_resources[903]))
            {
                $PLANET[$RESOURCE[903]] -= $cost_resources[903];
            }
            if (isset($cost_resources[921]))
            {
                $USER[$RESOURCE[921]] -= $cost_resources[921];
            }

            $build_array[] = [$element, $count];
            $PLANET['b_shipyard_id'] = serialize($build_array);
        }
    }

    public function show(): void
    {
        global $USER, $PLANET, $LNG, $RESOURCE, $RESLIST, $config, $REQUIREMENTS;

        if ($PLANET[$RESOURCE[21]] == 0
            && !$config->show_ships_no_shipyard)
        {
            $this->printMessage($LNG['bd_shipyard_required']);
        }

        $build_to_do = HTTP::_GP('fmenge', []);
        $action = HTTP::_GP('action', '');

        $not_building = true;
        if (!empty($PLANET['b_building_id']))
        {
            $current_queue = unserialize($PLANET['b_building_id']);
            foreach ($current_queue as $element_array)
            {
                if ($element_array[0] == 21
                    || $element_array[0] == 15)
                {
                    $not_building = false;
                    break;
                }
            }
        }

        $element_queue = unserialize($PLANET['b_shipyard_id']);
        if (empty($element_queue))
        {
            $count = 0;
        }
        else
        {
            $count = count($element_queue);
        }

        if ($USER['vacation_mode'] == 0
            && $not_building == true)
        {
            if (!empty($build_to_do))
            {
                $max_build_queue = $config->max_elements_ships;
                if ($max_build_queue != 0
                    && $count >= $max_build_queue)
                {
                    $this->printMessage(sprintf($LNG['bd_max_builds'], $max_build_queue));
                }

                $this->BuildAuftr($build_to_do);
            }

            if ($action == "delete")
            {
                $this->CancelAuftr();
            }
        }

        $element_in_queue = [];
        $element_queue = unserialize($PLANET['b_shipyard_id']);
        $build_list = [];
        $element_list = [];

        if (!empty($element_queue))
        {
            $shipyard = [];
            $queue_time = 0;
            foreach ($element_queue as $element)
            {
                if (empty($element))
                {
                    continue;
                }

                $element_in_queue[$element[0]] = true;
                $element_time = BuildFunctions::getBuildingTime($USER, $PLANET, $element[0]);
                $queue_time += $element_time * $element[1];
                $shipyard[] = [$LNG['tech'][$element[0]], $element[1], $element_time, $element[0]];
            }

            $build_list = [
                'Queue'                => $shipyard,
                'b_hangar_id_plus'     => $PLANET['b_shipyard'],
                'pretty_time_b_hangar' => pretty_time(max($queue_time - $PLANET['b_shipyard'], 0)),
            ];
        }

        $mode = HTTP::_GP('mode', 'fleet');

        if ($mode == 'defense')
        {
            $element_ids = array_merge($RESLIST['defense'], $RESLIST['missile']);
        }
        else
        {
            $element_ids = $RESLIST['fleet'];
        }

        $missiles = [];

        foreach ($RESLIST['missile'] as $elementID)
        {
            $missiles[$elementID] = $PLANET[$RESOURCE[$elementID]];
        }

        $max_missiles = BuildFunctions::getMaxConstructibleRockets($USER, $PLANET, $missiles);

        foreach ($element_ids as $element)
        {
            if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $element)
                && !$config->show_unlearned_ships)
            {
                continue;
            }

            $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $element);
            $cost_overflow = BuildFunctions::getRestPrice($USER, $PLANET, $element, $cost_resources);
            $element_time = BuildFunctions::getBuildingTime($USER, $PLANET, $element, $cost_resources);
            $buyable = BuildFunctions::isElementBuyable($USER, $PLANET, $element, $cost_resources);
            $max_buildable = BuildFunctions::getMaxConstructibleElements($USER, $PLANET, $element, $cost_resources);
            $solar_energy = round((($PLANET['temp_max'] + 160) / 6) * $config->energySpeed, 1);

            if (isset($max_missiles[$element]))
            {
                $max_buildable = min($max_buildable, $max_missiles[$element]);
            }

            $already_build = in_array($element, $RESLIST['one'])
                            && (isset($element_in_queue[$element]) || $PLANET[$RESOURCE[$element]] != 0);

            $require_array = [];

            if (isset($REQUIREMENTS[$element]))
            {
                foreach ($REQUIREMENTS[$element] as $require_id => $require_level)
                {
                    $require_array[] = [
                        'currentLevel' => ($require_id < 100) ? $PLANET[$RESOURCE[$require_id]] : $USER[$RESOURCE[$require_id]],
                        'neededLevel'  => $require_level,
                        'requireID'    => $require_id,
                    ];
                }

            }

            $element_list[$element] = [
                'id'                  => $element,
                'available'           => $PLANET[$RESOURCE[$element]],
                'costResources'       => $cost_resources,
                'costOverflow'        => $cost_overflow,
                'costOverflowTotal'   => array_sum($cost_overflow),
                'elementTime'         => $element_time,
                'buyable'             => $buyable,
                'maxBuildable'        => floatToString($max_buildable),
                'AlreadyBuild'        => $already_build,
                'technologySatisfied' => BuildFunctions::isTechnologieAccessible($USER, $PLANET, $element),
                'requirements'        => $require_array,
            ];
        }

        $this->assign([
            'elementList'       => $element_list,
            'NotBuilding'       => $not_building,
            'BuildList'         => $build_list,
            'maxlength'         => strlen($config->max_fleet_per_build),
            'mode'              => $mode,
            'SolarEnergy'       => $solar_energy,
            'userFleetPoints'   => pretty_number($USER['fleet_points']),
            'userDefensePoints' => pretty_number($USER['defs_points']),
        ]);

        $this->display('page.shipyard.default.tpl');
    }
}
