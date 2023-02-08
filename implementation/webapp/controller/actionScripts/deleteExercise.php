<?php
    //action script to delete an exercise
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/ExerciseController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    function actionDeleteExercise()
    {
        $exerciseController = new ExerciseController(ModelClassTypes::EXERCISE);

        $exerciseController -> deleteExercise();
    }

    actionDeleteExercise();
?>