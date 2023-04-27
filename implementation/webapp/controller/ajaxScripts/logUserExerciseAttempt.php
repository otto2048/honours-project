<?php
    //log a user exercise attempt with ajax
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserExerciseController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function logUserExerciseAttempt()
    {
        $exerciseController = new UserExerciseController(ModelClassTypes::USER_EXERCISE);

        if ($exerciseController->logUserExerciseAttempt())
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
    }

    logUserExerciseAttempt();
?>