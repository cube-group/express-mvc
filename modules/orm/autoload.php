<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/8/26
 * Time: 下午10:54
 */

namespace orm;

use log\Log;
use utils\Utils;

//extension check.
if ($ext = Utils::is_miss_ext(['pdo', 'pdo_mysql'])) {
    throw new \Exception('Ext ' . $ext . ' is not exist!');
}

/**
 * Class DB For the sql database :).
 * For sql.
 * You must setup the pdo extension before you use the DB.
 *
 * @package cube\orm
 */
final class DB
{
    private function __construct()
    {
        //__construct access denied
    }

    /**
     * database config.
     * array(
     *  'type'=>'mysql',
     *  'host'=>'127.0.0.1',
     *  'port'=>3306,
     *  'user'=>'root',
     *  'password'=>'',
     *  'orm'=>'system',
     *  'prefix'=>'orm prefix such as google_x_'
     * )
     *
     * @var
     */
    private static $options;

    /**
     * pdo connection instance.
     * @var
     */
    private static $pdo;

    /**
     * check the needed extensions.
     * @param $plugins
     * @return bool
     */
    public static function check_unknown_extension($extension = null)
    {
        $env_extensions = get_loaded_extensions();
        foreach ($env_extensions as $key => $ext) {
            $env_extensions[$key] = strtolower($ext);
        }
        foreach ($extension as $ext) {
            if (!in_array($ext, $env_extensions)) {
                return $ext;
            }
        }
        return null;
    }


    /**
     * initialize database config.
     *
     * @param $options
     */
    public static function init($options)
    {
        if (empty($options['type'])) {
            $options['type'] = 'mysql';
        }
        self::$options = $options;
    }


    /**
     * create database connection.
     */
    private static function connect()
    {
        if (!self::$options) {
            throw new \Exception('DB OPTIONS IS NULL');
        }
        $options = self::$options;
        self::$pdo = new \PDO(
            $options['type'] . ':host=' . $options['host'] . ';port=' . $options['port'] . ';dbname=' . $options['db'],
            $options['username'],
            $options['password'],
            [\PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"]
        );
    }

    /**
     * create orm orm instance.
     * DB::model('list');
     *
     * @param $tableName database list name(not contains table prefix)
     * @return DBModel
     */
    public static function model($tableName)
    {
        if (empty(self::$options)) {
            throw new \Exception('No orm options.');
        }

        if (empty(self::$pdo)) {
            self::connect();
        }

        return new DBModel(!self::$options['prefix'] ? $tableName : (self::$options['prefix'] . $tableName));
    }

    /**
     * execute sql.
     * (no collection returned)
     * for update/delete/insert.
     *
     * @param $sql
     * @param $task run as the task mode(default value : false)
     * @return int (-1:error,0:no effect,>=1:number of affected rows);
     */
    public static function exec($sql, $task = false)
    {
        if (empty(self::$pdo)) {
            self::connect();
        }
        try {
            Log::mysql($sql . ' task: ' . ($task ? 'true' : 'false'));
            if ($task == true) {
                self::$pdo->beginTransaction();
            }
            $result = self::$pdo->exec($sql);
            if ($result !== false) {
                if ($task == true) self::$pdo->commit();
            } else {
                if ($task == true) self::$pdo->rollBack();
                return -1;
            }
            return $result;
        } catch (\PDOException $e) {
            Log::mysql('=> Error ' . $e->getTraceAsString());
        }
        return -1;
    }

    /**
     * execute sql.
     * (collection returned)
     *
     * @param $sql
     * @param $task run as the task mode(default value : false)
     * @return array data collection
     */
    public static function query($sql, $task = false)
    {
        if (empty(self::$pdo)) {
            self::connect();
        }
        try {
            Log::mysql($sql . ' task: ' . ($task ? 'true' : 'false'));
            if ($task == true) {
                self::$pdo->beginTransaction();
            }
            if ($stat = self::$pdo->query($sql)) {
                $result = $stat->fetchAll(\PDO::FETCH_ASSOC);
                if ($task == true) self::$pdo->commit();
            } else {
                if ($task == true) self::$pdo->rollBack();
            }
            return $result;
        } catch (\PDOException $e) {
            Log::mysql($sql . ' => Error ' . $e->getTraceAsString());
        }
        return false;
    }

    /**
     * Get PDO Statement.
     * @param $sql
     * @param $task
     * @return null
     */
    public static function value($sql, $task = false)
    {
        if (!empty(self::$pdo)) {
            try {
                Log::mysql($sql . ' STAT task: ' . ($task ? 'true' : 'false'));
                if ($task == true) {
                    self::$pdo->beginTransaction();
                }

                if ($stat = self::$pdo->query($sql)) {
                    $result = $stat->fetchColumn();
                    if ($task == true) self::$pdo->commit();
                } else {
                    if ($task == true) self::$pdo->rollBack();
                }

                return $result;
            } catch (\PDOException $e) {
                Log::mysql($sql . ' => Error ' . $e->getTraceAsString());
            }
        }
        return null;
    }

    /**
     * Close the orm instance.
     * @return bool
     */
    public static function close()
    {
        self::$pdo = null;
        return true;
    }
}


/**
 * Class DBModel.
 * sql orm model unit.
 * @package com\cube\orm
 */
class DBModel
{
    /**
     * the state of support task.
     * @var bool
     */
    private $_task = false;
    /**
     * the name of the table.
     * @var string
     */
    private $_table_name = '';
    /**
     * sql where string.
     * @var string
     */
    private $_where = '';
    /**
     * sql order string.
     * @var string
     */
    private $_order = '';
    /**
     * sql group string.
     * @var string
     */
    private $_group = '';
    /**
     * sql limit string.
     * @var string
     */
    private $_limit = '';

