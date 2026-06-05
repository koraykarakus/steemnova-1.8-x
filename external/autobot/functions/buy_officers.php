<?php
$config = file("config.txt");
$connection = mysqli_connect(trim($config[0]), trim($config[1]), trim($config[2]), trim($config[3]));
$officers = array(0 => 'rpg_geologist', 1 => 'rpg_admiral', 2 => 'rpg_engineer', 3 => 'rpg_technocrat', 4 => 'rpg_constructor', 5 => 'rpg_scientist', 6 => 'rpg_stocker', 7 => 'rpg_defender', 8 => 'rpg_bunker', 9 => 'rpg_espion', 10 => 'rpg_commander', 11 => 'rpg_destructor', 12 => 'rpg_general', 13 => 'rpg_raider', 14 => 'rpg_emperor', 15 => 'dm_resource');
$get_bots = mysqli_fetch_all(mysqli_query($connection, "SELECT id, darkmatter, rpg_geologist, rpg_admiral, rpg_engineer, rpg_technocrat, rpg_constructor, rpg_scientist, rpg_stocker, rpg_defender, rpg_bunker, rpg_espion, rpg_commander, rpg_destructor, rpg_general, rpg_raider, rpg_emperor, dm_resource FROM uni1_users WHERE email='bot'"));
for ($i = 0;$i <= count($get_bots) - 1;$i++) {
    for ($j = 0;$j <= count($officers) - 1;$j++) {
	$k = $j + 2;
	$officer_name = $officers[$j];
	$get_officer_data = mysqli_fetch_all(mysqli_query($connection, "SELECT maxLevel, cost921 FROM uni1_vars WHERE name='$officer_name'")) [0];
	if (($get_bots[$i][$k] < $get_officer_data[0]) && ($get_officer_data[1] <= $get_bots[$i][1])) {
	    // Buy officer
	    $id = $get_bots[$i][0];
	    $cost = $get_officer_data[1];
	    mysqli_query($connection, "UPDATE uni1_users SET $officer_name = $officer_name + 1 WHERE id = $id");
	    mysqli_query($connection, "UPDATE uni1_users SET darkmatter = darkmatter - $cost WHERE id = $id");
	    break;
	} else if ($get_bots[$i][$k] == $get_officer_data[0]) {
	    // Skip	
	} else if ($j == 15) {
	    // Buy resource power up
	    $time = time();
	    $k = 17;
	    $id = $get_bots[$i][0];
	    $cost = $get_officer_data[1];
	    if ($get_bots[$i][$k] < $time) {
		mysqli_query($connection, "UPDATE uni1_users SET dm_resource=($time+86400) WHERE id=$id");
	    } else {
		mysqli_query($connection, "UPDATE uni1_users SET dm_resource=dm_resource+86400 WHERE id=$id");
	    }
	    mysqli_query($connection, "UPDATE uni1_users SET darkmatter = darkmatter - $cost WHERE id = $id");
	} else {
	    echo 'Not enough Dark Matter - ' . $get_bots[$i][1] . ' / ' . $get_officer_data[1] . PHP_EOL;
	    break;
	}
    }
}
