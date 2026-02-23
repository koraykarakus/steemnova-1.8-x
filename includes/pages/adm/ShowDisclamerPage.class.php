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
class ShowDisclamerPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;

        $config = Config::get(Universe::getEmulated());

        $this->assign([
            'disclaimerAddress' => $config->disclamerAddress,
            'disclaimerPhone'   => $config->disclamerPhone,
            'disclaimerMail'    => $config->disclamerMail,
            'disclaimerNotice'  => $config->disclamerNotice,
        ]);

        $this->display('page.disclamer.default.tpl');

    }

    public function saveSettings(): void
    {

        global $LNG;

        $config = Config::get(Universe::getEmulated());

        $config_before = [
            'disclamerAddress' => $config->disclamerAddress,
            'disclamerPhone'   => $config->disclamerPhone,
            'disclamerMail'    => $config->disclamerMail,
            'disclamerNotice'  => $config->disclamerNotice,
        ];

        $disclaimerAddress = HTTP::_GP('disclaimerAddress', '', true);
        $disclaimerPhone = HTTP::_GP('disclaimerPhone', '', true);
        $disclaimerMail = HTTP::_GP('disclaimerMail', '', true);
        $disclaimerNotice = HTTP::_GP('disclaimerNotice', '', true);

        $config_after = [
            'disclamerAddress' => $disclaimerAddress,
            'disclamerPhone'   => $disclaimerPhone,
            'disclamerMail'    => $disclaimerMail,
            'disclamerNotice'  => $disclaimerNotice,
        ];

        foreach ($config_after as $key => $value)
        {
            $config->$key = $value;
        }
        $config->save();

        $LOG = new Log(3);
        $LOG->target = 5;
        $LOG->old = $config_before;
        $LOG->new = $config_after;
        $LOG->save();

        $redirectButton = [];
        $redirectButton[] = [
            'url'   => 'admin.php?page=disclamer&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirectButton);

    }

}
