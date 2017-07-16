<?php
    class SimpleFile {

//      name

        public function __construct($name) {
            $this->name = $name;
        }

        public function setValue($path, $value) {
            $jsonStr = null;
            if(file_exists($this->name)) {
                $tmpRead = fopen($this->name, "r");
                $jsonStr = fread($tmpRead, filesize($this->name));
                fclose($tmpRead);
            }

            $tmp = fopen($this->name, "w");
            $jsonObj = array(
                $path => $value
            );

            if($jsonStr != null) {
                $jsonObj = json_decode($jsonStr, true);

                $jsonObj[$path] = $value;
            }

            fwrite($tmp, json_encode($jsonObj));
        }

        public function addListValue($path, $value) {
            $val = $this->getValue($path);

            if($val != null) {
                if(is_array($val)) {
                    $val[count($val)] = $value;
                } else {
                    $val = [$value];
                }
            } else {
                $val = [$value];
            }

            $this->setValue($path, $val);
        }

        public function getList($path) {
            $val = $this->getValue($path);

            if($val != null) {
                if(is_array($val))
                    return $val;
            }

            return array();
        }

        public function getKeys() {
            if(file_exists($this->name)) {
                $tmpRead = fopen($this->name, "r");
                $jsonStr = fread($tmpRead, filesize($this->name));
                fclose($tmpRead);
            } else return array();

            if($jsonStr == null) return array();

            $jsonObj = json_decode($jsonStr, true);

            var_dump($jsonStr);

            if($jsonObj == null) return array();

            return array_keys($jsonObj);
        }

        public function getValue($path) {
            if(file_exists($this->name)) {
                $tmpRead = fopen($this->name, "r");
                $jsonStr = fread($tmpRead, filesize($this->name));
                fclose($tmpRead);
            } else return null;

            if($jsonStr == null) return null;

            $jsonObj = json_decode($jsonStr, true);

            if($jsonObj == null) return null;

            return $jsonObj[$path];
        }

        public function hasValue($path) {
            if(file_exists($this->name)) {
                return $this->getValue($path) == null;
            }
            return false;
        }

        public function isEmpty() {
            if(file_exists($this->name)) {
                $tmpRead = fopen($this->name, "r");
                $jsonStr = fread($tmpRead, filesize($this->name));
                fclose($tmpRead);

                return strlen($jsonStr) == 0;
            }
            return true;
        }
    }
?>
