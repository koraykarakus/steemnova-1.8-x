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

class ResourceUpdate
{
    /**
     * reference of the config object
     * @var Config
     */
    private $config = null;

    private bool $is_global_mode = false;
    private int $time = 0;
    private string $hash = '';
    private int $production_time = 0;

    private array $planet = [];
    private array $user = [];
    private $builded = [];
    private $build = false;
    private $tech = false;

    public function __construct($build = true, $tech = true)
    {
        $this->build = $build;
        $this->tech = $tech;
    }

    public function setData(array $user, array $planet): void
    {
        $this->user = $user;
        $this->planet = $planet;
    }

    public function getData(): array
    {
        return [$this->user, $this->planet];
    }

    public function ReturnVars(): array
    {
        if ($this->is_global_mode)
        {
            $GLOBALS['USER'] = $this->user;
            $GLOBALS['PLANET'] = $this->planet;
            return [];
        }
        else
        {
            return [$this->user, $this->planet];
        }
    }

    public function CreateHash(): string
    {
        global $RESLIST, $RESOURCE;
        $hash = [];
        foreach ($RESLIST['prod'] as $id)
        {
            $hash[] = $this->planet[$RESOURCE[$id]];
            $hash[] = $this->planet[$RESOURCE[$id].'_percent'];
        }

        $ressource = array_merge([], $RESLIST['resstype'][1], $RESLIST['resstype'][2]);
        foreach ($ressource as $id)
        {
            $hash[] = $this->config->{$RESOURCE[$id].'_basic_income'};
        }

        $hash[] = $this->config->resource_multiplier;
        $hash[] = $this->config->storage_multiplier;
        $hash[] = $this->config->energySpeed;
        $hash[] = $this->user['factor']['Resource'];
        $hash[] = $this->user['factor']['Energy'];
        $hash[] = $this->planet[$RESOURCE[22]];
        $hash[] = $this->planet[$RESOURCE[23]];
        $hash[] = $this->planet[$RESOURCE[24]];
        $hash[] = $this->user[$RESOURCE[131]];
        $hash[] = $this->user[$RESOURCE[132]];
        $hash[] = $this->user[$RESOURCE[133]];
        return md5(implode("::", $hash));
    }

    public function CalcResource(
        $user = null,
        $planet = null,
        $save = false,
        $time = null,
        $hash = true
    ): array {
        $this->is_global_mode = !isset($user, $planet) ? true : false;
        $this->user = $this->is_global_mode ? $GLOBALS['USER'] : $user;
        $this->planet = $this->is_global_mode ? $GLOBALS['PLANET'] : $planet;
        $this->time = is_null($time) ? TIMESTAMP : $time;
        $this->config = Config::get($this->user['universe']);

        if ($this->user['vacation_mode'] == 1)
        {
            return $this->ReturnVars();
        }

        if ($this->build)
        {
            $this->ShipyardQueue();
            if ($this->tech == true && $this->user['b_tech'] != 0 && $this->user['b_tech'] < $this->time)
            {
                $this->ResearchQueue();
            }
            if ($this->planet['b_building'] != 0)
            {
                $this->BuildingQueue();
            }
        }

        $this->UpdateResource($this->time, $hash);

        if ($save === true)
        {
            $this->SavePlanetToDB($this->user, $this->planet);
        }

        return $this->ReturnVars();
    }

    public function UpdateResource(int $time, bool $hash = false): void
    {
        $this->production_time = ($time - $this->planet['last_update']);

        if ($this->production_time > 0)
        {
            $this->planet['last_update'] = $time;
            if ($hash === false)
            {
                $this->ReBuildCache();
            }
            else
            {
                $this->hash = $this->CreateHash();

                if ($this->planet['eco_hash'] !== $this->hash)
                {
                    $this->planet['eco_hash'] = $this->hash;
                    $this->ReBuildCache();
                }
            }
            $this->ExecCalc();
        }
    }

