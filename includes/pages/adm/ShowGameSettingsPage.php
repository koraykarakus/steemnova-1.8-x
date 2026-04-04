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

    public function buildings(): void
    {
        $db = Database::get();
        $sql = "SELECT * FROM %%VARS%% WHERE element_id < 100;";
        $buildings_data = $db->select($sql);

        $buildings_list = [];
        foreach ($buildings_data as $c_building)
        {
            $buildings_list[$c_building['element_id']] = [
                'factor'  => $c_building['factor'],
                'cost901' => $c_building['cost901'],
                'cost902' => $c_building['cost902'],
                'cost903' => $c_building['cost903'],
                'cost911' => $c_building['cost911'],
                'cost921' => $c_building['cost921'],
            ];
        }

        $this->assign([
            'buildings_list' => $buildings_data,
        ]);

        $this->display('page.gameSettings.buildings.tpl');
    }

    public function updateBuildings()
    {
        $element_id = HTTP::_GP('element_id', 0);
        $factor = HTTP::_GP('factor', 0.00);
        $cost_metal = HTTP::_GP('cost_metal', 0);
        $cost_crystal = HTTP::_GP('cost_crystal', 0);
        $cost_deu = HTTP::_GP('cost_deu', 0);
        $cost_energy = HTTP::_GP('cost_energy', 0);
        $cost_dm = HTTP::_GP('cost_dm', 0);

        $sql = "UPDATE %%VARS%% SET 
        factor = :factor,
        cost901 = :cost901,
        cost902 = :cost902,
        cost903 = :cost903,
        cost911 = :cost911,
        cost921 = :cost921
        WHERE element_id = :element_id;";

        Database::get()->update($sql, [
            ':factor'     => $factor,
            ':cost901'    => $cost_metal,
            ':cost902'    => $cost_crystal,
            ':cost903'    => $cost_deu,
            ':cost911'    => $cost_energy,
            ':cost921'    => $cost_dm,
            ':element_id' => $element_id,
        ]);

        ClearCache();
        $this->redirectTo('admin.php?page=gameSettings&mode=buildings');
    }

    public function restoreBuildings(): void
    {
        $db = Database::get();
        $sql = "DELETE FROM %%VARS%% WHERE element_id < 100; 
        INSERT INTO %%VARS%% SELECT * FROM %%VARS_DEFAULT%% WHERE element_id < 100";
        $db->nativeQuery($sql);
        ClearCache();
        $this->redirectTo('admin.php?page=gameSettings&mode=buildings');
    }

    public function rapidFire(): void
    {
        global $RESLIST;
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
            'elements'        => array_merge($RESLIST['fleet'], $RESLIST['defense']),
        ]);

        $this->display('page.gameSettings.rapidFire.tpl');
    }

    public function removeRapidFire()
    {
        global $LNG;
        $element_id = HTTP::_GP('element_id', 0);
        $rapidfire_id = HTTP::_GP('rapidfire_id', 0);

        if ($element_id === 0
            || $rapidfire_id === 0)
        {
            $this->printMessage($LNG['rf_err_remove_1']);
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

    public function addRapidFire()
    {
        global $LNG;
        $element_id = HTTP::_GP('element_id', 0);
        $rapidfire_id = HTTP::_GP('rapidfire_id', 0);
        $shoots = HTTP::_GP('shoots', 0);

        if ($element_id === 0
            || $rapidfire_id === 0
            || $shoots === 0)
        {
            $this->printMessage($LNG['rf_err_add_1']);
        }

        $db = Database::get();

        $sql = "SELECT COUNT(*) as count FROM %%VARS_RAPIDFIRE%% 
        WHERE element_id = :element_id AND rapidfire_id = :rapidfire_id;";

        $count = $db->selectSingle($sql, [
            ':element_id'   => $element_id,
            ':rapidfire_id' => $rapidfire_id,
        ], 'count');

        if ($count > 0)
        {
            $this->printMessage($LNG['rf_err_add_2'], $this->createButtonBack());
        }

        $sql = "INSERT INTO %%VARS_RAPIDFIRE%% SET element_id = :element_id, 
        rapidfire_id = :rapidfire_id, shoots = :shoots;";

        $db->insert($sql, [
            ':element_id'   => $element_id,
            ':rapidfire_id' => $rapidfire_id,
            ':shoots'       => $shoots,
        ]);

        ClearCache();
        $this->printMessage($LNG['rf_suc_add'], $this->createButtonBack());
    }

    public function updateRapidFire()
    {
        global $LNG;

        $shoots = HTTP::_GP('shoots', [0]);
        $element_id = HTTP::_GP('element_id', 0);
        if (empty($shoots)
            || $element_id === 0)
        {
            $this->printMessage($LNG['rf_err_update_1']);
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
