<?php

/**
 *
 */
class ShowPlanetFieldsPage extends AbstractAdminPage
{

  function __construct()
  {
    parent::__construct();
  }

  function show(){
    global $config;

    $this->assign(array(
      'planet_1_field_min' => $config->planet_1_field_min,
      'planet_1_field_max' => $config->planet_1_field_max,
      'planet_2_field_min' => $config->planet_2_field_min,
      'planet_2_field_max' => $config->planet_2_field_max,
      'planet_3_field_min' => $config->planet_3_field_min,
      'planet_3_field_max' => $config->planet_3_field_max,
      'planet_4_field_min' => $config->planet_4_field_min,
      'planet_4_field_max' => $config->planet_4_field_max,
      'planet_5_field_min' => $config->planet_5_field_min,
      'planet_5_field_max' => $config->planet_5_field_max,
      'planet_6_field_min' => $config->planet_6_field_min,
      'planet_6_field_max' => $config->planet_6_field_max,
      'planet_7_field_min' => $config->planet_7_field_min,
      'planet_7_field_max' => $config->planet_7_field_max,
      'planet_8_field_min' => $config->planet_8_field_min,
      'planet_8_field_max' => $config->planet_8_field_max,
      'planet_9_field_min' => $config->planet_9_field_min,
      'planet_9_field_max' => $config->planet_9_field_max,
      'planet_10_field_min' => $config->planet_10_field_min,
      'planet_10_field_max' => $config->planet_10_field_max,
      'planet_11_field_min' => $config->planet_11_field_min,
      'planet_11_field_max' => $config->planet_11_field_max,
      'planet_12_field_min' => $config->planet_12_field_min,
      'planet_12_field_max' => $config->planet_12_field_max,
      'planet_13_field_min' => $config->planet_13_field_min,
      'planet_13_field_max' => $config->planet_13_field_max,
      'planet_14_field_min' => $config->planet_14_field_min,
      'planet_14_field_max' => $config->planet_14_field_max,
      'planet_15_field_min' => $config->planet_15_field_min,
      'planet_15_field_max' => $config->planet_15_field_max,
    ));

    $this->display('page.planetfields.default.tpl');
  }

