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
        global $USER, $PLANET, $reslist, $resource, $config;

        $db = Database::get();

        $this->tpl_obj->loadscript('flotten.js');

        $tech_expedition = $USER[$resource[124]];

        $active_expedition = 0;
        $max_expedition = 0;
        if ($tech_expedition >= 1)
        {
            $active_expedition = FleetFunctions::GetCurrentFleets($USER['id'], 15, true);
            $max_expedition = floor(sqrt($tech_expedition)) + $USER['factor']['Expedition'];
        }

        $max_fleet_slots = FleetFunctions::GetMaxFleetSlots($USER);

        $target_galaxy = HTTP::_GP('galaxy', (int) $PLANET['galaxy']);
        $target_system = HTTP::_GP('system', (int) $PLANET['system']);
        $target_planet = HTTP::_GP('planet', (int) $PLANET['planet']);
        $target_type = HTTP::_GP('planettype', (int) $PLANET['planet_type']);
        $target_mission = HTTP::_GP('target_mission', 0);

        $active_fleet_slots = $db->rowCount();

        $fleets_on_planet = [];

        foreach ($reslist['fleet'] as $c_fleet_id)
        {
            if ($PLANET[$resource[$c_fleet_id]] == 0)
            {
                continue;
            }

            $fleets_on_planet[] = [
                'id'    => $c_fleet_id,
                'speed' => FleetFunctions::GetFleetMaxSpeed($c_fleet_id, $USER),
                'count' => $PLANET[$resource[$c_fleet_id]],
            ];
        }

        $stay_selector = [];

        for ($i = 1; $i <= $USER[$resource[124]]; $i++)
        {
            $stay_selector[$i] = $i / $config->expedition_speed;
        }

        $this->assign([
            'FleetsOnPlanet'     => $fleets_on_planet,
            'activeExpedition'   => $active_expedition,
            'maxExpedition'      => $max_expedition,
            'activeFleetSlots'   => $active_fleet_slots,
            'maxFleetSlots'      => $max_fleet_slots,
            'targetGalaxy'       => $target_galaxy,
            'targetSystem'       => $target_system,
            'targetPlanet'       => $target_planet,
            'targetType'         => $target_type,
            'targetMission'      => $target_mission,
            'isVacation'         => inVacationMode($USER),
            'bonusAttack'        => $USER[$resource[109]] * 10 + $USER['factor']['Attack'] * 100,
            'bonusDefensive'     => $USER[$resource[110]] * 10 + $USER['factor']['Defensive'] * 100,
            'bonusShield'        => $USER[$resource[111]] * 10 + $USER['factor']['Shield'] * 100,
            'bonusCombustion'    => $USER[$resource[115]] * 10,
            'bonusImpulse'       => $USER[$resource[117]] * 20,
            'bonusHyperspace'    => $USER[$resource[118]] * 30,
            'galaxy'             => $PLANET['galaxy'],
            'system'             => $PLANET['system'],
            'StaySelector'       => $stay_selector,
            'recaptchaPublicKey' => $config->google_recaptcha_public_key,
        ]);

        $this->display('page.fleetTable.default.tpl');
    }

}
