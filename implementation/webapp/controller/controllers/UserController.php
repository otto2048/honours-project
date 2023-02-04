<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    class UserController extends Controller
    {
        //function to handle user login
        //return void
        public function loginUser()
        {
            //user input
            $usernameInput=$_POST['username'];
            $passwordInput=$_POST['password'];

            //sanitize user input
            $username = $this->validationObj->cleanInput($usernameInput);

            //check username is valid
            if (!$this->validationObj->validatePK(Validation::USER, $username))
            {
                //end session
                session_destroy();

                //return to the login page
                header("location: /honours/webapp/view/login.php"."?error_message=login_failed");

                return;
            }
            
            //variable to hold user data
            $userData = null;

            $authenticated = $this->modelObj->loginUser($userData, $username, $passwordInput);

            if ($authenticated)
            {
                //set up session variables
                $userData = json_decode($userData, JSON_INVALID_UTF8_SUBSTITUTE);
                $_SESSION["userId"] = $userData[0]["userId"];
                $_SESSION["permissionLevel"] = $userData[0]["permissionLevel"];

                //send to home page         
                header("location: /honours/webapp/view/index.php");     
            }
            else
            {
                //end session
                session_destroy();

                //return to login page
                header("location: /honours/webapp/view/login.php"."?error_message=login_failed");
            }
        }
    }

?>