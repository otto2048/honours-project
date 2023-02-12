<?php
    //kill the container running the compiler app

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/CompilerController.php");

    function killCompiler()
    {
        $compiler = new CompilerController();

        if ($compiler->killCompiler())
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
    }

    killCompiler();
?>