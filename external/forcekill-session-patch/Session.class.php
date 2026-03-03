<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

class Session
{
    private static $obj = null;
    private static $iniSet = false;
    private $data = null;

    /**
     * Set PHP session settings
     *
     * @return bool
     */

    public static function init()
    {
        if (self::$ini_set === true)
        {
            return false;
        }
        self::$ini_set = true;

        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', 0);
        ini_set('session.auto_start', '0');
        ini_set('session.serialize_handler', 'php');
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '1000');
        ini_set('session.bug_compat_warn', '0');
        ini_set('session.bug_compat_42', '0');
        ini_set('session.cookie_httponly', true);
        ini_set('session.save_path', CACHE_PATH.'sessions');
        ini_set('upload_tmp_dir', CACHE_PATH.'sessions');

        $HTTP_ROOT = MODE === 'INSTALL' ? dirname(HTTP_ROOT) : HTTP_ROOT;

        session_set_cookie_params(SESSION_LIFETIME, $HTTP_ROOT, null, HTTPS, true);
        session_cache_limiter('nocache');
        session_name('2Moons');

        return true;
    }

    private static function getTempPath()
    {
        require_once 'includes/libs/wcf/BasicFileUtil.class.php';
        return BasicFileUtil::getTempFolder();
    }

    /**
     * Create an empty session
     *
     * @return String
     */

    public static function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED']))
        {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        }
        elseif (!empty($_SERVER['HTTP_FORWARDED_FOR']))
        {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        elseif (!empty($_SERVER['HTTP_FORWARDED']))
        {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        }
        elseif (!empty($_SERVER['REMOTE_ADDR']))
        {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $ipAddress = 'UNKNOWN';
        }
        return $ipAddress;
    }

    /**
     * Create an empty session
     *
     * @return Session
     */

    public static function create()
    {
        if (!self::existsActiveSession())
        {
            self::$obj = new self();
            register_shutdown_function([self::$obj, 'save']);
            @session_start();
            session_regenerate_id(true);
        }

        return self::$obj;
    }

    /**
     * Wake an active session
     *
     * @return Session
     */

    public static function load()
    {
        if (!self::existsActiveSession())
        {
            self::init();
            session_start();
            if (isset($_SESSION['obj']))
            {
                self::$obj = unserialize($_SESSION['obj']);
                register_shutdown_function([self::$obj, 'save']);
            }
            else
            {
                self::create();
            }
        }

        return self::$obj;
    }

    /**
     * Check if an active session exists
     *
     * @return bool
     */

    public static function existsActiveSession()
    {
        return isset(self::$obj);
    }

    public function __construct()
    {
        self::init();
    }

    public function __sleep()
    {
        return ['data'];
    }

    public function __wakeup()
    {

    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->data[$name]))
        {
            return $this->data[$name];
        }
        else
        {
            return null;
        }
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function save()
    {
        // do not save an empty session
        $session_id = session_id();
        if (empty($session_id))
        {
            return;
        }

        // sessions require an valid user.
        if (empty($this->data['userId']))
        {
            $this->delete();
        }

        $user_ip_address = self::getClientIp();

        $sql = 'REPLACE INTO %%SESSION%% SET
		sessionID	= :session_id,
		userID		= :user_id,
		lastonline	= :last_activity,
		userIP		= :user_address,
		created		= :created;';

        $db = Database::get();

        $sql_created = 'SELECT created FROM %%SESSION%% WHERE sessionID = :session_id 
        AND userID = :user_id;';

        $created = $db->selectSingle($sql_created, [
            ':session_id' => session_id(),
            ':user_id'    => $this->data['userId'],
        ], 'created');

        if (empty($created))
        {
            $created = time();
            $sql_is_exists = 'SELECT * FROM uni1_session_forcekill WHERE sessionID = :session_id;';
            $session_is_exists = $db->selectSingle($sql_is_exists, [
                ':session_id' => session_id(),
            ]);
            if (empty($session_is_exists))
            {
                $sql_forcekill = 'REPLACE INTO uni1_session_forcekill SET 
                sessionID	= :session_id, 
                created	= :created;';

                $db->replace($sql_forcekill, [
                    ':session_id' => session_id(),
                    ':created'    => $created,
                ]);
            }
        }

        $db->replace($sql, [
            ':session_id'    => session_id(),
            ':user_id'       => $this->data['userId'],
            ':last_activity' => time(),
            ':user_address'  => $user_ip_address,
            ':created'       => $created,
        ]);

        // Remove collided sessions
        $sql = 'DELETE FROM %%SESSION%% WHERE (userID = :user_id AND sessionID != :session_id);';
        $db->delete($sql, [
            ':user_id'    => $this->data['userId'],
            ':session_id' => session_id(),
        ]);

        // Remove old sessions
        if ($created + SESSION_LIFETIME < time())
        {
            $sql = 'DELETE FROM %%SESSION%% WHERE (userID = :user_id AND sessionID = :session_id);';
            $db->delete($sql, [
                ':user_id'    => $this->data['userId'],
                ':session_id' => session_id(),
            ]);
        }

        // Force kill sessions ID colliders
        $sql_force_kill = 'SELECT created FROM uni1_session_forcekill 
        WHERE (sessionID = :session_id AND created < :time);';

        $sql_force_kill_data = $db->selectSingle($sql_force_kill, [
            ':session_id' => session_id(),
            ':time'       => time() - SESSION_LIFETIME,
        ], 'created');

        if (!empty($sql_force_kill_data))
        {
            $sql = 'DELETE FROM %%SESSION%% WHERE (userID = :user_id AND sessionID = :session_id);';
            $db->delete($sql, [
                ':user_id'    => $this->data['userId'],
                ':session_id' => session_id(),
            ]);
        }

        $sql = 'UPDATE %%USERS%% SET
		onlinetime	= :last_activity,
		user_lastip = :user_address
		WHERE
		id = :user_id;';

        $db->update($sql, [
            ':user_address'  => $user_ip_address,
            ':last_activity' => TIMESTAMP,
            ':user_id'       => $this->data['userId'],
        ]);

        $this->data['last_activity'] = TIMESTAMP;
        $this->data['session_id'] = session_id();
        $this->data['user_ip_address'] = $user_ip_address;
        $this->data['request_path'] = $this->getRequestPath();

        $_SESSION['obj'] = serialize($this);

        @session_write_close();
    }

    public function delete()
    {
        $sql = 'DELETE FROM %%SESSION%% WHERE sessionID = :session_id;';
        $db = Database::get();

        $db->delete($sql, [
            ':session_id' => session_id(),
        ]);

        @session_destroy();
    }

    public function isValidSession()
    {
        // if($this->compareIpAddress($this->data['user_ip_address'], self::getClientIp(), COMPARE_IP_BLOCKS) === false)
        // {
        // return false;
        // }

        if (!isset($_SESSION["obj"]))
        {
            return false;
        }

        if ($this->data['last_activity'] < TIMESTAMP - SESSION_LIFETIME)
        {
            return false;
        }

        $sql = 'SELECT COUNT(*) as record FROM %%SESSION%% WHERE sessionID = :session_id;';
        $db = Database::get();

        $sessionCount = $db->selectSingle($sql, [
            ':session_id' => session_id(),
        ], 'record');

        if ($sessionCount == 0)
        {
            return false;
        }

        return true;
    }

    public function selectActivePlanet()
    {
        $httpData = HTTP::_GP('cp', 0);

        if (!empty($httpData))
        {
            $sql = 'SELECT id FROM %%PLANETS%% WHERE id = :planetId AND id_owner = :userId;';

            $db = Database::get();
            $planetId = $db->selectSingle($sql, [
                ':userId'   => $this->data['userId'],
                ':planetId' => $httpData,
            ], 'id');

            if (!empty($planetId))
            {
                $this->data['planetId'] = $planetId;
            }
        }
    }

    private function getRequestPath()
    {
        return HTTP_ROOT.(!empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '');
    }

    private function compareIpAddress($ip1, $ip2, $blockCount)
    {
        if (strpos($ip2, ':') !== false && strpos($ip1, ':') !== false)
        {
            $s_ip = $this->short_ipv6($ip1, $blockCount);
            $u_ip = $this->short_ipv6($ip2, $blockCount);
        }
        else
        {
            $s_ip = implode('.', array_slice(explode('.', $ip1), 0, $blockCount));
            $u_ip = implode('.', array_slice(explode('.', $ip2), 0, $blockCount));
        }

        return ($s_ip == $u_ip);
    }

    private function short_ipv6($ip, $length)
    {
        if ($length < 1)
        {
            return '';
        }

        $blocks = substr_count($ip, ':') + 1;
        if ($blocks < 9)
        {
            $ip = str_replace('::', ':' . str_repeat('0000:', 9 - $blocks), $ip);
        }
        if ($ip[0] == ':')
        {
            $ip = '0000' . $ip;
        }
        if ($length < 4)
        {
            $ip = implode(':', array_slice(explode(':', $ip), 0, 1 + $length));
        }

        return $ip;
    }
}
