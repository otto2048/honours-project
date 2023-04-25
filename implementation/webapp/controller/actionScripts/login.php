<?php
    //action script to login a user
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionLoginUser()
    {
        $loginController = new UserController(ModelClassTypes::USER);

        $loginController -> loginUser($_POST["username"], $_POST["password"]);
    }

    actionLoginUser();
?>