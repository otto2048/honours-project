<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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