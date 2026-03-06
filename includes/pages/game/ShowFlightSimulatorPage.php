<?php

class ShowFlightSimulatorPage extends AbstractGamePage
{
    public static $require_module = MODULE_FLIGHT_SIMULATOR;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $reslist, $resource, $PLANET;

        $possible_ships = [];
        foreach ($reslist['fleet'] as $c_id)
        {
            if ($c_id == 212
                || $c_id == 221)
            {
                continue;
            }

            $possible_ships[] = [
                'id'    => $c_id,
                'count' => $PLANET[$resource[$c_id]],
            ];
        }

        $this->assign([
            'startGalaxy'    => $PLANET['galaxy'],
            'startSystem'    => $PLANET['system'],
            'startPlanet'    => $PLANET['planet'],
            'ships'          => $possible_ships,
            'combustionTech' => $USER['combustion_tech'],
            'hyperspaceTech' => $USER['hyperspace_motor_tech'],
            'impulseTech'    => $USER['impulse_motor_tech'],
            'page'           => HTTP::_GP('page', ''),
        ]);

        $this->display('page.flightSimulator.default.tpl');
    }

    public function calcFleetSpeed(): void
    {
        global $reslist;

        $fleet = $player = [];
        foreach ($reslist['fleet'] as $c_id)
        {
            if ($c_id == 212
                || $c_id == 221)
            {
                continue;
            }

            $fleet[$c_id] = HTTP::_GP("ship_$c_id", 0);
        }

        foreach ($fleet as $key => $count)
        {
            if ($count == 0)
            {
                unset($fleet[$key]);
            }
        }

        $player_class = 0;

        $player = [
            'hyperspace_motor_tech' => HTTP::_GP('hyperspaceTech', 0),
            'combustion_tech'       => HTTP::_GP('combustionTech', 0),
            'impulse_motor_tech'    => HTTP::_GP('impulseTech', 0),
            'player_class'          => $player_class,
        ];

        $start = [
            0 => HTTP::_GP('startGalaxy', 0),
            1 => HTTP::_GP('startSystem', 0),
            2 => HTTP::_GP('startPlanet', 0),
        ];

        $end = [
            0 => HTTP::_GP('endGalaxy', 0),
            1 => HTTP::_GP('endSystem', 0),
            2 => HTTP::_GP('endPlanet', 0),
        ];

        $distance = FleetFunctions::GetTargetDistance($start, $end);
        $max_speed = FleetFunctions::GetFleetMaxSpeed($fleet, $player);
        $game_speed = FleetFunctions::GetGameSpeedFactor();

        $speed_factor = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1];

        $time_seconds = [];

        if ($max_speed)
        {
            foreach ($speed_factor as $factor)
            {
                $time_seconds[$factor] = round(FleetFunctions::GetMissionDuration($factor, $max_speed, $distance, $game_speed, $player));
            }
        }

        $this->sendJSON($time_seconds);

    }

}
