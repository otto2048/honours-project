<?php
    //launch a container with compiler app in it for user to use
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/CompilerController.php");

    function launchCompiler()
    {
        $compiler = new CompilerController();

        //if the compiler is running, kill it
        if ($compiler->getCompilerStatus())
        {
            echo "killing";
            if (!$compiler->killCompiler())
            {
                echo 0;
                return;
            }
        }

        //launch the compiler
        if ($compiler->launchCompiler())
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
    }

    launchCompiler();
?>