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
class ShowDisclaimerPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $config = Config::get(Universe::getEmulated());

        $this->assign([
            'disclaimer_address' => $config->disclaimer_address,
            'disclaimer_phone'   => $config->disclaimer_phone,
            'disclaimer_mail'    => $config->disclaimer_mail,
            'disclaimer_notice'  => $config->disclaimer_notice,
        ]);

        $this->display('page.disclaimer.default.tpl');
    }

    public function saveSettings(): void
    {
        global $LNG;

        $config = Config::get(Universe::getEmulated());

        $config_before = [
            'disclaimer_address' => $config->disclaimer_address,
            'disclaimer_phone'   => $config->disclaimer_phone,
            'disclaimer_mail'    => $config->disclaimer_mail,
            'disclaimer_notice'  => $config->disclaimer_notice,
        ];

        $disclaimer_address = HTTP::_GP('disclaimer_address', '', true);
        $disclaimer_phone = HTTP::_GP('disclaimer_phone', '', true);
        $disclaimer_mail = HTTP::_GP('disclaimer_mail', '', true);
        $disclaimer_notice = HTTP::_GP('disclaimer_notice', '', true);

        $config_after = [
            'disclaimer_address' => $disclaimer_address,
            'disclaimer_phone'   => $disclaimer_phone,
            'disclaimer_mail'    => $disclaimer_mail,
            'disclaimer_notice'  => $disclaimer_notice,
        ];

        foreach ($config_after as $key => $value)
        {
            $config->$key = $value;
        }

        $config->save();

        $log = new Log(3);
        $log->target = 5;
        $log->old = $config_before;
        $log->new = $config_after;
        $log->save();

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=disclaimer&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirect_button);
    }

}
