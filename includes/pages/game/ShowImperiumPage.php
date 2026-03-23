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

class ShowImperiumPage extends AbstractGamePage
{
    public static $require_module = MODULE_IMPERIUM;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $RESOURCE, $RESLIST;

        $db = Database::get();

        $order = $USER['planet_sort_order'] == 1 ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :user_id AND destroyed = '0' ORDER BY ";

        switch ($USER['planet_sort'])
        {
            case 2:
                $sql .= 'name '.$order;
                break;
            case 1:
                $sql .= 'galaxy '.$order.', system '.$order.', planet '.$order.', planet_type '.$order;
                break;
            default:
                $sql .= 'id '.$order;
                break;
        }

        $planets_raw = $db->select($sql, [
            ':user_id' => $USER['id'],
        ]);

        $planets = [];

        $planet_ress = new ResourceUpdate();

        foreach ($planets_raw as $c_planet)
        {
            list($USER, $c_planet) = $planet_ress->CalcResource($USER, $c_planet, true);

            $planets[] = $c_planet;
            unset($c_planet);
        }

        $planet_list = [];
        $config = Config::get($USER['universe']);
        foreach ($planets as $c_planet)
        {
            $planet_list['name'][$c_planet['id']] = $c_planet['name'];
            $planet_list['image'][$c_planet['id']] = $c_planet['image'];

            $planet_list['coords'][$c_planet['id']]['galaxy'] = $c_planet['galaxy'];
            $planet_list['coords'][$c_planet['id']]['system'] = $c_planet['system'];
            $planet_list['coords'][$c_planet['id']]['planet'] = $c_planet['planet'];

            $planet_list['field'][$c_planet['id']]['current'] = $c_planet['field_current'];
            $planet_list['field'][$c_planet['id']]['max'] = CalculateMaxPlanetFields($c_planet);

            $planet_list['energy_used'][$c_planet['id']] = $c_planet['energy'] + $c_planet['energy_used'];

            $planet_list['resource'][901][$c_planet['id']] = $c_planet['metal'];
            $planet_list['resource'][902][$c_planet['id']] = $c_planet['crystal'];
            $planet_list['resource'][903][$c_planet['id']] = $c_planet['deuterium'];
            $planet_list['resource'][911][$c_planet['id']] = $c_planet['energy'];

            if ($c_planet['planet_type'] == 1)
            {
                $basic[901][$c_planet['id']] = $config->metal_basic_income * $config->resource_multiplier;
                $basic[902][$c_planet['id']] = $config->crystal_basic_income * $config->resource_multiplier;
                $basic[903][$c_planet['id']] = $config->deuterium_basic_income * $config->resource_multiplier;
            }
            else
            {
                $basic[901][$c_planet['id']] = 0;
                $basic[902][$c_planet['id']] = 0;
                $basic[903][$c_planet['id']] = 0;
            }

            $planet_list['resourcePerHour'][901][$c_planet['id']] = $basic[901][$c_planet['id']] + $c_planet['metal_perhour'];
            $planet_list['resourcePerHour'][902][$c_planet['id']] = $basic[902][$c_planet['id']] + $c_planet['crystal_perhour'];
            $planet_list['resourcePerHour'][903][$c_planet['id']] = $basic[903][$c_planet['id']] + $c_planet['deuterium_perhour'];

            $planet_list['planet_type'][$c_planet['id']] = $c_planet['planet_type'];

            foreach ($RESLIST['build'] as $elementID)
            {
                $planet_list['build'][$elementID][$c_planet['id']] = $c_planet[$RESOURCE[$elementID]];
            }

            foreach ($RESLIST['fleet'] as $elementID)
            {
                $planet_list['fleet'][$elementID][$c_planet['id']] = $c_planet[$RESOURCE[$elementID]];
            }

            foreach ($RESLIST['defense'] as $elementID)
            {
                $planet_list['defense'][$elementID][$c_planet['id']] = $c_planet[$RESOURCE[$elementID]];
            }

            $planet_list['missiles'][502][$c_planet['id']] = $c_planet[$RESOURCE[502]];
            $planet_list['missiles'][503][$c_planet['id']] = $c_planet[$RESOURCE[503]];
        }

        foreach ($RESLIST['tech'] as $elementID)
        {
            $planet_list['tech'][$elementID] = $USER[$RESOURCE[$elementID]];
        }

        $this->assign([
            'colspan'    => count($planets) + 2,
            'planetList' => $planet_list,
        ]);

        $this->display('page.empire.default.tpl');
    }
}
