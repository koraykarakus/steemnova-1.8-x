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

class ShowTicketPage extends AbstractGamePage
{
    public static $require_module = MODULE_SUPPORT;

    private $ticket_obj;

    public function __construct()
    {
        parent::__construct();
        require('includes/classes/class.SupportTickets.php');
        $this->ticket_obj = new SupportTickets();
    }

    public function show(): void
    {
        global $USER, $LNG;

        $db = Database::get();

        $sql = "SELECT t.*, COUNT(a.ticketID) as answer
		FROM %%TICKETS%% t
		INNER JOIN %%TICKETS_ANSWER%% a USING (ticketID)
		WHERE t.ownerID = :userID GROUP BY a.ticketID ORDER BY t.ticketID DESC;";

        $ticket_result = $db->select($sql, [
            ':userID' => $USER['id'],
        ]);

        $ticket_list = [];

        foreach ($ticket_result as $c_ticket)
        {
            $c_ticket['time'] = _date($LNG['php_tdformat'], $c_ticket['time'], $USER['timezone']);

            $ticket_list[$c_ticket['ticketID']] = $c_ticket;
        }

        $this->assign([
            'ticketList' => $ticket_list,
        ]);

        $this->display('page.ticket.default.tpl');
    }

    public function create(): void
    {
        $category_list = $this->ticket_obj->getCategoryList();

        $this->assign([
            'categoryList' => $category_list,
        ]);

        $this->display('page.ticket.create.tpl');
    }

    public function send(): void
    {
        global $USER, $LNG;

        $ticket_id = HTTP::_GP('id', 0);
        $category_id = HTTP::_GP('category', 0);
        $message = HTTP::_GP('message', '', true);
        $subject = HTTP::_GP('subject', '', true);

        if (empty($message))
        {
            if (empty($ticket_id))
            {
                $this->redirectTo('game.php?page=ticket&mode=create');
            }
            else
            {
                $this->redirectTo('game.php?page=ticket&mode=view&id='.$ticket_id);
            }
        }

        if (empty($ticket_id))
        {
            if (empty($subject))
            {
                $this->printMessage($LNG['ti_error_no_subject'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'javascript:window.history.back()',
                ]]);
            }

            $ticket_id = $this->ticket_obj->createTicket($USER['id'], $category_id, $subject);
        }
        else
        {
            $db = Database::get();

            $sql = "SELECT status FROM %%TICKETS%% WHERE ticketID = :ticketID;";
            $ticket_status = $db->selectSingle($sql, [
                ':ticketID' => $ticket_id,
            ], 'status');

            if ($ticket_status == 2)
            {
                $this->printMessage($LNG['ti_error_closed']);
            }
        }

        $this->ticket_obj->createAnswer(
            $ticket_id,
            $USER['id'],
            $USER['username'],
            $subject,
            $message,
            0
        );

        $this->redirectTo('game.php?page=ticket&mode=view&id='.$ticket_id);
    }

    public function view(): void
    {
        global $USER, $LNG;

        require_once 'includes/classes/BBCode.class.php';

        $db = Database::get();

        $ticket_id = HTTP::_GP('id', 0);

        $sql = "SELECT a.*, t.categoryID, t.status 
        FROM %%TICKETS_ANSWER%% a INNER JOIN %%TICKETS%% t USING(ticketID) 
        WHERE a.ticketID = :ticketID AND t.ownerID = :ownerID ORDER BY a.answerID;";

        $answer_result = $db->select($sql, [
            ':ticketID' => $ticket_id,
            ':ownerID'  => $USER['id'],
        ]);

        $answer_list = [];

        if (empty($answer_result))
        {
            $this->printMessage(sprintf($LNG['ti_not_exist'], $ticket_id), [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=ticket',
            ]]);
        }

        $ticket_status = 0;

        foreach ($answer_result as $c_answer)
        {
            $c_answer['time'] = _date($LNG['php_tdformat'], $c_answer['time'], $USER['timezone']);
            $c_answer['message'] = BBCode::parse($c_answer['message']);
            $answer_list[$c_answer['answerID']] = $c_answer;
            if (empty($ticket_status))
            {
                $ticket_status = $c_answer['status'];
            }
        }

        $category_list = $this->ticket_obj->getCategoryList();

        $this->assign([
            'ticketID'     => $ticket_id,
            'categoryList' => $category_list,
            'answerList'   => $answer_list,
            'status'       => $ticket_status,
        ]);

        $this->display('page.ticket.view.tpl');
    }
}
