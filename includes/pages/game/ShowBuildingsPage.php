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

class ShowBuildingsPage extends AbstractGamePage
{
    public static $require_module = MODULE_BUILDING;

    public function __construct()
    {
        parent::__construct();
    }

    private function CancelBuildingFromQueue(): bool
    {
        global $PLANET, $USER, $resource;
        $current_queue = unserialize($PLANET['b_building_id'] ?? '');
        if (empty($current_queue))
        {
            $PLANET['b_building_id'] = '';
            $PLANET['b_building'] = 0;
            return false;
        }

        $element = $current_queue[0][0];
        $build_level = $current_queue[0][1];
        $build_mode = $current_queue[0][4];

        $cost_resources = BuildFunctions::getElementPrice(
            $USER,
            $PLANET,
            $element,
            $build_mode == 'destroy',
            $build_level
        );

        if (isset($cost_resources[901]))
        {
            $PLANET[$resource[901]] += $cost_resources[901];
        }
        if (isset($cost_resources[902]))
        {
            $PLANET[$resource[902]] += $cost_resources[902];
        }
        if (isset($cost_resources[903]))
        {
            $PLANET[$resource[903]] += $cost_resources[903];
        }
        if (isset($cost_resources[921]))
        {
            $USER[$resource[921]] += $cost_resources[921];
        }
        array_shift($current_queue);
        if (count($current_queue) == 0)
        {
            $PLANET['b_building'] = 0;
            $PLANET['b_building_id'] = '';
        }
        else
        {
            $build_end_time = TIMESTAMP;
            $new_queue_array = [];
            foreach ($current_queue as $list_id_array)
            {
                if ($element == $list_id_array[0])
                {
                    continue;
                }

                $build_end_time += BuildFunctions::getBuildingTime(
                    $USER,
                    $PLANET,
                    $list_id_array[0],
                    $cost_resources,
                    $list_id_array[4] == 'destroy'
                );
                $list_id_array[3] = $build_end_time;
                $new_queue_array[] = $list_id_array;
            }

            if (!empty($new_queue_array))
            {
                $PLANET['b_building'] = TIMESTAMP;
                $PLANET['b_building_id'] = serialize($new_queue_array);
                $this->eco_obj->setData($USER, $PLANET);
                $this->eco_obj->SetNextQueueElementOnTop();
                list($USER, $PLANET) = $this->eco_obj->getData();
            }
            else
            {
                $PLANET['b_building'] = 0;
                $PLANET['b_building_id'] = '';
            }
        }
        return true;
    }

    private function RemoveBuildingFromQueue($queue_id): void
    {
        global $USER, $PLANET;
        if ($queue_id <= 1
            || empty($PLANET['b_building_id']))
        {
            return;
        }

        $current_queue = unserialize($PLANET['b_building_id']);
        $actual_count = count($current_queue);
        if ($actual_count <= 1)
        {
            $this->CancelBuildingFromQueue();
            return;
        }

        if ($queue_id - $actual_count >= 1)
        {
            // Avoid race conditions
            return;
        }

        $element = $current_queue[$queue_id - 1][0];
        $build_end_time = $current_queue[$queue_id - 2][3];
        unset($current_queue[$queue_id - 1]);
        $new_queue_array = [];
        foreach ($current_queue as $id => $list_id_array)
        {
            if ($id < $queue_id - 1)
            {
                $new_queue_array[] = $list_id_array;
            }
            else
            {
                if ($element == $list_id_array[0]
                    || empty($list_id_array[0]))
                {
                    continue;
                }

                $build_end_time += BuildFunctions::getBuildingTime(
                    $USER,
                    $PLANET,
                    $list_id_array[0],
                    null,
                    $list_id_array[4] == 'destroy',
                    $list_id_array[1]
                );
                $list_id_array[3] = $build_end_time;
                $new_queue_array[] = $list_id_array;
            }
        }

        if (!empty($new_queue_array))
        {
            $PLANET['b_building_id'] = serialize($new_queue_array);
        }
        else
        {
            $PLANET['b_building_id'] = "";
        }

    }

