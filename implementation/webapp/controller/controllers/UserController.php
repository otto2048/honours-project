<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    class UserController extends Controller
    {
        //function to handle user login
        //return void
        public function loginUser()
        {
            //user input
            $passwordInput=$_POST['password'];

            $data = new \stdClass();
            $data -> username = $_POST['username'];
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //check username is valid
            if (!$this->validationObj->validatePK(ModelClassTypes::USER, $jsonData))
            {
                //end session
                session_destroy();

                //return to the login page
                header("location: /honours/webapp/view/login.php"."?error_message=login_failed");

                return;
            }
            
            //variable to hold user data
            $userData = null;

            //get the santized and validated version of the data
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $authenticated = $this->modelObj->loginUser($userData, $data["username"], $passwordInput);

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