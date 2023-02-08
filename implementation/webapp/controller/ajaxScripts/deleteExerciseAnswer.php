<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    function deleteExerciseAnswer()
    {
        $validate = new Validation();

        //validate and sanitize input
        $answerId = $validate->cleanInput($_POST["itemId"]);

        if (!$validate->validateInt($answerId))
        {
            echo 0;
            return;
        }

        $exerciseModel = new ExerciseModel();

        if ($exerciseModel->deleteExerciseAnswer($answerId))
        {
            echo 1;
        }
        else
        {
            echo 0;
        }

    }

    deleteExerciseAnswer();
?>