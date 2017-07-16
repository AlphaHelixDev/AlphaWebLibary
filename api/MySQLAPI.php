<?php

include "MySQLFileManager.php";

class MySQLAPI {

    const VARCHAR = "VARCHAR";
    const CHAR = "CHAR";
    const TEXT = "TEXT";
    const INT = "INT";
    const BIGINT = "BIGINT";
    const SMALLINT = "SMALLINT";
    const TINYINT = "TINYINT";

    private static $apis = array();
    private static $connections = array();

    /*
    private String username;
    private String password;
    private String database;
    private String host;
    private String port;
     */

    public static function register() {
        $mf = new MySQLFileManager();

        $mf->setupConnection();
    }

    public function __construct($user, $password, $database, $host, $port) {
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->host = $host;
        $this->port = $port;

        if(MySQLAPI::getMySQL($database) == null) {
            self::$apis[count(MySQLAPI::$apis)] = $this;
        }
    }

    /**
     * @param $database
     * @return MySQLAPI
     */
    public static function getMySQL($database) {
        if(is_array(MySQLAPI::$apis)) {
            foreach (MySQLAPI::$apis as $api) {
                if(strcmp($api->database, $database) == 0) return $api;
            }
        }
        return null;
    }

    /**
     * @return MySQLAPI[]
     */
    public static function getApis() {
        if(is_array(self::$apis))
            return self::$apis;
        return array();
    }

    /**
     * @return PDO
     */
    public function getConnection() {
        return self::$connections[$this->database];
    }

    public function initConnection() {
        if(!$this->isConnected()) {
            self::$connections[$this->database] =
                new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database, $this->user, $this->password);
        }
    }

    public function isConnected() {
        if(array_key_exists($this->database, self::$connections))
            return self::$connections[$this->database] != null;
        return false;
    }

    public function closeConnection() {
        if($this->isConnected()) {
            self::$connections[$this->database] = null;
        }
    }
}