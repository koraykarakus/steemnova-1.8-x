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

class MissionCaseAttack extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        global $RESOURCE, $RESLIST;

        $db = Database::get();
        $config = Config::get($this->_fleet['fleet_universe']);

        $message_html = <<<HTML
        <div class="raportMessage">
            <table>
                <tr>
                    <td colspan="2"><a href="report.php?page=report&id=%s" target="_blank"><span class="%s">%s %s (%s)</span></a></td>
                </tr>
                <tr>
                    <td>%s</td><td><span class="%s">%s: %s</span>&nbsp;<span class="%s">%s: %s</span></td>
                </tr>
                <tr>
                    <td>%s</td><td><span>%s:&nbsp;<span class="reportSteal element901">%s</span>&nbsp;</span><span>%s:&nbsp;<span class="reportSteal element902">%s</span>&nbsp;</span><span>%s:&nbsp;<span class="reportSteal element903">%s</span></span></td>
                </tr>
                <tr>
                    <td>%s</td><td><span>%s:&nbsp;<span class="reportDebris element901">%s</span>&nbsp;</span><span>%s:&nbsp;<span class="reportDebris element902">%s</span></span></td>
                </tr>
            </table>
        </div>
        HTML;
        //Minize HTML
        $message_html = str_replace(["\n", "\t", "\r"], "", $message_html);

        $sql = "SELECT * FROM %%PLANETS%% WHERE id = :planet_id;";
        $target_planet = $db->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_end_id'],
        ]);

        // return fleet if target planet deleted
        if ($target_planet == false)
        {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
            return;
        }

        $sql = "SELECT * FROM %%USERS%% WHERE id = :user_id;";
        $target_user = $db->selectSingle($sql, [
            ':user_id' => $target_planet['id_owner'],
        ]);
        $target_user['factor'] = getFactors($target_user, 'basic', $this->_fleet['fleet_start_time']);

        $planet_updater = new ResourceUpdate();

        list($target_user, $target_planet) = $planet_updater->CalcResource(
            $target_user,
            $target_planet,
            true,
            $this->_fleet['fleet_start_time']
        );

        $incoming_fleets = [];
        if ($this->_fleet['fleet_group'] != 0)
        {
            $sql = "DELETE FROM %%ACS%% WHERE id = :acs_id;";
            $db->delete($sql, [
                ':acs_id' => $this->_fleet['fleet_group'],
            ]);

            $sql = "SELECT * FROM %%FLEETS%% WHERE fleet_group = :acs_id;";

            $incoming_fleets_result = $db->select($sql, [
                ':acs_id' => $this->_fleet['fleet_group'],
            ]);

            foreach ($incoming_fleets_result as $incoming_fleet_row)
            {
                $incoming_fleets[$incoming_fleet_row['fleet_id']] = $incoming_fleet_row;
            }

            unset($incoming_fleets_result);
        }
        else
        {
            $incoming_fleets = [$this->_fleet['fleet_id'] => $this->_fleet];
        }

        $fleet_attack = [];
        $user_attack = [];
        foreach ($incoming_fleets as $fleet_id => $fleet_detail)
        {
            $sql = "SELECT * FROM %%USERS%% WHERE id = :user_id;";
            $fleet_attack[$fleet_id]['player'] = $db->selectSingle($sql, [
                ':user_id' => $fleet_detail['fleet_owner'],
            ]);

            $fleet_attack[$fleet_id]['player']['factor'] = getFactors(
                $fleet_attack[$fleet_id]['player'],
                'attack',
                $this->_fleet['fleet_start_time']
            );
            $fleet_attack[$fleet_id]['fleetDetail'] = $fleet_detail;
            $fleet_attack[$fleet_id]['unit'] = FleetFunctions::unserialize($fleet_detail['fleet_array']);

            $user_attack[$fleet_attack[$fleet_id]['player']['id']] = $fleet_attack[$fleet_id]['player']['username'];
        }

        $sql = "SELECT * FROM %%FLEETS%%
		WHERE fleet_mission		= :mission
		AND fleet_end_id		= :fleet_end_id
		AND fleet_start_time 	<= :time_stamp
		AND fleet_end_stay 		>= :time_stamp;";

        $target_fleets_result = $db->select($sql, [
            ':mission'      => 5,
            ':fleet_end_id' => $this->_fleet['fleet_end_id'],
            ':time_stamp'   => TIMESTAMP,
        ]);

        $fleet_defend = [];
        $user_defend = [];
        foreach ($target_fleets_result as $fleet_detail)
        {
            $fleet_id = $fleet_detail['fleet_id'];

            $sql = "SELECT * FROM %%USERS%% WHERE id = :user_id;";
            $fleet_defend[$fleet_id]['player'] = $db->selectSingle($sql, [
                ':user_id' => $fleet_detail['fleet_owner'],
            ]);

            $fleet_defend[$fleet_id]['player']['factor'] = getFactors(
                $fleet_defend[$fleet_id]['player'],
                'attack',
                $this->_fleet['fleet_start_time']
            );
            $fleet_defend[$fleet_id]['fleetDetail'] = $fleet_detail;
            $fleet_defend[$fleet_id]['unit'] = FleetFunctions::unserialize($fleet_detail['fleet_array']);

            $user_defend[$fleet_defend[$fleet_id]['player']['id']] = $fleet_defend[$fleet_id]['player']['username'];
        }

        unset($target_fleets_result);

        $fleet_defend[0]['player'] = $target_user;
        $fleet_defend[0]['player']['factor'] = getFactors(
            $fleet_defend[0]['player'],
            'attack',
            $this->_fleet['fleet_start_time']
        );
        $fleet_defend[0]['fleetDetail'] = [
            'fleet_start_galaxy' => $target_planet['galaxy'],
            'fleet_start_system' => $target_planet['system'],
            'fleet_start_planet' => $target_planet['planet'],
            'fleet_start_type'   => $target_planet['planet_type'],
        ];

        $fleet_defend[0]['unit'] = [];

        foreach (array_merge($RESLIST['fleet'], $RESLIST['defense']) as $element_id)
        {
            if (empty($target_planet[$RESOURCE[$element_id]]))
            {
                continue;
            }

            $fleet_defend[0]['unit'][$element_id] = $target_planet[$RESOURCE[$element_id]];
        }

        $user_defend[$fleet_defend[0]['player']['id']] = $fleet_defend[0]['player']['username'];

        require_once 'includes/classes/missions/functions/calculateAttack.php';

        $fleet_into_debris = $config->debris_percentage_fleet;
        $def_into_debris = $config->debris_percentage_defense;

        $combat_result = calculateAttack($fleet_attack, $fleet_defend, $fleet_into_debris, $def_into_debris);

        foreach ($fleet_attack as $fleet_id => $fleet_detail)
        {
            $fleet_array = '';
            $total_count = 0;

            $fleet_detail['unit'] = array_filter($fleet_detail['unit']);
            foreach ($fleet_detail['unit'] as $elementID => $amount)
            {
                $fleet_array .= $elementID.','.floatToString($amount).';';
                $total_count += $amount;
            }

            if ($total_count == 0)
            {
                if ($this->_fleet['fleet_id'] == $fleet_id)
                {
                    $this->KillFleet();
                }
                else
                {
                    $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
					FROM %%FLEETS%%
					INNER JOIN %%FLEETS_EVENT%% ON fleetID = fleet_id
					WHERE fleet_id = :fleetId;';

                    $db->delete($sql, [
                        ':fleetId' => $fleet_id,
                    ]);
                }

                $sql = 'UPDATE %%LOG_FLEETS%% SET fleet_state = :fleetState WHERE fleet_id = :fleetId;';
                $db->update($sql, [
                    ':fleetId'    => $fleet_id,
                    ':fleetState' => FLEET_HOLD,
                ]);

                unset($fleet_attack[$fleet_id]);
            }
            elseif ($total_count > 0)
            {
                $sql = "UPDATE %%FLEETS%% fleet, %%LOG_FLEETS%% log SET
				fleet.fleet_array	= :fleet_data,
				fleet.fleet_amount	= :fleet_count,
				log.fleet_array		= :fleet_data,
				log.fleet_amount	= :fleet_count
				WHERE fleet.fleet_id = :fleet_id AND log.fleet_id = :fleet_id;";

                $db->update($sql, [
                    ':fleet_data'  => substr($fleet_array, 0, -1),
                    ':fleet_count' => $total_count,
                    ':fleet_id'    => $fleet_id,
                ]);
            }
            else
            {
                throw new OutOfRangeException("Negative Fleet amount ....");
            }
        }

        foreach ($fleet_defend as $fleet_id => $fleet_detail)
        {
            if ($fleet_id != 0)
            {
                // Stay fleet
                $fleet_array = '';
                $total_count = 0;

                $fleet_detail['unit'] = array_filter($fleet_detail['unit']);

                foreach ($fleet_detail['unit'] as $elementID => $amount)
                {
                    $fleet_array .= $elementID.','.floatToString($amount).';';
                    $total_count += $amount;
                }

                if ($total_count == 0)
                {
                    $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
					FROM %%FLEETS%%
					INNER JOIN %%FLEETS_EVENT%% ON fleetID = fleet_id
					WHERE fleet_id = :fleet_id;';

                    $db->delete($sql, [
                        ':fleet_id' => $fleet_id,
                    ]);

                    $sql = 'UPDATE %%LOG_FLEETS%% SET fleet_state = :fleet_state 
                    WHERE fleet_id = :fleet_id;';
                    $db->update($sql, [
                        ':fleet_id'    => $fleet_id,
                        ':fleet_state' => FLEET_HOLD,
                    ]);

                    unset($fleet_attack[$fleet_id]);
                }
                elseif ($total_count > 0)
                {
                    $sql = "UPDATE %%FLEETS%% fleet, %%LOG_FLEETS%% log SET
					fleet.fleet_array	= :fleet_data,
					fleet.fleet_amount	= :fleet_count,
					log.fleet_array		= :fleet_data,
					log.fleet_amount	= :fleet_count
					WHERE fleet.fleet_id = :fleet_id AND log.fleet_id = :fleet_id;";

                    $db->update($sql, [
                        ':fleet_data'  => substr($fleet_array, 0, -1),
                        ':fleet_count' => $total_count,
                        ':fleet_id'    => $fleet_id,
                    ]);
                }
                else
                {
                    throw new OutOfRangeException("Negative Fleet amount ....");
                }
            }
            else
            {
                $params = [':planet_id' => $this->_fleet['fleet_end_id']];

                // Planet fleet
                $fleet_array = [];
                foreach ($fleet_detail['unit'] as $element_id => $amount)
                {
                    $fleet_array[] = '`'.$RESOURCE[$element_id].'` = :'.$RESOURCE[$element_id];
                    $params[':'.$RESOURCE[$element_id]] = $amount;
                }

                if (!empty($fleet_array))
                {
                    $sql = 'UPDATE %%PLANETS%% SET '.implode(', ', $fleet_array).' WHERE id = :planet_id;';
                    $db->update($sql, $params);
                }
            }
        }

        $steal_resource = [
            901 => 0,
            902 => 0,
            903 => 0,
        ];

        if ($combat_result['won'] == "a")
        {
            require_once 'includes/classes/missions/functions/calculateSteal.php';
            $steal_resource = calculateSteal($fleet_attack, $target_planet);
        }

        if ($this->_fleet['fleet_end_type'] == 3)
        {
            // Use planet debris, if attack on moons
            $sql = "SELECT debris_metal, debris_crystal FROM %%PLANETS%% WHERE id_moon = :id_moon;";
            $target_debris = $db->selectSingle($sql, [
                ':id_moon' => $this->_fleet['fleet_end_id'],
            ]);
            $target_planet['debris_metal'] += $target_debris['debris_metal'];
            $target_planet['debris_crystal'] += $target_debris['debris_crystal'];
        }

        $debris = [];
        $planet_debris = [];
        $debris_resource = [901, 902];
        foreach ($debris_resource as $element_id)
        {
            $debris[$element_id] = $combat_result['debris']['attacker'][$element_id] + $combat_result['debris']['defender'][$element_id];
            $planet_debris[$element_id] = $target_planet['debris_'.$RESOURCE[$element_id]] + $debris[$element_id];
        }

        $debris_total = array_sum($debris);

        $moon_factor = $config->moon_factor;
        $max_moon_chance = $config->moon_chance;

        if ($target_planet['id_moon'] == 0 && $target_planet['planet_type'] == 1)
        {
            $chance_create_moon = round($debris_total / 100000 * $moon_factor);
            $chance_create_moon = min($chance_create_moon, $max_moon_chance);
        }
        else
        {
            $chance_create_moon = 0;
        }

        $report_info = [
            'thisFleet'           => $this->_fleet,
            'debris'              => $debris,
            'stealResource'       => $steal_resource,
            'moonChance'          => $chance_create_moon,
            'moonDestroy'         => false,
            'moonName'            => null,
            'moonDestroyChance'   => null,
            'moonDestroySuccess'  => null,
            'fleetDestroyChance'  => null,
            'fleetDestroySuccess' => null,
        ];

        $rand_chance = mt_rand(1, 100);
        if ($rand_chance <= $chance_create_moon)
        {
            $LNG = $this->getLanguage($target_user['lang']);
            $report_info['moonName'] = $LNG['type_planet_3'];

            PlayerUtil::createMoon(
                $this->_fleet['fleet_universe'],
                $this->_fleet['fleet_end_galaxy'],
                $this->_fleet['fleet_end_system'],
                $this->_fleet['fleet_end_planet'],
                $target_user['id'],
                $chance_create_moon
            );

            if ($config->debris_moon == 1)
            {
                foreach ($debris_resource as $element_id)
                {
                    $planet_debris[$element_id] = 0;
                }
            }
        }

        require_once 'includes/classes/missions/functions/GenerateReport.php';
        $report_data = GenerateReport($combat_result, $report_info);

        switch ($combat_result['won'])
        {
            case "a":
                // Win
                $attack_status = 'wons';
                $defend_status = 'loos';
                $class = ['raportWin', 'raportLose'];
                break;
            case "r":
                // Lose
                $attack_status = 'loos';
                $defend_status = 'wons';
                $class = ['raportLose', 'raportWin'];
                break;
            case "w":
            default:
                // Draw
                $attack_status = 'draws';
                $defend_status = 'draws';
                $class = ['raportDraw', 'raportDraw'];
                break;
        }

        $report_id = md5(uniqid('', true).TIMESTAMP);

        $sql = 'INSERT INTO %%RW%% SET
		rid 		= :report_id,
		raport 		= :report_data,
		time 		= :time,
		attacker	= :attackers,
		defender	= :defenders;';

        $db->insert($sql, [
            ':report_id'   => $report_id,
            ':report_data' => serialize($report_data),
            ':time'        => $this->_fleet['fleet_start_time'],
            ':attackers'   => implode(',', array_keys($user_attack)),
            ':defenders'   => implode(',', array_keys($user_defend)),
        ]);

        $i = 0;

        foreach ([$user_attack, $user_defend] as $data)
        {
            foreach ($data as $userID => $userName)
            {
                $LNG = $this->getLanguage(null, $userID);

                $message = sprintf(
                    $message_html,
                    $report_id,
                    $class[$i],
                    $LNG['sys_mess_attack_report'],
                    sprintf(
                        $LNG['sys_adress_planet'],
                        $this->_fleet['fleet_end_galaxy'],
                        $this->_fleet['fleet_end_system'],
                        $this->_fleet['fleet_end_planet']
                    ),
                    $LNG['type_planet_short_'.$this->_fleet['fleet_end_type']],
                    $LNG['sys_lost'],
                    $class[0],
                    $LNG['sys_attack_attacker_pos'],
                    pretty_number($combat_result['unitLost']['attacker']),
                    $class[1],
                    $LNG['sys_attack_defender_pos'],
                    pretty_number($combat_result['unitLost']['defender']),
                    $LNG['sys_gain'],
                    $LNG['tech'][901],
                    pretty_number($steal_resource[901]),
                    $LNG['tech'][902],
                    pretty_number($steal_resource[902]),
                    $LNG['tech'][903],
                    pretty_number($steal_resource[903]),
                    $LNG['sys_debris'],
                    $LNG['tech'][901],
                    pretty_number($debris[901]),
                    $LNG['tech'][902],
                    pretty_number($debris[902])
                );

                PlayerUtil::sendMessage(
                    $userID,
                    0,
                    $LNG['sys_mess_tower'],
                    3,
                    $LNG['sys_mess_attack_report'],
                    $message,
                    $this->_fleet['fleet_start_time'],
                    null,
                    1,
                    $this->_fleet['fleet_universe']
                );

                $sql = "INSERT INTO %%TOPKB_USERS%% SET
				rid			= :report_id,
				role		= :user_role,
				username	= :user_name,
				uid			= :user_id;";

                $db->insert($sql, [
                    ':report_id' => $report_id,
                    ':user_role' => $i + 1,
                    ':user_name' => $userName,
                    ':user_id'   => $userID,
                ]);
            }

            $i++;
        }

        if ($this->_fleet['fleet_end_type'] == 3)
        {
            $debrisType = 'id_moon';
        }
        else
        {
            $debrisType = 'id';
        }

        $sql = 'UPDATE %%PLANETS%% SET
		debris_metal	= :metal,
		debris_crystal	= :crystal
		WHERE '.$debrisType.' = :planetId;';

        $db->update($sql, [
            ':metal'    => $planet_debris[901],
            ':crystal'  => $planet_debris[902],
            ':planetId' => $this->_fleet['fleet_end_id'],
        ]);

        $sql = 'UPDATE %%PLANETS%% SET
		metal		= metal - :metal,
		crystal		= crystal - :crystal,
		deuterium	= deuterium - :deuterium
		WHERE id = :planetId;';

        $db->update($sql, [
            ':metal'     => $steal_resource[901],
            ':crystal'   => $steal_resource[902],
            ':deuterium' => $steal_resource[903],
            ':planetId'  => $this->_fleet['fleet_end_id'],
        ]);

        $sql = 'INSERT INTO %%TOPKB%% SET
		units 		= :units,
		rid			= :reportId,
		time		= :time,
		universe	= :universe,
		result		= :result;';

        $db->insert($sql, [
            ':units'    => $combat_result['unitLost']['attacker'] + $combat_result['unitLost']['defender'],
            ':reportId' => $report_id,
            ':time'     => $this->_fleet['fleet_start_time'],
            ':universe' => $this->_fleet['fleet_universe'],
            ':result'   => $combat_result['won'],
        ]);

        $sql = 'UPDATE %%USERS%% SET
		`'.$attack_status.'` = `'.$attack_status.'` + 1,
		kbmetal		= kbmetal + :debrisMetal,
		kbcrystal	= kbcrystal + :debrisCrystal,
		lostunits	= lostunits + :lostUnits,
		desunits	= desunits + :destroyedUnits
		WHERE id IN ('.implode(',', array_keys($user_attack)).');';

        $db->update($sql, [
            ':debrisMetal'    => $debris[901],
            ':debrisCrystal'  => $debris[902],
            ':lostUnits'      => $combat_result['unitLost']['attacker'],
            ':destroyedUnits' => $combat_result['unitLost']['defender'],
        ]);

        $sql = 'UPDATE %%USERS%% SET
		`'.$defend_status.'` = `'.$defend_status.'` + 1,
		kbmetal		= kbmetal + :debrisMetal,
		kbcrystal	= kbcrystal + :debrisCrystal,
		lostunits	= lostunits + :lostUnits,
		desunits	= desunits + :destroyedUnits
		WHERE id IN ('.implode(',', array_keys($user_defend)).');';

        $db->update($sql, [
            ':debrisMetal'    => $debris[901],
            ':debrisCrystal'  => $debris[902],
            ':lostUnits'      => $combat_result['unitLost']['defender'],
            ':destroyedUnits' => $combat_result['unitLost']['attacker'],
        ]);

        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planet_id;';
        $planet_name = Database::get()->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_start_id'],
        ], 'name');

        $message = sprintf(
            $LNG['sys_fleet_won'],
            $planet_name,
            GetTargetAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            4,
            $LNG['sys_mess_fleetback'],
            $message,
            $this->_fleet['fleet_end_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->RestoreFleet();
    }
}
