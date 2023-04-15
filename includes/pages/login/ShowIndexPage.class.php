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

class ShowIndexPage extends AbstractLoginPage
{
	function __construct()
	{
		parent::__construct();
		$this->setWindow('light');
	}

	function show()
	{
		global $LNG, $config;

		$referralID		= HTTP::_GP('ref', 0);
		if(!empty($referralID))
		{
			$this->redirectTo('index.php?page=register&referralID='.$referralID);
		}

		$universeSelect	= array();



		$Code	= HTTP::_GP('code', 0);
		$loginCode	= false;
		if(isset($LNG['login_error_'.$Code]))
		{
			$loginCode	= $LNG['login_error_'.$Code];
		}

		$this->assign(array(
			'code'					=> $loginCode,
			'use_recaptcha_on_login' => $config->use_recaptcha_on_login,
			'csrfToken' => $this->generateCSRFToken(),
		));



		$this->display('page.index.default.tpl');
	}
}
