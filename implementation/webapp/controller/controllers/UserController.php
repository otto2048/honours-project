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
            $usernameInput = $_POST["username"];

            $username = $this->validationObj->cleanInput($usernameInput);

            //prepare error message
            $message[0]["success"] = false;
            $message[0]["content"] = "Login failed, invalid username or password";

            //check username is valid
            if (!$this->validationObj->validateString($username, Validation::USERNAME_LENGTH))
            {
                //end session
                session_destroy();

                //return to the login page with error message
                header("location: /honours/webapp/view/login.php"."?message=".urlencode(json_encode($message)));

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

                //return to the login page with error message
                header("location: /honours/webapp/view/login.php"."?message=".urlencode(json_encode($message)));
            }
        }

        public function deleteUser()
        {
            //user input into json object
            $data = new \stdClass();
            $data -> userId = $_GET['userId'];
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/users/userDashboard.php";
            $this->failurePath = "/honours/webapp/view/adminArea/users/user.php?id=".$this->validationObj->cleanInput($data->userId);

            return parent::delete($jsonData);
        }
    }

?>