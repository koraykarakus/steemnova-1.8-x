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

/**
 *
 */
class ShowMessageListPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG, $USER;
        $page = HTTP::_GP('side', 1);
        $type = HTTP::_GP('type', 100);
        $sender = HTTP::_GP('sender', '', UTF8_SUPPORT);
        $receiver = HTTP::_GP('receiver', '', UTF8_SUPPORT);
        $date_start = HTTP::_GP('dateStart', []);
        $date_end = HTTP::_GP('dateEnd', []);

        $db = Database::get();

        $per_side = 50;

        $message_list = [];
        $user_where_sql = $date_where_sql = $countJoinSQL = '';

        $categories = $LNG['mg_type'];
        unset($categories[999]);

        $date_start = array_filter($date_start, 'is_numeric');
        $date_end = array_filter($date_end, 'is_numeric');

        $use_date_start = count($date_start) == 3;
        $use_date_end = count($date_end) == 3;

        if ($type != 100)
        {
            if (!empty($sender))
            {
                $sql = "SELECT COUNT(*) as count FROM %%MESSAGES%%
				LEFT JOIN %%USERS%% as u ON message_sender = u.id
				WHERE message_type = :type AND message_universe = :universe AND u.username = :sender ";

                if ($use_date_start
                    && $use_date_end)
                {
                    $sql .= ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']).' AND '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time > '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time < '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }

                $message_count = $db->selectSingle($sql, [
                    ':sender'   => $sender,
                    ':type'     => $type,
                    ':universe' => Universe::getEmulated(),
                ], 'count');

            }
            elseif (!empty($receiver))
            {

                $sql = "SELECT COUNT(*) as count FROM %%MESSAGES%%
				LEFT JOIN %%USERS%% as u ON message_owner = u.id
				WHERE message_type = :type AND message_universe = :universe AND u.username = :receiver ";

                if ($use_date_start
                    && $use_date_end)
                {
                    $sql .= ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']).' AND '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time > '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time < '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }

                $message_count = $db->selectSingle($sql, [
                    ':type'     => $type,
                    ':receiver' => $receiver,
                    ':universe' => Universe::getEmulated(),
                ], 'count');

            }
            else
            {
                $sql = "SELECT COUNT(*) as count FROM %%MESSAGES%%
				WHERE message_universe = :universe ";

                if ($use_date_start
                    && $use_date_end)
                {
                    $sql .= ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']).' AND '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time > '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time < '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }

                $message_count = $db->selectSingle($sql, [
                    ':universe' => Universe::getEmulated(),
                ], 'count');
            }

        }
        else
        {

            if (!empty($sender))
            {
                $sql = "SELECT COUNT(*) as count FROM %%MESSAGES%%
				LEFT JOIN %%USERS%% as u ON message_sender = u.id
				WHERE message_universe = :universe ";

                if ($use_date_start
                    && $use_date_end)
                {
                    $sql .= ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']).' AND '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time > '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time < '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }

                $message_count = $db->selectSingle($sql, [
                    ':universe' => Universe::getEmulated(),
                ], 'count');

            }
            elseif (!empty($receiver))
            {
                $sql = "SELECT COUNT(*) as count FROM %%MESSAGES%%
				LEFT JOIN %%USERS%% as u ON message_owner = u.id
				WHERE message_universe = :universe ";

                if ($use_date_start
                    && $use_date_end)
                {
                    $sql .= ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']).' AND '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time > '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time < '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }

                $message_count = $db->selectSingle($sql, [
                    ':universe' => Universe::getEmulated(),
                ], 'count');

            }
            else
            {
                $sql = "SELECT COUNT(*) as count FROM %%MESSAGES%%
				WHERE message_universe = :universe ";

                if ($use_date_start
                    && $use_date_end)
                {
                    $sql .= ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']).' AND '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time > '.mktime(0, 0, 0, (int) $date_start['month'], (int) $date_start['day'], (int) $date_start['year']);
                }
                elseif ($use_date_start)
                {
                    $sql .= ' AND message_time < '.mktime(23, 59, 59, (int) $date_end['month'], (int) $date_end['day'], (int) $date_end['year']);
                }

                $message_count = $db->selectSingle($sql, [
                    ':universe' => Universe::getEmulated(),
                ], 'count');
            }

        }

        $max_page = max(1, ceil($message_count / $per_side));
        $page = max(1, min($page, $max_page));

        $sql_limit = (($page - 1) * $per_side).", ".($per_side - 1);

        if ($type == 100)
        {

            $sql = "SELECT u.username, us.username as senderName, m.*
			FROM %%MESSAGES%% as m
			LEFT JOIN %%USERS%% as u ON m.message_owner = u.id
			LEFT JOIN %%USERS%% as us ON m.message_sender = us.id
			WHERE m.message_universe = :universe
			".$date_where_sql."
			".$user_where_sql."
			ORDER BY message_time DESC, message_id DESC
			LIMIT ".$sql_limit.";";

            $message_raw = $db->select($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }
        else
        {

            $sql = "SELECT u.username, us.username as senderName, m.*
			FROM %%MESSAGES%% as m
			LEFT JOIN %%USERS%% as u ON m.message_owner = u.id
			LEFT JOIN %%USERS%% as us ON m.message_sender = us.id
			WHERE m.message_type = ".$type." AND message_universe = :universe
			".$date_where_sql."
			".$user_where_sql."
			ORDER BY message_time DESC, message_id DESC
			LIMIT ".$sql_limit.";";

            $message_raw = $db->select($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        foreach ($message_raw as $c_message)
        {
            $message_list[$c_message['message_id']] = [
                'sender'   => empty($c_message['senderName']) ? $c_message['message_from'] : $c_message['senderName'].' (ID:&nbsp;'.$c_message['message_sender'].')',
                'receiver' => $c_message['username'].' (ID:&nbsp;'.$c_message['message_owner'].')',
                'subject'  => $c_message['message_subject'],
                'text'     => $c_message['message_text'],
                'type'     => $c_message['message_type'],
                'deleted'  => $c_message['message_deleted'] != null,
                'time'     => str_replace(' ', '&nbsp;', _date($LNG['php_tdformat'], $c_message['message_time']), $USER['timezone']),
            ];
        }

        $this->assign([
            'categories'  => $categories,
            'maxPage'     => $max_page,
            'page'        => $page,
            'messageList' => $message_list,
            'type'        => $type,
            'dateStart'   => $date_start,
            'dateEnd'     => $date_end,
            'sender'      => $sender,
            'receiver'    => $receiver,
        ]);

        $this->display('page.messagelist.default.tpl');

    }

}
