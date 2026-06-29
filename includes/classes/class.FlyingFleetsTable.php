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

class FlyingFleetsTable
{
    protected $user_id = null;
    protected $planet_id = null;
    protected $is_phalanx = false;
    protected $missions = false;

    public function __construct()
    {

    }

    public function setUser(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setPlanet(int $planet_id): void
    {
        $this->planet_id = $planet_id;
    }

    public function setPhalanxMode(): void
    {
        $this->is_phalanx = true;
    }

    public function setMissions($missions): void
    {
        $this->missions = implode(',', array_filter(explode(',', $missions), 'is_numeric'));
    }

    private function getFleets($acs_id = false): array
    {
        if ($this->is_phalanx)
        {
            $where = '(fleet_start_id = :planetId AND fleet_start_type = 1 AND fleet_mission != 4) OR
					  (fleet_end_id = :planetId AND fleet_end_type = 1 AND fleet_mission != 8 AND fleet_mess IN (0, 2))';

            $param = [
                ':planetId' => $this->planet_id,
            ];
        }
        elseif (!empty($acs_id))
        {
            $where = 'fleet_group = :acsId';
            $param = [
                ':acsId' => $acs_id,
            ];
        }
        elseif ($this->missions)
        {
            $where = '(fleet_owner = :userId OR (fleet_target_owner = :userId AND fleet_mission != 8)) AND fleet_mission IN ('.$this->missions.')';
            $param = [
                ':userId' => $this->user_id,
            ];
        }
        else
        {
            $where = 'fleet_owner = :userId OR (fleet_target_owner = :userId AND fleet_mission != 8)';
            $param = [
                ':userId' => $this->user_id,
            ];
        }

        $sql = 'SELECT DISTINCT fleet.*, ownuser.username as own_username, targetuser.username as target_username,
		ownplanet.name as own_planetname, targetplanet.name as target_planetname
		FROM %%FLEETS%% fleet
		LEFT JOIN %%USERS%% ownuser ON (ownuser.id = fleet.fleet_owner)
		LEFT JOIN %%USERS%% targetuser ON (targetuser.id = fleet.fleet_target_owner)
		LEFT JOIN %%PLANETS%% ownplanet ON (ownplanet.id = fleet.fleet_start_id)
		LEFT JOIN %%PLANETS%% targetplanet ON (targetplanet.id = fleet.fleet_end_id)
		WHERE '.$where.';';

        return Database::get()->select($sql, $param);
    }

    public function renderTable(): array
    {
        $fleet_result = $this->getFleets();
        $acs_done = [];
        $fleet_data = [];

        foreach ($fleet_result as $fleet_row)
        {
            if ($fleet_row['fleet_mess'] == 0
                && $fleet_row['fleet_start_time'] > TIMESTAMP
                && ($fleet_row['fleet_group'] == 0 || !isset($acs_done[$fleet_row['fleet_group']])))
            {
                $acs_done[$fleet_row['fleet_group']] = true;
                $fleet_data[$fleet_row['fleet_start_time'].$fleet_row['fleet_id']] = $this->BuildFleetEventTable($fleet_row, 0);
            }

            if ($fleet_row['fleet_mission'] == 10
                || $fleet_row['fleet_mission'] == 17
                || ($fleet_row['fleet_mission'] == 4
                && $fleet_row['fleet_mess'] == 0))
            {
                continue;
            }

            if ($fleet_row['fleet_end_stay'] != $fleet_row['fleet_start_time']
                && $fleet_row['fleet_end_stay'] > TIMESTAMP
                && ($this->is_phalanx
                && $fleet_row['fleet_end_id'] == $this->planet_id))
            {
                $fleet_data[$fleet_row['fleet_end_stay'].$fleet_row['fleet_id']] = $this->BuildFleetEventTable($fleet_row, 2);
            }

            $missions_ok = 5;
            if ($fleet_row['fleet_end_stay'] > TIMESTAMP
                && $fleet_row['fleet_mission'] == $missions_ok)
            {
                $fleet_data[$fleet_row['fleet_end_stay'].$fleet_row['fleet_id']] = $this->BuildFleetEventTable($fleet_row, 2);
            }

            if ($fleet_row['fleet_owner'] != $this->user_id)
            {
                continue;
            }

            if ($fleet_row['fleet_end_time'] > TIMESTAMP)
            {
                $fleet_data[$fleet_row['fleet_end_time'].$fleet_row['fleet_id']] = $this->BuildFleetEventTable($fleet_row, 1);
            }
        }

        ksort($fleet_data);
        return $fleet_data;
    }

    private function BuildFleetEventTable($fleet_row, $fleet_state): array
    {
        $time = 0;
        $rest = 0;

        if ($fleet_state == 0
            && $this->is_phalanx
            && $fleet_row['fleet_group'] != 0
            && (strpos((isset($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", 'page=phalanx') !== false))
        {
            // Rebuilt the code above to eliminate possible errors with ACS without Phalanx.
            $acs_result = $this->getFleets($fleet_row['fleet_group']);
            $event_string = '';

            foreach ($acs_result as $acs_row)
            {
                if ($acs_row['fleet_group'] != 0)
                {
                    $return = $this->getEventData($acs_row, $fleet_state);

                    $rest = $return[0];
                    $event_string .= $return[1].'<br><br>';
                    $time = $return[2];
                }
            }

            $event_string = substr($event_string, 0, -8);
        }
        elseif ($fleet_state == 0
            && $fleet_row['fleet_group'] != 0)
        {
            $acs_result = $this->getFleets($fleet_row['fleet_group']);
            $event_string = '';

            foreach ($acs_result as $acs_row)
            {
                $return = $this->getEventData($acs_row, $fleet_state);

                $rest = $return[0];
                $event_string .= $return[1].'<br><br>';
                $time = $return[2];
            }

            $event_string = substr($event_string, 0, -8);
        }
        else
        {
            list($rest, $event_string, $time) = $this->getEventData($fleet_row, $fleet_state);
        }

        return [
            'text'       => $event_string,
            'returntime' => $time,
            'resttime'   => $rest,
        ];
    }

    public function getEventData($fleet_row, $status): array
    {
        global $LNG;
        $owner = $fleet_row['fleet_owner'] == $this->user_id;
        $fleet_style = [
            1  => 'attack',
            2  => 'federation',
            3  => 'transport',
            4  => 'deploy',
            5  => 'hold',
            6  => 'espionage',
            7  => 'colony',
            8  => 'harvest',
            9  => 'destroy',
            10 => 'missile',
            11 => 'transport',
            15 => 'transport',
            16 => 'transport',
            17 => 'transport',
        ];

        $good_missions = [3, 5];
        $mission_type = $fleet_row['fleet_mission'];

        $fleet_prefix = ($owner == true) ? 'own' : '';
        $fleet_type = $fleet_prefix.$fleet_style[$mission_type];
        $fleet_name = (!$owner && ($mission_type == 1 || $mission_type == 2) && $status == FLEET_OUTWARD && $fleet_row['fleet_target_owner'] != $this->user_id) ? $LNG['cff_acs_fleet'] : $LNG['ov_fleet'];
        $fleet_content = $this->CreateFleetPopupedFleetLink($fleet_row, $fleet_name, $fleet_prefix.$fleet_style[$mission_type]);
        $fleet_capacity = $this->CreateFleetPopupedMissionLink($fleet_row, $LNG['type_mission_'.$mission_type], $fleet_prefix.$fleet_style[$mission_type]);
        $fleet_status = [0 => 'flight', 1 => 'return' , 2 => 'holding'];
        $start_type = $LNG['type_planet_'.$fleet_row['fleet_start_type']];
        $target_type = $LNG['type_planet_'.$fleet_row['fleet_end_type']];

        if ($mission_type == 8)
        {
            if ($status == FLEET_OUTWARD)
            {
                $event_string = sprintf(
                    $LNG['cff_mission_own_recy_0'],
                    $fleet_content,
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    GetTargetAddressLink($fleet_row, $fleet_type),
                    $fleet_capacity
                );
            }
            else
            {
                $event_string = sprintf(
                    $LNG['cff_mission_own_recy_1'],
                    $fleet_content,
                    GetTargetAddressLink($fleet_row, $fleet_type),
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    $fleet_capacity
                );
            }
        }
        elseif ($mission_type == 10)
        {
            if ($owner)
            {
                $event_string = sprintf(
                    $LNG['cff_mission_own_mip'],
                    $fleet_row['fleet_amount'],
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    $target_type,
                    $fleet_row['target_planetname'],
                    GetTargetAddressLink($fleet_row, $fleet_type)
                );
            }
            else
            {
                $event_string = sprintf(
                    $LNG['cff_mission_target_mip'],
                    $fleet_row['fleet_amount'],
                    $this->BuildHostileFleetPlayerLink($fleet_row),
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    $target_type,
                    $fleet_row['target_planetname'],
                    GetTargetAddressLink($fleet_row, $fleet_type)
                );
            }
        }
        elseif ($mission_type == 11
            || $mission_type == 15)
        {
            if ($status == FLEET_OUTWARD)
            {

                $event_string = sprintf(
                    $LNG['cff_mission_own_expo_0'],
                    $fleet_content,
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    GetTargetAddressLink($fleet_row, $fleet_type),
                    $fleet_capacity
                );

            }
            elseif ($status == FLEET_HOLD)
            {
                $event_string = sprintf(
                    $LNG['cff_mission_own_expo_2'],
                    $fleet_content,
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    GetTargetAddressLink($fleet_row, $fleet_type),
                    $fleet_capacity
                );
            }
            else
            {
                $event_string = sprintf(
                    $LNG['cff_mission_own_expo_1'],
                    $fleet_content,
                    GetTargetAddressLink($fleet_row, $fleet_type),
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    $fleet_capacity
                );
            }
        }
        else
        {
            if ($owner == true)
            {
                if ($status == FLEET_OUTWARD)
                {
                    if (!$owner
                        && ($mission_type == 1
                        || $mission_type == 2))
                    {
                        $message = $LNG['cff_mission_acs'];
                    }
                    else
                    {
                        $message = $LNG['cff_mission_own_0'];
                    }

                    $event_string = sprintf(
                        $message,
                        $fleet_content,
                        $start_type,
                        $fleet_row['own_planetname'],
                        GetStartAddressLink($fleet_row, $fleet_type),
                        $target_type,
                        $fleet_row['target_planetname'],
                        GetTargetAddressLink($fleet_row, $fleet_type),
                        $fleet_capacity
                    );
                }
                elseif ($status == FLEET_RETURN)
                {
                    $event_string = sprintf(
                        $LNG['cff_mission_own_1'],
                        $fleet_content,
                        $target_type,
                        $fleet_row['target_planetname'],
                        GetTargetAddressLink($fleet_row, $fleet_type),
                        $start_type,
                        $fleet_row['own_planetname'],
                        GetStartAddressLink($fleet_row, $fleet_type),
                        $fleet_capacity
                    );
                }
                else
                {
                    $event_string = sprintf(
                        $LNG['cff_mission_own_2'],
                        $fleet_content,
                        $start_type,
                        $fleet_row['own_planetname'],
                        GetStartAddressLink($fleet_row, $fleet_type),
                        $target_type,
                        $fleet_row['target_planetname'],
                        GetTargetAddressLink($fleet_row, $fleet_type),
                        $fleet_capacity
                    );
                }
            }
            else
            {
                if ($status == FLEET_HOLD)
                {
                    $message = $LNG['cff_mission_target_stay'];
                }
                elseif (in_array($mission_type, $good_missions))
                {
                    $message = $LNG['cff_mission_target_good'];
                }
                else
                {
                    $message = $LNG['cff_mission_target_bad'];
                }

                $event_string = sprintf(
                    $message,
                    $fleet_content,
                    $this->BuildHostileFleetPlayerLink($fleet_row),
                    $start_type,
                    $fleet_row['own_planetname'],
                    GetStartAddressLink($fleet_row, $fleet_type),
                    $target_type,
                    $fleet_row['target_planetname'],
                    GetTargetAddressLink($fleet_row, $fleet_type),
                    $fleet_capacity
                );
            }
        }
        $event_string = '<span class="'.$fleet_status[$status].' '.$fleet_type.'">'.$event_string.'</span>';

        if ($status == FLEET_OUTWARD)
        {
            $time = $fleet_row['fleet_start_time'];
        }
        elseif ($status == FLEET_RETURN)
        {
            $time = $fleet_row['fleet_end_time'];
        }
        elseif ($status == FLEET_HOLD)
        {
            $time = $fleet_row['fleet_end_stay'];
        }
        else
        {
            $time = TIMESTAMP;
        }

        $rest = $time - TIMESTAMP;
        return [$rest, $event_string, $time];
    }

    private function BuildHostileFleetPlayerLink($fleet_row): string
    {
        global $LNG;
        return $fleet_row['own_username'] .
            ' <a href="#" onclick="return Dialog.PM(' .
            $fleet_row['fleet_owner'] .
            ')">' .
            $LNG['PM'] .
            '</a>';
    }

    private function CreateFleetPopupedMissionLink($fleet_row, $text, $fleet_type): string
    {
        global $LNG;
        $total_res = $fleet_row['fleet_resource_metal'] +
                    $fleet_row['fleet_resource_crystal'] +
                    $fleet_row['fleet_resource_deuterium'] +
                    $fleet_row['fleet_resource_darkmatter'];

        if ($total_res != 0
            && !$this->is_phalanx)
        {
            $text_for_blind = $LNG['tech'][900].': ';
            $text_for_blind .= floatToString($fleet_row['fleet_resource_metal']).' '.$LNG['tech'][901];
            $text_for_blind .= '; '.floatToString($fleet_row['fleet_resource_crystal']).' '.$LNG['tech'][902];
            $text_for_blind .= '; '.floatToString($fleet_row['fleet_resource_deuterium']).' '.$LNG['tech'][903];

            if ($fleet_row['fleet_resource_darkmatter'] > 0)
            {
                $text_for_blind .= '; '.floatToString($fleet_row['fleet_resource_darkmatter']).' '.$LNG['tech'][921];
            }

            $f_res = '<table style=\'width:200px\'>';
            $f_res .= '<tr><td style=\'width:50%;color:white\'>'.$LNG['tech'][901].'</td><td style=\'width:50%;color:white\'>'. pretty_number($fleet_row['fleet_resource_metal']).'</td></tr>';
            $f_res .= '<tr><td style=\'width:50%;color:white\'>'.$LNG['tech'][902].'</td><td style=\'width:50%;color:white\'>'. pretty_number($fleet_row['fleet_resource_crystal']).'</td></tr>';
            $f_res .= '<tr><td style=\'width:50%;color:white\'>'.$LNG['tech'][903].'</td><td style=\'width:50%;color:white\'>'. pretty_number($fleet_row['fleet_resource_deuterium']).'</td></tr>';
            if ($fleet_row['fleet_resource_darkmatter'] > 0)
            {
                $f_res .= '<tr><td style=\'width:50%;color:white\'>'.$LNG['tech'][921].'</td><td style=\'width:50%;color:white\'>'. pretty_number($fleet_row['fleet_resource_darkmatter']).'</td></tr>';
            }
            $f_res .= '</table>';

            $mission_popup = '<a data-bs-toggle="tooltip"
			data-bs-placement="bottom"
			data-bs-html="true"
			title="' .
            $f_res .
            '" class="' .
            $fleet_type .
            '">' .
            $text .
            '</a><span class="textForBlind"> (' .
            $text_for_blind .
            ')</span>';

        }
        else
        {
            $mission_popup = $text;
        }

        return $mission_popup;
    }

    private function CreateFleetPopupedFleetLink($fleet_row, $text, $FleetType): string
    {
        global $LNG, $USER, $RESOURCE;
        $spy_tech = $USER[$RESOURCE[106]];
        $owner = $fleet_row['fleet_owner'] == $this->user_id;
        $fleet_rec = explode(';', $fleet_row['fleet_array']);
        $fleet_popup = '<a class="fleet_info">';
        $fleet_popup .= "<div class='tooltip tooltip_bottom'><table>";

        $text_for_blind = '';
        if ($this->is_phalanx
            || $spy_tech >= 4
            || $owner)
        {

            if ($spy_tech < 8
                && !$owner)
            {
                $fleet_popup .= '<tr><td>' .
                    $LNG['cff_aproaching'] .
                    $fleet_row['fleet_amount'] .
                    $LNG['cff_ships'] .
                    ':</td></tr>';

                $text_for_blind = $LNG['cff_aproaching'].$fleet_row['fleet_amount'].$LNG['cff_ships'].': ';
            }
            $shipsData = [];
            foreach ($fleet_rec as $Item => $Group)
            {
                if (empty($Group))
                {
                    continue;
                }

                $Ship = explode(',', $Group);
                if ($owner)
                {
                    $fleet_popup .= '<tr><td>' .
                    $LNG['tech'][$Ship[0]] .
                    ':</td><td>' .
                    pretty_number($Ship[1]) .
                    '</td></tr>';
                    $shipsData[] = floatToString($Ship[1]) .
                    ' ' .
                    $LNG['tech'][$Ship[0]];
                }
                else
                {
                    if ($spy_tech >= 8)
                    {
                        $fleet_popup .= '<tr><td>' .
                        $LNG['tech'][$Ship[0]] .
                        ':</td><td>' .
                        pretty_number($Ship[1]) .
                        '</td></tr>';
                        $shipsData[] = floatToString($Ship[1]) .
                        ' ' .
                        $LNG['tech'][$Ship[0]];
                    }
                    else
                    {
                        $fleet_popup .= '<tr><td>' .
                        $LNG['tech'][$Ship[0]] .
                        '</td></tr>';
                        $shipsData[] = $LNG['tech'][$Ship[0]];
                    }
                }
            }
            $text_for_blind .= implode('; ', $shipsData);
        }
        else
        {
            $fleet_popup .= '<tr><td>' .
            $LNG['cff_no_fleet_data'] .
            '</span></td></tr>';

            $text_for_blind = $LNG['cff_no_fleet_data'];
        }

        $fleet_popup .= '</table></div>' .
        $text .
        '</a><span class="textForBlind"> (' .
        $text_for_blind .
        ')</span>';

        return $fleet_popup;
    }
}
