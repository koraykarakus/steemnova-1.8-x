<?php

class Buildings
{
    protected static array $facility_ids = [14, 15, 21, 31, 33, 34, 41, 42, 43, 44];

    private static function isFacility($id): bool
    {
        return in_array($id, self::$facility_ids);
    }

    public static function filterElements($elements, $type): array
    {
        $arr = [];
        foreach ($elements as $id)
        {
            if ($type === 1
                && self::isFacility($id))
            {
                continue;
            }
            elseif ($type === 2
                && !self::isFacility($id))
            {
                continue;
            }

            $arr[] = $id;
        }

        return $arr;
    }

    public static function getQueueData(): array
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

    public static function AddBuildingToQueue($element, $add_mode = true): void
    {
        global $PLANET, $USER, $RESOURCE, $RESLIST, $PRICELIST, $config;

        if (!in_array($element, $RESLIST['allow'][$PLANET['planet_type']])
            || !BuildFunctions::isTechnologieAccessible($USER, $PLANET, $element)
            || ($element == 31 && $USER["b_tech_planet"] != 0)
            || (($element == 15 || $element == 21) && !empty($PLANET['b_shipyard_id']))
            || (!$add_mode && $PLANET[$RESOURCE[$element]] == 0)
        ) {
            return;
        }

        $current_queue = unserialize($PLANET['b_building_id'] ?? '');
        $demolished_queue = 0;

        if (!empty($current_queue))
        {
            $actual_count = count($current_queue);
            $demolished_queue = count($current_queue);
            foreach (Buildings::getQueueData()['queue'] as $queue_info)
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
        $build_level = $PLANET[$RESOURCE[$element]] + (int) $add_mode;

        if ($actual_count == 0)
        {
            if ($PRICELIST[$element]['max'] < $build_level)
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

            if ($PRICELIST[$element]['max'] < $build_level)
            {
                return;
            }

            $element_time = BuildFunctions::getBuildingTime($USER, $PLANET, $element, null, !$add_mode, $build_level);
            $build_end_time = $current_queue[$actual_count - 1][3] + $element_time;
            $current_queue[] = [$element, $build_level, $element_time, $build_end_time, $build_mode];
            $PLANET['b_building_id'] = serialize($current_queue);
        }

    }

    public static function CancelBuildingFromQueue($eco_obj): bool
    {
        global $PLANET, $USER, $RESOURCE;
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
            $PLANET[$RESOURCE[901]] += $cost_resources[901];
        }
        if (isset($cost_resources[902]))
        {
            $PLANET[$RESOURCE[902]] += $cost_resources[902];
        }
        if (isset($cost_resources[903]))
        {
            $PLANET[$RESOURCE[903]] += $cost_resources[903];
        }
        if (isset($cost_resources[921]))
        {
            $USER[$RESOURCE[921]] += $cost_resources[921];
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
                $eco_obj->setData($USER, $PLANET);
                $eco_obj->SetNextQueueElementOnTop();
                list($USER, $PLANET) = $eco_obj->getData();
            }
            else
            {
                $PLANET['b_building'] = 0;
                $PLANET['b_building_id'] = '';
            }
        }
        return true;
    }

    public static function RemoveBuildingFromQueue($queue_id, $eco_obj): void
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
            self::CancelBuildingFromQueue($eco_obj);
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
}
