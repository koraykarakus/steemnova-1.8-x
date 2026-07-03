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

class SupportTickets
{
    public function createTicket($ownerID, $categoryID, $subject)
    {
        $sql = 'INSERT INTO %%TICKETS%% SET
		owner_id	= :owner_id,
		universe	= :universe,
		category_id	= :category_id,
		subject		= :subject,
		time		= :time;';

        Database::get()->insert($sql, [
            ':owner_id'    => $ownerID,
            ':universe'    => Universe::current(),
            ':category_id' => $categoryID,
            ':subject'     => $subject,
            ':time'        => TIMESTAMP,
        ]);

        return Database::get()->lastInsertId();
    }

    public function createAnswer($ticketID, $ownerID, $ownerName, $subject, $message, $status)
    {
        $sql = 'INSERT INTO %%TICKETS_ANSWER%% SET
		ticket_id	= :ticket_id,
		owner_id	= :owner_id,
		owner_name	= :owner_name,
		subject		= :subject,
		message		= :message,
		time		= :time;';

        Database::get()->insert($sql, [
            ':ticket_id'  => $ticketID,
            ':owner_id'   => $ownerID,
            ':owner_name' => $ownerName,
            ':subject'    => $subject,
            ':message'    => $message,
            ':time'       => TIMESTAMP,
        ]);

        $answerId = Database::get()->lastInsertId();

        $sql = 'UPDATE %%TICKETS%% SET status = :status WHERE ticket_id = :ticket_id;';

        Database::get()->update($sql, [
            ':status'    => $status,
            ':ticket_id' => $ticketID,
        ]);

        return $answerId;
    }

    public function getCategoryList()
    {
        $sql = 'SELECT * FROM %%TICKETS_CATEGORY%%;';

        $categoryResult = Database::get()->select($sql);
        $categoryList = [];

        foreach ($categoryResult as $categoryRow)
        {
            $categoryList[$categoryRow['category_id']] = $categoryRow['name'];
        }

        return $categoryList;
    }
}
