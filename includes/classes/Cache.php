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
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 * @link https://github.com/jkroepke/2Moons
 */

require 'includes/classes/cache/builder/BuildCache.interface.php';
require 'includes/classes/cache/resource/CacheFile.class.php';

class Cache
{
    private $cache_resource = null;
    private $cache_builder = [];
    private $cache_obj = [];

    private static $obj = null;

    public static function get()
    {
        if (is_null(self::$obj))
        {
            self::$obj = new self();
        }

        return self::$obj;
    }

    private function __construct()
    {
        $this->cache_resource = new CacheFile();
    }

    public function add($key, $class_name)
    {
        $this->cache_builder[$key] = $class_name;
    }

    public function getData($key, $rebuild = true)
    {
        if (!isset($this->cache_obj[$key]) && !$this->load($key))
        {
            if ($rebuild)
            {
                $this->buildCache($key);
            }
            else
            {
                return [];
            }
        }
        return $this->cache_obj[$key];
    }

    public function flush($key)
    {
        if (!isset($this->cache_obj[$key]) && !$this->load($key))
        {
            $this->buildCache($key);
        }

        $this->cache_resource->flush($key);
        return $this->buildCache($key);
    }

    public function load($key)
    {
        $cache_data = $this->cache_resource->open($key);

        if ($cache_data === false)
        {
            return false;
        }

        $cache_data = unserialize($cache_data);
        if ($cache_data === false)
        {
            return false;
        }

        $this->cache_obj[$key] = $cache_data;
        return true;
    }

    public function buildCache($key)
    {
        $class_name = $this->cache_builder[$key];

        $path = 'includes/classes/cache/builder/'.$class_name.'.class.php';
        require_once $path;

        /** @var BuildCache $cache_builder  */
        $cache_builder = new $class_name();
        $cache_data = $cache_builder->buildCache();
        $cache_data = (array) $cache_data;
        $this->cache_obj[$key] = $cache_data;
        $cache_data = serialize($cache_data);
        $this->cache_resource->store($key, $cache_data);
        return true;
    }
}
