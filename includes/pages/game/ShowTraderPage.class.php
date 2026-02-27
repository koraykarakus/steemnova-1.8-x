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

class ShowTraderPage extends AbstractGamePage
{
    public static $require_module = MODULE_TRADER;

    public function __construct()
    {
        parent::__construct();
    }

    public static $charge = [
        901 => [901 => 1, 902 => 2, 903 => 4],
        902 => [901 => 0.5, 902 => 1, 903 => 2],
        903 => [901 => 0.25, 902 => 0.5, 903 => 1],
    ];

    public function show(): void
    {
        global $LNG, $USER, $resource;

        $darkmatter_cost_trader = Config::get()->darkmatter_cost_trader;

        $this->assign([
            'tr_cost_dm_trader'  => sprintf($LNG['tr_cost_dm_trader'], pretty_number($darkmatter_cost_trader), $LNG['tech'][921]),
            'charge'             => self::$charge,
            'resource'           => $resource,
            'requiredDarkMatter' => $USER['darkmatter'] < $darkmatter_cost_trader ? sprintf($LNG['tr_not_enought'], $LNG['tech'][921]) : false,
        ]);

        $this->display("page.trader.default.tpl");
    }

    public function trade(): void
    {
        global $USER, $LNG;

        if ($USER['darkmatter'] < Config::get()->darkmatter_cost_trader)
        {
            $this->redirectTo('game.php?page=trader');
        }

        $resource_id = HTTP::_GP('resource', 0);

        if (!in_array($resource_id, array_keys(self::$charge)))
        {
            $this->printMessage($LNG['invalid_action'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=trader',
            ]]);
        }

        $trade_resources = array_values(array_diff(
            array_keys(self::$charge[$resource_id]),
            [$resource_id]
        ));

        $this->tplObj->loadscript("trader.js");
        $this->assign([
            'tradeResourceID' => $resource_id,
            'tradeResources'  => $trade_resources,
            'charge'          => self::$charge[$resource_id],
        ]);

        $this->display('page.trader.trade.tpl');
    }

    public function send(): void
    {
        global $USER, $PLANET, $LNG, $resource;

        if ($USER['darkmatter'] < Config::get()->darkmatter_cost_trader)
        {
            $this->redirectTo('game.php?page=trader');
        }

        $resource_id = HTTP::_GP('resource', 0);

        if (!in_array($resource_id, array_keys(self::$charge)))
        {
            $this->printMessage($LNG['invalid_action'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=trader',
            ]]);
        }

        $get_trade_resources = HTTP::_GP('trade', []);

        $trade_resources = array_values(array_diff(array_keys(self::$charge[$resource_id]), [$resource_id]));
        $trade_sum = 0;

        foreach ($trade_resources as $c_trade_ress_id)
        {
            if (!isset($get_trade_resources[$c_trade_ress_id]))
            {
                continue;
            }
            $trade_amount = max(0, round((float) $get_trade_resources[$c_trade_ress_id]));

            if (empty($trade_amount)
                || !isset(self::$charge[$resource_id][$c_trade_ress_id]))
            {
                continue;
            }

            if (isset($PLANET[$resource[$resource_id]]))
            {
                $used_resources = $trade_amount * self::$charge[$resource_id][$c_trade_ress_id];

                if ($used_resources > $PLANET[$resource[$resource_id]])
                {
                    if ($trade_sum > 0)
                    {
                        $USER[$resource[921]] -= Config::get()->darkmatter_cost_trader;
                    }

                    $this->printMessage(sprintf($LNG['tr_not_enought'], $LNG['tech'][$resource_id]), [[
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=trader',
                    ]]);
                }

                $trade_sum += $trade_amount;
                $PLANET[$resource[$resource_id]] -= $used_resources;
            }
            elseif (isset($USER[$resource[$resource_id]]))
            {
                if ($resource_id == 921)
                {
                    $USER[$resource[$resource_id]] -= Config::get()->darkmatter_cost_trader;
                }

                $used_resources = $trade_amount * self::$charge[$resource_id][$c_trade_ress_id];

                if ($used_resources > $USER[$resource[$resource_id]])
                {
                    $this->printMessage(sprintf($LNG['tr_not_enought'], $LNG['tech'][$resource_id]), [[
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=trader',
                    ]]);
                }

                $trade_sum += $trade_amount;
                $USER[$resource[$resource_id]] -= $used_resources;

                if ($resource_id == 921)
                {
                    $USER[$resource[$resource_id]] += Config::get()->darkmatter_cost_trader;
                }
            }
            else
            {
                throw new Exception('Unknown resource ID #'.$resource_id);
            }

            if (isset($PLANET[$resource[$c_trade_ress_id]]))
            {
                $PLANET[$resource[$c_trade_ress_id]] += $trade_amount;
            }
            elseif (isset($USER[$resource[$c_trade_ress_id]]))
            {
                $USER[$resource[$c_trade_ress_id]] += $trade_amount;
            }
            else
            {
                throw new Exception('Unknown resource ID #'.$c_trade_ress_id);
            }
        }

        if ($trade_sum > 0)
        {
            $USER[$resource[921]] -= Config::get()->darkmatter_cost_trader;
        }

        $this->printMessage($LNG['tr_exchange_done'], [[
            'label' => $LNG['sys_forward'],
            'url'   => 'game.php?page=trader',
        ]]);
    }
}
