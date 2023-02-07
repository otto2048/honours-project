<?php
    include 'Session.php';

    //unset variables
    session_unset();

    session_destroy();

    // TODO: if this user is a guest, delete all of their data
?>