    /**
     * DB constructor.
     * @param $table_name
     */
    public function __construct($table_name)
    {
        $this->_table_name = $table_name;
    }

    /**
     * the orm action as the task.
     *
     * DB::model('list')->task()->insert(array('a'=>1));
     * SQL:insert into list (a) values (1);
     * @return mixed
     */
    public function task()
    {
        $this->_task = true;
        return $this;
    }

    /**
     * where.
     *
     * DB::model('list')->where('a=1 and (b=2 or c=3)')->select();
     * SQL:select * from list where a=1 and (b=2 or c=3);
     *
     * @param $options
     * @return $this
     */
    public function where($options)
    {
        if (!empty($options)) {
            $this->_where = $options;
        }
        return $this;
    }

    /**
     * select by the order.
     *
     * DB::model('list')->order('userid ASC')->select();
     * SQL:select * from list order by userid asc;
     *
     * DB::model('list')->order(array('userid ASC','username DESC'))->select();
     * SQL:select * from list order by userid asc,username desc;
     *
     * @param $options
     * @return $this
     */
    public function order($options)
    {
        if (!empty($options)) {
            if (is_array($options) && count(options) > 0) {
                $this->_order = join(',', $options);
            } else {
                $this->_order = $options;
            }
        }
        return $this;
    }

    /**
     * select by the group.
     *
     * DB::model('list')->group('userid')->select();
     * SQL:select * from list group by userid;
     *
     * DB::model('list')->group(array('userid','username'))->select();
     * SQL:select * from list group by userid,username;
     *
     * @param $options
     * @return $this
     */
    public function group($options)
    {
        if (!empty($options)) {
            if (is_array($options) && count(options) > 0) {
                $this->_group = join(',', $options);
            } else {
                $this->_group = $options;
            }
        }
        return $this;
    }

    /**
     * limit pages.
     *
     * DB::model('list')->limit(0,10)->select();
     * SQL:select * from list limit 0,10;
     *
     * @param $start
     * @param $length
     * @return $this
     */
    public function limit($start, $length)
    {
        if ($start >= 0 && $length > 0) {
            $this->_limit = $start . ',' . $length;
        }
        return $this;
    }

    /**
     * get the count of the select.
     *
     * DB::model('list')->count();
     * SQL:select count(*) from list;
     *
     * DB::model('list')->where('a=1')->count();
     * SQL:select count(*) from list where a=1;
     *
     * @return array
     */
    public function count()
    {
        $sql = 'SELECT COUNT(*)';
        $sql .= ' FROM ' . $this->_table_name;
        if (!empty($this->_where)) {
            $sql .= ' WHERE ' . $this->_where;
        }
        $sql .= ';';
        return DB::value($sql, $this->_task);
    }

