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

class MissionCaseMIP extends MissionFunctions implements Mission
{
    public function __construct($fleet)
    {
        $this->_fleet = $fleet;
    }

    public function TargetEvent()
    {
        global $RESOURCE, $RESLIST;

        $db = Database::get();

        $sql_fields = [];
        $element_ids = array_merge($RESLIST['defense'], $RESLIST['missile']);

        foreach ($element_ids as $element_id)
        {
            $sql_fields[] = '%%PLANETS%%.`' . $RESOURCE[$element_id] . '`';
        }

        $sql = 'SELECT lang, shield_tech,
		%%PLANETS%%.id, name, id_owner, ' . implode(', ', $sql_fields) . '
		FROM %%PLANETS%%
		INNER JOIN %%USERS%% ON id_owner = %%USERS%%.id
		WHERE %%PLANETS%%.id = :planet_id;';

        $target_data = $db->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_end_id'],
        ]);

        if ($this->_fleet['fleet_end_type'] == 3)
        {
            $sql = 'SELECT ' . $RESOURCE[502] . ' FROM %%PLANETS%% WHERE id_moon = :id_moon;';
            $target_data[$RESOURCE[502]] = $db->selectSingle($sql, [
                ':id_moon' => $this->_fleet['fleet_end_id'],
            ], $RESOURCE[502]);
        }

        $sql = 'SELECT lang, military_tech FROM %%USERS%% WHERE id = :user_id;';
        $sender_data = $db->selectSingle($sql, [
            ':user_id' => $this->_fleet['fleet_owner'],
        ]);

        if (
            !in_array($this->_fleet['fleet_target_obj'], array_merge($RESLIST['defense'], $RESLIST['missile']))
            || $this->_fleet['fleet_target_obj'] == 502
            || $this->_fleet['fleet_target_obj'] == 0
        ) {
            $primary_target = 401;
        }
        else
        {
            $primary_target = $this->_fleet['fleet_target_obj'];
        }

        $target_defensive = [];

        foreach ($element_ids as $element_id)
        {
            $target_defensive[$element_id] = $target_data[$RESOURCE[$element_id]];
        }

        unset($target_defensive[502]);

        $sender_data['LNG'] = $this->getLanguage($sender_data['lang']);
        $target_data['LNG'] = $this->getLanguage($target_data['lang']);

        $sender_data['MSG'] = false;
        $target_data['MSG'] = false;

        if ($target_data[$RESOURCE[502]] >= $this->_fleet['fleet_amount'])
        {
            $sender_data['MSG'] = $sender_data['LNG']['sys_irak_no_att'];
            $target_data['MSG'] = $target_data['LNG']['sys_irak_no_att'];
            $where = $this->_fleet['fleet_end_type'] == 3 ? 'id_moon' : 'id';

            $sql = 'UPDATE %%PLANETS%% SET ' . $RESOURCE[502] . ' = ' .
            $RESOURCE[502] . ' - :amount WHERE ' . $where . ' = :planet_id;';

            $db->update($sql, [
                ':amount'    => $this->_fleet['fleet_amount'],
                ':planet_id' => $target_data['id'],
            ]);
        }
        else
        {
            if ($target_data[$RESOURCE[502]] > 0)
            {
                $where = $this->_fleet['fleet_end_type'] == 3 ? 'id_moon' : 'id';
                $sql = 'UPDATE %%PLANETS%% SET ' . $RESOURCE[502] . ' = :amount WHERE ' . $where . ' = :planet_id;';

                $db->update($sql, [
                    ':amount'    => 0,
                    ':planet_id' => $target_data['id'],
                ]);
            }

            $target_defensive = array_filter($target_defensive);

            if (!empty($target_defensive))
            {
                require_once 'includes/classes/missions/functions/calculateMIPAttack.php';
                $result = calculateMIPAttack(
                    $target_data["shield_tech"],
                    $sender_data["military_tech"],
                    $this->_fleet['fleet_amount'],
                    $target_defensive,
                    $primary_target,
                    $target_data[$RESOURCE[502]]
                );

                $result = array_filter($result);

                $sender_data['MSG'] = sprintf($sender_data['LNG']['sys_irak_def'], $target_data[$RESOURCE[502]]) . '<br><br>';
                $target_data['MSG'] = sprintf($target_data['LNG']['sys_irak_def'], $target_data[$RESOURCE[502]]) . '<br><br>';

                ksort($result, SORT_NUMERIC);

                foreach ($result as $element => $destroy)
                {
                    $sender_data['MSG'] .= sprintf('%s (- %d)<br>', $sender_data['LNG']['tech'][$element], $destroy);
                    $target_data['MSG'] .= sprintf('%s (- %d)<br>', $target_data['LNG']['tech'][$element], $destroy);

                    $sql = 'UPDATE %%PLANETS%% SET ' . $RESOURCE[$element] . ' = ' . $RESOURCE[$element] . ' - :amount WHERE id = :planet_id;';
                    $db->update($sql, [
                        ':planet_id' => $target_data['id'],
                        ':amount'    => $destroy,
                    ]);
                }
            }
            else
            {
                $sender_data['MSG'] = $sender_data['LNG']['sys_irak_no_def'];
                $target_data['MSG'] = $target_data['LNG']['sys_irak_no_def'];
            }
        }

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planet_id;';
        $planet_name = Database::get()->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_start_id'],
        ], 'name');

        $owner_link = $planet_name . " " . GetStartAddressLink($this->_fleet);
        $target_link = $target_data['name'] . " " . GetTargetAddressLink($this->_fleet);
        $sender_data['MSG'] = sprintf(
            $sender_data['LNG']['sys_irak_mess'],
            $this->_fleet['fleet_amount'],
            $owner_link,
            $target_link
        ) . $sender_data['MSG'];

        $target_data['MSG'] = sprintf(
            $target_data['LNG']['sys_irak_mess'],
            $this->_fleet['fleet_amount'],
            $owner_link,
            $target_link
        ) . $target_data['MSG'];

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $sender_data['LNG']['sys_mess_tower'],
            3,
            $sender_data['LNG']['sys_irak_subject'],
            $sender_data['MSG'],
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_target_owner'],
            0,
            $target_data['LNG']['sys_mess_tower'],
            3,
            $target_data['LNG']['sys_irak_subject'],
            $target_data['MSG'],
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->KillFleet();
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        return;
    }
}