  function send(){
    global $LNG, $config;

    $config_before = array(
      'planet_1_field_min' => $config->planet_1_field_min,
      'planet_1_field_max' => $config->planet_1_field_max,
      'planet_2_field_min' => $config->planet_2_field_min,
      'planet_2_field_max' => $config->planet_2_field_max,
      'planet_3_field_min' => $config->planet_3_field_min,
      'planet_3_field_max' => $config->planet_3_field_max,
      'planet_4_field_min' => $config->planet_4_field_min,
      'planet_4_field_max' => $config->planet_4_field_max,
      'planet_5_field_min' => $config->planet_5_field_min,
      'planet_5_field_max' => $config->planet_5_field_max,
      'planet_6_field_min' => $config->planet_6_field_min,
      'planet_6_field_max' => $config->planet_6_field_max,
      'planet_7_field_min' => $config->planet_7_field_min,
      'planet_7_field_max' => $config->planet_7_field_max,
      'planet_8_field_min' => $config->planet_8_field_min,
      'planet_8_field_max' => $config->planet_8_field_max,
      'planet_9_field_min' => $config->planet_9_field_min,
      'planet_9_field_max' => $config->planet_9_field_max,
      'planet_10_field_min' => $config->planet_10_field_min,
      'planet_10_field_max' => $config->planet_10_field_max,
      'planet_11_field_min' => $config->planet_11_field_min,
      'planet_11_field_max' => $config->planet_11_field_max,
      'planet_12_field_min' => $config->planet_12_field_min,
      'planet_12_field_max' => $config->planet_12_field_max,
      'planet_13_field_min' => $config->planet_13_field_min,
      'planet_13_field_max' => $config->planet_13_field_max,
      'planet_14_field_min' => $config->planet_14_field_min,
      'planet_14_field_max' => $config->planet_14_field_max,
      'planet_15_field_min' => $config->planet_15_field_min,
      'planet_15_field_max' => $config->planet_15_field_max,
    );

    $planet_1_field_min = HTTP::_GP('planet_1_field_min', 95);
    $planet_1_field_max = HTTP::_GP('planet_1_field_max', 108);
    $planet_2_field_min = HTTP::_GP('planet_2_field_min', 97);
    $planet_2_field_max = HTTP::_GP('planet_2_field_max', 110);
    $planet_3_field_min = HTTP::_GP('planet_3_field_min', 98);
    $planet_3_field_max = HTTP::_GP('planet_3_field_max', 137);
    $planet_4_field_min = HTTP::_GP('planet_4_field_min', 123);
    $planet_4_field_max = HTTP::_GP('planet_4_field_max', 203);
    $planet_5_field_min = HTTP::_GP('planet_5_field_min', 148);
    $planet_5_field_max = HTTP::_GP('planet_5_field_max', 210);
    $planet_6_field_min = HTTP::_GP('planet_6_field_min', 148);
    $planet_6_field_max = HTTP::_GP('planet_6_field_max', 226);
    $planet_7_field_min = HTTP::_GP('planet_7_field_min', 141);
    $planet_7_field_max = HTTP::_GP('planet_7_field_max', 273);
    $planet_8_field_min = HTTP::_GP('planet_8_field_min', 169);
    $planet_8_field_max = HTTP::_GP('planet_8_field_max', 246);
    $planet_9_field_min = HTTP::_GP('planet_9_field_min', 161);
    $planet_9_field_max = HTTP::_GP('planet_9_field_max', 238);
    $planet_10_field_min = HTTP::_GP('planet_10_field_min', 154);
    $planet_10_field_max = HTTP::_GP('planet_10_field_max', 224);
    $planet_11_field_min = HTTP::_GP('planet_11_field_min', 148);
    $planet_11_field_max = HTTP::_GP('planet_11_field_max', 204);
    $planet_12_field_min = HTTP::_GP('planet_12_field_min', 136);
    $planet_12_field_max = HTTP::_GP('planet_12_field_max', 171);
    $planet_13_field_min = HTTP::_GP('planet_13_field_min', 109);
    $planet_13_field_max = HTTP::_GP('planet_13_field_max', 121);
    $planet_14_field_min = HTTP::_GP('planet_14_field_min', 81);
    $planet_14_field_max = HTTP::_GP('planet_14_field_max', 93);
    $planet_15_field_min = HTTP::_GP('planet_15_field_min', 65);
    $planet_15_field_max = HTTP::_GP('planet_15_field_max', 74);

    $config_after = array(
      'planet_1_field_min' => $planet_1_field_min,
      'planet_1_field_max' => $planet_1_field_max,
      'planet_2_field_min' => $planet_2_field_min,
      'planet_2_field_max' => $planet_2_field_max,
      'planet_3_field_min' => $planet_3_field_min,
      'planet_3_field_max' => $planet_3_field_max,
      'planet_4_field_min' => $planet_4_field_min,
      'planet_4_field_max' => $planet_4_field_max,
      'planet_5_field_min' => $planet_5_field_min,
      'planet_5_field_max' => $planet_5_field_max,
      'planet_6_field_min' => $planet_6_field_min,
      'planet_6_field_max' => $planet_6_field_max,
      'planet_7_field_min' => $planet_7_field_min,
      'planet_7_field_max' => $planet_7_field_max,
      'planet_8_field_min' => $planet_8_field_min,
      'planet_8_field_max' => $planet_8_field_max,
      'planet_9_field_min' => $planet_9_field_min,
      'planet_9_field_max' => $planet_9_field_max,
      'planet_10_field_min' => $planet_10_field_min,
      'planet_10_field_max' => $planet_10_field_max,
      'planet_11_field_min' => $planet_11_field_min,
      'planet_11_field_max' => $planet_11_field_max,
      'planet_12_field_min' => $planet_12_field_min,
      'planet_12_field_max' => $planet_12_field_max,
      'planet_13_field_min' => $planet_13_field_min,
      'planet_13_field_max' => $planet_13_field_max,
      'planet_14_field_min' => $planet_14_field_min,
      'planet_14_field_max' => $planet_14_field_max,
      'planet_15_field_min' => $planet_15_field_min,
      'planet_15_field_max' => $planet_15_field_max,
    );

    foreach($config_after as $key => $value)
    {
      $config->$key	= $value;
    }
    $config->save();

    $LOG = new Log(3);
    $LOG->target = 1;
    $LOG->old = $config_before;
    $LOG->new = $config_after;
    $LOG->save();


    $redirectButton = array();
    $redirectButton[] = array(
      'url' => 'admin.php?page=planetFields&mode=show',
      'label' => $LNG['uvs_back']
    );

    $this->printMessage($LNG['settings_successful'],$redirectButton);

  }

