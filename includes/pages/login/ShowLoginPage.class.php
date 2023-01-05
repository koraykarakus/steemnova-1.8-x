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


class ShowLoginPage extends AbstractLoginPage
{
	public static $requireModule = 0;

	function __construct()
	{
		parent::__construct();
	}

	function show($error = NULL)
	{
		$this->setWindow('light');

		$this->assign(array(
			'error' => $error
		));

		$this->display('page.index.default.tpl');
	}

	function validate(){
		global $config, $LNG;

		$db = Database::get();

		$userEmail = HTTP::_GP('userEmail', '', true);
		$password = HTTP::_GP('password', '', true);

		$error = array();

		if (empty($userEmail)) {
			$error['email'][] = $LNG['login_error_1'];
		}

		if (empty($password)) {
			$error['password'][] = $LNG['login_error_2'];
		}

		if (!empty($password) && !empty($userEmail)) {
			$sql = "SELECT id, password FROM %%USERS%% WHERE email = :email AND universe = :universe;";

			$loginData = $db->selectSingle($sql, array(
				':email'	=> $userEmail,
				':universe'	=> Universe::current(),
			));

			if (!$loginData) {
				$error['email'][] = $LNG['login_error_3'];
			}

		}


		if ($config->capaktiv === '1')
		{
      require('includes/libs/reCAPTCHA/src/autoload.php');

      $recaptcha = new \ReCaptcha\ReCaptcha($config->capprivate);
      $resp = $recaptcha->verify(HTTP::_GP('g-recaptcha-response', ''), Session::getClientIp());
      if (!$resp->isSuccess())
      {
          $error['recaptcha'][]	= $LNG['login_error_4'];
      }
		}

		if (isset($loginData['password'])) {
			if (!password_verify($password,$loginData['password'])) {
				$error['password'][] = $LNG['login_error_5'];
			}
		}

		if (empty($error))
		{
			$session	= Session::create();
			$session->userId		= (int) $loginData['id'];
			$session->adminAccess	= 0;
			$session->save();

			HTTP::redirectTo('game.php');
		}
		else
		{
			$this->show($error);
		}

	}


}
