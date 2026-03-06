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
class ShowCreatePage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {

        $this->assign([

        ]);

        $this->display('page.create.default.tpl');
    }

    public function user(): void
    {
        global $LNG, $USER;

        $auth = [];
        $auth[AUTH_USR] = $LNG['user_level_'.AUTH_USR];

        if ($USER['authlevel'] >= AUTH_OPS)
        {
            $auth[AUTH_OPS] = $LNG['user_level_'.AUTH_OPS];
        }

        if ($USER['authlevel'] >= AUTH_MOD)
        {
            $auth[AUTH_MOD] = $LNG['user_level_'.AUTH_MOD];
        }

        if ($USER['authlevel'] >= AUTH_ADM)
        {
            $auth[AUTH_ADM] = $LNG['user_level_'.AUTH_ADM];
        }

        $this->assign([
            'admin_auth' => $USER['authlevel'],
            'Selector'   => ['auth' => $auth, 'lang' => $LNG->getAllowedLangs(false)],
        ]);

        $this->display('page.create.user.tpl');
    }

    public function createUser(): void
    {
        global $LNG;
        $LNG->includeData(['PUBLIC']);

        $db = Database::get();

        $user_name = HTTP::_GP('name', '', UTF8_SUPPORT);
        $user_pass = HTTP::_GP('password', '');
        $user_pass_2 = HTTP::_GP('password2', '');
        $user_mail = HTTP::_GP('email', '');
        $user_mail_2 = HTTP::_GP('email2', '');
        $user_auth = HTTP::_GP('authlevel', 0);
        $galaxy = HTTP::_GP('galaxy', 0);
        $system = HTTP::_GP('system', 0);
        $planet = HTTP::_GP('planet', 0);
        $language = HTTP::_GP('lang', '');

        $sql = "SELECT (SELECT COUNT(*) FROM %%USERS%% WHERE universe = :universe AND username = :user_name) +
		(SELECT COUNT(*) FROM %%USERS_VALID%% WHERE universe = :universe AND username = :user_name) as count;";

        $exists_user = $db->selectSingle($sql, [
            ':universe'  => Universe::getEmulated(),
            ':user_name' => $user_name,
        ], 'count');

        $sql = "SELECT (SELECT COUNT(*) FROM %%USERS%% WHERE universe = :universe AND (email = :user_mail OR email_2 = :user_mail)) +
		(SELECT COUNT(*) FROM %%USERS_VALID%% WHERE universe = :universe AND email = :user_mail) as count;";

        $exists_mails = $db->selectSingle($sql, [
            ':universe'  => Universe::getEmulated(),
            ':user_mail' => $user_mail,
        ], 'count');

        $errors = "";

        $config = Config::get(Universe::getEmulated());

        if (!PlayerUtil::isMailValid($user_mail))
        {
            $errors .= $LNG['invalid_mail_adress'];
        }

        if (empty($user_name))
        {
            $errors .= $LNG['empty_user_field'];
        }

        if (strlen($user_pass) < 6)
        {
            $errors .= $LNG['password_lenght_error'];
        }

        if ($user_pass != $user_pass_2)
        {
            $errors .= $LNG['different_passwords'];
        }

        if ($user_mail != $user_mail_2)
        {
            $errors .= $LNG['different_mails'];
        }

        if (!PlayerUtil::isNameValid($user_name))
        {
            $errors .= $LNG['user_field_specialchar'];
        }

        if ($exists_user != 0)
        {
            $errors .= $LNG['user_already_exists'];
        }

        if ($exists_mails != 0)
        {
            $errors .= $LNG['mail_already_exists'];
        }

        if (!PlayerUtil::isPositionFree(Universe::getEmulated(), $galaxy, $system, $planet))
        {
            $errors .= $LNG['planet_already_exists'];
        }

        if ($galaxy > $config->max_galaxy
            || $system > $config->max_system
            || $planet > $config->max_planets)
        {
            $errors .= $LNG['po_complete_all2'];
        }

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=create&mode=user',
            'label' => $LNG['uvs_back'],
        ];

        if (!empty($errors))
        {
            $this->printMessage($errors, $redirect_button);
        }

        $language = array_key_exists($language, $LNG->getAllowedLangs(false)) ? $language : $config->lang;

        PlayerUtil::createPlayer(
            Universe::getEmulated(),
            $user_name,
            PlayerUtil::cryptPassword($user_pass),
            $user_mail,
            $language,
            $galaxy,
            $system,
            $planet,
            $LNG['fcm_planet'],
            $user_auth
        );

        $this->printMessage($LNG['new_user_success'], $redirect_button);
    }

    public function moon(): void
    {

        global $USER, $LNG;

        $this->assign([
            'admin_auth' => $USER['authlevel'],

        ]);

        $this->display('page.create.moon.tpl');

    }

    public function createMoon(): void
    {
        global $LNG;
        $planet_id = HTTP::_GP('add_moon', 0);
        $moon_name = HTTP::_GP('name', '', UTF8_SUPPORT);
        $diameter = HTTP::_GP('diameter', 0);

        $sql = "SELECT temp_max, temp_min, id_luna, galaxy, system, planet, 
        planet_type, destruyed, id_owner 
        FROM %%PLANETS%% 
        WHERE id = :planet_id AND universe = :universe 
        AND planet_type = '1' AND destruyed = '0';";

        $moon_planet = Database::get()->selectSingle($sql, [
            ':planet_id' => $planet_id,
            ':universe'  => Universe::getEmulated(),
        ]);

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=create&mode=moon',
            'label' => $LNG['uvs_back'],
        ];

        if (!$moon_planet)
        {
            $this->printMessage($LNG['mo_planet_doesnt_exist'], $redirect_button);
        }

        $moon_id = PlayerUtil::createMoon(
            Universe::getEmulated(),
            $moon_planet['galaxy'],
            $moon_planet['system'],
            $moon_planet['planet'],
            $moon_planet['id_owner'],
            20,
            (($_POST['diameter_check'] == 'on') ? null : $diameter),
            $moon_name
        );

        if ($moon_id !== false)
        {
            $this->printMessage($LNG['mo_moon_added'], $redirect_button);
        }
        else
        {
            $this->printMessage($LNG['mo_moon_unavaible'], $redirect_button);
        }

    }

    public function planet(): void
    {
        global $USER;

        $this->assign([
            'admin_auth' => $USER['authlevel'],
        ]);

        $this->display('page.create.planet.tpl');
    }

    public function createPlanet(): void
    {
        global $LNG;

        $id = HTTP::_GP('id', 0);
        $galaxy = HTTP::_GP('galaxy', 0);
        $system = HTTP::_GP('system', 0);
        $planet = HTTP::_GP('planet', 0);
        $name = HTTP::_GP('name', '', UTF8_SUPPORT);
        $field_max = HTTP::_GP('field_max', 0);

        $config = Config::get(Universe::getEmulated());

        if ($galaxy > $config->max_galaxy
            || $system > $config->max_system
            || $planet > $config->max_planets)
        {
            $this->printMessage($LNG['po_complete_all2']);
        }

        $sql = "SELECT id, authlevel FROM %%USERS%% WHERE id = :id AND universe = :universe;";

        $is_user = Database::get()->selectSingle($sql, [
            ':id'       => $id,
            ':universe' => Universe::getEmulated(),
        ]);

        if (!PlayerUtil::checkPosition(Universe::getEmulated(), $galaxy, $system, $planet)
            || !isset($is_user))
        {
            $this->printMessage($LNG['po_complete_all']);
        }

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=create&mode=planet',
            'label' => $LNG['uvs_back'],
        ];

        try
        {
            $planet_id = PlayerUtil::createPlanet(
                $galaxy,
                $system,
                $planet,
                Universe::getEmulated(),
                $id,
                null,
                false,
                $is_user['authlevel']
            );
        }
        catch (\Exception $e)
        {
            $error_msg = $e->getMessage();
            $this->printMessage($error_msg, $redirect_button);
        }

        if ($field_max > 0)
        {
            $sql = "UPDATE %%PLANETS%% SET field_max = :field_max WHERE id = :planet_id;";

            Database::get()->update($sql, [
                ':field_max' => $field_max,
                ':planet_id' => $planet_id,
            ]);

        }

        if (!empty($name))
        {
            $sql = "UPDATE %%PLANETS%% SET name = :name WHERE id = :planet_id;";

            Database::get()->update($sql, [
                ':name'      => $name,
                ':planet_id' => $planet_id,
            ]);
        }

        $this->printMessage($LNG['po_complete_succes']);
    }

}
