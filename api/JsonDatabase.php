<?php

include "MySQLDatabase.php";

class JsonDatabase {

    const NAME = "name";
    const NUMBER = "number";
    const UUID = "uuid";

    public function __construct($id, $table, $database) {
        $this->id = $id;
        $this->database = new MySQLDatabase($table, $database);

        $this->database->create(array(
            MySQLDatabase::createColumn($id, MySQLAPI::VARCHAR, 50, "PRIMARY KEY"),
            MySQLDatabase::createColumn("val", MySQLAPI::TEXT, 500000, "")
        ));
    }

    public function setValue($idVal, $val) {
        if($this->database->contains($this->id, $idVal))
            $this->database->update($this->id, $idVal, "val", json_encode($val));
        else
            $this->database->insert(array($idVal, json_encode($val)));
    }

    public function getValue($idVal) {
        if($this->database->contains($this->id, $idVal)) {
            return json_decode($this->database->getResult($this->id, $idVal, "val"));
        }
    }

    public function getValues() {
        $vals = array();

        foreach ($this->database->getList("val") as $json) {
            $vals[count($vals)] = json_decode($json);
        }

        return $vals;
    }

    public function hasValue($id) {
        return $this->database->contains($this->id, $id);
    }

}