    private function ExecCalc(): void
    {
        if ($this->planet['planet_type'] == 3)
        {
            return;
        }

        $max_metal_storage = $this->planet['metal_max'] * $this->config->max_overflow;
        $max_crystal_storage = $this->planet['crystal_max'] * $this->config->max_overflow;
        $max_deu_storage = $this->planet['deuterium_max'] * $this->config->max_overflow;

        $metal_theoretical = $this->production_time * (($this->config->metal_basic_income * $this->config->resource_multiplier) + $this->planet['metal_perhour']) / 3600;

        if ($metal_theoretical < 0)
        {
            $this->planet['metal'] = max($this->planet['metal'] + $metal_theoretical, 0);
        }
        elseif ($this->planet['metal'] <= $max_metal_storage)
        {
            $this->planet['metal'] = min($this->planet['metal'] + $metal_theoretical, $max_metal_storage);
        }

        $crystal_theoretical = $this->production_time * (($this->config->crystal_basic_income * $this->config->resource_multiplier) + $this->planet['crystal_perhour']) / 3600;
        if ($crystal_theoretical < 0)
        {
            $this->planet['crystal'] = max($this->planet['crystal'] + $crystal_theoretical, 0);
        }
        elseif ($this->planet['crystal'] <= $max_crystal_storage)
        {
            $this->planet['crystal'] = min($this->planet['crystal'] + $crystal_theoretical, $max_crystal_storage);
        }

        $deu_theoretical = $this->production_time * (($this->config->deuterium_basic_income * $this->config->resource_multiplier) + $this->planet['deuterium_perhour']) / 3600;
        if ($deu_theoretical < 0)
        {
            $this->planet['deuterium'] = max($this->planet['deuterium'] + $deu_theoretical, 0);
        }
        elseif ($this->planet['deuterium'] <= $max_deu_storage)
        {
            $this->planet['deuterium'] = min($this->planet['deuterium'] + $deu_theoretical, $max_deu_storage);
        }

        $this->planet['metal'] = max($this->planet['metal'], 0);
        $this->planet['crystal'] = max($this->planet['crystal'], 0);
        $this->planet['deuterium'] = max($this->planet['deuterium'], 0);
    }

    public static function getProd(string $calculation, $element = false): string
    {
        global $RESOURCE, $RESLIST, $USER, $PLANET;

        if ($element)
        {
            $build_energy = $USER[$RESOURCE[113]];
            $build_temp = $PLANET['temp_max'];
            $build_level_factor = $PLANET[$RESOURCE[$element] . '_percent'];

            if (in_array($element, array_merge($RESLIST['build'], $RESLIST['fleet'], $RESLIST['defense'])))
            {
                $build_level = $PLANET[$RESOURCE[$element]];
            }
            elseif (in_array($element, array_merge($RESLIST['tech'], $RESLIST['officers'])))
            {
                $build_level = $USER[$RESOURCE[$element]];
            }
            else
            {
                $build_level = 0;
            }

            $calculation = str_replace('this->', "", $calculation);
        }

        return 'return '.$calculation.';';
    }

    public static function getNetworkLevel(array $user, array $planet): array
    {
        global $RESOURCE;

        $research_level_list = [$planet[$RESOURCE[31]]];
        if ($user[$RESOURCE[123]] > 0)
        {
            $sql = 'SELECT '.$RESOURCE[31].' FROM %%PLANETS%% WHERE id != :planet_id AND id_owner = :user_id AND destroyed = 0 ORDER BY '.$RESOURCE[31].' DESC LIMIT :limit;';
            $research_result = Database::get()->select($sql, [
                ':limit'     => (int) $user[$RESOURCE[123]],
                ':planet_id' => $planet['id'],
                ':user_id'   => $user['id'],
            ]);

            foreach ($research_result as $research_row)
            {
                $research_level_list[] = $research_row[$RESOURCE[31]];
            }
        }

        return $research_level_list;
    }

