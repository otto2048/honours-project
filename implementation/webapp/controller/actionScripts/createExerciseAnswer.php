<?php
    //action script to create an exercise answer
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/ExerciseAnswerController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionCreateExerciseAnswer()
    {
        $exerciseController = new ExerciseAnswerController(ModelClassTypes::EXERCISE_ANSWER);

        $exerciseController -> createExerciseAnswer();
    }

    actionCreateExerciseAnswer();
?>