  function default(){

    global $config, $LNG;

    $planet_1_field_min =  95;
    $planet_1_field_max = 108;
    $planet_2_field_min = 97;
    $planet_2_field_max = 110;
    $planet_3_field_min = 98;
    $planet_3_field_max = 137;
    $planet_4_field_min = 123;
    $planet_4_field_max = 203;
    $planet_5_field_min = 148;
    $planet_5_field_max = 210;
    $planet_6_field_min = 148;
    $planet_6_field_max = 226;
    $planet_7_field_min = 141;
    $planet_7_field_max = 273;
    $planet_8_field_min = 169;
    $planet_8_field_max = 246;
    $planet_9_field_min = 161;
    $planet_9_field_max = 238;
    $planet_10_field_min = 154;
    $planet_10_field_max = 224;
    $planet_11_field_min = 148;
    $planet_11_field_max = 204;
    $planet_12_field_min = 136;
    $planet_12_field_max = 171;
    $planet_13_field_min = 109;
    $planet_13_field_max = 121;
    $planet_14_field_min = 81;
    $planet_14_field_max = 93;
    $planet_15_field_min = 65;
    $planet_15_field_max = 74;

    $config_after = array(
      'planet_1_field_min' => $planet_1_field_min,
      'planet_1_field_max' => $planet_1_field_max,
      'planet_2_field_min' => $planet_2_field_min,
      'planet_2_field_max' => $planet_2_field_max,
      'planet_3_field_min' => $planet_3_field_min,
      'planet_3_field_max' => $planet_3_field_max,
      'planet_4_field_min' => $planet_4_field_min,
      'planet_4_field_max' => $planet_4_field_max,
      'planet_5_field_min' => $planet_5_field_min,
      'planet_5_field_max' => $planet_5_field_max,
      'planet_6_field_min' => $planet_6_field_min,
      'planet_6_field_max' => $planet_6_field_max,
      'planet_7_field_min' => $planet_7_field_min,
      'planet_7_field_max' => $planet_7_field_max,
      'planet_8_field_min' => $planet_8_field_min,
      'planet_8_field_max' => $planet_8_field_max,
      'planet_9_field_min' => $planet_9_field_min,
      'planet_9_field_max' => $planet_9_field_max,
      'planet_10_field_min' => $planet_10_field_min,
      'planet_10_field_max' => $planet_10_field_max,
      'planet_11_field_min' => $planet_11_field_min,
      'planet_11_field_max' => $planet_11_field_max,
      'planet_12_field_min' => $planet_12_field_min,
      'planet_12_field_max' => $planet_12_field_max,
      'planet_13_field_min' => $planet_13_field_min,
      'planet_13_field_max' => $planet_13_field_max,
      'planet_14_field_min' => $planet_14_field_min,
      'planet_14_field_max' => $planet_14_field_max,
      'planet_15_field_min' => $planet_15_field_min,
      'planet_15_field_max' => $planet_15_field_max,
    );

    foreach($config_after as $key => $value)
    {
      $config->$key	= $value;
    }
    $config->save();

    $redirectButton = array();
    $redirectButton[] = array(
      'url' => 'admin.php?page=planetFields&mode=show',
      'label' => $LNG['uvs_back']
    );

    $this->printMessage($LNG['settings_successful'],$redirectButton);


  }


}










 ?>
