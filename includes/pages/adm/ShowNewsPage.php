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
class ShowNewsPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG, $USER;

        $db = Database::get();
        $sql = "SELECT * FROM %%NEWS%% ORDER BY id ASC;";
        $news = $db->select($sql);

        $news_list = [];
        foreach ($news as $c_news)
        {
            $news_list[] = [
                'id'    => $c_news['id'],
                'title' => $c_news['title'],
                'date'  => _date(
                    $LNG['php_tdformat'],
                    $c_news['date'],
                    $USER['timezone']
                ),
                'user'    => $c_news['user'],
                'confirm' => sprintf($LNG['nws_confirm'], $c_news['title']),
            ];
        }

        $this->assign([
            'news_list'  => $news_list,
            'news_total' => sprintf(
                $LNG['nws_total'],
                $news_list && count($news_list)
            ),
        ]);

        $this->display('page.news.default.tpl');

    }

    public function create(): void
    {
        global $LNG;

        $this->assign([
            'nws_head' => $LNG['nws_head_create'],
        ]);

        $this->display('page.news.create.tpl');
    }

    public function createSend(): void
    {
        global $USER;

        $title = HTTP::_GP('title', '', true);
        $text = HTTP::_GP('text', '', true);

        $db = Database::get();

        $sql = "INSERT INTO %%NEWS%% (`id`, `user`, `date`, `title`, `text`) VALUES
		(NULL, :user_name, :action_time, :title, :news_text);";

        $db->insert($sql, [
            ':user_name'   => $USER['username'],
            ':action_time' => TIMESTAMP,
            ':title'       => $title,
            ':news_text'   => $text,
        ]);

        $this->show();
    }

    public function delete(): void
    {
        $db = Database::get();

        $sql = "DELETE FROM %%NEWS%% WHERE `id` = :news_id;";

        $db->delete($sql, [
            ':news_id' => HTTP::_GP('id', 0),
        ]);

        $this->show();
    }

    public function edit(): void
    {
        global $LNG;

        $db = Database::get();

        $sql = "SELECT `id`, `title`, `text` FROM %%NEWS%% WHERE `id` = :id;";

        $news = $db->selectSingle($sql, [
            ':id' => HTTP::_GP('id', 0),
        ]);

        if ($news === false)
        {
            $this->printMessage('wrong id!');
        }

        $this->assign([
            'mode'       => 1,
            'news_head'  => sprintf($LNG['nws_head_edit'], $news['title']),
            'news_id'    => $news['id'],
            'news_title' => $news['title'],
            'news_text'  => $news['text'],
        ]);

        $this->display('page.news.edit.tpl');

    }

    public function editSend(): void
    {
        $db = Database::get();

        $title = HTTP::_GP('title', '', true);
        $text = HTTP::_GP('text', '', true);
        $edit_id = HTTP::_GP('id', 0);

        $sql = "UPDATE %%NEWS%% SET `title` = :title, 
        `text` = :news_text, `date` = :action_time 
        WHERE `id` = :edit_id;";

        $db->update($sql, [
            ':title'       => $title,
            ':news_text'   => $text,
            ':action_time' => TIMESTAMP,
            ':edit_id'     => $edit_id,
        ]);

        $this->show();
    }

}