    private function AddBuildingToQueue($element, $add_mode = true): void
    {
        global $PLANET, $USER, $resource, $reslist, $pricelist, $config;

        if (!in_array($element, $reslist['allow'][$PLANET['planet_type']])
            || !BuildFunctions::isTechnologieAccessible($USER, $PLANET, $element)
            || ($element == 31 && $USER["b_tech_planet"] != 0)
            || (($element == 15 || $element == 21) && !empty($PLANET['b_shipyard_id']))
            || (!$add_mode && $PLANET[$resource[$element]] == 0)
        ) {
            return;
        }

        $current_queue = unserialize($PLANET['b_building_id'] ?? '');
        $demolished_queue = 0;

        if (!empty($current_queue))
        {
            $actual_count = count($current_queue);
            $demolished_queue = count($current_queue);
            foreach ($this->getQueueData()['queue'] as $queue_info)
            {
                if ($queue_info['destroy'])
                {

                    $demolished_queue = $demolished_queue - 2;
                }
                $demolished_queue = max(0, $demolished_queue);
            }
        }
        else
        {
            $current_queue = [];
            $actual_count = 0;
        }

        $current_max_fields = CalculateMaxPlanetFields($PLANET);

        if (($config->max_elements_build != 0 && $actual_count == $config->max_elements_build)
            || ($add_mode && $PLANET["field_current"] >= ($current_max_fields - $demolished_queue)))
        {
            return;
        }

        $build_mode = $add_mode ? 'build' : 'destroy';
        $build_level = $PLANET[$resource[$element]] + (int) $add_mode;

        if ($actual_count == 0)
        {
            if ($pricelist[$element]['max'] < $build_level)
            {
                return;
            }

            $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $element, !$add_mode, $build_level);

            if (!BuildFunctions::isElementBuyable($USER, $PLANET, $element, $cost_resources))
            {
                return;
            }

            if (isset($cost_resources[901]))
            {
                $PLANET[$resource[901]] -= $cost_resources[901];
            }
            if (isset($cost_resources[902]))
            {
                $PLANET[$resource[902]] -= $cost_resources[902];
            }
            if (isset($cost_resources[903]))
            {
                $PLANET[$resource[903]] -= $cost_resources[903];
            }
            if (isset($cost_resources[921]))
            {
                $USER[$resource[921]] -= $cost_resources[921];
            }

            $element_time = BuildFunctions::getBuildingTime($USER, $PLANET, $element, $cost_resources);
            $build_end_time = TIMESTAMP + $element_time;

            $PLANET['b_building_id'] = serialize([[$element, $build_level, $element_time, $build_end_time, $build_mode]]);
            $PLANET['b_building'] = $build_end_time;

        }
        else
        {
            $add_level = 0;
            foreach ($current_queue as $QueueSubArray)
            {
                if ($QueueSubArray[0] != $element)
                {
                    continue;
                }

                if ($QueueSubArray[4] == 'build')
                {
                    $add_level++;
                }
                else
                {
                    $add_level--;
                }
            }

            $build_level += $add_level;

            if (!$add_mode 
                && $build_level == 0)
            {
                return;
            }

            if ($pricelist[$element]['max'] < $build_level)
            {
                return;
            }

            $element_time = BuildFunctions::getBuildingTime($USER, $PLANET, $element, null, !$add_mode, $build_level);
            $build_end_time = $current_queue[$actual_count - 1][3] + $element_time;
            $current_queue[] = [$element, $build_level, $element_time, $build_end_time, $build_mode];
            $PLANET['b_building_id'] = serialize($current_queue);
        }

    }

    private function getQueueData(): array
    {
        global $LNG, $PLANET, $USER;

        $script_data = [];
        $quick_info = [];

        if ($PLANET['b_building'] == 0
            || $PLANET['b_building_id'] == "")
        {
            return ['queue' => $script_data, 'quickinfo' => $quick_info];
        }

        $build_queue = unserialize($PLANET['b_building_id']);

        foreach ($build_queue as $build_array)
        {
            if ($build_array[3] < TIMESTAMP)
            {
                continue;
            }

            $quick_info[$build_array[0]] = $build_array[1];

            $script_data[] = [
                'element'  => $build_array[0],
                'level'    => $build_array[1],
                'time'     => $build_array[2],
                'resttime' => ($build_array[3] - TIMESTAMP),
                'destroy'  => ($build_array[4] == 'destroy'),
                'endtime'  => _date('U', $build_array[3], $USER['timezone']),
                'display'  => _date($LNG['php_tdformat'], $build_array[3], $USER['timezone']),
            ];
        }

        return ['queue' => $script_data, 'quickinfo' => $quick_info];
    }

    public function show(): void
    {
        global $ProdGrid, $LNG, $resource, $reslist, $PLANET, $USER, $pricelist, $config, $requeriments;

        $cmd = HTTP::_GP('cmd', '');

        // wellformed buildURLs
        if (!empty($cmd)
            && $_SERVER['REQUEST_METHOD'] === 'POST'
            && $USER['urlaubs_modus'] == 0)
        {
            $element = HTTP::_GP('building', 0);
            $list_id = HTTP::_GP('listid', 0);
            switch ($cmd)
            {
                case 'cancel':
                    $this->CancelBuildingFromQueue();
                    break;
                case 'remove':
                    $this->RemoveBuildingFromQueue($list_id);
                    break;
                case 'insert':
                    $this->AddBuildingToQueue($element, true);
                    break;
                case 'destroy':
                    $this->AddBuildingToQueue($element, false);
                    break;
            }

            $this->redirectTo('game.php?page=buildings');
        }

        $queue_data = $this->getQueueData();
        $queue = $queue_data['queue'];
        $queue_count = count($queue);

        $queue_destroy = $queue_count;
        foreach ($queue as $queue_info)
        {
            if ($queue_info['destroy'])
            {

                $queue_destroy = $queue_destroy - 2;
            }
            $queue_destroy = max(0, $queue_destroy);
        }

        $can_build_element = inVacationMode($USER)
        || $config->max_elements_build == 0
        || $queue_count < $config->max_elements_build;
        $current_max_fields = CalculateMaxPlanetFields($PLANET);

        $room_is_ok = $PLANET['field_current'] < ($current_max_fields - $queue_destroy);

        $BuildEnergy = $USER[$resource[113]];
        $BuildLevelFactor = 10;
        $BuildTemp = $PLANET['temp_max'];

        $build_info_list = [];
        $elements = $reslist['allow'][$PLANET['planet_type']];

        foreach ($elements as $c_element)
        {
            if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $c_element)
                && !$config->show_unlearned_buildings)
            {
                continue;
            }

            $info_energy = "";
            $require_energy = 0;

            if (isset($queue_data['quickinfo'][$c_element]))
            {
                $level_to_build = $queue_data['quickinfo'][$c_element];
            }
            else
            {
                $level_to_build = $PLANET[$resource[$c_element]];
            }

            if (in_array($c_element, $reslist['prod']))
            {
                $BuildLevel = $PLANET[$resource[$c_element]];
                $need = eval(ResourceUpdate::getProd($ProdGrid[$c_element]['production'][911], $c_element));

                $BuildLevel = $level_to_build + 1;
                $prod = eval(ResourceUpdate::getProd($ProdGrid[$c_element]['production'][911], $c_element));

                $require_energy = $prod - $need;
                $require_energy = round($require_energy * $config->energySpeed);

                if ($require_energy < 0)
                {
                    $info_energy = sprintf($LNG['bd_need_engine'], pretty_number(abs($require_energy)), $LNG['tech'][911]);
                }
                else
                {
                    $info_energy = sprintf($LNG['bd_more_engine'], pretty_number(abs($require_energy)), $LNG['tech'][911]);
                }
            }

            $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $c_element, false, $level_to_build + 1);
            $cost_overflow = BuildFunctions::getRestPrice($USER, $PLANET, $c_element, $cost_resources);
            $element_time = BuildFunctions::getBuildingTime($USER, $PLANET, $c_element, $cost_resources);
            $destroy_resources = BuildFunctions::getElementPrice($USER, $PLANET, $c_element, true);
            $destroy_time = BuildFunctions::getBuildingTime($USER, $PLANET, $c_element, $destroy_resources);
            $destroy_overflow = BuildFunctions::getRestPrice($USER, $PLANET, $c_element, $destroy_resources);
            $buyable = $queue_count != 0 || BuildFunctions::isElementBuyable($USER, $PLANET, $c_element, $cost_resources);

            $require_array = [];

            if (isset($requeriments[$c_element]))
            {
                foreach ($requeriments[$c_element] as $require_id => $require_level)
                {
                    $require_array[] = [
                        'currentLevel' => ($require_id < 100) ? $PLANET[$resource[$require_id]] : $USER[$resource[$require_id]],
                        'neededLevel'  => $require_level,
                        'requireID'    => $require_id,
                    ];
                }

            }

            $build_info_list[$c_element] = [
                'level'               => $PLANET[$resource[$c_element]],
                'maxLevel'            => $pricelist[$c_element]['max'],
                'infoEnergyShort'     => pretty_number($require_energy),
                'infoEnergyLong'      => $info_energy,
                'costResources'       => $cost_resources,
                'costOverflow'        => $cost_overflow,
                'costOverflowTotal'   => array_sum($cost_overflow),
                'elementTime'         => $element_time,
                'destroyResources'    => $destroy_resources,
                'destroyTime'         => $destroy_time,
                'destroyOverflow'     => $destroy_overflow,
                'buyable'             => $buyable,
                'levelToBuild'        => $level_to_build,
                'technologySatisfied' => BuildFunctions::isTechnologieAccessible($USER, $PLANET, $c_element),
                'requeriments'        => $require_array,
            ];
        }

        if ($queue_count != 0)
        {
            $this->tpl_obj->loadscript('buildlist.js');
        }

        $this->assign([
            'BuildInfoList'   => $build_info_list,
            'CanBuildElement' => $can_build_element,
            'RoomIsOk'        => $room_is_ok,
            'Queue'           => $queue,
            'isBusy'          => ['shipyard' => !empty($PLANET['b_shipyard_id']), 'research' => $USER['b_tech_planet'] != 0],
            'HaveMissiles'    => (bool) $PLANET[$resource[503]] + $PLANET[$resource[502]],
            'usedField'       => $PLANET['field_current'],
            'maxField'        => CalculateMaxPlanetFields($PLANET),
            'userBuildPoints' => pretty_number($USER['build_points']),
        ]);

        $this->display('page.buildings.default.tpl');
    }
}
