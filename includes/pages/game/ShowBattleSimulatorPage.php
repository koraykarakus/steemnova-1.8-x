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

class ShowBattleSimulatorPage extends AbstractGamePage
{
    public static $require_module = MODULE_SIMULATOR;

    public function __construct()
    {
        parent::__construct();
    }

    public function send(): void
    {
        global $reslist, $pricelist, $LNG;

        if (!isset($_REQUEST['battleinput']))
        {
            $this->sendJSON(0);
        }

        $battle_array = $_REQUEST['battleinput'];
        $elements = [0, 0];
        foreach ($battle_array as $battle_slot_id => $battle_slot)
        {
            if (isset($battle_slot[0]) && (array_sum($battle_slot[0]) > 0 || $battle_slot_id == 0))
            {
                $attacker = [];
                $attacker['fleetDetail'] = [
                    'fleet_start_galaxy'       => 1,
                    'fleet_start_system'       => 33,
                    'fleet_start_planet'       => 7,
                    'fleet_start_type'         => 1,
                    'fleet_end_galaxy'         => 1,
                    'fleet_end_system'         => 33,
                    'fleet_end_planet'         => 7,
                    'fleet_end_type'           => 1,
                    'fleet_resource_metal'     => 0,
                    'fleet_resource_crystal'   => 0,
                    'fleet_resource_deuterium' => 0,
                ];

                $attacker['player'] = [
                    'id'            => (1000 + $battle_slot_id + 1),
                    'username'      => $LNG['bs_atter'].' Nr.'.($battle_slot_id + 1),
                    'military_tech' => $battle_slot[0][109],
                    'defence_tech'  => $battle_slot[0][110],
                    'shield_tech'   => $battle_slot[0][111],
                    'dm_defensive'  => 0,
                    'dm_attack'     => 0,
                ];

                $attacker['player']['factor'] = getFactors($attacker['player'], 'attack');

                foreach ($battle_slot[0] as $ID => $Count)
                {
                    if (!in_array($ID, $reslist['fleet']) || $battle_slot[0][$ID] <= 0)
                    {
                        unset($battle_slot[0][$ID]);
                    }
                }

                $attacker['unit'] = $battle_slot[0];

                $attackers[] = $attacker;
            }

            if (isset($battle_slot[1]) && (array_sum($battle_slot[1]) > 0 || $battle_slot_id == 0))
            {
                $defender = [];
                $defender['fleetDetail'] = [
                    'fleet_start_galaxy'       => 1,
                    'fleet_start_system'       => 33,
                    'fleet_start_planet'       => 7,
                    'fleet_start_type'         => 1,
                    'fleet_end_galaxy'         => 1,
                    'fleet_end_system'         => 33,
                    'fleet_end_planet'         => 7,
                    'fleet_end_type'           => 1,
                    'fleet_resource_metal'     => 0,
                    'fleet_resource_crystal'   => 0,
                    'fleet_resource_deuterium' => 0,
                ];

                $defender['player'] = [
                    'id'            => (2000 + $battle_slot_id + 1),
                    'username'      => $LNG['bs_deffer'].' Nr.'.($battle_slot_id + 1),
                    'military_tech' => $battle_slot[1][109],
                    'defence_tech'  => $battle_slot[1][110],
                    'shield_tech'   => $battle_slot[1][111],
                    'dm_attack'     => 0,
                    'dm_defensive'  => 0,
                ];

                $defender['player']['factor'] = getFactors($defender['player'], 'attack');

                foreach ($battle_slot[1] as $ID => $Count)
                {
                    if ((!in_array($ID, $reslist['fleet'])
                        && !in_array($ID, $reslist['defense']))
                        || $battle_slot[1][$ID] <= 0)
                    {
                        unset($battle_slot[1][$ID]);
                    }
                }

                $defender['unit'] = $battle_slot[1];
                $defenders[] = $defender;
            }
        }

        $LNG->includeData(['FLEET']);

        require_once 'includes/classes/missions/functions/calculateAttack.php';
        require_once 'includes/classes/missions/functions/calculateSteal.php';
        require_once 'includes/classes/missions/functions/GenerateReport.php';

        $combat_result = calculateAttack(
            $attackers,
            $defenders,
            Config::get()->debris_percentage_fleet,
            Config::get()->debris_percentage_defense
        );

        if ($combat_result['won'] == "a")
        {
            $steal_resource = calculateSteal($attackers, [
                'metal'     => $battle_array[0][1][901],
                'crystal'   => $battle_array[0][1][902],
                'deuterium' => $battle_array[0][1][903],
            ], true);
        }
        else
        {
            $steal_resource = [
                901 => 0,
                902 => 0,
                903 => 0,
            ];
        }

        $debris = [];

        foreach ([901, 902] as $element_id)
        {
            $debris[$element_id] = $combat_result['debris']['attacker'][$element_id] + 
            $combat_result['debris']['defender'][$element_id];
        }

        $debris_total = array_sum($debris);

        $moon_factor = Config::get()->moon_factor;
        $max_moon_chance = Config::get()->moon_chance;

        $chance_create_moon = round($debris_total / 100000 * $moon_factor);
        $chance_create_moon = min($chance_create_moon, $max_moon_chance);

        $sum_steal = array_sum($steal_resource);

        $steal_resource_information = sprintf(
            $LNG['bs_derbis_raport'],
            pretty_number(ceil($debris_total / $pricelist[209]['capacity'])),
            $LNG['tech'][209]
        );

        $steal_resource_information .= '<br>';

        $steal_resource_information .= sprintf(
            $LNG['bs_steal_raport'],
            pretty_number(ceil($sum_steal / $pricelist[202]['capacity'])),
            $LNG['tech'][202],
            pretty_number(ceil($sum_steal / $pricelist[203]['capacity'])),
            $LNG['tech'][203]
        );

        $report_info = [
            'thisFleet' => [
                'fleet_start_galaxy' => 1,
                'fleet_start_system' => 33,
                'fleet_start_planet' => 7,
                'fleet_start_type'   => 1,
                'fleet_end_galaxy'   => 1,
                'fleet_end_system'   => 33,
                'fleet_end_planet'   => 7,
                'fleet_end_type'     => 1,
                'fleet_start_time'   => TIMESTAMP,
            ],
            'debris'              => $debris,
            'stealResource'       => $steal_resource,
            'moonChance'          => $chance_create_moon,
            'moonDestroy'         => false,
            'moonName'            => null,
            'moonDestroyChance'   => null,
            'moonDestroySuccess'  => null,
            'fleetDestroyChance'  => null,
            'fleetDestroySuccess' => null,
            'additionalInfo'      => $steal_resource_information,
        ];

        $report_data = GenerateReport($combat_result, $report_info);
        $report_id = md5(uniqid('', true).TIMESTAMP);

        $db = Database::get();

        $sql = "INSERT INTO %%RW%% SET rid = :report_id, raport = :report_data, time = :time;";
        $db->insert($sql, [
            ':report_id'   => $report_id,
            ':report_data' => serialize($report_data),
            ':time'       => TIMESTAMP,
        ]);

        $this->sendJSON($report_id);
    }

