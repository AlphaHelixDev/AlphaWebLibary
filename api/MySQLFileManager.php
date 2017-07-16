<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 16.07.17
 * Time: 18:06
 */

include "SimpleFile.php";

class MySQLFileManager extends SimpleFile {

    public function __construct() {
        parent::__construct("mysql.json");
        $this->addValues();
    }

    public function addValues() {
        if(!$this->isEmpty()) return;

        $this->setValue("database", array(
            "user" => "root",
            "password" => "password",
            "host" => "localhost",
            "port" => "3306"
        ));
    }

    public function setupConnection() {
        foreach ($this->getKeys() as $key) {
            foreach ($this->getValue($key) as $dbInfoInJson) {

                $dbInfoArray = json_decode($dbInfoInJson, true);

                new MySQLAPI(
                    $dbInfoArray["user"],
                    $dbInfoArray["password"],
                    $key,
                    $dbInfoArray["host"],
                    $dbInfoArray["port"]
                );
            }
        }
    }
}