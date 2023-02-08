<?php
    //action script to create an exercise
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/ExerciseController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionCreateExercise()
    {
        $exerciseController = new ExerciseController(ModelClassTypes::EXERCISE);

        $exerciseController -> createExercise();
    }

    actionCreateExercise();
?>