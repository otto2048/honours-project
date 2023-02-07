<?php
    //action script to create a user
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionSignUpUser()
    {
        $userController = new UserController(ModelClassTypes::USER);

        $userController -> signUpUser();
    }

    actionSignUpUser();
?>