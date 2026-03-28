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

class ShowInformationPage extends AbstractGamePage
{
    public static $require_module = MODULE_INFORMATION;

    protected $disable_eco_system = true;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getNextJumpWaitTime($last_time): int
    {
        return (int) $last_time + (int) Config::get()->gate_wait_time;
    }

    public function sendFleet(): void
    {
        global $PLANET, $USER, $RESOURCE, $LNG, $RESLIST;

        $db = Database::get();

        $next_jump_time = self::getNextJumpWaitTime($PLANET['last_jump_time']);

        if (TIMESTAMP < $next_jump_time)
        {
            $this->sendJSON([
                'message' => $LNG['in_jump_gate_already_used'].' '.pretty_time($next_jump_time - TIMESTAMP),
                'error'   => true,
            ]);
        }

        $target_planet = HTTP::_GP('jmpto', (int) $PLANET['id']);

        $sql = "SELECT id, last_jump_time 
        FROM %%PLANETS%% 
        WHERE id = :target_id AND id_owner = :user_id AND jump_gate > 0;";

        $target_gate = $db->selectSingle($sql, [
            ':target_id' => $target_planet,
            ':user_id'   => $USER['id'],
        ]);

        if (!isset($target_gate)
            || $target_planet == $PLANET['id'])
        {
            $this->sendJSON([
                'message' => $LNG['in_jump_gate_doesnt_have_one'],
                'error'   => true,
            ]);
        }

        $next_jump_time = self::getNextJumpWaitTime($target_gate['last_jump_time']);

        if (TIMESTAMP < $next_jump_time)
        {
            $this->sendJSON([
                'message' => $LNG['in_jump_gate_not_ready_target'].' '.pretty_time($next_jump_time - TIMESTAMP),
                'error'   => true,
            ]);
        }

        $ship_array = [];
        $sub_query_ori = "";
        $sub_query_des = "";
        $ships = HTTP::_GP('ship', []);

        foreach ($RESLIST['fleet'] as $c_ship)
        {
            if (!isset($ships[$c_ship])
                || $c_ship == 212)
            {
                continue;
            }

            $ship_array[$c_ship] = max(0, min($ships[$c_ship], $PLANET[$RESOURCE[$c_ship]]));

            if (empty($ship_array[$c_ship]))
            {
                continue;
            }

            $sub_query_ori .= $RESOURCE[$c_ship]." = ".$RESOURCE[$c_ship]." - ".$ship_array[$c_ship].", ";
            $sub_query_des .= $RESOURCE[$c_ship]." = ".$RESOURCE[$c_ship]." + ".$ship_array[$c_ship].", ";
            $PLANET[$RESOURCE[$c_ship]] -= $ship_array[$c_ship];
        }

        if (empty($sub_query_ori))
        {
            $this->sendJSON([
                'message' => $LNG['in_jump_gate_error_data'],
                'error'   => true,
            ]);
        }

        $array_merge = [':planet_id' => $PLANET['id'], ':jump_time' => TIMESTAMP];
        $sql = "UPDATE %%PLANETS%% 
        SET ".$sub_query_ori." `last_jump_time` = :jump_time WHERE id = :planet_id;";

        $db->update($sql, $array_merge);

        $sql = "UPDATE %%PLANETS%% 
        SET ".$sub_query_des." `last_jump_time` = :jump_time WHERE id = :target_id;";

        $db->update($sql, [
            ':target_id' => $target_planet,
            ':jump_time' => TIMESTAMP,
        ]);

        $PLANET['last_jump_time'] = TIMESTAMP;
        $next_jump_time = self::getNextJumpWaitTime($PLANET['last_jump_time']);
        $this->sendJSON([
            'message' => sprintf($LNG['in_jump_gate_done'], pretty_time($next_jump_time - TIMESTAMP)),
            'error'   => false,
        ]);
    }

    private function getAvailableFleets(): array
    {
        global $RESLIST, $RESOURCE, $PLANET;

        $fleet_list = [];

        foreach ($RESLIST['fleet'] as $ship)
        {
            if ($ship == 212
                || $PLANET[$RESOURCE[$ship]] <= 0)
            {
                continue;
            }

            $fleet_list[$ship] = $PLANET[$RESOURCE[$ship]];
        }

        return $fleet_list;
    }

