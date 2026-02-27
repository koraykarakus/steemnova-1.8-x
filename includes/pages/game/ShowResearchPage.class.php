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

require_once('AbstractGamePage.class.php');

class ShowResearchPage extends AbstractGamePage
{
    public static $require_module = MODULE_RESEARCH;

    public function __construct()
    {
        parent::__construct();
    }

    private function CheckLabSettingsInQueue(): bool
    {
        global $USER;
        $db = Database::get();
        $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :owner;";
        $planets = $db->select($sql, [
            ':owner' => $USER['id'],
        ]);

        foreach ($planets as $c_planet)
        {
            if ($c_planet['b_building'] == 0)
            {
                continue;
            }

            $current_queue = unserialize($c_planet['b_building_id'] ?? '');
            foreach ($current_queue as $c_id)
            {
                if ($c_id[0] == 6
                    || $c_id[0] == 31)
                {
                    return false;
                }
            }
        }

        return true;
    }

    private function CancelBuildingFromQueue(): bool
    {
        global $PLANET, $USER, $resource;
        $current_queue = unserialize($USER['b_tech_queue']);

        if (empty($current_queue)
            || empty($USER['b_tech']))
        {
            $USER['b_tech_queue'] = '';
            $USER['b_tech_planet'] = 0;
            $USER['b_tech_id'] = 0;
            $USER['b_tech'] = 0;

            return false;
        }

        $db = Database::get();

        $element_id = $USER['b_tech_id'];

        $cost_resources = BuildFunctions::getElementPrice(
            $USER,
            $PLANET,
            $element_id,
            false,
            $USER[$resource[$element_id]] + 1
        );

        if ($PLANET['id'] == $USER['b_tech_planet'])
        {
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
        }
        else
        {
            $params = ['techPlanet' => $USER['b_tech_planet']];
            $sql = "UPDATE %%PLANETS%% SET ";
            if (isset($cost_resources[901]))
            {
                $sql .= $resource[901]." = ".$resource[901]." + :".$resource[901].", ";
                $params[':'.$resource[901]] = $cost_resources[901];
            }
            if (isset($cost_resources[902]))
            {
                $sql .= $resource[902]." = ".$resource[902]." + :".$resource[902].", ";
                $params[':'.$resource[902]] = $cost_resources[902];
            }
            if (isset($cost_resources[903]))
            {
                $sql .= $resource[903]." = ".$resource[903]." + :".$resource[903].", ";
                $params[':'.$resource[903]] = $cost_resources[903];
            }

            $sql = substr($sql, 0, -2);
            $sql .= " WHERE id = :techPlanet;";

            $db->update($sql, $params);
        }

        if (isset($cost_resources[921]))
        {
            $USER[$resource[921]] += $cost_resources[921];
        }

        $USER['b_tech_id'] = 0;
        $USER['b_tech'] = 0;
        $USER['b_tech_planet'] = 0;

        array_shift($current_queue);

        if (count($current_queue) == 0)
        {
            $USER['b_tech_queue'] = '';
            $USER['b_tech_planet'] = 0;
            $USER['b_tech_id'] = 0;
            $USER['b_tech'] = 0;
        }
        else
        {
            $build_end_time = TIMESTAMP;
            $new_current_queue = [];
            foreach ($current_queue as $c_id)
            {
                if ($element_id == $c_id[0]
                    || empty($c_id[0]))
                {
                    continue;
                }

                if ($c_id[4] != $PLANET['id'])
                {
                    $sql = "SELECT :resource6, :resource31, id FROM %%PLANETS%% WHERE id = :id;";
                    $cplanet = $db->selectSingle($sql, [
                        ':resource6'  => $resource[6],
                        ':resource31' => $resource[31],
                        ':id'         => $c_id[4],
                    ]);
                }
                else
                {
                    $cplanet = $PLANET;
                }

                $cplanet[$resource[31].'_inter'] = $this->eco_obj->getNetworkLevel($USER, $cplanet);
                $build_end_time += BuildFunctions::getBuildingTime(
                    $USER,
                    $cplanet,
                    $c_id[0],
                    null,
                    false,
                    $c_id[1]
                );
                $c_id[3] = $build_end_time;
                $new_current_queue[] = $c_id;
            }

            if (!empty($new_current_queue))
            {
                $USER['b_tech'] = TIMESTAMP;
                $USER['b_tech_queue'] = serialize($new_current_queue);
                $this->eco_obj->setData($USER, $PLANET);
                $this->eco_obj->SetNextQueueTechOnTop();
                list($USER, $PLANET) = $this->eco_obj->getData();
            }
            else
            {
                $USER['b_tech'] = 0;
                $USER['b_tech_queue'] = '';
            }
        }

        return true;
    }

