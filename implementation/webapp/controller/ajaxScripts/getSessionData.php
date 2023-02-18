<?php

    require_once("../Session.php");

    function getSessionData()
    {
        $sessionVariables = new \stdClass();
        $sessionVariables -> username = $_SESSION["username"];

        //echo json string of session data variables
        echo json_encode($sessionVariables);
    }

    getSessionData();

?>