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

class ShowResourcesPage extends AbstractGamePage
{
    public static $require_module = MODULE_RESSOURCE_LIST;

    public function __construct()
    {
        parent::__construct();
    }

    public function send(): void
    {
        global $RESOURCE, $USER, $PLANET;

        if ($USER['vacation_mode'] == 0)
        {
            $update_sql = [];
            if (!isset($_POST['prod']))
            {
                $_POST['prod'] = [];
            }

            $param = [':planetId' => $PLANET['id']];

            foreach ($_POST['prod'] as $resource_id => $val)
            {
                $field_name = $RESOURCE[$resource_id].'_percent';
                if (!isset($PLANET[$field_name])
                    || !in_array($val, range(0, 10)))
                {
                    continue;
                }

                $update_sql[] = $field_name." = :".$field_name;
                $param[':'.$field_name] = (int) $val;
                $PLANET[$field_name] = $val;
            }

            if (!empty($update_sql))
            {
                $sql = 'UPDATE %%PLANETS%% SET '.implode(', ', $update_sql).' WHERE id = :planetId;';

                Database::get()->update($sql, $param);

                $this->eco_obj->setData($USER, $PLANET);
                $this->eco_obj->ReBuildCache();
                list($USER, $PLANET) = $this->eco_obj->getData();
                $PLANET['eco_hash'] = $this->eco_obj->CreateHash();
            }
        }

        $this->save();
        $this->redirectTo('game.php?page=resources');
    }

    public function show(): void
    {
        global $LNG, $PRODGRID, $RESOURCE, $RESLIST, $USER, $PLANET;

        $config = Config::get();

        if ($USER['vacation_mode'] == 1
            || $PLANET['planet_type'] != 1)
        {
            $basic_income[901] = 0;
            $basic_income[902] = 0;
            $basic_income[903] = 0;
            $basic_income[911] = 0;
        }
        else
        {
            $basic_income[901] = $config->{$RESOURCE[901].'_basic_income'};
            $basic_income[902] = $config->{$RESOURCE[902].'_basic_income'};
            $basic_income[903] = $config->{$RESOURCE[903].'_basic_income'};
            $basic_income[911] = $config->{$RESOURCE[911].'_basic_income'};
        }

        $temp = [
            901 => [
                'plus'  => 0,
                'minus' => 0,
            ],
            902 => [
                'plus'  => 0,
                'minus' => 0,
            ],
            903 => [
                'plus'  => 0,
                'minus' => 0,
            ],
            911 => [
                'plus'  => 0,
                'minus' => 0,
            ],
        ];

        $ress_ids = array_merge([], $RESLIST['resstype'][1], $RESLIST['resstype'][2]);

        $prod_level = 0;
        if ($PLANET['energy_used'] != 0)
        {
            $prod_level = min(1, $PLANET['energy'] / abs($PLANET['energy_used']));
        }

        /* Data for eval */
        $BuildEnergy = $USER[$RESOURCE[113]];
        $BuildTemp = $PLANET['temp_max'];
        $production_list = [];
        foreach ($RESLIST['prod'] as $c_prod_id)
        {
            if (isset($PLANET[$RESOURCE[$c_prod_id]])
                && $PLANET[$RESOURCE[$c_prod_id]] == 0)
            {
                continue;
            }

            if (isset($USER[$RESOURCE[$c_prod_id]])
                && $USER[$RESOURCE[$c_prod_id]] == 0)
            {
                continue;
            }

            $production_list[$c_prod_id] = [
                'production'   => [901 => 0, 902 => 0, 903 => 0, 911 => 0],
                'elementLevel' => $PLANET[$RESOURCE[$c_prod_id]],
                'prodLevel'    => $PLANET[$RESOURCE[$c_prod_id].'_percent'],
            ];

            /* Data for eval */
            $build_level = $PLANET[$RESOURCE[$c_prod_id]];
            $build_level_factor = $PLANET[$RESOURCE[$c_prod_id].'_percent'];

            foreach ($ress_ids as $c_id)
            {
                if (!isset($PRODGRID[$c_prod_id]['production'][$c_id]))
                {
                    continue;
                }

                $production = eval(ResourceUpdate::getProd(
                    $PRODGRID[$c_prod_id]['production'][$c_id],
                    $c_prod_id
                ));

                if (in_array($c_id, $RESLIST['resstype'][2]))
                {
                    $production *= $config->energySpeed;
                }
                else
                {
                    $production *= $prod_level * $config->resource_multiplier;
                }

                $production_list[$c_prod_id]['production'][$c_id] = $production;

                if ($production > 0)
                {
                    if ($PLANET[$RESOURCE[$c_id]] == 0)
                    {
                        continue;
                    }

                    $temp[$c_id]['plus'] += $production;
                }
                else
                {
                    $temp[$c_id]['minus'] += $production;
                }
            }
        }

        $storage = [
            901 => shortly_number($PLANET[$RESOURCE[901].'_max']),
            902 => shortly_number($PLANET[$RESOURCE[902].'_max']),
            903 => shortly_number($PLANET[$RESOURCE[903].'_max']),
        ];

        $basic_production = [
            901 => $basic_income[901] * $config->resource_multiplier,
            902 => $basic_income[902] * $config->resource_multiplier,
            903 => $basic_income[903] * $config->resource_multiplier,
            911 => $basic_income[911] * $config->energySpeed,
        ];

        $total_production = [
            901 => $PLANET[$RESOURCE[901].'_perhour'] + $basic_production[901],
            902 => $PLANET[$RESOURCE[902].'_perhour'] + $basic_production[902],
            903 => $PLANET[$RESOURCE[903].'_perhour'] + $basic_production[903],
            911 => $PLANET[$RESOURCE[911]] + $basic_production[911] + $PLANET[$RESOURCE[911].'_used'],
        ];

        $bonus_production = [
            901 => $temp[901]['plus'] * ($USER['factor']['Resource'] + 0.02 * $USER[$RESOURCE[131]]),
            902 => $temp[902]['plus'] * ($USER['factor']['Resource'] + 0.02 * $USER[$RESOURCE[132]]),
            903 => $temp[903]['plus'] * ($USER['factor']['Resource'] + 0.02 * $USER[$RESOURCE[133]]),
            911 => $temp[911]['plus'] * $USER['factor']['Energy'],
        ];

        $daily_production = [
            901 => $total_production[901] * 24,
            902 => $total_production[902] * 24,
            903 => $total_production[903] * 24,
            911 => $total_production[911],
        ];

        $weekly_production = [
            901 => $total_production[901] * 168,
            902 => $total_production[902] * 168,
            903 => $total_production[903] * 168,
            911 => $total_production[911],
        ];

        $prod_selector = [];

        foreach (range(10, 0) as $percent)
        {
            $prod_selector[$percent] = ($percent * 10).'%';
        }

        $this->assign([
            'header'           => sprintf($LNG['rs_production_on_planet'], $PLANET['name']),
            'prodSelector'     => $prod_selector,
            'productionList'   => $production_list,
            'basicProduction'  => $basic_production,
            'totalProduction'  => $total_production,
            'bonusProduction'  => $bonus_production,
            'dailyProduction'  => $daily_production,
            'weeklyProduction' => $weekly_production,
            'storage'          => $storage,
        ]);

        $this->display('page.resources.default.tpl');
    }
}