    public function ReBuildCache(): void
    {
        global $PRODGRID, $RESOURCE, $RESLIST;

        if ($this->planet['planet_type'] == 3)
        {
            $this->config->metal_basic_income = 0;
            $this->config->crystal_basic_income = 0;
            $this->config->deuterium_basic_income = 0;
        }

        $temp = [
            901 => [
                'max'   => 0,
                'plus'  => 0,
                'minus' => 0,
            ],
            902 => [
                'max'   => 0,
                'plus'  => 0,
                'minus' => 0,
            ],
            903 => [
                'max'   => 0,
                'plus'  => 0,
                'minus' => 0,
            ],
            911 => [
                'plus'  => 0,
                'minus' => 0,
            ],
        ];

        $build_temp = $this->planet['temp_max'];
        $build_energy = $this->user[$RESOURCE[113]];

        foreach ($RESLIST['storage'] as $prod_id)
        {
            foreach ($RESLIST['resstype'][1] as $id)
            {
                if (!isset($PRODGRID[$prod_id]['storage'][$id]))
                {
                    continue;
                }

                $build_level = $this->planet[$RESOURCE[$prod_id]];
                $temp[$id]['max'] += round(eval(self::getProd($PRODGRID[$prod_id]['storage'][$id])));
            }
        }

        $ress_ids = array_merge([], $RESLIST['resstype'][1], $RESLIST['resstype'][2]);

        foreach ($RESLIST['prod'] as $prod_id)
        {
            $build_level_factor = $this->planet[$RESOURCE[$prod_id].'_percent'];
            $build_level = $this->planet[$RESOURCE[$prod_id]];

            foreach ($ress_ids as $id)
            {
                if (!isset($PRODGRID[$prod_id]['production'][$id]))
                {
                    continue;
                }

                $production = eval(self::getProd($PRODGRID[$prod_id]['production'][$id]));

                if ($production > 0)
                {
                    $temp[$id]['plus'] += $production;
                }
                else
                {
                    if (in_array($id, $RESLIST['resstype'][1]) && $this->planet[$RESOURCE[$id]] == 0)
                    {
                        continue;
                    }

                    $temp[$id]['minus'] += $production;
                }
            }
        }

        $this->planet['metal_max'] = $temp[901]['max'] * $this->config->storage_multiplier * (1 + $this->user['factor']['ResourceStorage']);
        $this->planet['crystal_max'] = $temp[902]['max'] * $this->config->storage_multiplier * (1 + $this->user['factor']['ResourceStorage']);
        $this->planet['deuterium_max'] = $temp[903]['max'] * $this->config->storage_multiplier * (1 + $this->user['factor']['ResourceStorage']);

        $this->planet['energy'] = round($temp[911]['plus'] * $this->config->energySpeed * (1 + $this->user['factor']['Energy']));
        $this->planet['energy_used'] = $temp[911]['minus'] * $this->config->energySpeed;
        if ($this->planet['energy_used'] == 0)
        {
            $this->planet['metal_perhour'] = 0;
            $this->planet['crystal_perhour'] = 0;
            $this->planet['deuterium_perhour'] = 0;
        }
        else
        {
            $prod_level = min(1, $this->planet['energy'] / abs($this->planet['energy_used']));

            $this->planet['metal_perhour'] = ($temp[901]['plus'] * (1 + $this->user['factor']['Resource'] + 0.02 * $this->user[$RESOURCE[131]]) * $prod_level + $temp[901]['minus']) * $this->config->resource_multiplier;
            $this->planet['crystal_perhour'] = ($temp[902]['plus'] * (1 + $this->user['factor']['Resource'] + 0.02 * $this->user[$RESOURCE[132]]) * $prod_level + $temp[902]['minus']) * $this->config->resource_multiplier;
            $this->planet['deuterium_perhour'] = ($temp[903]['plus'] * (1 + $this->user['factor']['Resource'] + 0.02 * $this->user[$RESOURCE[133]]) * $prod_level + $temp[903]['minus']) * $this->config->resource_multiplier;
        }
    }

    private function ShipyardQueue(): bool
    {
        global $RESOURCE;

        $build_queue = unserialize($this->planet['b_shipyard_id'] ?? '');
        if (!$build_queue)
        {
            $this->planet['b_shipyard'] = 0;
            $this->planet['b_shipyard_id'] = '';
            return false;
        }

        $this->planet['b_shipyard'] += ($this->time - $this->planet['last_update']);
        $build_array = [];
        foreach ($build_queue as $item)
        {
            $acum_time = BuildFunctions::getBuildingTime($this->user, $this->planet, $item[0]);
            $build_array[] = [$item[0], $item[1], $acum_time];
        }

        $new_queue = [];
        $done = false;
        foreach ($build_array as $item)
        {
            $element = $item[0];
            $count = $item[1];

            if ($done == false)
            {
                $build_time = $item[2];
                $element = (int) $element;
                if ($build_time == 0)
                {
                    if (!isset($this->builded[$element]))
                    {
                        $this->builded[$element] = 0;
                    }

                    $this->builded[$element] += $count;
                    $this->planet[$RESOURCE[$element]] += $count;
                    continue;
                }

                $build = max(min(floor($this->planet['b_shipyard'] / $build_time), $count), 0);

                if ($build == 0)
                {
                    $new_queue[] = [$element, $count];
                    $done = true;
                    continue;
                }

                if (!isset($this->builded[$element]))
                {
                    $this->builded[$element] = 0;
                }

                $this->builded[$element] += $build;
                $this->planet['b_shipyard'] -= $build * $build_time;
                $this->planet[$RESOURCE[$element]] += $build;
                $count -= $build;

                if ($count == 0)
                {
                    continue;
                }
                else
                {
                    $done = true;
                }
            }
            $new_queue[] = [$element, $count];
        }
        $this->planet['b_shipyard_id'] = !empty($new_queue) ? serialize($new_queue) : '';

        return true;
    }

