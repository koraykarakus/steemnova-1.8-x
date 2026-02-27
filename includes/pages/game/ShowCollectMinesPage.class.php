<?php

/**
 *
 */

class ShowCollectMinesPage extends AbstractGamePage
{
    public static $require_module = MODULE_COLLECT_MINES;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $PLANET, $resource, $LNG, $db, $config;

        //Don't allow user to collect mine if in vacation mode
        if (isVacationMode($USER))
        {
            $this->printMessage($LNG['cm_error_1']);
        }

        $from = HTTP::_GP('from', '');

        if (!$config->collect_mines_under_attack)
        {
            $sql = "SELECT COUNT(*) as count FROM %%FLEETS%% WHERE
                    fleet_owner != :userId AND fleet_mess = 0 AND
                    fleet_target_owner = :userId AND fleet_mission = 1 AND hasCanceled = 0 AND fleet_start_time < :limitTime;";

            $attacking_fleets_count = $db->selectSingle($sql, [
                ':userId'    => $USER['id'],
                ':limitTime' => TIMESTAMP + 5 * 60,
            ], 'count');

            if ($attacking_fleets_count > 0)
            {
                $this->printMessage($LNG['cm_error_2']);
            }

        }

        $timelimit = $config->collect_mine_time_minutes * 60;

        $lastcollect = TIMESTAMP - $USER['last_collect_mine_time'];

        //if conditions is not satisfied return without calculating anything ..
        if ($lastcollect < $timelimit)
        {
            $this->printMessage(sprintf(
                $LNG['cm_error_3'],
                $config->collect_mine_time_minutes
            ));
        }

        $res_update_obj = new ResourceUpdate();

        $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :userID AND destruyed = '0'";

        $planets = $db->select($sql, [
            ':userID' => $USER['id'],
        ]);

        foreach ($planets as $c_planet)
        {
            list($USER, $c_planet) = $res_update_obj->CalcResource($USER, $c_planet, true);
            $PLANETS[] = $c_planet;
            unset($c_planet);
        }

        $metal = $crystal = $deuterium = [];

        foreach ($PLANETS as $c_planet)
        {
            if ($c_planet['id'] != $PLANET['id'])
            {
                $metal[] = $c_planet['metal'];
                $crystal[] = $c_planet['crystal'];
                $deuterium[] = $c_planet['deuterium'];
            }
        }

        //reset resources of other planets as 0
        $sql_reset = "UPDATE %%PLANETS%% SET metal = :metal, 
        deuterium = :deuterium, crystal = :crystal
        WHERE id_owner = :user_id AND id != :planet_id;";

        $db->update($sql_reset, [
            ':metal'     => 0,
            ':deuterium' => 0,
            ':crystal'   => 0,
            ':user_id'    => $USER['id'],
            ':planet_id'  => $PLANET['id'],
        ]);

        $metal_sum = $crystal_sum = $deuterium_sum = 0;

        foreach ($metal as $val)
        {
            $metal_sum += $val;
        }

        foreach ($crystal as $val)
        {
            $crystal_sum += $val;
        }

        foreach ($deuterium as $val)
        {
            $deuterium_sum += $val;
        }

        $PLANET[$resource[901]] += $metal_sum;
        $PLANET[$resource[902]] += $crystal_sum;
        $PLANET[$resource[903]] += $deuterium_sum;

        $sql = "UPDATE %%USERS%% 
        SET last_collect_mine_time = :time_collect 
        WHERE id = :user_id;";

        $db->update($sql, [
            ':time_collect' => TIMESTAMP,
            ':user_id'      => $USER['id'],
        ]);

        $this->redirectTo("game.php?page=$from");
    }

}
