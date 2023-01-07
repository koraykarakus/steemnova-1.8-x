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
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

if (!allowedTo(str_replace(array(dirname(__FILE__), '\\', '/', '.php'), '', __FILE__))) throw new Exception("Permission error!");

function ShowNewsPage(){
	global $LNG, $USER;

	$db = Database::get();

	if($_GET['action'] == 'send') {
		$edit_id 	= HTTP::_GP('id', 0);
		$title 		= HTTP::_GP('title', '', true);
		$text 		= HTTP::_GP('text', '', true);
		$query		= ($_GET['mode'] == 2) ? "" : "";

		if ( $_GET['mode'] == 2) {
			$sql = "INSERT INTO %%NEWS%% (`id`, `user`, `date`, `title`, `text`) VALUES
			(NULL, :userName, :actionTime, :title, :newsText);";

			$db->insert($sql,array(
				':userName' => $USER['username'],
				':actionTime' => TIMESTAMP,
				':title' => $title,
				':newsText' => $text
			));

		}else {
			$sql = "UPDATE %%NEWS%% SET `title` = :title, `text` = :newsText, `date` = :actionTime WHERE `id` = :edit_id LIMIT 1;";

			$db->update($sql,array(
				':title' => $title,
				':newsText' => $newsText,
				':actionTime' => TIMESTAMP,
				':edit_id' => $edit_id
			));

		}

	} elseif($_GET['action'] == 'delete' && isset($_GET['id'])) {

		$sql = "DELETE FROM %%NEWS%% WHERE `id` = :news_id;";

		$db->delete($sql,array(
			':news_id' => HTTP::_GP('id', 0)
		));

	}

	$sql = "SELECT * FROM %%NEWS%% ORDER BY id ASC;";

	$news = $db->select($sql);

	foreach ($news as $u) {
		$NewsList[]	= array(
			'id'		=> $u['id'],
			'title'		=> $u['title'],
			'date'		=> _date($LNG['php_tdformat'], $u['date'], $USER['timezone']),
			'user'		=> $u['user'],
			'confirm'	=> sprintf($LNG['nws_confirm'], $u['title']),
		);
	}

	$template	= new template();


	if($_GET['action'] == 'edit' && isset($_GET['id'])) {

		$sql = "SELECT id, title, text FROM %%NEWS%% WHERE id = :id;";

		$db->select($sql,array(
			':id' => HTTP::_GP('id',0)
		));

		$template->assign_vars(array(
			'mode'			=> 1,
			'nws_head'		=> sprintf($LNG['nws_head_edit'], $News['title']),
			'news_id'		=> $News['id'],
			'news_title'	=> $News['title'],
			'news_text'		=> $News['text'],
		));
	} elseif($_GET['action'] == 'create') {
		$template->assign_vars(array(
			'mode'			=> 2,
			'nws_head'		=> $LNG['nws_head_create'],
		));
	}

	$template->assign_vars(array(
		'NewsList'		=> $NewsList,
		'button_submit'	=> $LNG['button_submit'],
		'nws_total'		=> sprintf($LNG['nws_total'], $NewsList && count($NewsList)),
		'nws_news'		=> $LNG['nws_news'],
		'nws_id'		=> $LNG['nws_id'],
		'nws_title'		=> $LNG['nws_title'],
		'nws_date'		=> $LNG['nws_date'],
		'nws_from'		=> $LNG['nws_from'],
		'nws_del'		=> $LNG['nws_del'],
		'nws_create'	=> $LNG['nws_create'],
		'nws_content'	=> $LNG['nws_content'],
	));

	$template->show('NewsPage.tpl');
}
