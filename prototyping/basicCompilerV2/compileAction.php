<?php

    //get code sent to server
    $input = $_POST["code"];

    $tmpFile = fopen("tmpFile.cpp", "w");

    fwrite($tmpFile, $input);

    fclose($tmpFile);

    $compiled = shell_exec("g++ tmpFile.cpp -o executable 2>&1");

    if ($compiled)
    {
        echo $compiled;
    }
    else
    {
        echo '<script type="text/javascript">window.open("debug.html", name="_self")</script>'; 
      //  echo shell_exec("./executable");
    }

?>