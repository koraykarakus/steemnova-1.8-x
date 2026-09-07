<?php

/**
 *  Steemnova
 *   by Adam "dotevo" Jordanek 2018
 *
 * For the full copyright and license information, please view the LICENSE
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>

 */

class ShowMarketPlacePage extends AbstractGamePage
{
    public static int $require_module = MODULE_MARKET_PLACE;

    public function __construct()
    {
        parent::__construct();
    }

    private function checkSlots(array $USER): array
    {
        global $LNG;

        $actual_fleets = FleetFunctions::GetCurrentFleets($USER['id']);

        if (FleetFunctions::GetMaxFleetSlots($USER) <= $actual_fleets)
        {
            return ['result' => -1, 'message' => $LNG['fl_no_slots']];
        }

        return ['result' => 0];
    }

    private function checkTechs($seller): array
    {
        global $USER, $RESOURCE, $LNG;

        $attack = $USER[$RESOURCE[109]] * 10 + $USER['factor']['Attack'] * 100;
        $defensive = $USER[$RESOURCE[110]] * 10 + $USER['factor']['Defensive'] * 100;
        $shield = $USER[$RESOURCE[111]] * 10 + $USER['factor']['Shield'] * 100;

        $seller['factor'] = getFactors($seller);
        $attack_targ = $seller[$RESOURCE[109]] * 10 + $seller['factor']['Attack'] * 100;
        $defensive_targ = $seller[$RESOURCE[110]] * 10 + $seller['factor']['Defensive'] * 100;
        $shield_targ = $seller[$RESOURCE[111]] * 10 + $seller['factor']['Shield'] * 100;

        if ($attack > $attack_targ
            || $defensive > $defensive_targ
            || $shield > $shield_targ)
        {
            return [
                'buyable' => false,
                'reason'  => $LNG['market_buyable_no_tech'],
            ];
        }

        return ["buyable" => true,
            'reason'      => ''];
    }

    private function checkDiplo(
        $visibility,
        $level,
        $seller_ally,
        $ally
    ): array {
        global $LNG;

        if ($visibility == 2
            && $level == 5)
        {
            return [
                'buyable' => false,
                'reason'  => $LNG['market_buyable_no_enemies'],
            ];
        }

        if ($visibility == 1
            && $ally != $seller_ally
            && ($level == null || $level > 3))
        {
            return [
                'buyable' => false,
                'reason'  => $LNG['market_buyable_only_trade_partners'],
            ];
        }

        return ["buyable" => true,
            "reason"      => ''];
    }

    private function getResourceTradeHistory(): array
    {
        $db = Database::get();

        $sql = 'SELECT
			buy_time as time,
			ex_resource_type as res_type,
			ex_resource_amount as amount,
			seller.fleet_resource_metal as metal,
			seller.fleet_resource_crystal as crystal,
			seller.fleet_resource_deuterium as deuterium
			FROM %%TRADES%%
			JOIN %%LOG_FLEETS%% seller ON seller.fleet_id = seller_fleet_id
			JOIN %%LOG_FLEETS%% buyer ON buyer.fleet_id = buyer_fleet_id
			WHERE transaction_type = 0 ORDER BY time DESC LIMIT 40;';

        $trades = $db->select($sql, [
            // TODO LIMIT
        ]);

        return $trades;
    }

    private function getFleetTradeHistory(): array
    {
        global $LNG;

        $db = Database::get();

        $sql = 'SELECT
			seller.fleet_array as fleet,
			buy_time as time,
			ex_resource_type as res_type,
			ex_resource_amount as amount
			FROM %%TRADES%%
			JOIN %%LOG_FLEETS%% seller ON seller.fleet_id = seller_fleet_id
			JOIN %%LOG_FLEETS%% buyer ON buyer.fleet_id = buyer_fleet_id
			WHERE transaction_type = 1 ORDER BY time DESC LIMIT 40;';

        $trades = $db->select($sql, [
            //TODO LIMIT
        ]);

        for ($i = 0; $i < count($trades);$i++)
        {
            $fleet = FleetFunctions::unserialize($trades[$i]['fleet']);
            $fleet_str = '';
            foreach ($fleet as $name => $amount)
            {
                $fleet_str .= $LNG['shortNames'][$name].' x'.$amount."\n";
            }
            $trades[$i]['fleet_str'] = $fleet_str;
        }

        return $trades;
    }

