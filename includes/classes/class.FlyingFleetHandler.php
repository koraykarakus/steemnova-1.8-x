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

class FlyingFleetHandler
{
    protected string $token;

    public static array $mission_obj_pattern = [
        1  => 'MissionCaseAttack',
        2  => 'MissionCaseACS',
        3  => 'MissionCaseTransport',
        4  => 'MissionCaseStay',
        5  => 'MissionCaseStayAlly',
        6  => 'MissionCaseSpy',
        7  => 'MissionCaseColonisation',
        8  => 'MissionCaseRecycling',
        9  => 'MissionCaseDestruction',
        10 => 'MissionCaseIPM',
        11 => 'MissionCaseFoundDM',
        15 => 'MissionCaseExpedition',
        16 => 'MissionCaseTrade',
        17 => 'MissionCaseTransfer',
    ];

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function run(): void
    {
        require_once 'includes/classes/class.MissionFunctions.php';
        require_once 'includes/classes/missions/Mission.interface.php';

        $db = Database::get();

        $sql = 'SELECT %%FLEETS%%.*
		FROM %%FLEETS_EVENT%%
		INNER JOIN %%FLEETS%% ON fleetID = fleet_id
		WHERE `lock` = :token;';

        $fleet_result = $db->select($sql, [
            ':token' => $this->token,
        ]);

        foreach ($fleet_result as $c_fleet)
        {
            if (!isset(self::$mission_obj_pattern[$c_fleet['fleet_mission']]))
            {
                $sql = 'DELETE FROM %%FLEETS%% WHERE fleet_id = :fleet_id;';

                $db->delete($sql, [
                    ':fleet_id' => $c_fleet['fleet_id'],
                ]);

                continue;
            }

            $mission_name = self::$mission_obj_pattern[$c_fleet['fleet_mission']];

            $path = 'includes/classes/missions/'.$mission_name.'.class.php';
            require_once $path;
            /** @var Mission $mission_obj */
            $mission_obj = new $mission_name($c_fleet);

            switch ($c_fleet['fleet_mess'])
            {
                case 0:
                    $mission_obj->TargetEvent();
                    break;
                case 1:
                    $mission_obj->ReturnEvent();
                    break;
                case 2:
                    $mission_obj->EndStayEvent();
                    break;
            }
        }
    }
}
