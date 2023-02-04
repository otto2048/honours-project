<?php
    //action script to login a user
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserController.php");

    function actionLoginUser()
    {
        $loginController = new UserController("UserModel");

        $loginController -> loginUser();
    }

    actionLoginUser();
?>