    private function doBuy(): string
    {
        global $USER, $PLANET, $RESOURCE, $LNG, $PRICELIST;
        $fleet_id = HTTP::_GP('fleetID', 0);
        $ship_type = HTTP::_GP('shipType', "");
        $db = Database::get();

        // Slots checking
        $check_result = $this->checkSlots($USER);
        if ($check_result['result'] < 0)
        {
            return $check_result['message'];
        }

        // Get trade fleet
        $sql = "SELECT * FROM %%FLEETS%% 
        JOIN %%TRADES%% ON fleet_id = seller_fleet_id 
        JOIN %%USERS%% ON fleet_owner = id 
        WHERE fleet_id = :fleet_id AND fleet_mess = 2;";

        $fleet_result = $db->select($sql, [
            ':fleet_id' => $fleet_id,
        ]);

        // Error: no results
        if ($db->rowCount() == 0)
        {
            return $LNG['market_p_msg_not_found'];
        }

        if ($fleet_result[0]['filter_visibility'] != 0
            && $USER['id'] != $fleet_result[0]['id'])
        {
            //Check packts
            $sql = "SELECT * FROM %%DIPLO%% 
            WHERE (owner_1 = :ow AND owner_2 = :ow2) 
            OR (owner_2 = :ow AND owner_1 = :ow2) AND accept = 1;";

            $res = $db->select($sql, [
                ':ow'  => $USER['ally_id'],
                ':ow2' => $fleet_result[0]['ally_id'],
            ]);

            $level = null;
            if ($db->rowCount() != 0)
            {
                $level = $res[0]['level'];
            }

            $buy = $this->checkDiplo(
                $fleet_result[0]['filter_visibility'],
                $level,
                $fleet_result[0]['ally_id'],
                $USER['ally_id']
            );

            if (!$buy['buyable'])
            {
                return $buy['reason'];
            }
        }

        if ($fleet_result[0]['transaction_type'] == 1)
        {
            $buy = $this->checkTechs($fleet_result[0]);
            if (!$buy['buyable'])
            {
                return $buy['reason'];
            }
        }

        // if not in range 1-3
        if ($fleet_result[0]['ex_resource_type'] >= 4
            || $fleet_result[0]['ex_resource_type'] <= 0)
        {
            return $LNG['market_p_msg_wrong_resource_type'];
        }

        $factor = 1 + $USER['factor']['ShipStorage'];

        //-------------FLEET SIZE CALCULATION---------------
        $fleet_result = $fleet_result[0];
        $amount = $fleet_result['ex_resource_amount'];

        $F1capacity = 0;
        $F1type = 0;
        // PRIO for LC
        if ($ship_type == 1)
        {
            $F1capacity = $PRICELIST[202]['capacity'] * $factor;
            $F1type = 202;
        }
        // PRIO for HC
        else
        {
            $F1capacity = $PRICELIST[203]['capacity'] * $factor;
            $F1type = 203;
        }

        $F1 = min($PLANET[$RESOURCE[$F1type]], ceil($amount / $F1capacity));

        // taken
        $amountTMP = $amount - $F1 * $F1capacity;
        // If still fleet needed
        $F2 = 0;
        $F2capacity = 0;
        $F2type = 0;
        if ($amountTMP > 0)
        {
            // We need HC
            if ($ship_type == 1)
            {
                $F2capacity = $PRICELIST[203]['capacity'] * $factor;
                $F2type = 203;
            }
            // We need LC
            else
            {
                $F2capacity = $PRICELIST[202]['capacity'] * $factor;
                $F2type = 202;
            }
            $F2 = min($PLANET[$RESOURCE[$F2type]], ceil($amountTMP / $F2capacity));
            $amountTMP -= $F2 * $F2capacity;
        }
        //------------------------------------------------------------------------

        if ($amountTMP > 0)
        {
            return $LNG['market_p_msg_more_ships_is_needed'];
        }

        $fleetArrayTMP = [];
        $fleetArrayTMP = [$F1type => $F1, $F2type => $F2];
        $fleetArray = $fleetArrayTMP;
        $fleetArray = array_filter($fleetArrayTMP);
        $SpeedFactor = FleetFunctions::GetGameSpeedFactor();
        $distance = FleetFunctions::GetTargetDistance([$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']], [$fleet_result['fleet_end_galaxy'], $fleet_result['fleet_end_system'], $fleet_result['fleet_end_planet']]);
        $speed_all_min = FleetFunctions::GetFleetMaxSpeed($fleetArray, $USER);
        $Duration = FleetFunctions::GetMissionDuration(10, $speed_all_min, $distance, $SpeedFactor, $USER);
        $consumption = FleetFunctions::GetFleetConsumption($fleetArray, $Duration, $distance, $USER, $SpeedFactor);

        $fleetStartTime = $Duration + TIMESTAMP;
        $fleetStayTime = $fleetStartTime;
        $fleetEndTime = $fleetStayTime + $Duration;

        $met = 0;
        $cry = 0;
        $deu = 0;
        if ($fleet_result['ex_resource_type'] == 1)
        {
            $met = $amount;
        }
        elseif ($fleet_result['ex_resource_type'] == 2)
        {
            $cry = $amount;
        }
        elseif ($fleet_result['ex_resource_type'] == 3)
        {
            $deu = $amount;
        }

        $fleet_resource = [
            901 => $met,
            902 => $cry,
            903 => $deu,
        ];

        if ($PLANET[$RESOURCE[901]] - $fleet_resource[901] < 0
            || $PLANET[$RESOURCE[902]] - $fleet_resource[902] < 0
            || $PLANET[$RESOURCE[903]] - $fleet_resource[903] - $consumption < 0)
        {
            return $LNG['market_p_msg_resources_error'];
        }

        $PLANET[$RESOURCE[901]] -= $fleet_resource[901];
        $PLANET[$RESOURCE[902]] -= $fleet_resource[902];
        $PLANET[$RESOURCE[903]] -= $fleet_resource[903] + $consumption;

        $buyerfleet = FleetFunctions::sendFleet(
            $fleetArray,
            3/*Transport*/,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $fleet_result['fleet_owner'],
            $fleet_result['fleet_start_id'],
            $fleet_result['fleet_start_galaxy'],
            $fleet_result['fleet_start_system'],
            $fleet_result['fleet_start_planet'],
            $fleet_result['fleet_start_type'],
            $fleet_resource,
            $fleetStartTime,
            $fleetStayTime,
            $fleetEndTime,
            0,
            0,
            1
        );

        /////////////////////////////////////////////////////////////////////////////
        /// SEND/
        $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";
        $USER_2 = Database::get()->selectSingle($sql, [
            ':userId' => $fleet_result['fleet_owner'],
        ]);
        $fleet_array = FleetFunctions::unserialize($fleet_result['fleet_array']);
        $speed_factor = FleetFunctions::GetGameSpeedFactor();
        $distance = FleetFunctions::GetTargetDistance([$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']], [$fleet_result['fleet_end_galaxy'], $fleet_result['fleet_end_system'], $fleet_result['fleet_end_planet']]);
        $speed_all_min = FleetFunctions::GetFleetMaxSpeed($fleet_array, $USER_2);
        $Duration = FleetFunctions::GetMissionDuration(10, $speed_all_min, $distance, $speed_factor, $USER_2);
        //$consumption		= FleetFunctions::GetFleetConsumption($fleet_array, $Duration, $distance, $fleetResult['fleet_owner'], $SpeedFactor);

        $fleetStartTime = $Duration + TIMESTAMP;
        $fleetStayTime = $fleetStartTime;
        $fleetEndTime = $fleetStayTime + $Duration;

        $params = [
            ':fleetID'            => $fleet_id,
            ':fleet_target_owner' => $USER['id'],
            ':fleet_end_id'       => $PLANET['id'],
            ':fleet_end_planet'   => $PLANET['planet'],
            ':fleet_end_system'   => $PLANET['system'],
            ':fleet_end_galaxy'   => $PLANET['galaxy'],
            ':fleet_start_time'   => $fleetStartTime,
            ':fleet_end_stay'     => $fleetStayTime,
            ':fleet_end_time'     => $fleetEndTime,
            ':fleet_mission'      => $fleet_result['transaction_type'] == 0 ? 3 : 17,
            ':fleet_no_m_return'  => 1,
            ':fleet_mess'         => 0,
        ];

        $sql = "UPDATE %%FLEETS%% SET `fleet_no_m_return` = :fleet_no_m_return, `fleet_end_id` = :fleet_end_id,`fleet_target_owner` = :fleet_target_owner, `fleet_mess` = :fleet_mess, `fleet_mission` = :fleet_mission, `fleet_end_stay` = :fleet_end_stay ,`fleet_end_time` = :fleet_end_time ,`fleet_start_time` = :fleet_start_time , `fleet_end_planet` = :fleet_end_planet, `fleet_end_system` = :fleet_end_system, `fleet_end_galaxy` = :fleet_end_galaxy WHERE fleet_id = :fleetID;";
        $db->update($sql, $params);

        $sql = "UPDATE %%LOG_FLEETS%% SET `fleet_no_m_return` = :fleet_no_m_return, `fleet_end_id` = :fleet_end_id,`fleet_target_owner` = :fleet_target_owner, `fleet_mess` = :fleet_mess, `fleet_mission` = :fleet_mission, `fleet_end_stay` = :fleet_end_stay ,`fleet_end_time` = :fleet_end_time ,`fleet_start_time` = :fleet_start_time , `fleet_end_planet` = :fleet_end_planet, `fleet_end_system` = :fleet_end_system, `fleet_end_galaxy` = :fleet_end_galaxy WHERE fleet_id = :fleetID;";
        $db->update($sql, $params);

        $sql = 'UPDATE %%FLEETS_EVENT%% SET  `time` = :endTime WHERE fleetID	= :fleetId;';
        $db->update($sql, [
            ':fleetId' => $fleet_id,
            ':endTime' => $fleetStartTime,
        ]);

        $sql = 'UPDATE %%TRADES%% SET  `buyer_fleet_id` = :buyerFleetId,`buy_time` = NOW() WHERE seller_fleet_id	= :fleetId;';
        $db->update($sql, [
            ':fleetId'      => $fleet_id,
            ':buyerFleetId' => $buyerfleet,
        ]);

        $LC = 0;
        $HC = 0;

        if (array_key_exists(202, $fleetArrayTMP))
        {
            $LC = $fleetArrayTMP[202];
        }

        if (array_key_exists(203, $fleetArrayTMP))
        {
            $HC = $fleetArrayTMP[203];
        }

        // To customer
        $message = sprintf(
            $LNG['market_msg_trade_bought'],
            $fleet_result['fleet_start_galaxy'].":".$fleet_result['fleet_start_system'].":".$fleet_result['fleet_start_planet'],
            $fleet_resource[901],
            $LNG['tech'][901],
            $fleet_resource[902],
            $LNG['tech'][902],
            $fleet_resource[903],
            $LNG['tech'][903],
            $consumption,
            $LNG['tech'][903]
        );

        PlayerUtil::sendMessage(
            $USER['id'],
            0,
            $LNG['market_msg_trade_from'],
            4,
            $LNG['market_msg_trade_topic'],
            $message,
            TIMESTAMP,
            null,
            1,
            $fleet_result['fleet_universe']
        );

        // To salesmen
        $message = sprintf(
            $LNG['market_msg_trade_sold'],
            $PLANET['galaxy'].":".$PLANET['system'].":".$PLANET['planet'],
            $fleet_result['fleet_resource_metal'],
            $LNG['tech'][901],
            $fleet_result['fleet_resource_crystal'],
            $LNG['tech'][902],
            $fleet_result['fleet_resource_deuterium'],
            $LNG['tech'][903]
        );

        PlayerUtil::sendMessage(
            $fleet_result['fleet_owner'],
            0,
            $LNG['market_msg_trade_from'],
            4,
            $LNG['market_msg_trade_topic'],
            $message,
            TIMESTAMP,
            null,
            1,
            $fleet_result['fleet_universe']
        );

        return sprintf($LNG['market_p_msg_sent'], $LC, $HC);
    }

    public function show(): void
    {
        global $USER, $PLANET, $LNG;

        $action = HTTP::_GP('action', "");
        $db = Database::get();

        $message = "";
        if ($action == "buy")
        {
            $message = $this->doBuy();
        }

        $sql = 'SELECT *
			FROM %%FLEETS%%
			JOIN %%USERS%% ON fleet_owner = id
			JOIN %%TRADES%% ON fleet_id = seller_fleet_id
			LEFT JOIN (
				SELECT owner_2 as al ,level, accept  FROM %%DIPLO%% WHERE owner_1 = :al
				UNION
				SELECT owner_1 as al,level, accept  FROM %%DIPLO%% WHERE owner_2 = :al) as packts
			ON al = ally_id
			WHERE fleet_mission = 16 AND fleet_mess = 2 ORDER BY fleet_end_time ASC;';

        $fleet_result = $db->select($sql, [
            ':al' => $USER['ally_id'],
        ]);

        $flying_fleet_list = [];

        foreach ($fleet_result as $c_fleet)
        {
            $res_name = " ";
            // TODO TRANSLATION
            switch ($c_fleet['ex_resource_type'])
            {
                case 1:
                    $res_name = $LNG['tech'][901];
                    break;
                case 2:
                    $res_name = $LNG['tech'][902];
                    break;
                case 3:
                    $res_name = $LNG['tech'][903];
                    break;
                default:
                    break;
            }

            // Level of diplo
            if ($c_fleet['accept'] == 0)
            {
                $c_fleet['level'] = null;
            }

            $speed_factor = FleetFunctions::GetGameSpeedFactor();
            // FROM
            $FROM_fleet = FleetFunctions::unserialize($c_fleet['fleet_array']);
            $FROM_Distance = FleetFunctions::GetTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$c_fleet['fleet_end_galaxy'], $c_fleet['fleet_end_system'], $c_fleet['fleet_end_planet']]
            );
            $FROM_SpeedAllMin = FleetFunctions::GetFleetMaxSpeed($FROM_fleet, $c_fleet);
            $FROM_Duration = FleetFunctions::GetMissionDuration(
                10,
                $FROM_SpeedAllMin,
                $FROM_Distance,
                $speed_factor,
                $c_fleet
            );

            // TO
            $TO_Distance = FleetFunctions::GetTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$c_fleet['fleet_start_galaxy'], $c_fleet['fleet_start_system'], $c_fleet['fleet_start_planet']]
            );
            $TO_LC_SPEED = FleetFunctions::GetFleetMaxSpeed([202 => 1], $USER);
            $TO_LC_DUR = FleetFunctions::GetMissionDuration(
                10,
                $TO_LC_SPEED,
                $TO_Distance,
                $speed_factor,
                $USER
            );
            $TO_HC_SPEED = FleetFunctions::GetFleetMaxSpeed([203 => 1], $USER);
            $TO_HC_DUR = FleetFunctions::GetMissionDuration(
                10,
                $TO_HC_SPEED,
                $TO_Distance,
                $speed_factor,
                $USER
            );

            $fleet_str = '';
            foreach ($FROM_fleet as $name => $amount)
            {
                $fleet_str .= $LNG['shortNames'][$name].' x'.$amount."\n";
            }

            //Level 5 - enemies
            //Level 0 - 3 alliance
            $buy = $this->checkDiplo(
                $c_fleet['filter_visibility'],
                $c_fleet['level'],
                $c_fleet['ally_id'],
                $USER['ally_id']
            );

            //Fleet market
            if ($buy['buyable']
                && $c_fleet['transaction_type'] == 1)
            {
                $buy = $this->checkTechs($c_fleet);
            }

            $total = $c_fleet['fleet_resource_metal'] + $c_fleet['fleet_resource_crystal'] + $c_fleet['fleet_resource_deuterium'];

            $flying_fleet_list[] = [
                'id'       => $c_fleet['fleet_id'],
                'username' => $c_fleet['username'],
                'type'     => $c_fleet['transaction_type'],

                'fleet_resource_metal'         => $c_fleet['fleet_resource_metal'],
                'fleet_resource_crystal'       => $c_fleet['fleet_resource_crystal'],
                'fleet_resource_deuterium'     => $c_fleet['fleet_resource_deuterium'],
                'fleet'                        => $fleet_str,
                'diplo'                        => $c_fleet['level'],
                'from_alliance'                => $c_fleet['ally_id'] == $USER['ally_id'],
                'possible_to_buy'              => $buy['buyable'],
                'reason'                       => $buy['reason'],
                'fleet_wanted_resource'        => $res_name,
                'fleet_wanted_resource_id'     => $c_fleet['ex_resource_type'],
                'fleet_wanted_resource_amount' => $c_fleet['ex_resource_amount'],

                'end' => $c_fleet['fleet_end_stay'] - TIMESTAMP,

                'from_duration'  => $FROM_Duration,
                'to_lc_duration' => $TO_LC_DUR,
                'to_hc_duration' => $TO_HC_DUR,
                //'distance' => $FROM_Duration//$Distance    		= FleetFunctions::GetTargetDistance(array($PLANET['galaxy'], $PLANET['system'], $PLANET['planet']), array($fleetsRow['fleet_end_galaxy'], $fleetsRow['fleet_end_system'], $fleetsRow['fleet_end_planet'])),
            ];
        }

        $this->tpl_obj->loadscript('marketplace.js');

        $this->assign([
            'message'           => $message,
            'flying_fleet_fist' => $flying_fleet_list,
            'resource_history'  => $this->getResourceTradeHistory(),
            'fleet_history'     => $this->getFleetTradeHistory(),
        ]);

        $this->display('page.marketPlace.default.tpl');
    }
}
