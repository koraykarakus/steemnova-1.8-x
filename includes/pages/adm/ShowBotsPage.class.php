<?php

/**
 *
 */
class ShowBotsPage extends AbstractAdminPage
{
    protected $all_names = [];

    protected $title = ['Marshal', 'Czar', 'Governor', 'Technocrat', 'Geologist', 'Commander',
        'Lord', 'Commodore', 'Chancellor', 'Emperor', 'Mogul', 'Sovereign', 'Proconsul',
        'Stadtholder', 'Renegade', 'Lieutenant', 'Admiral', 'Vice', 'Consul', 'Chief',
        'President', 'Procurator', 'Engineer', 'Constable', 'Bandit', 'Senator', 'Viceregent',
        'Captain', 'Director', 'Kualla', 'Padme'];

    protected $name = ['Yakini', 'Astra', 'Cosmos', 'Skat', 'Nemesis', 'Mars', 'Icarus', 'Helix', 'Cetus',
        'Hydra', 'Genesis', 'Octans', 'Remus', 'Sigma', 'Pavo', 'Navi', 'Rocket', 'Erdemas',
        'Europa', 'Ceres', 'Ferret', 'Cupid', 'Sirius', 'Antimatter', 'Centauri', 'Midas',
        'Quantum', 'Dorado', 'Deimos', 'Keid', 'Andromeda', 'Apollo',
        'Saturn', 'Spica', 'Majoris', 'Vega', 'Pathfinder', 'Kuma', 'Cosmo',
        'Gravity', 'Uranus', 'Ares', 'Janus', 'Transit', 'Uriel',
        'Scorpius', 'Omicron', 'Sol', 'Mimas', 'Euler', 'Castor',
        'Probe', 'Neso', 'Retina', 'Io', 'Leda', 'Ceti', 'Moon', 'Herschel',
        'Varilla', 'Tarvos', 'Pollux', 'Sunspot', 'Mariner', 'Zuben', 'Nestor',
        'Grus', 'Themis', 'Klio', 'Puck', 'Japetus', 'Scout', 'Solar', 'Kale', 'Lambda',
        'Leto', 'Amidala', 'Zagadra', 'Seti', 'Tycho', 'Sputnik', 'Navi', 'Starburst',
        'Comet', 'Sagan', 'Atik', 'Gamma', 'Dorado', 'Jones', 'Lepus', 'Taurus', 'Owl',
        'Zenith', 'Auriga', 'Jericho', 'Mimas', 'Voyager', 'Spirit', 'Explorer', 'Palma',
        'Gliese', 'Cassini', 'Pan', 'Neptune', 'Discory', 'Polaris', 'Barym', 'Spacewalk',
        'Ganimed', 'Forma', 'Pulsar', 'Holmes', 'Rhea', 'Deneb',
        'Nova', 'Omega', 'Zagadra', 'Hunter', 'Ranger', 'Zibal', 'Asteroid'];

