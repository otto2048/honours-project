<?php
    //action script to delete a user
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionDeleteUser()
    {
        $userController = new UserController(ModelClassTypes::USER);

        $userController -> deleteUser();
    }

    actionDeleteUser();
?>