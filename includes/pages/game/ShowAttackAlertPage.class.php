<?php

class ShowAttackAlertPage extends AbstractGamePage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {

        global $USER;

        $db = Database::get();

        $sql = "SELECT (SELECT
  		COUNT(*) FROM %%FLEETS%% WHERE
  		fleet_owner != :userId AND fleet_mess = 0 AND fleet_universe = :universe AND fleet_target_owner = :userId AND (fleet_mission = 1 OR fleet_mission = 9) AND hasCanceled=0) AS attack,
  		(SELECT
  		COUNT(*) FROM %%FLEETS%% WHERE
  		fleet_owner != :userId AND fleet_mess = 0 AND fleet_universe = :universe AND fleet_target_owner = :userId AND fleet_mission = 6 AND hasCanceled=0) AS spy
  		FROM DUAL ";

        $fleets = $db->selectSingle($sql, [
            ':userId'   => $USER['id'],
            ':universe' => Universe::current(),
        ]);

        if ($fleets['attack'] > 0 && $fleets['spy'] > 0)
        {
            $data = "spy";
        }
        elseif ($fleets['attack'] > 0 && $fleets['spy'] == 0)
        {
            $data = "attack";
        }
        elseif ($fleets['spy'] > 0 && $fleets['attack'] == 0)
        {
            $data = "spy";
        }
        else
        {
            $data = "noattack";
        }

        $this->sendJSON($data);

    }

}
