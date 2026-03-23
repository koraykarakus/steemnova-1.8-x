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

class BuildFunctions
{
    public static $bonus_list = [
        'Attack',
        'Defensive',
        'Shield',
        'BuildTime',
        'ResearchTime',
        'ShipTime',
        'DefensiveTime',
        'Resource',
        'Energy',
        'ResourceStorage',
        'ShipStorage',
        'FlyTime',
        'FleetSlots',
        'Planets',
        'SpyPower',
        'Expedition',
        'GateCoolTime',
        'MoreFound',
    ];

    public static function getBonusList()
    {
        return self::$bonus_list;
    }

    public static function getRestPrice($USER, $PLANET, $element, $element_price = null)
    {
        global $RESOURCE;

        if (!isset($element_price))
        {
            $element_price = self::getElementPrice($USER, $PLANET, $element);
        }

        $overflow = [];

        foreach ($element_price as $res_type => $res_price)
        {
            $available = isset($PLANET[$RESOURCE[$res_type]]) ?
            $PLANET[$RESOURCE[$res_type]] :
            $USER[$RESOURCE[$res_type]];

            $overflow[$res_type] = max($res_price - floor($available), 0);
        }

        return $overflow;
    }

    public static function getElementPrice(
        $USER,
        $PLANET,
        $element,
        $for_destroy = false,
        $for_level = null
    ) {
        global $PRICELIST, $RESOURCE, $RESLIST;

        if (in_array($element, $RESLIST['fleet'])
            || in_array($element, $RESLIST['defense'])
            || in_array($element, $RESLIST['missile']))
        {
            $element_level = $for_level;
        }
        elseif (isset($for_level))
        {
            $element_level = $for_level;
        }
        elseif (isset($PLANET[$RESOURCE[$element]]))
        {
            $element_level = $PLANET[$RESOURCE[$element]];
        }
        elseif (isset($USER[$RESOURCE[$element]]))
        {
            $element_level = $USER[$RESOURCE[$element]];
        }
        else
        {
            return [];
        }

        $price = [];
        foreach ($RESLIST['ressources'] as $resType)
        {
            if (!isset($PRICELIST[$element]['cost'][$resType]))
            {
                continue;
            }
            $ressource_amount = $PRICELIST[$element]['cost'][$resType];

            if ($ressource_amount == 0)
            {
                continue;
            }

            $price[$resType] = $ressource_amount;

            if (isset($PRICELIST[$element]['factor'])
                && $PRICELIST[$element]['factor'] != 0
                && $PRICELIST[$element]['factor'] != 1)
            {
                $price[$resType] *= pow($PRICELIST[$element]['factor'], $element_level - 1);
            }

            if ($for_level
                && (in_array($element, $RESLIST['fleet'])
                || in_array($element, $RESLIST['defense'])
                || in_array($element, $RESLIST['missile'])))
            {
                $price[$resType] *= $element_level;
            }

            if ($for_destroy === true)
            {
                $price[$resType] /= 2;
            }
        }

        return $price;
    }

    public static function isTechnologieAccessible($USER, $PLANET, $element)
    {
        global $REQUIREMENTS, $RESOURCE;

        if (!isset($REQUIREMENTS[$element]))
        {
            return true;
        }

        foreach ($REQUIREMENTS[$element] as $req_element => $ele_level)
        {
            if ((isset($USER[$RESOURCE[$req_element]])
                && $USER[$RESOURCE[$req_element]] < $ele_level)
                || (isset($PLANET[$RESOURCE[$req_element]])
                && $PLANET[$RESOURCE[$req_element]] < $ele_level)
            ) {
                return false;
            }
        }
        return true;
    }

