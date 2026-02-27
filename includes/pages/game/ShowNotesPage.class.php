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

class ShowNotesPage extends AbstractGamePage
{
    public static $require_module = MODULE_NOTICE;

    public function __construct()
    {
        parent::__construct();
        $this->setWindow('popup');
        $this->initTemplate();
    }

    public function show(): void
    {
        global $LNG, $USER;

        $db = Database::get();

        $sql = "SELECT * FROM %%NOTES%% 
        WHERE `owner` = :userID 
        ORDER BY `priority` DESC, `time` DESC;";

        $notes = $db->select($sql, [
            ':userID' => $USER['id'],
        ]);

        $notes_list = [];

        foreach ($notes as $c_note)
        {
            $notes_list[$c_note['id']] = [
                'time'     => _date($LNG['php_tdformat'], $c_note['time'], $USER['timezone']),
                'title'    => $c_note['title'],
                'size'     => strlen($c_note['text']),
                'priority' => $c_note['priority'],
            ];
        }

        $this->assign([
            'notesList' => $notes_list,
        ]);

        $this->display('page.notes.default.tpl');
    }

    public function detail(): void
    {
        global $LNG, $USER;

        $note_id = HTTP::_GP('id', 0);

        $db = Database::get();

        $sql = "SELECT * FROM %%NOTES%% 
        WHERE id = :note_id AND owner = :userID;";

        $note_detail = $db->selectSingle($sql, [
            ':userID'  => $USER['id'],
            ':note_id' => $note_id,
        ]);

        if (!$note_detail)
        {
            $this->printMessage('wrong note id');
        }

        $this->tpl_obj->execscript("$('#cntChars').text($('#text').val().length);");
        $this->assign([
            'PriorityList' => [2 => $LNG['nt_important'],
                1                => $LNG['nt_normal'],
                0                => $LNG['nt_unimportant']],
            'noteDetail' => $note_detail,
        ]);

        $this->display('page.notes.detail.tpl');
    }

    public function insert(): void
    {
        global $LNG, $USER, $config;
        $priority = HTTP::_GP('priority', 1);
        $title = HTTP::_GP('title', '', true);
        $text = HTTP::_GP('text', '', true);
        $id = HTTP::_GP('id', 0);
        $title = !empty($title) ? $title : $LNG['nt_no_title'];
        $text = !empty($text) ? $text : $LNG['nt_no_text'];

        $db = Database::get();

        if ($id == 0)
        {

            $sql = "SELECT COUNT(*) as count FROM %%NOTES%% WHERE owner = :userID;";

            $user_notes_count = $db->selectSingle($sql, [
                ':userID' => $USER['id'],
            ], 'count');

            if ($user_notes_count >= $config->user_max_notes)
            {
                $this->printMessage(sprintf(
                    $LNG['nt_error_add_1'],
                    $config->user_max_notes,
                ));
            }

            $sql = "INSERT INTO %%NOTES%% SET `owner` = :userID, 
            `time` = :time, priority = :priority, 
            title = :title, `text` = :text, 
            universe = :universe;";

            $db->insert($sql, [
                ':userID'   => $USER['id'],
                ':time'     => TIMESTAMP,
                ':priority' => $priority,
                ':title'    => $title,
                ':text'     => $text,
                ':universe' => Universe::current(),
            ]);

        }
        else
        {
            $sql = "UPDATE %%NOTES%% SET `time` = :time, 
            `priority` = :priority, `title` = :title, `text` = :text 
            WHERE id = :noteID;";

            $db->update($sql, [
                ':noteID'   => $id,
                ':time'     => TIMESTAMP,
                ':priority' => $priority,
                ':title'    => $title,
                ':text'     => $text,
            ]);
        }

        $this->redirectTo('game.php?page=notes');
    }

    public function delete(): void
    {
        global $USER;

        $delete_ids = HTTP::_GP('delmes', []);
        $delete_ids = array_keys($delete_ids);
        $delete_ids = array_filter($delete_ids, 'is_numeric');

        if (!empty($delete_ids))
        {
            $sql = 'DELETE FROM %%NOTES%% 
            WHERE id IN ('.implode(', ', $delete_ids).') AND owner = :userID;';

            Database::get()->delete($sql, [
                ':userID' => $USER['id'],
            ]);
        }

        $this->redirectTo('game.php?page=notes');
    }

}
