<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/ExerciseAnswerController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function deleteExerciseAnswer()
    {
        $exerciseController = new ExerciseAnswerController(ModelClassTypes::EXERCISE_ANSWER);

        if ($exerciseController->deleteExerciseAnswer())
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