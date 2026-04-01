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

        ClearCache();
        $this->redirectTo('admin.php?page=gameSettings&mode=rapidFire');
    }

    public function updateRapidFire()
    {
        $shoots = HTTP::_GP('shoots', [0]);
        $element_id = HTTP::_GP('element_id', 0);
        if (empty($shoots)
            || $element_id === 0)
        {
            $this->printMessage('inputs are empty or wrong element_id');
        }

        $db = Database::get();
        foreach ($shoots as $id => $val)
        {
            $sql = "UPDATE %%VARS_RAPIDFIRE%% SET shoots = :shoots 
            WHERE element_id = :element_id AND rapidfire_id = :rapidfire_id;";
            $db->update($sql, [
                ':shoots'       => $val,
                ':element_id'   => $element_id,
                ':rapidfire_id' => $id,
            ]);
        }

        ClearCache();
        $this->redirectTo('admin.php?page=gameSettings&mode=rapidFire');
    }

    public function restoreRapidFire()
    {
        $sql = "TRUNCATE TABLE %%VARS_RAPIDFIRE%%;
        INSERT INTO %%VARS_RAPIDFIRE%% 
        SELECT * FROM %%VARS_RAPIDFIRE_DEFAULT%%;";
        Database::get()->nativeQuery($sql);
        ClearCache();
        $this->redirectTo('admin.php?page=gameSettings&mode=rapidFire');
    }
}