    public function destroyMissiles(): void
    {
        global $RESOURCE, $PLANET;

        $db = Database::get();

        $missile = HTTP::_GP('missile', []);
        $PLANET[$RESOURCE[502]] -= max(0, min($missile[502], $PLANET[$RESOURCE[502]]));
        $PLANET[$RESOURCE[503]] -= max(0, min($missile[503], $PLANET[$RESOURCE[503]]));

        $sql = "UPDATE %%PLANETS%% SET ".$RESOURCE[502]." = :resource_502_val, ".$RESOURCE[503]." = :resource_503_val WHERE id = :planet_id;";
        $db->update($sql, [
            ':resource_502_val' => $PLANET[$RESOURCE[502]],
            ':resource_503_val' => $PLANET[$RESOURCE[503]],
            ':planet_id'        => $PLANET['id'],
        ]);

        $this->sendJSON([$PLANET[$RESOURCE[502]], $PLANET[$RESOURCE[503]]]);
    }

    private function getTargetGates(): array
    {
        global $RESOURCE, $USER, $PLANET;

        $db = Database::get();

        $order = $USER['planet_sort_order'] == 1 ? "DESC" : "ASC" ;
        $sort = $USER['planet_sort'];

        $sql = "SELECT id, name, galaxy, system, planet, last_jump_time, " .
        $RESOURCE[43] .
        " FROM %%PLANETS%% WHERE id != :planetID AND id_owner = :userID AND planet_type = '3' AND " .
        $RESOURCE[43] .
        " > 0 ORDER BY ";

        switch ($sort)
        {
            case 1:
                $sql .= 'galaxy ' .
                $order .
                ', system ' .
                $order .
                ', planet ' .
                $order .
                ', planet_type ' .
                $order;
                break;
            case 2:
                $sql .= 'name ' . $order;
                break;
            default:
                $sql .= 'id ' . $order;
                break;
        }

        $moon_result = $db->select($sql, [
            ':planetID' => $PLANET['id'],
            ':userID'   => $USER['id'],
        ]);

        $moon_list = [];

        foreach ($moon_result as $c_moon)
        {
            $next_jump_time = self::getNextJumpWaitTime($c_moon['last_jump_time']);
            $moon_list[$c_moon['id']] = '[' .
            $c_moon['galaxy'] .
            ':' .
            $c_moon['system'] .
            ':' .
            $c_moon['planet'] .
            '] ' .
            $c_moon['name'] .
            (TIMESTAMP < $next_jump_time ? ' (' . pretty_time($next_jump_time - TIMESTAMP) . ')' : '');
        }

        return $moon_list;
    }

