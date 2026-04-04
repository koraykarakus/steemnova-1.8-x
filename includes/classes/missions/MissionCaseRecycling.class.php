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

class MissionCaseRecycling extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        global $PRICELIST, $RESOURCE;

        $resource_ids = [901, 902, 903, 921];
        $debris_ids = [901, 902];
        $res_query = [];
        $collect_query = [];

        $collected_goods = [];
        foreach ($debris_ids as $debris_id)
        {
            $collected_goods[$debris_id] = 0;
            $res_query[] = 'debris_'.$RESOURCE[$debris_id];
        }

        $sql = 'SELECT '.implode(',', $res_query).', ('.implode(' + ', $res_query).') as total
		FROM %%PLANETS%% WHERE id = :planet_id';

        $target_data = Database::get()->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_end_id'],
        ]);

        if (!empty($target_data['total']))
        {
            $sql = 'SELECT * FROM %%USERS%% WHERE id = :user_id;';
            $target_user = Database::get()->selectSingle($sql, [
                ':user_id' => $this->_fleet['fleet_owner'],
            ]);

            $target_user_factors = getFactors($target_user);
            $ship_storage_factor = 1 + $target_user_factors['ShipStorage'];

            // Get fleet capacity
            $fleet_data = FleetFunctions::unserialize($this->_fleet['fleet_array']);

            $recycler_storage = 0;
            $other_fleet_storage = 0;

            foreach ($fleet_data as $ship_id => $ship_amount)
            {
                if ($ship_id == 209 || $ship_id == 219)
                {
                    $recycler_storage += $PRICELIST[$ship_id]['capacity'] * $ship_amount;
                }
                else
                {
                    $other_fleet_storage += $PRICELIST[$ship_id]['capacity'] * $ship_amount;
                }
            }

            $recycler_storage *= $ship_storage_factor;
            $other_fleet_storage *= $ship_storage_factor;

            $incoming_goods = 0;
            foreach ($resource_ids as $resource_id)
            {
                $incoming_goods += $this->_fleet['fleet_resource_'.$RESOURCE[$resource_id]];
            }

            $total_storage = $recycler_storage + min(0, $other_fleet_storage - $incoming_goods);

            $param = [
                ':planet_id' => $this->_fleet['fleet_end_id'],
            ];

            // fast way
            $collect_factor = min(1, $total_storage / $target_data['total']);
            foreach ($debris_ids as $debris_id)
            {
                $fleet_col_name = 'fleet_resource_'.$RESOURCE[$debris_id];
                $debris_col_name = 'debris_'.$RESOURCE[$debris_id];

                $collected_goods[$debris_id] = ceil($target_data[$debris_col_name] * $collect_factor);
                $collect_query[] = $debris_col_name.' = GREATEST(0, '.$debris_col_name.' - :'.$RESOURCE[$debris_id].')';
                $param[':'.$RESOURCE[$debris_id]] = $collected_goods[$debris_id];

                $this->UpdateFleet($fleet_col_name, $this->_fleet[$fleet_col_name] + $collected_goods[$debris_id]);
            }

            $sql = 'UPDATE %%PLANETS%% SET '.implode(',', $collect_query) . ' WHERE id = :planet_id;';

            Database::get()->update($sql, $param);
        }

        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $message = sprintf(
            $LNG['sys_recy_gotten'],
            pretty_number($collected_goods[901]),
            $LNG['tech'][901],
            pretty_number($collected_goods[902]),
            $LNG['tech'][902]
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            5,
            $LNG['sys_recy_report'],
            $message,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planet_id;';
        $planet_name = Database::get()->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_start_id'],
        ], 'name');

        $message = sprintf(
            $LNG['sys_tran_mess_owner'],
            $planet_name,
            GetStartAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            4,
            $LNG['sys_mess_fleetback'],
            $message,
            $this->_fleet['fleet_end_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->savePlanetProduction($this->_fleet['fleet_start_id'], $this->_fleet['fleet_end_time']);

        $this->RestoreFleet();
    }
}