    private function RemoveBuildingFromQueue($queue_id): bool
    {
        global $USER, $PLANET, $resource;

        $current_queue = unserialize($USER['b_tech_queue']);
        if ($queue_id <= 1
            || empty($current_queue))
        {
            return false;
        }

        $actual_count = count($current_queue);
        if ($actual_count <= 1)
        {
            return $this->CancelBuildingFromQueue();
        }

        if (!isset($current_queue[$queue_id - 2]))
        {
            return false;
        }

        $element_id = $current_queue[$queue_id - 2][0];
        $build_end_time = $current_queue[$queue_id - 2][3];
        unset($current_queue[$queue_id - 1]);
        $new_current_queue = [];
        foreach ($current_queue as $ID => $c_list_id_array)
        {
            if ($ID < $queue_id - 1)
            {
                $new_current_queue[] = $c_list_id_array;
            }
            else
            {
                if ($element_id == $c_list_id_array[0])
                {
                    continue;
                }

                if ($c_list_id_array[4] != $PLANET['id'])
                {
                    $db = Database::get();

                    $sql = "SELECT :resource6, :resource31, id FROM %%PLANETS%% WHERE id = :id;";
                    $cplanet = $db->selectSingle($sql, [
                        ':resource6'  => $resource[6],
                        ':resource31' => $resource[31],
                        ':id'         => $c_list_id_array[4],
                    ]);
                }
                else
                {
                    $cplanet = $PLANET;
                }

                $cplanet[$resource[31].'_inter'] = $this->eco_obj->getNetworkLevel($USER, $cplanet);

                $build_end_time += BuildFunctions::getBuildingTime(
                    $USER,
                    $cplanet,
                    null,
                    $c_list_id_array[0]
                );

                $c_list_id_array[3] = $build_end_time;
                $new_current_queue[] = $c_list_id_array;
            }
        }

        if (!empty($new_current_queue))
        {
            $USER['b_tech_queue'] = serialize($new_current_queue);
        }
        else
        {
            $USER['b_tech_queue'] = "";
        }

        return true;
    }

    private function AddBuildingToQueue($elementId, $AddMode = true): bool
    {
        global $PLANET, $USER, $resource, $reslist, $pricelist;
        $config = Config::get();

        if (!in_array($elementId, $reslist['tech'])
            || !BuildFunctions::isTechnologieAccessible($USER, $PLANET, $elementId)
            || !$this->CheckLabSettingsInQueue($PLANET))
        {
            return false;
        }

        $CurrentQueue = unserialize($USER['b_tech_queue'] ?? '');

        if (!empty($CurrentQueue))
        {
            $ActualCount = count($CurrentQueue);
        }
        else
        {
            $CurrentQueue = [];
            $ActualCount = 0;
        }

        if ($config->max_elements_tech != 0
            && $config->max_elements_tech <= $ActualCount)
        {
            return false;
        }

        $BuildLevel = $USER[$resource[$elementId]] + 1;
        if ($ActualCount == 0)
        {
            if ($pricelist[$elementId]['max'] < $BuildLevel)
            {
                return false;
            }

            $costResources = BuildFunctions::getElementPrice($USER, $PLANET, $elementId, !$AddMode, $BuildLevel);

            if (!BuildFunctions::isElementBuyable($USER, $PLANET, $elementId, $costResources))
            {
                return false;
            }

            if (isset($costResources[901]))
            {
                $PLANET[$resource[901]] -= $costResources[901];
            }
            if (isset($costResources[902]))
            {
                $PLANET[$resource[902]] -= $costResources[902];
            }
            if (isset($costResources[903]))
            {
                $PLANET[$resource[903]] -= $costResources[903];
            }
            if (isset($costResources[921]))
            {
                $USER[$resource[921]] -= $costResources[921];
            }

            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $elementId, $costResources);
            $BuildEndTime = TIMESTAMP + $elementTime;

            $USER['b_tech_queue'] = serialize([[$elementId, $BuildLevel, $elementTime, $BuildEndTime, $PLANET['id']]]);
            $USER['b_tech'] = $BuildEndTime;
            $USER['b_tech_id'] = $elementId;
            $USER['b_tech_planet'] = $PLANET['id'];
        }
        else
        {
            $addLevel = 0;
            foreach ($CurrentQueue as $QueueSubArray)
            {
                if ($QueueSubArray[0] != $elementId)
                {
                    continue;
                }

                $addLevel++;
            }

            $BuildLevel += $addLevel;

            if ($pricelist[$elementId]['max'] < $BuildLevel)
            {
                return false;
            }

            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $elementId, null, !$AddMode, $BuildLevel);