    private function BuildingQueue(): void
    {
        while ($this->CheckPlanetBuildingQueue())
        {
            $this->SetNextQueueElementOnTop();
        }
    }

    private function CheckPlanetBuildingQueue(): bool
    {
        global $RESOURCE, $RESLIST;

        if (empty($this->planet['b_building_id'])
            || $this->planet['b_building'] > $this->time)
        {
            return false;
        }

        $current_queue = unserialize($this->planet['b_building_id']);

        $element = $current_queue[0][0];
        $build_end_time = $current_queue[0][3];
        $build_mode = $current_queue[0][4];

        if (!isset($this->builded[$element]))
        {
            $this->builded[$element] = 0;
        }

        if ($build_mode == 'build')
        {
            $this->planet['field_current'] += 1;
            $this->planet[$RESOURCE[$element]] += 1;
            $this->builded[$element] += 1;
        }
        else
        {
            $this->planet['field_current'] -= 1;
            $this->planet[$RESOURCE[$element]] -= 1;
            $this->builded[$element] -= 1;
        }

        array_shift($current_queue);
        $on_hash = in_array($element, $RESLIST['prod']);
        $this->UpdateResource($build_end_time, !$on_hash);

        if (count($current_queue) == 0)
        {
            $this->planet['b_building'] = 0;
            $this->planet['b_building_id'] = '';

            return false;
        }
        else
        {
            $this->planet['b_building_id'] = serialize($current_queue);
            return true;
        }
    }