    /**
     * 查询符合当前sql的和.
     *
     * DB::model('list')->sum('score');
     * SQL:select sum(score) from list;
     *
     * DB::model('list')->where('a=1')->sum('score');
     * SQL:select sum(score) from list where a=1;
     *
     * @param $value
     * @return array|int
     */
    public function sum($value)
    {
        if (empty($value)) {
            return -1;
        }
        $sql = 'SELECT SUM(' . $value . ')';
        $sql .= ' FROM ' . $this->_table_name;
        if (!empty($this->_where)) {
            $sql .= ' WHERE ' . $this->_where;
        }
        $sql .= ';';
        return DB::value($sql, $this->_task);
    }

    /**
     * select.
     *
     * DB::model('list')->select();
     * SQL:select * FROM list;
     *
     * DB::model('list')->select('username');
     * SQL:select username from list;
     *
     * DB::model('list')->select(array('username','team'));
     * SQL:select username,team from list;
     *
     * @param null $options
     * @return array
     */
    public function select($options = null)
    {
        $sql = 'SELECT ';
        if (!empty($options)) {
            if (is_array($options) && count($options) > 0) {
                $sql .= join(',', $options);
            } else {
                $sql .= $options;
            }
        } else {
            $sql .= '*';
        }
        $sql .= ' FROM ' . $this->_table_name;
        if (!empty($this->_where)) {
            $sql .= ' WHERE ' . $this->_where;
        }
        if (!empty($this->_group)) {
            $sql .= ' GROUP BY ' . $this->_group;
        }
        if (!empty($this->_order)) {
            $sql .= ' ORDER BY ' . $this->_order;
        }
        if (!empty($this->_limit)) {
            $sql .= ' LIMIT ' . $this->_limit;
        }
        $sql .= ';';
        return DB::query($sql, $this->_task);
    }

    /**
     * update.
     *
     * DB::model('list')->where('a=1 and b="world"')->update(array('c'=>2,'d'=>'"hello"'));
     * SQL:update list c=2,d="hello" where a=1 and b="world";
     *
     * @param $options
     * @return int
     */
    public function update($options)
    {
        $sql = 'UPDATE ' . $this->_table_name . ' SET ';
        if (!empty($options) && is_array($options) && count($options) > 0) {
            $sets = array();
            foreach ($options as $key => $value) {
                array_push($sets, $key . '=' . $value);
            }
            $sql .= join(',', $sets);
        } else {
            return false;
        }
        if (!empty($this->_where)) {
            $sql .= ' WHERE ' . $this->_where;
        }
        $sql .= ';';
        return DB::exec($sql, $this->_task);
    }

    /**
     * delete.
     *
     * DB::model('list')->where('a=1')->delete();
     * SQL:delete from list where a=1;
     *
     * @return int
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->_table_name;
        if (!empty($this->_where)) {
            $sql .= ' WHERE ' . $this->_where;
            $sql .= ';';
        }
        return DB::exec($sql, $this->_task);
    }

    /**
     * insert action.
     *
     * DB::model('list')->where('a=1')->insert(array('a'=>1,'c'='2'));
     * SQL:insert into list (a,c) values (1,2);
     *
     * DB::model('list')->insert(array('a'=>1,'b'=>2));
     * SQL:INSERT INTO list (a,b) SELECT (1,2) FROM VISUAL WHERE NOT EXISTS (SELECT * FROM list WHERE name="hello");
     *
     * @param $options
     * @return int
     */
    public function insert($options)
    {
        $sql = 'INSERT INTO ' . $this->_table_name;
        if (!empty($options) && is_array($options) && count($options) > 0) {
            $columns = array();
            $values = array();
            foreach ($options as $key => $value) {
                array_push($columns, $key);
                array_push($values, $value);
            }
            $sql .= ' (' . join(',', $columns) . ')';

            if (!empty($this->_where)) {
                $unique_key = explode('=', $this->_where)[0];
                $sql .= ' SELECT ' . join(',', $values) . ' FROM DUAL WHERE NOT EXISTS(SELECT ';
                $sql .= $unique_key . ' FROM ' . $this->_table_name . ' WHERE ' . $this->_where . ')';
            } else {
                $sql .= ' VALUES (' . join(',', $values) . ')';
            }
            $sql .= ';';
        } else {
            return false;
        }
        return DB::exec($sql, $this->_task);
    }
}