    public function show(): void
    {
        global $USER, $PLANET, $reslist, $resource;

        $slots = HTTP::_GP('slots', 1);

        $battle_array[0][0][109] = $USER[$resource[109]];
        $battle_array[0][0][110] = $USER[$resource[110]];
        $battle_array[0][0][111] = $USER[$resource[111]];

        if (empty($_REQUEST['battleinput']))
        {
            foreach ($reslist['fleet'] as $ID)
            {
                if (FleetFunctions::GetFleetMaxSpeed($ID, $USER) > 0)
                {
                    // Add just flyable elements
                    $battle_array[0][0][$ID] = $PLANET[$resource[$ID]];
                }
            }
        }
        else
        {
            $battle_array = HTTP::_GP('battleinput', []);
        }

        if (isset($_REQUEST['im']))
        {
            foreach ($_REQUEST['im'] as $ID => $Count)
            {
                $battle_array[0][1][$ID] = floatToString($Count);
            }
        }

        $this->tpl_obj->loadscript('battlesim.js');

        $this->assign([
            'Slots'         => $slots,
            'battleinput'   => $battle_array,
            'fleetList'     => $reslist['fleet'],
            'defensiveList' => $reslist['defense'],
        ]);

        $this->display('page.battleSimulator.default.tpl');
    }
}
