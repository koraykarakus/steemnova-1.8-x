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

class ShowSupportPage extends AbstractAdminPage
{
    private $ticketObj;

    public function __construct()
    {
        parent::__construct();

        require('includes/classes/class.SupportTickets.php');
        $this->ticketObj = new SupportTickets();
        // 2Moons 1.7TO1.6 PageClass Wrapper
        $ACTION = HTTP::_GP('mode', 'show');
        if (is_callable([$this, $ACTION]))
        {
            $this->{$ACTION}();
        }
        else
        {
            $this->show();
        }
    }

    public function show(): void
    {
        global $USER, $LNG;

        $db = Database::get();

        $sql = "SELECT t.*, u.username, COUNT(a.ticket_id) as answer FROM
		%%TICKETS%% as t INNER JOIN %%TICKETS_ANSWER%% as a USING (ticket_id)
		INNER JOIN %%USERS%% as u ON u.id = t.owner_id WHERE t.universe = :universe
		GROUP BY a.ticket_id ORDER BY t.ticket_id DESC;";

        $ticket_result = $db->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        $ticket_list = [];

        foreach ($ticket_result as &$c_ticket)
        {
            $c_ticket['time'] = _date($LNG['php_tdformat'], $c_ticket['time'], $USER['timezone']);

            $ticket_list[$c_ticket['ticket_id']] = $c_ticket;
        }
        unset($c_ticket);

        $this->assign([
            'ticketList' => $ticket_list,
        ]);

        $this->display('page.ticket.default.tpl');
    }

    public function send(): void
    {
        global $USER, $LNG;

        $db = Database::get();

        $ticket_id = HTTP::_GP('id', 0);
        $message = HTTP::_GP('message', '', true);
        $change = HTTP::_GP('change_status', 0);

        $sql = "SELECT owner_id, subject, status 
        FROM %%TICKETS%% WHERE ticket_id = :ticket_id;";

        $ticket_detail = $db->selectSingle($sql, [
            ':ticket_id' => $ticket_id,
        ]);

        $status = ($change ? ($ticket_detail['status'] <= 1 ? 2 : 1) : 1);

        if (!$change
            && empty($message))
        {
            HTTP::redirectTo('admin.php?page=support&mode=view&id='.$ticket_id);
        }

        $subject = "RE: ".$ticket_detail['subject'];

        if ($change
            && $status == 1)
        {
            $this->ticketObj->createAnswer($ticket_id, $USER['id'], $USER['username'], $subject, $LNG['ti_admin_open'], $status);
        }

        if (!empty($message))
        {
            $this->ticketObj->createAnswer($ticket_id, $USER['id'], $USER['username'], $subject, $message, $status);
        }

        if ($change
            && $status == 2)
        {
            $this->ticketObj->createAnswer($ticket_id, $USER['id'], $USER['username'], $subject, $LNG['ti_admin_close'], $status);
        }

        $subject = sprintf($LNG['sp_answer_message_title'], $ticket_id);
        $text = sprintf($LNG['sp_answer_message'], $ticket_id);

        PlayerUtil::sendMessage(
            $ticket_detail['owner_id'],
            $USER['id'],
            $USER['username'],
            4,
            $subject,
            $text,
            TIMESTAMP,
            null,
            1,
            Universe::getEmulated()
        );

        HTTP::redirectTo('admin.php?page=support');
    }

    public function view(): void
    {
        global $USER, $LNG;

        $db = Database::get();

        $ticket_id = HTTP::_GP('id', 0);

        $sql = "SELECT a.*, t.category_id, t.status FROM %%TICKETS_ANSWER%% as a
		INNER JOIN %%TICKETS%% as t USING(ticket_id) WHERE a.ticket_id = :ticket_id
		ORDER BY a.answer_id;";

        $answer_result = $db->select($sql, [
            ':ticket_id' => $ticket_id,
        ]);

        $answer_list = [];

        $ticket_status = 0;
        foreach ($answer_result as &$c_answer)
        {

            if (empty($ticket_status))
            {
                $ticket_status = $c_answer['status'];
            }

            $c_answer['time'] = _date($LNG['php_tdformat'], $c_answer['time'], $USER['timezone']);
            $c_answer['message'] = BBCode::parse($c_answer['message']);

            $answer_list[$c_answer['answer_id']] = $c_answer;
        }
        unset($c_answer);

        $category_list = $this->ticketObj->getCategoryList();

        $this->assign([
            'ticketID'      => $ticket_id,
            'ticket_status' => $ticket_status,
            'categoryList'  => $category_list,
            'answerList'    => $answer_list,
        ]);

        $this->display('page.ticket.view.tpl');
    }
}
