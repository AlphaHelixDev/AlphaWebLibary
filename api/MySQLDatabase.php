<?php

include "MySQLAPI.php";

class MySQLDatabase {

    private static $tableInfos = array(); // Map
    private static $tableNames = array(); // List

    public function __construct($table, $database) {
        $this->table = $table;
        $this->database = $database;
    }

    public function create($columns) {
        $tableInfo = null;

        if(count($columns) > 1) {
            foreach ($columns as $column) {
                $tableInfo .= ", " . $column;
            }
        } else {
            $tableInfo = $columns[0];
        }

        $tableInfo = $this->replace_first($tableInfo, ", ",  "");

        if(!in_array($this->table, self::$tableNames))
            self::$tableNames[count(self::$tableNames)] = $this->table;

        if(!array_key_exists($this->table, self::$tableInfos))
            self::$tableInfos[$this->table] = $tableInfo;

        $api = MySQLAPI::getMySQL($this->database);

        if($api != null) {
            if($api->isConnected()) {
                $qry = "CREATE TABLE IF NOT EXISTS " . $this->table . " ($tableInfo)";
                $api->getConnection()->exec($qry);
            }
        }
    }

    public function remove($condition, $value) {
        $api = MySQLAPI::getMySQL($this->database);

        if($api != null) {
            if($api->isConnected()) {
                $qry = "DELETE FROM " . $this->table . " WHERE(?=?)";
                $prep = $api->getConnection()->prepare($qry);
                $prep->execute(array($condition, $value));
            }
        }
    }

    public function insert($values) {
        $val = null;
        foreach ($values as $value) {
            $val .= ", '" . $value . "'";
        }

        $val = $this->replace_first($val, ", ",  "");

        $api = MySQLAPI::getMySQL($this->database);

        if($api != null) {
            if($api->isConnected()) {
                $qry = "INSERT INTO " . $this->table . " VALUES (" . $val . ")";
                $prep = $api->getConnection()->prepare($qry);
                $prep->execute();
            }
        }
    }

    public static function createColumn($name, $dataType, $size, $params) {
        return "$name $dataType ($size) $params";
    }

    public function contains($condition, $value) {
        return $this->getResult($condition, $value, $condition) != null;
    }

    public function update($condition, $val, $column, $updateVal) {
        $api = MySQLAPI::getMySQL($this->database);

        if($api != null) {
            if ($api->isConnected()) {
                $qry = "UPDATE " . $this->table . " SET $column=? WHERE $condition=?";
                $prep = $api->getConnection()->prepare($qry);
                $prep->execute(array($updateVal, $val));
            }
        }
    }

    public function getResult($condition, $value, $column) {
        $api = MySQLAPI::getMySQL($this->database);

        if($api != null) {
            if($api->isConnected()) {
                $qry = "SELECT * FROM " . $this->table . " WHERE ($condition=?)";
                $prep = $api->getConnection()->prepare($qry);
                $prep->execute(array($value));
                $headarr = $prep->fetchAll()[0];

                if(!array_key_exists($column, $headarr)) return null;

                return $headarr[$column];
            }
        }
    }

    public function getList($column) {
        $api = MySQLAPI::getMySQL($this->database);

        if($api != null) {
            if ($api->isConnected()) {
                $qry = "SELECT $column FROM " . $this->table;
                $prep = $api->getConnection()->prepare($qry);
                $prep->execute();

                return $prep->fetchAll();
            }
        }
    }

    private function getColoumnName($column) {
        if(MySQLDatabase::$tableInfos[$this->table] == null) return null;

        $info = explode(", ", MySQLDatabase::$tableInfos[$this->table]);

        return explode(" ", $info[$column - 1])[0];
    }

    private function getColoumnAmount() {
        if(MySQLDatabase::$tableInfos[$this->table] == null)
            return 0;

        if(!strpos(MySQLDatabase::$tableInfos[$this->table], ", ") !== false)
            return 1;

        $info = explode(", ", MySQLDatabase::$tableInfos[$this->table]);
        return strlen($info);
    }

    public function replace_first($haystack, $needle, $replace) {
        $pos = strpos($haystack, $needle);
        if ($pos !== false) {
            return substr_replace($haystack, $replace, $pos, strlen($needle));
        }
    }
}