<?php

    //action script to update a exercise
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/ExerciseController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionUpdateExercise()
    {
        $exerciseController = new ExerciseController(ModelClassTypes::USER);

        $exerciseController -> updateExercise();
    }

    actionUpdateExercise();
?>