    protected $title_count;
    protected $name_count;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $this->display('page.bots.default.tpl');
    }

    public function create(): void
    {
        $this->display('page.bots.create.tpl');
    }

    public function generateName(): string
    {
        $random_name = $this->title[rand(0, ($this->title_count - 1))] .
        ' ' .
        $this->name[rand(0, ($this->name_count - 1))];

        $i = 0;
        foreach ($this->all_names as $c_name)
        {
            if (strpos($c_name, $random_name) !== false)
            {
                $i++;
            }
        }

        if ($i > 0)
        {
            $random_name = $random_name . ' (' . ($i + 1) . ')';
        }

        return $random_name;
    }

    public function getAllNames(): void
    {
        $db = Database::get();

        $sql = "SELECT username FROM %%USERS%%";

        $user_name_array = $db->select($sql);

        foreach ($user_name_array as $c_name)
        {
            $this->all_names[] = $c_name['username'];
        }
    }

    public function createSend(): void
    {
        global $LNG;

        $config = Config::get(Universe::getEmulated());
        $db = Database::get();

        $target_galaxy = HTTP::_GP('target_galaxy', 1);
        $bots_number = HTTP::_GP('bots_number', 0);
        $bot_name_type = HTTP::_GP('bot_name_type', 0);
        $bots_dm = HTTP::_GP('bots_dm', 0);
        $bots_password = HTTP::_GP('bots_password', '', true);

        $planet_metal = HTTP::_GP('planet_metal', 0);
        $planet_crystal = HTTP::_GP('planet_crystal', 0);
        $planet_deuterium = HTTP::_GP('planet_deuterium', 0);
        $planet_field_max = HTTP::_GP('planet_field_max', 163);

        if ($bots_number == 0)
        {
            $this->printMessage('Enter bots number to be created !', $this->createButtonBack());
        }

        if (empty($bots_password))
        {
            $this->printMessage('Enter a password for bots !', $this->createButtonBack());
        }

        if ($target_galaxy > $config->max_galaxy
            || $target_galaxy < 1)
        {
            $this->printMessage('Wrong galaxy !', $this->createButtonBack());
        }

        if ($bot_name_type == 0)
        {
            $this->getAllNames();
            $this->name_count = count($this->name);
            $this->title_count = count($this->title);
        }

        $number_planets_max = $config->max_system * $config->max_planets;

        $sql = "SELECT COUNT(*) as planet_number 
        FROM %%PLANETS%% 
        WHERE galaxy = :target_galaxy AND universe = :universe;";

        $used_planet_slots = $db->selectSingle($sql, [
            ':target_galaxy' => $target_galaxy,
            ':universe'      => Universe::getEmulated(),
        ], 'planet_number');

        $number_planets_max -= $used_planet_slots;

        if ($bots_number > $number_planets_max)
        {
            $this->printMessage('universe do not have enough space for bots !');
        }

        $sql = "SELECT galaxy, system, planet 
        FROM %%PLANETS%% 
        WHERE universe = :universe AND galaxy = :target_galaxy";

        $current_planets = $db->select($sql, [
            ':universe'      => Universe::getEmulated(),
            ':target_galaxy' => $target_galaxy,
        ]);

        $coordinates_not_available = [];
        foreach ($current_planets as $c_planet)
        {
            $coordinates_not_available[] = $c_planet['galaxy'] .
            ":" .
            $c_planet['system'] .
            ":" .
            $c_planet['planet'];
        }

        $all_coordinates = [];

        for ($i = 1; $i <= $config->max_system; $i++)
        {

            for ($j = 1; $j <= $config->max_planets ; $j++)
            {
                $all_coordinates[] = $target_galaxy . ":" . $i . ":" . $j;
            }

        }

        $possible_coordinates = array_diff($all_coordinates, $coordinates_not_available);

        $bot_info = [];
        $universeCurrent = Universe::getEmulated();

        $sql = "SELECT COUNT(*) as count FROM %%USERS%% WHERE is_bot = 1;";
        $bots_num = $db->selectSingle($sql, [], 'count');

        //generate main planet coordinates for bots
        for ($i = 1; $i <= $bots_number; $i++)
        {

            $random_num = mt_rand(0, count($possible_coordinates) - 1);
            $coordinate = explode(':', $possible_coordinates[$random_num]);

            $bot_info[] = [
                'galaxy'     => $coordinate[0],
                'system'     => $coordinate[1],
                'planet'     => $coordinate[2],
                'username'   => ($bot_name_type == 1) ? 'bot ' . $i : $this->generateName(),
                'email'      => 'bot' . $i + $bots_num . '@2moons.de',
                'lang'       => 'tr',
                'darkmatter' => $bots_dm,
            ];

            unset($possible_coordinates[$random_num]);
            $possible_coordinates = array_values($possible_coordinates);
        }

        $sql_user = $save_sql_user = "INSERT INTO %%USERS%% (username, password, email, email_2, lang, universe, galaxy, system, planet, darkmatter,register_time,onlinetime, is_bot) VALUES ";

        $bots_password = PlayerUtil::cryptPassword($bots_password);

        $i = 0;
        foreach ($bot_info as $c_bot_info)
        {
            $sql_user .= "('" . $c_bot_info['username'] . "', '" . $bots_password . "', '"
            . $c_bot_info['email'] . "', '" . $c_bot_info['email'] . "', '" . $c_bot_info['lang'] . "', "
            . $universeCurrent . ", " . $c_bot_info['galaxy'] . ", " . $c_bot_info['system'] . ", "
            . $c_bot_info['planet'] . ", " . $c_bot_info['darkmatter'] . ", " . TIMESTAMP . ", " . TIMESTAMP . ", " . "1" . "), ";

            $i++;

            if ($i == 50)
            {
                $i = 0;

                $sql_user = substr($sql_user, 0, -2) . ";" ;
                $db->insert($sql_user);
                $sql_user = $save_sql_user;
            }

        }

        if ($sql_user != $save_sql_user)
        {
            $sql_user = substr($sql_user, 0, -2) . ";" ;
            $db->insert($sql_user);
            $sql_user = $save_sql_user;
        }

        $sql_planets = $save_sql_planets = "INSERT INTO %%PLANETS%% (name, universe, galaxy, system, planet, last_update, planet_type, image, field_max, temp_min, temp_max, metal, crystal, deuterium, is_bot) VALUES ";

        $planetData = [];
        require 'includes/PlanetData.php';

        // ??
        $diameter = (int) floor(1000 * sqrt($planet_field_max));

        $i = 0;
        foreach ($bot_info as $c_bot_info)
        {
            $data_index = (int) ceil($c_bot_info['planet'] / ($config->max_planets / count($planetData)));
            $planet_temp_max = $planetData[$data_index]['temp'];
            $planet_temp_min = $planet_temp_max - 40;

            $image_names = array_keys($planetData[$data_index]['image']);
            $image_name_type = $image_names[array_rand($image_names)];
            $image_name = $image_name_type;
            $image_name .= 'planet';
            $image_name .= $planetData[$data_index]['image'][$image_name_type] < 10 ? '0' : '';
            $image_name .= $planetData[$data_index]['image'][$image_name_type];

            $sql_planets .= "('" . $LNG['fcm_mainplanet'] . "', " . $universeCurrent . ", " . $c_bot_info['galaxy'] . ", "
            . $c_bot_info['system'] . ", " . $c_bot_info['planet'] . ", " . TIMESTAMP . ", " . "1" . ", '"
            . $image_name . "', " . $planet_field_max . ", " . $planet_temp_min . ", " . $planet_temp_max . ", "
            . $planet_metal . ", " . $planet_crystal . ", " . $planet_deuterium . ", " . "1" . "), ";

            $i++;

            if ($i == 50)
            {
                $i = 0;

                $sql_planets = substr($sql_planets, 0, -2) . ";" ;
                $db->insert($sql_planets);
                $sql_planets = $save_sql_planets;
            }

        }

        if ($sql_planets != $save_sql_planets)
        {
            $sql_planets = substr($sql_planets, 0, -2) . ";" ;
            $db->insert($sql_planets);
            $sql_planets = $save_sql_planets;
        }

        $sql = "SELECT id, galaxy, system, planet 
        FROM %%USERS%% 
        WHERE is_bot = 1 AND id_planet = 0 AND universe = :universe 
        ORDER BY id ASC;";

        $new_bots = $db->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        $sql = "SELECT id, galaxy, system, planet 
        FROM %%PLANETS%% WHERE is_bot = 1 AND id_owner IS NULL 
        AND universe = :universe 
        ORDER BY id ASC;";

        $new_bot_planets = $db->select($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        //refresh bot users

        $sql_refresh_bot_users = $save_sql_refresh_bot_users = "INSERT INTO %%USERS%% (id,universe,id_planet) VALUES ";

        $i = 0;
        foreach ($new_bots as $currentNewBot)
        {

            foreach ($new_bot_planets as $currentNewBotPlanet)
            {

                if ($currentNewBot['galaxy'] == $currentNewBotPlanet['galaxy']
                    && $currentNewBot['system'] == $currentNewBotPlanet['system']
                    && $currentNewBot['planet'] == $currentNewBotPlanet['planet']
                ) {
                    $i++;
                    $sql_refresh_bot_users .= "(" . $currentNewBot['id'] . ", " . $universeCurrent . ", " . $currentNewBotPlanet['id'] . "), ";

                    if ($i == 50)
                    {
                        $sql_refresh_bot_users = substr($sql_refresh_bot_users, 0, -2) . " ON DUPLICATE KEY UPDATE
            id = VALUES(id),
            universe = VALUES(universe),
            id_planet = VALUES(id_planet);";

                        $i = 0;

                        $db->insert($sql_refresh_bot_users);

                        $sql_refresh_bot_users = $save_sql_refresh_bot_users;

                    }

                    break;
                }

            }

        }

        if ($sql_refresh_bot_users != $save_sql_refresh_bot_users)
        {
            $sql_refresh_bot_users = substr($sql_refresh_bot_users, 0, -2) . " ON DUPLICATE KEY UPDATE
      id = VALUES(id),
      universe = VALUES(universe),
      id_planet = VALUES(id_planet);";
            $db->insert($sql_refresh_bot_users);
        }

        //refresh planets

        $sql_refresh_bot_planets = $save_sql_refresh_bot_planets = "INSERT INTO %%PLANETS%% (id,universe,id_owner) VALUES ";

        $i = 0;
        foreach ($new_bots as $c_new_bot)
        {

            foreach ($new_bot_planets as $c_new_bot_planet)
            {

                if ($c_new_bot['galaxy'] == $c_new_bot_planet['galaxy']
                    && $c_new_bot['system'] == $c_new_bot_planet['system']
                    && $c_new_bot['planet'] == $c_new_bot_planet['planet']
                ) {
                    $i++;
                    $sql_refresh_bot_planets .= "(" . $c_new_bot_planet['id'] . ", " . $universeCurrent . ", " . $c_new_bot['id']  . "), ";

                    if ($i == 50)
                    {
                        $sql_refresh_bot_planets = substr($sql_refresh_bot_planets, 0, -2) . " ON DUPLICATE KEY UPDATE
                        id = VALUES(id),
                        universe = VALUES(universe),
                        id_owner = VALUES(id_owner);";

                        $i = 0;

                        $db->insert($sql_refresh_bot_planets);

                        $sql_refresh_bot_planets = $save_sql_refresh_bot_planets;
                    }

                    break;
                }

            }

        }

        if ($sql_refresh_bot_planets != $save_sql_refresh_bot_planets)
        {
            $sql_refresh_bot_planets = substr($sql_refresh_bot_planets, 0, -2) . " ON DUPLICATE KEY UPDATE
            id = VALUES(id),
            universe = VALUES(universe),
            id_owner = VALUES(id_owner);";
            $db->insert($sql_refresh_bot_planets);
        }

        $this->printMessage('Bots created successfully');

    }

}
