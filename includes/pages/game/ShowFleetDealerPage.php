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

class ShowFleetDealerPage extends AbstractGamePage
{
    public static $require_module = MODULE_FLEET_TRADER;

    public function __construct()
    {
        parent::__construct();
    }

    public function send(): void
    {
        global $USER, $PLANET, $LNG, $pricelist, $resource;

        $ship_id = HTTP::_GP('shipID', 0);
        $count = max(0, round(HTTP::_GP('count', 0.0)));
        $allowed_ship_ids = explode(',', Config::get()->trade_allowed_ships);

        if (!empty($ship_id) 
            && !empty($count) 
            && in_array($ship_id, $allowed_ship_ids) 
            && $PLANET[$resource[$ship_id]] >= $count)
        {
            $trade_charge = 1 - (Config::get()->trade_charge / 100);
            $PLANET[$resource[901]] += $count * $pricelist[$ship_id]['cost'][901] * $trade_charge;
            $PLANET[$resource[902]] += $count * $pricelist[$ship_id]['cost'][902] * $trade_charge;
            $PLANET[$resource[903]] += $count * $pricelist[$ship_id]['cost'][903] * $trade_charge;
            $USER[$resource[921]] += $count * $pricelist[$ship_id]['cost'][921] * $trade_charge;

            $PLANET[$resource[$ship_id]] -= $count;

            $sql = 'UPDATE %%PLANETS%% SET ' . 
            $resource[$ship_id] . 
            ' = ' . 
            $resource[$ship_id].' - :count WHERE id = :planet_id;';
            
            Database::get()->update($sql, [
                ':count'    => $count,
                ':planet_id' => $PLANET['id'],
            ]);

            $this->printMessage($LNG['tr_exchange_done'], [[
                'label' => $LNG['sys_forward'],
                'url'   => 'game.php?page=fleetDealer',
            ]]);
        }
        else
        {
            $this->printMessage($LNG['tr_exchange_error'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetDealer',
            ]]);
        }

    }

    public function show(): void
    {
        global $PLANET, $LNG, $pricelist, $resource, $reslist;

        $cost = [];

        $allowed_ship_ids = explode(',', Config::get()->trade_allowed_ships);

        foreach ($allowed_ship_ids as $c_ship_id)
        {
            if (in_array($c_ship_id, $reslist['fleet']) 
                || in_array($c_ship_id, $reslist['defense']))
            {
                $cost[$c_ship_id] = [$PLANET[$resource[$c_ship_id]], $LNG['tech'][$c_ship_id], $pricelist[$c_ship_id]['cost']];
            }
        }

        if (empty($cost))
        {
            $this->printMessage($LNG['ft_empty'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetDealer',
            ]]);
        }

        $this->assign([
            'shipIDs'   => $allowed_ship_ids,
            'CostInfos' => $cost,
            'Charge'    => Config::get()->trade_charge,
        ]);

        $this->display('page.fleetDealer.default.tpl');
    }
}
