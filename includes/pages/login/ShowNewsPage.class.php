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

class ShowNewsPage extends AbstractLoginPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;

        $sql = "SELECT `date`, `title`, `text`, `user` 
        FROM %%NEWS%% ORDER BY `id` DESC;";

        $news_data = Database::get()->select($sql);

        $news_list = [];
        foreach ($news_data as $c_news)
        {
            $newsList[] = [
                'title' => $c_news['title'],
                'from'  => sprintf($LNG['news_from'], _date($LNG['php_tdformat'], $c_news['date']), $c_news['user']),
                'text'  => makebr($c_news['text']),
            ];
        }

        $this->assign([
            'newsList' => $news_list,
        ]);

        $this->display('page.news.default.tpl');
    }
}
