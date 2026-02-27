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

class ShowFleetMissilePage extends AbstractGamePage
{
    public static $require_module = MODULE_MISSILEATTACK;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $PLANET, $LNG, $reslist, $resource;

        $target_galaxy = HTTP::_GP('galaxy', 0);
        $target_system = HTTP::_GP('system', 0);
        $target_planet = HTTP::_GP('planet', 0);
        $target_type = HTTP::_GP('type', 0);
        $anz = min(HTTP::_GP('SendMI', 0), $PLANET['interplanetary_misil']);
        $primary_target = HTTP::_GP('Target', 0);

        $db = Database::get();

        $sql = "SELECT id, id_owner FROM %%PLANETS%%
        WHERE universe = :universe AND galaxy = :target_galaxy
        AND system = :target_system AND planet = :target_planet 
        AND planet_type = :target_type;";

        $target = $db->selectSingle($sql, [
            ':universe'      => Universe::current(),
            ':target_galaxy' => $target_galaxy,
            ':target_system'  => $target_system,
            ':target_planet'  => $target_planet,
            ':target_type'    => $target_type,
        ]);

        $range = FleetFunctions::GetMissileRange($USER[$resource[117]]);
        $systemMin = $PLANET['system'] - $range;
        $systemMax = $PLANET['system'] + $range;

        $error = [];

        if (IsVacationMode($USER))
        {
            $error[] = $LNG['fl_vacation_mode_active'];
        }

        if ($PLANET['silo'] < 4)
        {
            $error[] = $LNG['ma_silo_level'];
        }

        if ($USER['impulse_motor_tech'] == 0)
        {
            $error[] = $LNG['ma_impulse_drive_required'];
        }

        if ($target_galaxy != $PLANET['galaxy']
            || $target_system < $systemMin
            || $target_system > $systemMax)
        {
            $error[] = $LNG['ma_not_send_other_galaxy'];
        }

        if (!$target)
        {
            $error[] = $LNG['ma_planet_doesnt_exists'];
        }

        if (!in_array($primary_target, $reslist['defense']) 
            && $primary_target != 0)
        {
            $error[] = $LNG['ma_wrong_target'];
        }

        if ($PLANET['interplanetary_misil'] == 0)
        {
            $error[] = $LNG['ma_no_missiles'];
        }

        if ($anz <= 0)
        {
            $error[] = $LNG['ma_add_missile_number'];
        }

        if (empty($target))
        {
            $target = [];
            $target['id_owner'] = 0;
            $target_user = ['onlinetime' => 0, 'banaday' => 0, 'urlaubs_modus' => 0, 'authattack' => 0];
        }
        else
        {
            $target_user = GetUserByID($target['id_owner'], ['onlinetime', 'banaday', 'urlaubs_modus', 'authattack']);
        }

        if (Config::get()->adm_attack == 1 
            && $target_user['authattack'] > $USER['authlevel'])
        {
            $error[] = $LNG['fl_admin_attack'];
        }

        if ($target_user['urlaubs_modus'])
        {
            $error[] = $LNG['fl_in_vacation_player'];
        }

        $sql = "SELECT total_points FROM %%USER_POINTS%% WHERE id_owner = :ownerId;";

        $user2points = $db->selectSingle($sql, [
            ':ownerId' => $target['id_owner'],
        ]);

        $sql = 'SELECT total_points
		FROM %%USER_POINTS%%
		WHERE id_owner = :userId;';

        $USER += Database::get()->selectSingle($sql, [
            ':userId' => $USER['id'],
        ]);

        $is_noob_protect = CheckNoobProtec($USER, $user2points, $target_user);

        if ($is_noob_protect['NoobPlayer'])
        {
            $error[] = $LNG['fl_week_player'];
        }

        if ($is_noob_protect['StrongPlayer'])
        {
            $error[] = $LNG['fl_strong_player'];
        }

        if (!empty($error))
        {
            $error_text = "";
            foreach ($error as $c_text)
            {
                $error_text .= $c_text . "\n";
            }

            $this->printMessage($error_text);
        }

        $duration = FleetFunctions::GetMIPDuration($PLANET['system'], $target_system);

        $defense_label = ($primary_target == 0) ? 
                        $LNG['ma_all'] : 
                        $LNG['tech'][$primary_target];

        $fleet_array = [503 => $anz];

        $fleet_start_time = TIMESTAMP + $duration;
        $fleet_stay_time = $fleet_start_time;
        $fleet_end_time = $fleet_start_time;

        $fleet_resource = [
            901 => 0,
            902 => 0,
            903 => 0,
        ];

        // saving planet avoids a bug if shipyard is producing interplanetary missiles
        $this->save();

        FleetFunctions::sendFleet(
            $fleet_array,
            10,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $target['id_owner'],
            $target['id'],
            $target_galaxy,
            $target_system,
            $target_planet,
            $target_type,
            $fleet_resource,
            $fleet_start_time,
            $fleet_stay_time,
            $fleet_end_time,
            0,
            $primary_target
        );

        $this->printMessage("<b>".$anz."</b>". $LNG['ma_missiles_sended'].$defense_label);
    }
}
