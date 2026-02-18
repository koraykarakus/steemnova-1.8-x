<?php

class ShowFlightSimulatorPage extends AbstractGamePage
{
    public static $requireModule = MODULE_FLIGHT_SIMULATOR;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        global $USER, $LNG, $reslist, $resource, $PLANET;

        $possibleShips = [];

        foreach ($reslist['fleet'] as $ID)
        {
            if ($ID == 212 or $ID == 221)
            {
                continue;
            }

            $possibleShips[] = [
                'id'    => $ID,
                'count' => $PLANET[$resource[$ID]],
            ];

        }

        $this->assign([
            'startGalaxy'    => $PLANET['galaxy'],
            'startSystem'    => $PLANET['system'],
            'startPlanet'    => $PLANET['planet'],
            'ships'          => $possibleShips,
            'combustionTech' => $USER['combustion_tech'],
            'hyperspaceTech' => $USER['hyperspace_motor_tech'],
            'impulseTech'    => $USER['impulse_motor_tech'],
            'page'           => HTTP::_GP('page', ''),
        ]);

        $this->display('page.flightSimulator.default.tpl');
    }

    public function calcFleetSpeed()
    {
        global $USER,$PLANET,$reslist;

        $fleet = $player = [];
        foreach ($reslist['fleet'] as $ID)
        {
            if ($ID == 212 or $ID == 221)
            {
                continue;
            }

            $fleet[$ID] = HTTP::_GP("ship_$ID", 0);

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
        $maxSpeed = FleetFunctions::GetFleetMaxSpeed($fleet, $player);
        $gameSpeed = FleetFunctions::GetGameSpeedFactor();

        $speedFactor = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1];

        $timeSeconds = [];

        if ($maxSpeed)
        {
            foreach ($speedFactor as $factor)
            {
                $timeSeconds[$factor] = round(FleetFunctions::GetMissionDuration($factor, $maxSpeed, $distance, $gameSpeed, $player));
            }
        }

        $this->sendJSON($timeSeconds);

    }

}