    public function SetNextQueueElementOnTop(): bool
    {
        global $RESOURCE, $LNG;

        if (empty($this->planet['b_building_id']))
        {
            $this->planet['b_building'] = 0;
            $this->planet['b_building_id'] = '';
            return false;
        }

        $current_queue = unserialize($this->planet['b_building_id']);
        $loop = true;

        $build_end_time = 0;
        $new_queue = '';

        while ($loop === true)
        {
            $list_id_array = $current_queue[0];
            $element = $list_id_array[0];
            $level = $list_id_array[1];
            $build_mode = $list_id_array[4];
            $for_destroy = ($build_mode == 'destroy') ? true : false;

            $cost_resources = BuildFunctions::getElementPrice(
                $this->user,
                $this->planet,
                $element,
                $for_destroy,
                $level
            );

            $build_time = BuildFunctions::getBuildingTime(
                $this->user,
                $this->planet,
                $element,
                $cost_resources
            );

            $have_resources = BuildFunctions::isElementBuyable(
                $this->user,
                $this->planet,
                $element,
                $cost_resources
            );

            $build_end_time = $this->planet['b_building'] + $build_time;
            $current_queue[0] = [$element, $level, $build_time, $build_end_time, $build_mode];
            $have_no_more_level = false;

            if ($for_destroy
                && $this->planet[$RESOURCE[$element]] == 0)
            {
                $have_resources = false;
                $have_no_more_level = true;
            }

            if ($have_resources === true)
            {
                if (isset($cost_resources[901]))
                {
                    $this->planet[$RESOURCE[901]] -= $cost_resources[901];
                }
                if (isset($cost_resources[902]))
                {
                    $this->planet[$RESOURCE[902]] -= $cost_resources[902];
                }
                if (isset($cost_resources[903]))
                {
                    $this->planet[$RESOURCE[903]] -= $cost_resources[903];
                }
                if (isset($cost_resources[921]))
                {
                    $this->user[$RESOURCE[921]] -= $cost_resources[921];
                }
                $new_queue = serialize($current_queue);
                $loop = false;
            }
            else
            {
                if ($this->user['hof'] == 1)
                {
                    if ($have_no_more_level)
                    {
                        $message = sprintf($LNG['sys_nomore_level'], $LNG['tech'][$element]);
                    }
                    else
                    {
                        if (!isset($cost_resources[901]))
                        {
                            $cost_resources[901] = 0;
                        }
                        if (!isset($cost_resources[902]))
                        {
                            $cost_resources[902] = 0;
                        }
                        if (!isset($cost_resources[903]))
                        {
                            $cost_resources[903] = 0;
                        }

                        global $LNG;

                        if (empty($LNG))
                        {
                            // Fallback language
                            $LNG = new Language('en');
                            $LNG->includeData(['L18N', 'INGAME', 'TECH', 'CUSTOM']);
                        }

                        $message = sprintf(
                            $LNG['sys_notenough_money'],
                            $this->planet['name'],
                            $this->planet['id'],
                            $this->planet['galaxy'],
                            $this->planet['system'],
                            $this->planet['planet'],
                            $LNG['tech'][$element],
                            pretty_number($this->planet['metal']),
                            $LNG['tech'][901],
                            pretty_number($this->planet['crystal']),
                            $LNG['tech'][902],
                            pretty_number($this->planet['deuterium']),
                            $LNG['tech'][903],
                            pretty_number($cost_resources[901]),
                            $LNG['tech'][901],
                            pretty_number($cost_resources[902]),
                            $LNG['tech'][902],
                            pretty_number($cost_resources[903]),
                            $LNG['tech'][903]
                        );
                    }

                    PlayerUtil::sendMessage(
                        $this->user['id'],
                        0,
                        $LNG['sys_buildlist'],
                        99,
                        $LNG['sys_buildlist_fail'],
                        $message,
                        $this->time
                    );
                }

                array_shift($current_queue);

                if (count($current_queue) == 0)
                {
                    $build_end_time = 0;
                    $new_queue = '';
                    $loop = false;
                }
                else
                {
                    $base_time = $build_end_time - $build_time;
                    $new_queue = [];
                    foreach ($current_queue as $list_id_array)
                    {
                        $list_id_array[2] = BuildFunctions::getBuildingTime(
                            $this->user,
                            $this->planet,
                            $list_id_array[0],
                            null,
                            $list_id_array[4] == 'destroy'
                        );

                        $base_time += $list_id_array[2];
                        $list_id_array[3] = $base_time;
                        $new_queue[] = $list_id_array;
                    }
                    $current_queue = $new_queue;
                }
            }
        }

        $this->planet['b_building'] = $build_end_time;
        $this->planet['b_building_id'] = $new_queue;

        return true;
    }

    private function ResearchQueue(): void
    {
        while ($this->CheckUserTechQueue())
        {
            $this->SetNextQueueTechOnTop();
        }
    }

    private function CheckUserTechQueue(): bool
    {
        global $RESOURCE;

        if (empty($this->user['b_tech_id']) || $this->user['b_tech'] > $this->time)
        {
            return false;
        }

        if (!isset($this->builded[$this->user['b_tech_id']]))
        {
            $this->builded[$this->user['b_tech_id']] = 0;
        }

        $this->builded[$this->user['b_tech_id']] += 1;
        $this->user[$RESOURCE[$this->user['b_tech_id']]] += 1;

        $current_queue = unserialize($this->user['b_tech_queue']);
        array_shift($current_queue);

        $this->user['b_tech_id'] = 0;
        if (count($current_queue) == 0)
        {
            $this->user['b_tech'] = 0;
            $this->user['b_tech_id'] = 0;
            $this->user['b_tech_planet'] = 0;
            $this->user['b_tech_queue'] = '';
            return false;
        }
        else
        {
            $this->user['b_tech_queue'] = serialize(array_values($current_queue));
            return true;
        }
    }

