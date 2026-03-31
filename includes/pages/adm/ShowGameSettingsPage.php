<?php

/**
 *
 */
class ShowGameSettingsPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $this->display('page.gameSettings.default.tpl');
    }

    public function rapidFire(): void
    {
        global $LNG;
        $db = Database::get();
        $sql = "SELECT * FROM %%VARS_RAPIDFIRE%%;";
        $rapid_fire_data = $db->select($sql,[]);

        $rapid_fire_list = [];
        foreach ($rapid_fire_data as $c_data) {
            $rapid_fire_list[$c_data['element_id']][] = array(
                'id' => $c_data['rapidfire_id'],
                'shoots' => $c_data['shoots']
            );
        }

        $this->assign([
            'rapid_fire_list' => $rapid_fire_list,
        ]);

        $this->display('page.gameSettings.rapidFire.tpl');
    }
}