            $BuildEndTime = $CurrentQueue[$ActualCount - 1][3] + $elementTime;
            $CurrentQueue[] = [$elementId, $BuildLevel, $elementTime, $BuildEndTime, $PLANET['id']];
            $USER['b_tech_queue'] = serialize($CurrentQueue);
        }
        return true;
    }

    private function getQueueData(): array
    {
        global $LNG, $PLANET, $USER;

        $script_data = [];
        $quick_info = [];

        if ($USER['b_tech'] == 0)
        {
            return ['queue' => $script_data, 'quickinfo' => $quick_info];
        }

        $current_queue = unserialize($USER['b_tech_queue']);

        foreach ($current_queue as $build_array)
        {
            if ($build_array[3] < TIMESTAMP)
            {
                continue;
            }

            $planet_name = '';

            $quick_info[$build_array[0]] = $build_array[1];

            if ($build_array[4] != $PLANET['id'])
            {
                $planet_name = $USER['PLANETS'][$build_array[4]]['name'];
            }

            $script_data[] = [
                'element'  => $build_array[0],
                'level'    => $build_array[1],
                'time'     => $build_array[2],
                'resttime' => ($build_array[3] - TIMESTAMP),
                'destroy'  => ($build_array[4] == 'destroy'),
                'endtime'  => _date('U', $build_array[3], $USER['timezone']),
                'display'  => _date($LNG['php_tdformat'], $build_array[3], $USER['timezone']),
                'planet'   => $planet_name,
            ];
        }

        return ['queue' => $script_data, 'quickinfo' => $quick_info];
    }

    public function show(): void
    {
        global $PLANET, $USER, $LNG, $resource, $reslist, $pricelist, $config, $requeriments;

        if ($PLANET[$resource[31]] == 0
            && !$config->show_tech_no_research)
        {
            $this->printMessage($LNG['bd_lab_required']);
        }

        $cmd = HTTP::_GP('cmd', '');

        $element_id = HTTP::_GP('tech', 0);
        $list_id = HTTP::_GP('listid', 0);

        $PLANET[$resource[31].'_inter'] = ResourceUpdate::getNetworkLevel($USER, $PLANET);

        if (!empty($cmd)
            && $_SERVER['REQUEST_METHOD'] === 'POST'
            && $USER['urlaubs_modus'] == 0)
        {
            switch ($cmd)
            {
                case 'cancel':
                    $this->CancelBuildingFromQueue();
                    break;
                case 'remove':
                    $this->RemoveBuildingFromQueue($list_id);
                    break;
                case 'insert':
                    $this->AddBuildingToQueue($element_id, true);
                    break;
                case 'destroy':
                    $this->AddBuildingToQueue($element_id, false);
                    break;
            }

            $this->redirectTo('game.php?page=research');
        }

        $continue = $this->CheckLabSettingsInQueue($PLANET);

        $queue_data = $this->getQueueData();
        $tech_queue = $queue_data['queue'];
        $queue_count = count($tech_queue);

        if ($queue_count != 0)
        {
            $this->tplObj->loadscript('research.js');
        }

        $research_list = [];

        foreach ($reslist['tech'] as $c_element_id)
        {
            if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $c_element_id)
                && !$config->show_unlearned_ships)
            {
                continue;
            }

            if (isset($queue_data['quickinfo'][$c_element_id]))
            {
                $level_to_build = $queue_data['quickinfo'][$c_element_id];
            }
            else
            {
                $level_to_build = $USER[$resource[$c_element_id]];
            }

            $cost_resources = BuildFunctions::getElementPrice(
                $USER,
                $PLANET,
                $c_element_id,
                false,
                $level_to_build + 1
            );

            $cost_overflow = BuildFunctions::getRestPrice($USER, $PLANET, $c_element_id, $cost_resources);
            $element_time = BuildFunctions::getBuildingTime($USER, $PLANET, $c_element_id, $cost_resources);
            $buyable = $queue_count != 0 || BuildFunctions::isElementBuyable(
                $USER,
                $PLANET,
                $c_element_id,
                $cost_resources
            );

            $requireArray = [];

            if (isset($requeriments[$c_element_id]))
            {
                foreach ($requeriments[$c_element_id] as $require_id => $require_level)
                {
                    $requireArray[] = [
                        'currentLevel' => ($require_id < 100) ?
                                        $PLANET[$resource[$require_id]] :
                                        $USER[$resource[$require_id]],
                        'neededLevel' => $require_level,
                        'requireID'   => $require_id,
                    ];
                }

            }

            $research_list[$c_element_id] = [
                'id'                  => $c_element_id,
                'level'               => $USER[$resource[$c_element_id]],
                'maxLevel'            => $pricelist[$c_element_id]['max'],
                'costResources'       => $cost_resources,
                'costOverflow'        => $cost_overflow,
                'costOverflowTotal'   => array_sum($cost_overflow),
                'elementTime'         => $element_time,
                'buyable'             => $buyable,
                'levelToBuild'        => $level_to_build,
                'technologySatisfied' => BuildFunctions::isTechnologieAccessible($USER, $PLANET, $c_element_id),
                'requeriments'        => $requireArray,
            ];
        }

        $this->assign([
            'ResearchList'   => $research_list,
            'IsLabinBuild'   => !$continue,
            'IsFullQueue'    => $config->max_elements_tech == 0 || $config->max_elements_tech == count($tech_queue),
            'Queue'          => $tech_queue,
            'userTechPoints' => pretty_number($USER['tech_points']),
        ]);

        $this->display('page.research.default.tpl');
    }
}
