<?php
    //handle sessions
    session_start();

    if (!isset($_SESSION["userId"]))
    {
        session_destroy();
    }
?>