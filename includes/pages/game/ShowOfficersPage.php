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

class ShowOfficersPage extends AbstractGamePage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function UpdateExtra($element): void
    {
        global $PLANET, $USER, $RESOURCE, $PRICELIST;

        $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $element);

        if (!BuildFunctions::isElementBuyable($USER, $PLANET, $element, $cost_resources))
        {
            return;
        }

        $USER[$RESOURCE[$element]] = max($USER[$RESOURCE[$element]], TIMESTAMP) + $PRICELIST[$element]['time'];

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

        $sql = 'UPDATE %%USERS%% SET
				'.$RESOURCE[$element].' = :newTime
				WHERE
				id = :userId;';

        Database::get()->update($sql, [
            ':newTime' => $USER[$RESOURCE[$element]],
            ':userId'  => $USER['id'],
        ]);
    }

    public function UpdateOfficers($element): void
    {
        global $USER, $PLANET, $RESOURCE, $PRICELIST;

        $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $element);

        if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $element)
            || !BuildFunctions::isElementBuyable($USER, $PLANET, $element, $cost_resources)
            || $PRICELIST[$element]['max'] <= $USER[$RESOURCE[$element]])
        {
            return;
        }

        $USER[$RESOURCE[$element]] += 1;

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

        $sql = 'UPDATE %%USERS%% SET
		'.$RESOURCE[$element].' = :newTime
		WHERE
		id = :userId;';

        Database::get()->update($sql, [
            ':newTime' => $USER[$RESOURCE[$element]],
            ':userId'  => $USER['id'],
        ]);
    }

    public function show(): void
    {
        global $USER, $PLANET, $RESOURCE, $RESLIST, $LNG, $PRICELIST;

        $update_id = HTTP::_GP('id', 0);

        if (!empty($update_id)
            && $_SERVER['REQUEST_METHOD'] === 'POST'
            && $USER['urlaubs_modus'] == 0)
        {
            if (isModuleAvailable(MODULE_OFFICERS)
                && in_array($update_id, $RESLIST['officers']))
            {
                $this->UpdateOfficers($update_id);
            }
            elseif (isModuleAvailable(MODULE_DMEXTRAS)
                && in_array($update_id, $RESLIST['dmfunc']))
            {
                $this->UpdateExtra($update_id);
            }
        }

        $dm_list = [];
        $officer_list = [];

        if (isModuleAvailable(MODULE_DMEXTRAS))
        {
            foreach ($RESLIST['dmfunc'] as $c_element)
            {
                if ($USER[$RESOURCE[$c_element]] > TIMESTAMP)
                {
                    $this->tpl_obj->execscript("GetOfficerTime(".$c_element.", ".($USER[$RESOURCE[$c_element]] - TIMESTAMP).");");
                }

                $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $c_element);
                $buyable = BuildFunctions::isElementBuyable($USER, $PLANET, $c_element, $cost_resources);
                $cost_overflow = BuildFunctions::getRestPrice($USER, $PLANET, $c_element, $cost_resources);
                $element_bonus = BuildFunctions::getAvalibleBonus($c_element);

                $dm_list[$c_element] = [
                    'timeLeft'      => max($USER[$RESOURCE[$c_element]] - TIMESTAMP, 0),
                    'costResources' => $cost_resources,
                    'buyable'       => $buyable,
                    'time'          => $PRICELIST[$c_element]['time'],
                    'costOverflow'  => $cost_overflow,
                    'elementBonus'  => $element_bonus,
                ];
            }
        }

        if (isModuleAvailable(MODULE_OFFICERS))
        {
            foreach ($RESLIST['officers'] as $c_element)
            {
                if (!BuildFunctions::isTechnologieAccessible($USER, $PLANET, $c_element))
                {
                    continue;
                }

                $cost_resources = BuildFunctions::getElementPrice($USER, $PLANET, $c_element);
                $buyable = BuildFunctions::isElementBuyable($USER, $PLANET, $c_element, $cost_resources);
                $cost_overflow = BuildFunctions::getRestPrice($USER, $PLANET, $c_element, $cost_resources);
                $element_bonus = BuildFunctions::getAvalibleBonus($c_element);

                $officer_list[$c_element] = [
                    'level'         => $USER[$RESOURCE[$c_element]],
                    'maxLevel'      => $PRICELIST[$c_element]['max'],
                    'costResources' => $cost_resources,
                    'buyable'       => $buyable,
                    'costOverflow'  => $cost_overflow,
                    'elementBonus'  => $element_bonus,
                ];
            }
        }

        $this->assign([
            'officer_list'   => $officer_list,
            'darkmatterList' => $dm_list,
            'of_dm_trade'    => sprintf($LNG['of_dm_trade'], $LNG['tech'][921]),
        ]);

        $this->display('page.officers.default.tpl');
    }
}
