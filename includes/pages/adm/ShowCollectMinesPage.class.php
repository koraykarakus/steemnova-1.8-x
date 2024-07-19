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
class ShowCollectMinesPage extends AbstractAdminPage
{


	function __construct()
	{
		parent::__construct();
	}


	function show(){

		global $config;

		$this->assign(array(
      'collect_mines_under_attack' => $config->collect_mines_under_attack,
      'collect_mine_time_minutes' => $config->collect_mine_time_minutes,
		));

		$this->display('page.collect_mines.default.tpl');

	}

	function saveSettings(){
		global $LNG, $config;

			$config_before = array(
        'collect_mines_under_attack' => $config->collect_mines_under_attack,
        'collect_mine_time_minutes' => $config->collect_mine_time_minutes,
			);

			$collect_mines_under_attack = (HTTP::_GP('collect_mines_under_attack','off') == 'on') ? 1 : 0;
      $collect_mine_time_minutes = HTTP::_GP('collect_mine_time_minutes',30);


			$config_after = array(
        'collect_mines_under_attack' => $collect_mines_under_attack,
				'collect_mine_time_minutes' => $collect_mine_time_minutes,
			);

      foreach($config_after as $key => $value)
      {
        $config->$key	= $value;
      }
      $config->save();


			$LOG = new Log(3);
			$LOG->target = 1;
			$LOG->old = $config_before;
			$LOG->new = $config_after;
			$LOG->save();


			$redirectButton = array();
      $redirectButton[] = array(
        'url' => 'admin.php?page=collectMines&mode=show',
				'label' => $LNG['uvs_back']
      );

      $this->printMessage($LNG['settings_successful'],$redirectButton);

	}

}
