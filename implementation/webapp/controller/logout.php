<?php
    include 'Session.php';

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/CompilerController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function logout()
    {
        // if this user is a guest, delete all of their data
        if ($_SESSION["permissionLevel"] == PermissionLevels::GUEST)
        {
            $userController = new UserController(ModelClassTypes::USER);
            $userController -> deleteUser($_SESSION["userId"], true);
        }

        //kill the users compiler if the container is still running
        $compiler = new CompilerController();
        $compiler->killCompiler();

        //unset variables
        session_unset();

        session_destroy();
    }

    logout();

?>