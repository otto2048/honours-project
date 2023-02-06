<?php

    //action script to update a user
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionUpdateUser()
    {
        $userController = new UserController(ModelClassTypes::USER);

        $userController -> updateUser();
    }

    actionUpdateUser();
?>