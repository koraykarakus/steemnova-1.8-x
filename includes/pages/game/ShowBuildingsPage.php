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

    public function show(): void
    {
        global $PRODGRID, $LNG, $RESOURCE, $RESLIST, $PLANET, $USER, $PRICELIST, $config, $REQUIREMENTS;

        $cmd = HTTP::_GP('cmd', '');

        // wellformed buildURLs
        if (!empty($cmd)
            && $_SERVER['REQUEST_METHOD'] === 'POST'
            && $USER['vacation_mode'] == 0)
        {
            $element = HTTP::_GP('building', 0);
            $list_id = HTTP::_GP('listid', 0);
            switch ($cmd)
            {
                case 'cancel':
                    Buildings::CancelBuildingFromQueue($this->eco_obj);
                    break;
                case 'remove':
                    Buildings::RemoveBuildingFromQueue($list_id, $this->eco_obj);
                    break;
                case 'insert':
                    Buildings::AddBuildingToQueue($element, true);
                    break;
                case 'destroy':
                    Buildings::AddBuildingToQueue($element, false);
                    break;
            }

            $this->redirectTo('game.php?page=buildings');
        }

        $queue_data = Buildings::getQueueData();
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

        $build_energy = $USER[$RESOURCE[113]];
        $build_level_factor = 10;
        $build_temp = $PLANET['temp_max'];

        $build_info_list = [];
        $elements = Buildings::filterElements($RESLIST['allow'][$PLANET['planet_type']], 1);

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
                $level_to_build = $PLANET[$RESOURCE[$c_element]];
            }

            if (in_array($c_element, $RESLIST['prod']))
            {
                $build_level = $PLANET[$RESOURCE[$c_element]];
                $need = eval(ResourceUpdate::getProd($PRODGRID[$c_element]['production'][911], $c_element));

                $build_level = $level_to_build + 1;
                $prod = eval(ResourceUpdate::getProd($PRODGRID[$c_element]['production'][911], $c_element));

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

            if (isset($REQUIREMENTS[$c_element]))
            {
                foreach ($REQUIREMENTS[$c_element] as $require_id => $require_level)
                {
                    $require_array[] = [
                        'current_level' => ($require_id < 100) ? $PLANET[$RESOURCE[$require_id]] : $USER[$RESOURCE[$require_id]],
                        'needed_level'  => $require_level,
                        'require_id'    => $require_id,
                    ];
                }

            }

            $build_info_list[$c_element] = [
                'level'                => $PLANET[$RESOURCE[$c_element]],
                'max_level'            => $PRICELIST[$c_element]['max'],
                'info_energy_short'    => pretty_number($require_energy),
                'info_energy_long'     => $info_energy,
                'cost_resources'       => $cost_resources,
                'cost_overflow'        => $cost_overflow,
                'cost_overflow_total'  => array_sum($cost_overflow),
                'element_time'         => $element_time,
                'destroy_resources'    => $destroy_resources,
                'destroy_time'         => $destroy_time,
                'destroy_overflow'     => $destroy_overflow,
                'buyable'              => $buyable,
                'level_to_build'       => $level_to_build,
                'technology_satisfied' => BuildFunctions::isTechnologieAccessible($USER, $PLANET, $c_element),
                'requirements'         => $require_array,
            ];
        }

        if ($queue_count != 0)
        {
            $this->tpl_obj->loadscript('buildlist.js');
        }

        $this->assign([
            'build_info_list'   => $build_info_list,
            'can_build_element' => $can_build_element,
            'room_is_ok'        => $room_is_ok,
            'queue'             => $queue,
            'is_busy'           => ['shipyard' => !empty($PLANET['b_shipyard_id']), 'research' => $USER['b_tech_planet'] != 0],
            'have_missiles'     => (bool) $PLANET[$RESOURCE[503]] + $PLANET[$RESOURCE[502]],
            'used_field'        => $PLANET['field_current'],
            'max_field'         => CalculateMaxPlanetFields($PLANET),
            'user_build_points' => pretty_number($USER['build_points']),
        ]);

        $this->display('page.buildings.default.tpl');
    }
}
