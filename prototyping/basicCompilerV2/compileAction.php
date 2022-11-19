<?php

    //get code sent to server
    $input = $_POST["code-input"];

    //create cpp file
    $tmpFile = fopen("tmpFile.cpp", "w");

    //add code to cpp file
    fwrite($tmpFile, $input);

    fclose($tmpFile);

    //compile cpp into exe
    $compiled = shell_exec("g++ tmpFile.cpp -o js/executable 2>&1");

    if ($compiled)
    {
        //echo any error message from compilation
        echo $compiled;
    }
    else
    {
        //send to debug page
        echo '<script type="text/javascript">window.open("debug.html", name="_self")</script>';
    }

?>