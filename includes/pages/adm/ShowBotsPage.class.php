<?php

/**
 *
 */
class ShowBotsPage extends AbstractAdminPage
{

  function __construct()
  {
    parent::__construct();
  }

  function show(){

    $this->display('page.bots.default.tpl');
  }

  function create(){

    $this->display('page.bots.create.tpl');
  }

  function createSend(){

    $bots_number = HTTP::_GP('bots_number',0);

    echo "$bots_number";
  }

}










 ?>
