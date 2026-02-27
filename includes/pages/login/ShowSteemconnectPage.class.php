<?php

/**
 *  SteemNova
 *   by mys 2018
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package Steemnova
 * @author mys <miccelinski@gmail.com>
 * @licence MIT
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 */

class ShowSteemconnectPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $session = Session::create();

        require 'includes/classes/extauth/externalAuth.interface.php';
        require 'includes/classes/extauth/steemconnect.class.php';

        $method_class = 'SteemconnectAuth';

        /** @var externalAuth $authObj */
        $auth_obj = new $method_class();

        if (!$auth_obj->isActiveMode())
        {
            $session->delete();
            $this->redirectTo('index.php?code=5');
        }

        if (!$auth_obj->isValid())
        {
            $session->delete();
            $this->redirectTo('index.php?code=4');
        }

        $login_data = $auth_obj->getLoginData();

        if (empty($login_data))
        {
            // create account
            // $session->delete();
            $auth_obj->register();
            $login_data = $auth_obj->getLoginData();
        }

        $session->userId = (int) $login_data['id'];
        $session->adminAccess = 0;
        $session->data = $auth_obj->getAccountData();
        $session->save();
        $this->redirectTo("game.php");
    }
}
