<?php

    require_once("Model.php");

    Class dvd extends Model {
        public function getDVDs()
        {
            $this->sqlStmt = "SELECT * FROM dvds";

            return parent::selectAll();
        }
    }

    $test = new dvd();

    echo $test->getDVDs();

?>