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

require_once('./includes/enums/Login.class.php');

class ShowLoginPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function generateRememberMeToken($universe): array
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        return [
            'selector'  => $selector,
            'validator' => $validator,
            'full'      => $universe . ':' . $selector . ':' . $validator,
        ];
    }

    public function validate(): void
    {
        $email = HTTP::_GP('userEmail', '', true);
        $password = HTTP::_GP('password', '', true);
        $csrf_token = HTTP::_GP('csrfToken', '', true);
        $remember_me = HTTP::_GP('remember_me', 'false');
        $token_val = HTTP::_GP('rememberedTokenValidator', '');
        $token_sel = HTTP::_GP('rememberedTokenSelector', '');
        $mem_email = HTTP::_GP('rememberedEmail', '');
        $universe = HTTP::_GP('universe', Universe::current());
        $external_auth = false;

        $login_service = new LoginService();

        $result = $login_service->Login(
            $email,
            $password,
            $csrf_token,
            $token_val,
            $token_sel,
            $mem_email,
            $universe,
            $external_auth
        );

        if ($result == Login::success)
        {
            $session = Session::create();
            $session->userId = (int) $login_service->login_id;
            $session->adminAccess = 0;
            $session->save();

            $db = Database::get();
            if ($remember_me == 'true')
            {
                $token = $this->generateRememberMeToken($universe);

                //set a cookie
                HTTP::sendCookie('remember_me', $token['full'], TIMESTAMP + 60 * 60 * 24 * 30);

                //delete old remember me data

                $sql = "DELETE FROM %%REMEMBER_ME%% WHERE `user_id` = :userId;";

                $db->delete($sql, [
                    ':userId' => (int) $login_service->login_id,
                ]);

                //insert new remember data,

                $sql = "INSERT INTO %%REMEMBER_ME%% (`selector`,`hashed_validator`, `expiration_date`, `user_id`,`universe`)
				VALUES (:selector,:hashed_validator,:expiration_date,:user_id,:universe);";

                $db->insert($sql, [
                    ':selector'         => $token['selector'],
                    ':hashed_validator' => password_hash($token['validator'], PASSWORD_DEFAULT),
                    ':expiration_date'  => TIMESTAMP + 60 * 60 * 24 * 30, //30 days
                    ':user_id'          => (int) $login_service->login_id,
                    ':universe'         => $universe,
                ]);

            }

            if ($remember_me == "false")
            {
                $sql = "DELETE FROM %%REMEMBER_ME%% WHERE user_id = :userId;";
                $db->delete($sql, [
                    ':userId' => (int) $login_service->login_id,
                ]);
            }
        }

        $data = $login_service->getJsonDataByResultLogin($result);
        $this->sendJSON($data);
    }

}
