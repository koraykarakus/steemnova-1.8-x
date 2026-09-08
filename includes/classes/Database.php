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

class Database
{
    protected ?PDO $dbHandle = null;
    protected array $dbTableNames = [];
    protected bool|string $lastInsertId = false;
    protected bool|int $rowCount = false;
    protected int $queryCounter = 0;
    protected static Database $instance;

    public static function get(): self
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getDbTableNames(): array
    {
        return $this->dbTableNames;
    }

    public function getMySQLServerVersion(): string
    {
        $res = $this->dbHandle->getAttribute(PDO::ATTR_SERVER_VERSION);
        if ($res === null)
        {
            throw new Exception("Failed getAttribute : getMySQLServerVersion");
        }
        return $res;
    }

    public function getMySQLClientVersion(): string
    {
        $res = $this->dbHandle->getAttribute(PDO::ATTR_CLIENT_VERSION);
        if ($res === null)
        {
            throw new Exception("Failed getAttribute : getMySQLClientVersion");
        }
        return $res;
    }

    private function __clone()
    {

    }

    protected function __construct()
    {
        $database = [];
        require 'includes/config.php';
        //Connect
        $db = new PDO("mysql:host=".$database['host'].";port=".$database['port'].";dbname=".$database['databasename'], $database['user'], $database['userpw'], [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8mb4, NAMES utf8mb4, sql_mode = 'STRICT_ALL_TABLES'",
        ]);
        //error behaviour
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        // $db->query("set character set utf8");
        // $db->query("set names utf8");
        // $db->query("SET sql_mode = 'STRICT_ALL_TABLES'");
        $this->dbHandle = $db;

        $dbTableNames = [];

        include 'includes/dbtables.php';

        foreach ($dbTableNames as $key => $name)
        {
            $this->dbTableNames['keys'][] = '%%'.$key.'%%';
            $this->dbTableNames['names'][] = $name;
        }
    }

    public function disconnect(): void
    {
        $this->dbHandle = null;
    }

    public function getHandle(): PDO|null
    {
        return $this->dbHandle;
    }

    public function lastInsertId()
    {
        return $this->lastInsertId;
    }

    public function rowCount()
    {
        return $this->rowCount;
    }

    protected function _query(string $qry, array $params, string $type): PDOStatement|bool
    {
        if (in_array($type, ["insert", "select", "update", "delete", "replace"]) === false)
        {
            throw new Exception("Unsupported Query Type");
        }

        $this->lastInsertId = false;
        $this->rowCount = false;

        $qry = str_replace($this->dbTableNames['keys'], $this->dbTableNames['names'], $qry);

        /** @var PDOStatement $stmt */
        $stmt = $this->dbHandle->prepare($qry);

        if (isset($params[':limit']) || isset($params[':offset']))
        {
            foreach ($params as $param => $value)
            {
                if ($param == ':limit' || $param == ':offset')
                {
                    $stmt->bindValue($param, (int) $value, PDO::PARAM_INT);
                }
                else
                {
                    $stmt->bindValue($param, (int) $value, PDO::PARAM_STR);
                }
            }
        }

        try
        {
            $success = (count($params) !== 0 && !isset($params[':limit']) && !isset($params[':offset'])) ? $stmt->execute($params) : $stmt->execute();
        }
        catch (PDOException $e)
        {
            throw new Exception($e->getMessage()."<br>\r\n<br>\r\nQuery-Code:".str_replace(array_keys($params), array_values($params), $qry));
        }

        $this->queryCounter++;

        if (!$success)
        {
            return false;
        }

        if ($type === "insert")
        {
            $this->lastInsertId = $this->dbHandle->lastInsertId();
        }
        $this->rowCount = $stmt->rowCount();

        return ($type === "select") ? $stmt : true;
    }

    protected function getQueryType(string $qry): string
    {
        if (!preg_match('!^(\S+)!', $qry, $match))
        {
            throw new Exception("Invalid query $qry!");
        }

        if (!isset($match[1]))
        {
            throw new Exception("Invalid query $qry!");
        }

        return strtolower($match[1]);
    }

    public function delete(string $qry, array $params = []): PDOStatement|bool
    {
        if (($type = $this->getQueryType($qry)) !== "delete")
        {
            throw new Exception("Incorrect Delete Query");
        }

        return $this->_query($qry, $params, $type);
    }

    public function replace(string $qry, array $params = []): PDOStatement|bool
    {
        if (($type = $this->getQueryType($qry)) !== "replace")
        {
            throw new Exception("Incorrect Replace Query");
        }

        return $this->_query($qry, $params, $type);
    }

    public function update(string $qry, array $params = []): PDOStatement|bool
    {
        if (($type = $this->getQueryType($qry)) !== "update")
        {
            throw new Exception("Incorrect Update Query");
        }

        return $this->_query($qry, $params, $type);
    }

    public function insert(string $qry, array $params = []): PDOStatement|bool
    {
        if (($type = $this->getQueryType($qry)) !== "insert")
        {
            throw new Exception("Incorrect Insert Query");
        }

        return $this->_query($qry, $params, $type);
    }

    public function select(string $qry, array $params = []): array
    {
        if (($type = $this->getQueryType($qry)) !== "select")
        {
            throw new Exception("Incorrect Select Query");
        }

        $stmt = $this->_query($qry, $params, $type);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectSingle(string $qry, array $params = [], $field = false)
    {
        if (($type = $this->getQueryType($qry)) !== "select")
        {
            throw new Exception("Incorrect Select Query");
        }

        $stmt = $this->_query($qry, $params, $type);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($field === false || (empty($res))) ? $res : $res[$field];
    }

    /**
     * Lists column values of a table
     * with desired key from the
     * database as an array.
     *
     * @param  string 		$table
     * @param  string 		$column
     * @param  string|null 	$key
     * @return array
     */
    public function lists(string $table, string $column, ?string $key = null): array
    {
        $selects = implode(', ', is_null($key) ? [$column] : [$column, $key]);

        $qry = "SELECT {$selects} FROM %%{$table}%%;";
        $stmt = $this->_query($qry, [], 'select');

        $results = [];
        if (is_null($key))
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $results[] = $row[$column];
            }
        }
        else
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $results[$row[$key]] = $row[$column];
            }
        }

        return $results;
    }

    public function query(string $qry): void
    {
        $this->lastInsertId = false;
        $this->rowCount = false;
        $this->rowCount = $this->dbHandle->exec($qry);
        $this->queryCounter++;
    }

    public function nativeQuery(string $qry): array|bool
    {
        $this->lastInsertId = false;
        $this->rowCount = false;

        $qry = str_replace($this->dbTableNames['keys'], $this->dbTableNames['names'], $qry);

        /** @var PDOStatement $stmt */
        $stmt = $this->dbHandle->query($qry);

        $this->rowCount = $stmt->rowCount();

        $this->queryCounter++;
        return in_array($this->getQueryType($qry), ['select', 'show']) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : true;
    }

    public function getQueryCounter(): int
    {
        return $this->queryCounter;
    }

    public static function formatDate(int $time): string
    {
        return date('Y-m-d H:i:s', $time);
    }

    public function quote(string $str)
    {
        return $this->dbHandle->quote($str);
    }
}
