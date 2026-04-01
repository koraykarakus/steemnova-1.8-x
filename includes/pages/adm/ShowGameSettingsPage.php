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
        $rapid_fire_data = $db->select($sql, []);

        $rapid_fire_list = [];
        foreach ($rapid_fire_data as $c_data)
        {
            $rapid_fire_list[$c_data['element_id']][] = [
                'id'     => $c_data['rapidfire_id'],
                'shoots' => $c_data['shoots'],
            ];
        }

        $this->assign([
            'rapid_fire_list' => $rapid_fire_list,
        ]);

        $this->display('page.gameSettings.rapidFire.tpl');
    }

    public function removeRapidFire()
    {
        $element_id = HTTP::_GP('element_id', 0);
        $rapidfire_id = HTTP::_GP('rapidfire_id', 0);

        if ($element_id === 0
            || $rapidfire_id === 0)
        {
            $this->printMessage('wrong input !');
        }

        $sql = "DELETE FROM %%VARS_RAPIDFIRE%% 
        WHERE element_id = :element_id 
        AND rapidfire_id = :rapidfire_id";

        Database::get()->delete($sql, [
            ':element_id'   => $element_id,
            ':rapidfire_id' => $rapidfire_id,
        ]);

        $this->redirectTo('admin.php?page=gameSettings&mode=rapidFire');
    }
}