    public function show(): void
    {
        global $USER, $PLANET, $LNG, $RESOURCE, $PRICELIST, $RESLIST, $COMBATCAPS, $PRODGRID;

        $element_id = HTTP::_GP('id', 0);

        $this->setWindow('popup');
        $this->initTemplate();

        $production_table = [];
        $fleet_info = [];
        $missile_list = [];
        $gate_data = [];

        $current_level = 0;

        $ress_ids = array_merge([], $RESLIST['resstype'][1], $RESLIST['resstype'][2]);

        if (in_array($element_id, $RESLIST['prod']))
        {

            /* Data for eval */
            $BuildEnergy = $USER[$RESOURCE[113]];
            $BuildTemp = $PLANET['temp_max'];
            $build_level_factor = $PLANET[$RESOURCE[$element_id].'_percent'];

            $current_level = $PLANET[$RESOURCE[$element_id]];
            $build_start_lvl = max($current_level - 2, 0);
            for ($build_level = $build_start_lvl; $build_level < $build_start_lvl + 15; $build_level++)
            {
                foreach ($ress_ids as $c_id)
                {

                    if (!isset($PRODGRID[$element_id]['production'][$c_id]))
                    {
                        continue;
                    }

                    $production = eval(ResourceUpdate::getProd($PRODGRID[$element_id]['production'][$c_id], $element_id));

                    if (in_array($c_id, $RESLIST['resstype'][2]))
                    {
                        $production *= Config::get()->energySpeed;
                    }
                    else
                    {
                        $production *= Config::get()->resource_multiplier;
                    }

                    $production_table['production'][$build_level][$c_id] = $production;
                }
            }

            $production_table['usedResource'] = array_keys($production_table['production'][$build_start_lvl]);
        }
        if (in_array($element_id, $RESLIST['storage']))
        {
            $current_level = $PLANET[$RESOURCE[$element_id]];
            $build_start_lvl = max($current_level - 2, 0);

            for ($build_level = $build_start_lvl; $build_level < $build_start_lvl + 15; $build_level++)
            {
                foreach ($ress_ids as $c_id)
                {
                    if (!isset($PRODGRID[$element_id]['storage'][$c_id]))
                    {
                        continue;
                    }

                    $production = round(eval(ResourceUpdate::getProd($PRODGRID[$element_id]['storage'][$c_id])));
                    $production *= Config::get()->storage_multiplier;

                    $production_table['storage'][$build_level][$c_id] = $production;
                }
            }

            $production_table['usedResource'] = array_keys($production_table['storage'][$build_start_lvl]);
        }
        if (in_array($element_id, $RESLIST['fleet']))
        {
            $fleet_info = [
                'structure'    => $PRICELIST[$element_id]['cost'][901] + $PRICELIST[$element_id]['cost'][902],
                'tech'         => $PRICELIST[$element_id]['tech'],
                'attack'       => $COMBATCAPS[$element_id]['attack'],
                'shield'       => $COMBATCAPS[$element_id]['shield'],
                'capacity'     => $PRICELIST[$element_id]['capacity'],
                'speed1'       => $PRICELIST[$element_id]['speed'],
                'speed2'       => $PRICELIST[$element_id]['speed2'],
                'consumption1' => $PRICELIST[$element_id]['consumption'],
                'consumption2' => $PRICELIST[$element_id]['consumption2'],
                'rapidfire'    => [
                    'from' => [],
                    'to'   => [],
                ],
            ];

            $fleet_ids = array_merge($RESLIST['fleet'], $RESLIST['defense']);

            foreach ($fleet_ids as $fleetID)
            {
                if (isset($COMBATCAPS[$element_id]['sd'])
                    && !empty($COMBATCAPS[$element_id]['sd'][$fleetID]))
                {
                    $fleet_info['rapidfire']['to'][$fleetID] = $COMBATCAPS[$element_id]['sd'][$fleetID];
                }

                if (isset($COMBATCAPS[$fleetID]['sd'])
                    && !empty($COMBATCAPS[$fleetID]['sd'][$element_id]))
                {
                    $fleet_info['rapidfire']['from'][$fleetID] = $COMBATCAPS[$fleetID]['sd'][$element_id];
                }
            }
        }
        if (in_array($element_id, $RESLIST['defense']))
        {
            $fleet_info = [
                'structure' => $PRICELIST[$element_id]['cost'][901] + $PRICELIST[$element_id]['cost'][902],
                'attack'    => $COMBATCAPS[$element_id]['attack'],
                'shield'    => $COMBATCAPS[$element_id]['shield'],
                'rapidfire' => [
                    'from' => [],
                    'to'   => [],
                ],
            ];

            $fleet_ids = array_merge($RESLIST['fleet'], $RESLIST['defense']);

            foreach ($fleet_ids as $c_fleet_id)
            {
                if (isset($COMBATCAPS[$element_id]['sd'])
                    && !empty($COMBATCAPS[$element_id]['sd'][$c_fleet_id]))
                {
                    $fleet_info['rapidfire']['to'][$c_fleet_id] = $COMBATCAPS[$element_id]['sd'][$c_fleet_id];
                }

                if (isset($COMBATCAPS[$c_fleet_id]['sd'])
                    && !empty($COMBATCAPS[$c_fleet_id]['sd'][$element_id]))
                {
                    $fleet_info['rapidfire']['from'][$c_fleet_id] = $COMBATCAPS[$c_fleet_id]['sd'][$element_id];
                }
            }
        }

        if ($element_id == 43
            && $PLANET[$RESOURCE[43]] > 0)
        {
            $this->tpl_obj->loadscript('gate.js');
            $next_time = self::getNextJumpWaitTime($PLANET['last_jump_time']);
            $gate_data = [
                'nextTime'  => _date($LNG['php_tdformat'], $next_time, $USER['timezone']),
                'restTime'  => max(0, $next_time - TIMESTAMP),
                'startLink' => $PLANET['name'].' '.strip_tags(BuildPlanetAddressLink($PLANET)),
                'gateList'  => $this->getTargetGates(),
                'fleetList' => $this->getAvailableFleets(),
            ];
        }
        elseif ($element_id == 44
                && $PLANET[$RESOURCE[44]] > 0)
        {
            $missile_list = [
                502 => $PLANET[$RESOURCE[502]],
                503 => $PLANET[$RESOURCE[503]],
            ];
        }

        $this->assign([
            'elementID'       => $element_id,
            'productionTable' => $production_table,
            'CurrentLevel'    => $current_level,
            'MissileList'     => $missile_list,
            'FleetInfo'       => $fleet_info,
            'gateData'        => $gate_data,
        ]);

        if ($element_id <= 900
            || $element_id >= 930)
        {
            $this->assign([
                'Bonus' => BuildFunctions::getAvalibleBonus($element_id),
            ]);
        }

        $this->display('page.information.default.tpl');
    }
}
