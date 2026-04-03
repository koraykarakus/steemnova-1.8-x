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

class MissionCaseDestruction extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        global $RESOURCE, $RESLIST;

        $db = Database::get();

        $fleet_attack = [];
        $fleet_defend = [];

        $user_attack = [];
        $user_defend = [];

        $incoming_fleets = [];

        $steal_resource = [
            901 => 0,
            902 => 0,
            903 => 0,
        ];

        $debris = [];
        $planet_debris = [];

        $debris_resource = [901, 902];

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

        $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";
        $target_user = $db->selectSingle($sql, [
            ':userId' => $target_planet['id_owner'],
        ]);
        $target_user['factor'] = getFactors($target_user, 'basic', $this->_fleet['fleet_start_time']);

        $planet_updater = new ResourceUpdate();

        list($target_user, $target_planet) = $planet_updater->CalcResource(
            $target_user,
            $target_planet,
            true,
            $this->_fleet['fleet_start_time']
        );

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
            ':time_stamp'    => TIMESTAMP,
        ]);

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
        $fleet_defend[0]['player']['factor'] = getFactors($fleet_defend[0]['player'], 'attack', $this->_fleet['fleet_start_time']);
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

        $fleet_into_debris = Config::get($this->_fleet['fleet_universe'])->debris_percentage_fleet;
        $def_into_debris = Config::get($this->_fleet['fleet_universe'])->debris_percentage_defense;

        $combat_result = calculateAttack(
            $fleet_attack,
            $fleet_defend,
            $fleet_into_debris,
            $def_into_debris
        );

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
					WHERE fleet_id = :fleet_id;';

                    $db->delete($sql, [
                        ':fleet_id' => $fleet_id,
                    ]);
                }

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
                foreach ($fleet_detail['unit'] as $elementID => $amount)
                {
                    $fleet_array[] = '`'.$RESOURCE[$elementID].'` = :'.$RESOURCE[$elementID];
                    $params[':'.$RESOURCE[$elementID]] = $amount;
                }

                if (!empty($fleet_array))
                {
                    $sql = 'UPDATE %%PLANETS%% SET '.implode(', ', $fleet_array).' WHERE id = :planet_id;';
                    $db->update($sql, $params);
                }
            }
        }

        if ($combat_result['won'] == "a")
        {
            require_once 'includes/classes/missions/functions/calculateSteal.php';
            $steal_resource = calculateSteal($fleet_attack, $target_planet);
        }

        if ($this->_fleet['fleet_end_type'] == 3)
        {
            // Use planet debris, if attack on moons
            $sql = "SELECT debris_metal, debris_crystal FROM %%PLANETS%% 
            WHERE id_moon = :moon_id;";
            $target_debris = $db->selectSingle($sql, [
                ':moon_id' => $this->_fleet['fleet_end_id'],
            ]);
            $target_planet += $target_debris;
        }

        foreach ($debris_resource as $element_id)
        {
            $debris[$element_id] = $combat_result['debris']['attacker'][$element_id] + 
            $combat_result['debris']['defender'][$element_id];
            $planet_debris[$element_id] = $target_planet['debris_'.$RESOURCE[$element_id]] + 
            $debris[$element_id];
        }

        $report_info = [
            'thisFleet'           => $this->_fleet,
            'debris'              => $debris,
            'stealResource'       => $steal_resource,
            'moonChance'          => null,
            'moonDestroy'         => true,
            'moonName'            => null,
            'moonDestroyChance'   => null,
            'moonDestroySuccess'  => null,
            'fleetDestroyChance'  => null,
            'fleetDestroySuccess' => false,
        ];

        switch ($combat_result['won'])
        {
            // Win
            case "a":
                $moon_destroy_chance = round((100 - sqrt($target_planet['diameter'])) * sqrt($fleet_attack[$this->_fleet['fleet_id']]['unit'][214]), 1);

                // Max 100% | Min 0%
                $moon_destroy_chance = min($moon_destroy_chance, 100);
                $moon_destroy_chance = max($moon_destroy_chance, 0);

                $rand_chance = mt_rand(1, 100);
                if ($rand_chance <= $moon_destroy_chance)
                {
                    $sql = 'SELECT id FROM %%PLANETS%% WHERE id_moon = :id_moon;';
                    $planet_id = $db->selectSingle($sql, [
                        ':id_moon' => $target_planet['id'],
                    ], 'id');

                    $sql = 'UPDATE %%FLEETS%% SET
					fleet_start_type		= 1,
					fleet_start_id			= :planet_id
					WHERE fleet_start_id	= :id_moon;';

                    $db->update($sql, [
                        ':planet_id' => $planet_id,
                        ':id_moon'  => $target_planet['id'],
                    ]);

                    $sql = 'UPDATE %%FLEETS%% SET
					fleet_end_type	= 1,
					fleet_end_id	= :id_moon,
					fleet_mission	= IF(fleet_mission = 9, 1, fleet_mission)
					WHERE fleet_end_id = :planet_id
					AND fleet_id != :fleetId;';

                    $db->update($sql, [
                        ':planet_id' => $planet_id,
                        ':id_moon'  => $target_planet['id'],
                        ':fleetId'  => $this->_fleet['fleet_id'],
                    ]);

                    $sql = "UPDATE %%ACS%% SET target = :planet_id WHERE target = :id_moon;";
                    $db->update($sql, [
                        ':planet_id' => $planet_id,
                        ':id_moon'  => $target_planet['id'],
                    ]);

                    // Redirect fleets from moon to player's main planet.
                    $db->update("UPDATE %%FLEETS%% SET fleet_start_id = :main_id, fleet_start_galaxy = :main_galaxy, fleet_start_system = :main_system, fleet_start_planet = :main_planet, fleet_start_type = 1 WHERE fleet_start_id = :destroyed", [
                        ':main_id'     => $target_user['id_planet'],
                        ':main_galaxy' => $target_user['galaxy'],
                        ':main_system' => $target_user['system'],
                        ':main_planet' => $target_user['planet'],
                        ':destroyed'   => $target_planet['id'],
                    ]);

                    PlayerUtil::deletePlanet($target_planet['id']);

                    $report_info['moonDestroySuccess'] = 1;
                }
                else
                {
                    $report_info['moonDestroySuccess'] = 0;
                }

                $fleet_destroy_chance = round(sqrt($target_planet['diameter']) / 2);

                $rand_chance = mt_rand(1, 100);
                if ($rand_chance <= $fleet_destroy_chance)
                {
                    $this->KillFleet();
                    $report_info['fleetDestroySuccess'] = true;
                }
                else
                {
                    $report_info['fleetDestroySuccess'] = false;
                }

                $report_info['moonDestroyChance'] = $moon_destroy_chance;
                $report_info['fleetDestroyChance'] = $fleet_destroy_chance;

                $attack_status = 'wons';
                $defend_status = 'loos';
                $class = ['raportWin', 'raportLose'];
                break;
            case "r":
                // Lose
                $attack_status = 'loos';
                $defend_status = 'wons';
                $class = ['raportLose', 'raportWin'];
                $report_info['moonDestroySuccess'] = -1;
                break;
            default:
                // Draw
                $attack_status = 'draws';
                $defend_status = 'draws';
                $class = ['raportDraw', 'raportDraw'];
                $report_info['moonDestroySuccess'] = -1;
                break;
        }

        require_once 'includes/classes/missions/functions/GenerateReport.php';
        $report_data = GenerateReport($combat_result, $report_info);

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
            ':time'       => $this->_fleet['fleet_start_time'],
            ':attackers'  => implode(',', array_keys($user_attack)),
            ':defenders'  => implode(',', array_keys($user_defend)),
        ]);

        $i = 0;

        foreach ([$user_attack, $user_defend] as $data)
        {
            foreach ($data as $user_id => $user_name)
            {
                $LNG = $this->getLanguage(null, $user_id);

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
                    $user_id,
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
                    ':user_role' => 1,
                    ':user_name' => $user_name,
                    ':user_id'   => $user_id,
                ]);
            }

            $i++;
        }

        if ($this->_fleet['fleet_end_type'] == 3)
        {
            $debris_type = 'id_moon';
        }
        else
        {
            $debris_type = 'id';
        }

        $sql = 'UPDATE %%PLANETS%% SET
		debris_metal	= :metal,
		debris_crystal	= :crystal
		WHERE '.$debris_type.' = :planet_id;';

        $db->update($sql, [
            ':metal'    => $planet_debris[901],
            ':crystal'  => $planet_debris[902],
            ':planet_id' => $this->_fleet['fleet_end_id'],
        ]);

        $sql = 'UPDATE %%PLANETS%% SET
		metal		= metal - :metal,
		crystal		= crystal - :crystal,
		deuterium	= deuterium - :deuterium
		WHERE id = :planet_id;';

        $db->update($sql, [
            ':metal'     => $steal_resource[901],
            ':crystal'   => $steal_resource[902],
            ':deuterium' => $steal_resource[903],
            ':planet_id'  => $this->_fleet['fleet_end_id'],
        ]);

        $sql = 'INSERT INTO %%TOPKB%% SET
		units 		= :units,
		rid			= :report_id,
		time		= :time,
		universe	= :universe,
		result		= :result;';

        $db->insert($sql, [
            ':units'    => $combat_result['unitLost']['attacker'] + $combat_result['unitLost']['defender'],
            ':report_id' => $report_id,
            ':time'     => $this->_fleet['fleet_start_time'],
            ':universe' => $this->_fleet['fleet_universe'],
            ':result'   => $combat_result['won'],
        ]);

        $sql = 'UPDATE %%USERS%% SET
		`'.$attack_status.'` = `'.$attack_status.'` + 1,
		kbmetal		= kbmetal + :debris_metal,
		kbcrystal	= kbcrystal + :debris_crystal,
		lostunits	= lostunits + :lost_units,
		desunits	= desunits + :destroyed_units
		WHERE id IN ('.implode(',', array_keys($user_attack)).');';

        $db->update($sql, [
            ':debris_metal'    => $debris[901],
            ':debris_crystal'  => $debris[902],
            ':lost_units'      => $combat_result['unitLost']['attacker'],
            ':destroyed_units' => $combat_result['unitLost']['defender'],
        ]);

        $sql = 'UPDATE %%USERS%% SET
		`'.$defend_status.'` = `'.$defend_status.'` + 1,
		kbmetal		= kbmetal + :debris_metal,
		kbcrystal	= kbcrystal + :debris_crystal,
		lostunits	= lostunits + :lost_units,
		desunits	= desunits + :destroyed_units
		WHERE id IN ('.implode(',', array_keys($user_defend)).');';

        $db->update($sql, [
            ':debris_metal'    => $debris[901],
            ':debris_crystal'  => $debris[902],
            ':lost_units'      => $combat_result['unitLost']['defender'],
            ':destroyed_units' => $combat_result['unitLost']['attacker'],
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
