<?php

    //get code sent to server
    $input = $_POST["codeinput"];

    //create cpp file
    $tmpFile = fopen("tmpFile.cpp", "w");

    //add code to cpp file
    fwrite($tmpFile, $input);

    fclose($tmpFile);

    //compile cpp into exe
    $compiled = shell_exec("g++ tmpFile.cpp -o executable 2>&1");

    if ($compiled)
    {
        //echo any error message from compilation, this functionality will be moved to the compileForm page
        echo $compiled;
        echo "<br>";
        echo '<a class="btn btn-primary" href="compileForm.html">Back</a>';
    }
    else
    {
        //send to debug page
        echo '<script type="text/javascript">window.open("debug.html", name="_self")</script>';
    }

?>