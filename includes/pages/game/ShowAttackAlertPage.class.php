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

        $sql = "SELECT
        SUM(CASE WHEN fleet_mission IN (1,9) THEN 1 ELSE 0 END) AS attack,
        SUM(CASE WHEN fleet_mission = 6 THEN 1 ELSE 0 END) AS spy
        FROM %%FLEETS%%
        WHERE
        fleet_owner != :userId
        AND fleet_mess = 0
        AND fleet_universe = :universe
        AND fleet_target_owner = :userId
        AND hasCanceled = 0;";

        $fleets = $db->selectSingle($sql, [
            ':userId'   => $USER['id'],
            ':universe' => Universe::current(),
        ]);

        $data = "noattack";
        if ($fleets)
        {
            if ($fleets['attack'] > 0
                && $fleets['spy'] > 0)
            {
                $data = "spy";
            }
            elseif ($fleets['attack'] > 0
                && $fleets['spy'] == 0)
            {
                $data = "attack";
            }
            elseif ($fleets['spy'] > 0
                && $fleets['attack'] == 0)
            {
                $data = "spy";
            }
        }

        $this->sendJSON($data);
    }

}
