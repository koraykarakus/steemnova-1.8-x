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
class ShowActivePage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG, $USER;

        $db = Database::get();

        $sql = "SELECT * FROM %%USERS_VALID%% 
        WHERE `universe` = :universe ORDER BY validationID ASC;";

        $valid_users = $db->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        $users = [];
        foreach ($valid_users as $cur_user)
        {
            $users[] = [
                'id'            => $cur_user['validationID'],
                'name'          => $cur_user['userName'],
                'date'          => _date($LNG['php_tdformat'], $cur_user['date'], $USER['timezone']),
                'email'         => $cur_user['email'],
                'ip'            => $cur_user['ip'],
                'password'      => $cur_user['password'],
                'validationKey' => $cur_user['validationKey'],
            ];
        }

        $this->assign([
            'Users' => $users,
            'uni'   => Universe::getEmulated(),
        ]);

        $this->display('page.active.default.tpl');
    }

    public function delete(): void
    {

        $sql = "DELETE FROM %%USERS_VALID% 
        WHERE `validationID` = :validationID AND `universe` = :universe;";

        $db = Database::get();

        $id = HTTP::_GP('id', 0);

        $db->delete($sql, [
            ':validationID' => $id,
            ':universe'     => Universe::getEmulated(),
        ]);

        $this->printMessage('deleted successfully !');
    }

}