    public static function getBuildingTime(
        $USER,
        $PLANET,
        $element,
        $element_price = null,
        $for_destroy = false,
        $for_level = null
    ) {
        global $RESOURCE, $RESLIST, $REQUIREMENTS;

        $config = Config::get($USER['universe']);

        $time = 0;

        if (!isset($element_price))
        {
            $element_price = self::getElementPrice(
                $USER,
                $PLANET,
                $element,
                $for_destroy,
                $for_level
            );
        }

        $element_cost = 0;

        if (isset($element_price[901]))
        {
            $element_cost += $element_price[901];
        }

        if (isset($element_price[902]))
        {
            $element_cost += $element_price[902];
        }

        if (in_array($element, $RESLIST['build']))
        {
            $time = $element_cost / ($config->game_speed * (1 + $PLANET[$RESOURCE[14]])) * pow(0.5, $PLANET[$RESOURCE[15]]) * (1 + $USER['factor']['BuildTime']);
        }
        elseif (in_array($element, $RESLIST['fleet']))
        {
            $time = $element_cost / ($config->game_speed * (1 + $PLANET[$RESOURCE[21]])) * pow(0.5, $PLANET[$RESOURCE[15]]) * (1 + $USER['factor']['ShipTime']);
        }
        elseif (in_array($element, $RESLIST['defense']))
        {
            $time = $element_cost / ($config->game_speed * (1 + $PLANET[$RESOURCE[21]])) * pow(0.5, $PLANET[$RESOURCE[15]]) * (1 + $USER['factor']['DefensiveTime']);
        }
        elseif (in_array($element, $RESLIST['missile']))
        {
            $time = $element_cost / ($config->game_speed * (1 + $PLANET[$RESOURCE[21]])) * pow(0.5, $PLANET[$RESOURCE[15]]) * (1 + $USER['factor']['DefensiveTime']);
        }
        elseif (in_array($element, $RESLIST['tech']))
        {
            if (is_numeric($PLANET[$RESOURCE[31].'_inter']))
            {
                $Level = $PLANET[$RESOURCE[31]];
            }
            else
            {
                $Level = 0;
                foreach ($PLANET[$RESOURCE[31].'_inter'] as $Levels)
                {
                    if (!isset($REQUIREMENTS[$element][31])
                        || $Levels >= $REQUIREMENTS[$element][31])
                    {
                        $Level += $Levels;
                    }
                }
            }

            $time = $element_cost / (1000 * (1 + $Level)) / ($config->game_speed / 2500) * pow(1 - $config->factor_university / 100, $PLANET[$RESOURCE[6]]) * (1 + $USER['factor']['ResearchTime']);
        }

        if ($for_destroy)
        {
            $time = floor($time * 1300);
        }
        else
        {
            $time = floor($time * 3600);
        }
        return max($time, $config->min_build_time);
    }

    public static function isElementBuyable(
        $USER,
        $PLANET,
        $element,
        $element_price = null,
        $for_destroy = false,
        $for_level = null
    ) {
        $rest = self::getRestPrice(
            $USER,
            $PLANET,
            $element,
            $element_price,
            $for_destroy,
            $for_level
        );
        return count(array_filter($rest)) === 0;
    }

    public static function getMaxConstructibleElements(
        $USER,
        $PLANET,
        $element,
        $element_price = null
    ) {
        global $RESOURCE, $RESLIST;

        if (!isset($element_price))
        {
            $element_price = self::getElementPrice($USER, $PLANET, $element);
        }

        $max_element = [];

        foreach ($element_price as $resource_id => $price)
        {
            if (isset($PLANET[$RESOURCE[$resource_id]]))
            {
                $max_element[] = floor($PLANET[$RESOURCE[$resource_id]] / $price);
            }
            elseif (isset($USER[$RESOURCE[$resource_id]]))
            {
                $max_element[] = floor($USER[$RESOURCE[$resource_id]] / $price);
            }
            else
            {
                throw new Exception("Unknown Ressource ".$resource_id." at element ".$element.".");
            }
        }

        if (in_array($element, $RESLIST['one']))
        {
            $max_element[] = 1;
        }

        return min($max_element);
    }

    public static function getMaxConstructibleRockets($USER, $PLANET, $missiles = null)
    {
        global $RESOURCE, $RESLIST;

        if (!isset($missiles))
        {
            $missiles = [];

            foreach ($RESLIST['missile'] as $element_id)
            {
                $missiles[$element_id] = $PLANET[$RESOURCE[$element_id]];
            }
        }

        $build_array = !empty($PLANET['b_shipyard_id']) ?
                    unserialize($PLANET['b_shipyard_id']) :
                    [];

        $max_missiles = $PLANET[$RESOURCE[44]] * 10 * max(Config::get()->silo_factor, 1);

        foreach ($build_array as $element_array)
        {
            if (isset($missiles[$element_array[0]]))
            {
                $missiles[$element_array[0]] += $element_array[1];
            }
        }

        $actu_missiles = $missiles[502] + (2 * $missiles[503]);
        $missiles_space = max(0, $max_missiles - $actu_missiles);

        return [
            502 => $missiles_space,
            503 => floor($missiles_space / 2),
        ];
    }

    public static function getAvalibleBonus($element)
    {
        global $PRICELIST;

        $element_bonus = [];

        foreach (self::$bonus_list as $bonus)
        {
            $temp = (float) $PRICELIST[$element]['bonus'][$bonus][0];
            if (empty($temp))
            {
                continue;
            }

            $element_bonus[$bonus] = $PRICELIST[$element]['bonus'][$bonus];
        }

        return $element_bonus;
    }
}
