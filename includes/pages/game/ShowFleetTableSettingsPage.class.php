<?php

/**
 *
 */
class ShowFleetTableSettingsPage extends AbstractGamePage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function changeVisibility(): void
    {
        global $USER;

        $result = 0;

        ($USER['show_fleets_active']) ? $result = 0 : $result = 1;

        $sql = "UPDATE %%USERS%% SET `show_fleets_active` = " . $result . " WHERE id = :userId;";

        Database::get()->update($sql, [
            ':userId' => $USER['id'],
        ]);

        $this->sendJSON($result);
    }

}
