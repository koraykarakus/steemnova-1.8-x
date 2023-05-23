<?php


/**
 *
 */
class ShowFleetTableSettingsPage extends AbstractGamePage
{

  function __construct()
  {
    parent::__construct();
  }


  function changeVisibility(){

    global $USER;

    $result = 0;

    ($USER['show_fleets_active']) ? $result = 0 : $result = 1;

    $sql = "UPDATE %%USERS%% SET `show_fleets_active` = " . $result . " WHERE id = :userId;";

    Database::get()->update($sql,array(
      ':userId' => $USER['id']
    ));

    $this->sendJSON($result);

  }

}
