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

class ShowFleetTablePage extends AbstractGamePage
{
    public static $require_module = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $PLANET, $RESLIST, $RESOURCE, $config;

        $db = Database::get();

        $this->tpl_obj->loadscript('flotten.js');

        $active_expedition = FleetFunctions::GetCurrentFleets($USER['id'], 15, true);
        $max_expedition = FleetFunctions::getExpeditionLimit($USER);
        $max_fleet_slots = FleetFunctions::GetMaxFleetSlots($USER);

        $target_galaxy = HTTP::_GP('galaxy', (int) $PLANET['galaxy']);
        $target_system = HTTP::_GP('system', (int) $PLANET['system']);
        $target_planet = HTTP::_GP('planet', (int) $PLANET['planet']);
        $target_type = HTTP::_GP('planettype', (int) $PLANET['planet_type']);
        $target_mission = HTTP::_GP('target_mission', 0);

        $active_fleet_slots = $db->rowCount();

        $fleets_on_planet = [];

        foreach ($RESLIST['fleet'] as $c_fleet_id)
        {
            if ($PLANET[$RESOURCE[$c_fleet_id]] == 0)
            {
                continue;
            }

            $fleets_on_planet[] = [
                'id'    => $c_fleet_id,
                'speed' => FleetFunctions::GetFleetMaxSpeed($c_fleet_id, $USER),
                'count' => $PLANET[$RESOURCE[$c_fleet_id]],
            ];
        }

        $stay_selector = [];

        for ($i = 1; $i <= $USER[$RESOURCE[124]]; $i++)
        {
            $stay_selector[$i] = $i / $config->expedition_speed;
        }

        $this->assign([
            'fleets_on_planet'     => $fleets_on_planet,
            'active_expedition'    => $active_expedition,
            'max_expedition'       => $max_expedition,
            'active_fleet_slots'   => $active_fleet_slots,
            'max_fleet_slots'      => $max_fleet_slots,
            'target_galaxy'        => $target_galaxy,
            'target_system'        => $target_system,
            'target_planet'        => $target_planet,
            'target_type'          => $target_type,
            'target_mission'       => $target_mission,
            'in_vacation'          => inVacationMode($USER),
            'bonus_attack'         => $USER[$RESOURCE[109]] * 10 + $USER['factor']['Attack'] * 100,
            'bonus_defensive'      => $USER[$RESOURCE[110]] * 10 + $USER['factor']['Defensive'] * 100,
            'bonus_shield'         => $USER[$RESOURCE[111]] * 10 + $USER['factor']['Shield'] * 100,
            'bonus_combustion'     => $USER[$RESOURCE[115]] * 10,
            'bonus_impulse'        => $USER[$RESOURCE[117]] * 20,
            'bonus_hyperspace'     => $USER[$RESOURCE[118]] * 30,
            'galaxy'               => $PLANET['galaxy'],
            'system'               => $PLANET['system'],
            'stay_selector'        => $stay_selector,
            'recaptcha_public_key' => $config->google_recaptcha_public_key,
        ]);

        $this->display('page.fleetTable.default.tpl');
    }

}