    public function SetNextQueueTechOnTop(): void
    {
        global $RESOURCE, $LNG;

        if (empty($this->user['b_tech_queue']))
        {
            $this->user['b_tech'] = 0;
            $this->user['b_tech_id'] = 0;
            $this->user['b_tech_planet'] = 0;
            $this->user['b_tech_queue'] = '';
            return;
        }

        $current_queue = unserialize($this->user['b_tech_queue']);
        $loop = true;
        while ($loop == true)
        {
            $list_id_array = $current_queue[0];
            $is_another_planet = $list_id_array[4] != $this->planet['id'];
            if ($is_another_planet)
            {
                $sql = 'SELECT * FROM %%PLANETS%% WHERE id = :planetId;';
                $planet = Database::get()->selectSingle($sql, [
                    ':planetId' => $list_id_array[4],
                ]);

                $r_planet = new ResourceUpdate(true, false);
                list(, $planet) = $r_planet->CalcResource($this->user, $planet, false, $this->user['b_tech']);
            }
            else
            {
                $planet = $this->planet;
            }

            $planet[$RESOURCE[31].'_inter'] = self::getNetworkLevel($this->user, $planet);

            $element = $list_id_array[0];
            $level = $list_id_array[1];
            $cost_resources = BuildFunctions::getElementPrice($this->user, $planet, $element, false, $level);
            $build_time = BuildFunctions::getBuildingTime($this->user, $planet, $element, $cost_resources);
            $have_resources = BuildFunctions::isElementBuyable($this->user, $planet, $element, $cost_resources);
            $build_end_time = $this->user['b_tech'] + $build_time;
            $current_queue[0] = [$element, $level, $build_time, $build_end_time, $planet['id']];

            if ($have_resources == true)
            {
                if (isset($cost_resources[901]))
                {
                    $planet[$RESOURCE[901]] -= $cost_resources[901];
                }
                if (isset($cost_resources[902]))
                {
                    $planet[$RESOURCE[902]] -= $cost_resources[902];
                }
                if (isset($cost_resources[903]))
                {
                    $planet[$RESOURCE[903]] -= $cost_resources[903];
                }
                if (isset($cost_resources[921]))
                {
                    $this->user[$RESOURCE[921]] -= $cost_resources[921];
                }
                $this->user['b_tech_id'] = $element;
                $this->user['b_tech'] = $build_end_time;
                $this->user['b_tech_planet'] = $planet['id'];
                $this->user['b_tech_queue'] = serialize($current_queue);

                $loop = false;
            }
            else
            {
                if ($this->user['hof'] == 1)
                {
                    if (!isset($cost_resources[901]))
                    {
                        $cost_resources[901] = 0;
                    }
                    if (!isset($cost_resources[902]))
                    {
                        $cost_resources[902] = 0;
                    }
                    if (!isset($cost_resources[903]))
                    {
                        $cost_resources[903] = 0;
                    }

                    global $LNG;

                    if (empty($LNG))
                    {
                        // Fallback language
                        $LNG = new Language('en');
                        $LNG->includeData(['L18N', 'INGAME', 'TECH', 'CUSTOM']);
                    }

                    $message = sprintf(
                        $LNG['sys_notenough_money'],
                        $planet['name'],
                        $planet['id'],
                        $planet['galaxy'],
                        $planet['system'],
                        $planet['planet'],
                        $LNG['tech'][$element],
                        pretty_number($planet['metal']),
                        $LNG['tech'][901],
                        pretty_number($planet['crystal']),
                        $LNG['tech'][902],
                        pretty_number($planet['deuterium']),
                        $LNG['tech'][903],
                        pretty_number($cost_resources[901]),
                        $LNG['tech'][901],
                        pretty_number($cost_resources[902]),
                        $LNG['tech'][902],
                        pretty_number($cost_resources[903]),
                        $LNG['tech'][903]
                    );
                    PlayerUtil::sendMessage(
                        $this->user['id'],
                        0,
                        $LNG['sys_techlist'],
                        99,
                        $LNG['sys_buildlist_fail'],
                        $message,
                        $this->time
                    );
                }

                array_shift($current_queue);

                if (count($current_queue) == 0)
                {
                    $this->user['b_tech'] = 0;
                    $this->user['b_tech_id'] = 0;
                    $this->user['b_tech_planet'] = 0;
                    $this->user['b_tech_queue'] = '';

                    $loop = false;
                }
                else
                {
                    $base_time = $build_end_time - $build_time;
                    $new_queue = [];
                    foreach ($current_queue as $list_id_array)
                    {
                        $list_id_array[2] = BuildFunctions::getBuildingTime(
                            $this->user,
                            $planet,
                            $list_id_array[0]
                        );
                        $base_time += $list_id_array[2];
                        $list_id_array[3] = $base_time;
                        $new_queue[] = $list_id_array;
                    }
                    $current_queue = $new_queue;
                }
            }

            if ($is_another_planet)
            {
                $r_planet->SavePlanetToDB($this->user, $planet);
                $r_planet = null;
                unset($r_planet);
            }
            else
            {
                $this->planet = $planet;
            }
        }
    }

    public function SavePlanetToDB(array $USER = [], array $PLANET = []): array
    {
        global $RESOURCE, $RESLIST;

        if (empty($USER))
        {
            global $USER;
        }

        if (empty($PLANET))
        {
            global $PLANET;
        }

        $buildQueries = [];

        $params = [
            ':user_id'           => $USER['id'],
            ':planet_id'         => $PLANET['id'],
            ':metal'             => $PLANET['metal'],
            ':crystal'           => $PLANET['crystal'],
            ':deuterium'         => $PLANET['deuterium'],
            ':ecoHash'           => $PLANET['eco_hash'],
            ':last_update_time'  => $PLANET['last_update'],
            ':b_building'        => $PLANET['b_building'],
            ':b_building_id'     => $PLANET['b_building_id'],
            ':field_current'     => $PLANET['field_current'],
            ':b_shipyard_id'     => $PLANET['b_shipyard_id'],
            ':metal_perhour'     => $PLANET['metal_perhour'],
            ':crystal_perhour'   => $PLANET['crystal_perhour'],
            ':deuterium_perhour' => $PLANET['deuterium_perhour'],
            ':metal_max'         => $PLANET['metal_max'],
            ':crystal_max'       => $PLANET['crystal_max'],
            ':deuterium_max'     => $PLANET['deuterium_max'],
            ':energy_used'       => $PLANET['energy_used'],
            ':energy'            => $PLANET['energy'],
            ':b_shipyard'        => $PLANET['b_shipyard'],
            ':darkmatter'        => $USER['darkmatter'],
            ':b_tech'            => $USER['b_tech'],
            ':b_tech_id'         => $USER['b_tech_id'],
            ':b_tech_planet'     => $USER['b_tech_planet'],
            ':b_tech_queue'      => $USER['b_tech_queue'],
        ];

        if (!empty($this->builded))
        {
            foreach ($this->builded as $element => $count)
            {
                $element = (int) $element;

                if (empty($RESOURCE[$element]) || empty($count))
                {
                    continue;
                }

                if (in_array($element, $RESLIST['one']))
                {
                    $buildQueries[] = ', p.'.$RESOURCE[$element].' = :'.$RESOURCE[$element];
                    $params[':'.$RESOURCE[$element]] = '1';
                }
                elseif (isset($PLANET[$RESOURCE[$element]]))
                {
                    $buildQueries[] = ', p.'.$RESOURCE[$element].' = p.'.$RESOURCE[$element].' + :'.$RESOURCE[$element];
                    $params[':'.$RESOURCE[$element]] = floatToString($count);
                }
                elseif (isset($USER[$RESOURCE[$element]]))
                {
                    $buildQueries[] = ', u.'.$RESOURCE[$element].' = u.'.$RESOURCE[$element].' + :'.$RESOURCE[$element];
                    $params[':'.$RESOURCE[$element]] = floatToString($count);
                }
            }
        }

        $sql = 'UPDATE %%PLANETS%% as p,%%USERS%% as u SET
		p.metal				= :metal,
		p.crystal			= :crystal,
		p.deuterium			= :deuterium,
		p.eco_hash			= :ecoHash,
		p.last_update		= :last_update_time,
		p.b_building		= :b_building,
		p.b_building_id 	= :b_building_id,
		p.field_current 	= :field_current,
		p.b_shipyard_id		= :b_shipyard_id,
		p.metal_perhour		= :metal_perhour,
		p.crystal_perhour	= :crystal_perhour,
		p.deuterium_perhour	= :deuterium_perhour,
		p.metal_max			= :metal_max,
		p.crystal_max		= :crystal_max,
		p.deuterium_max		= :deuterium_max,
		p.energy_used		= :energy_used,
		p.energy			= :energy,
		p.b_shipyard		= :b_shipyard,
		u.darkmatter		= :darkmatter,
		u.b_tech			= :b_tech,
		u.b_tech_id			= :b_tech_id,
		u.b_tech_planet		= :b_tech_planet,
		u.b_tech_queue		= :b_tech_queue
		'.implode("\n", $buildQueries).'
		WHERE p.id = :planet_id AND u.id = :user_id AND p.version = p.version;';

        Database::get()->update($sql, $params);

        $this->builded = [];

        return [$USER, $PLANET];
    }
}
