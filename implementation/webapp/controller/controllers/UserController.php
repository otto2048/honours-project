<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    class UserController extends Controller
    {
        //function to handle user login
        //return void
        public function loginUser($usernameInput, $passwordInput)
        {
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
                $_SESSION["username"] = $userData[0]["username"];
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

        public function deleteUser($userId, $automated = false)
        {
            //user input into json object
            $data = new \stdClass();
            $data -> userId = $userId;
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare error message
            if (!$automated)
            {
                $failureMessage[0]["success"] = false;
                $failureMessage[0]["content"] = "Failed to delete user. Try again?";
    
                $this->failurePathVariables["message"] = json_encode($failureMessage);
                $this->failurePathVariables["id"] = $this->validationObj->cleanInput($data->userId);
    
                //prepare success message
                $successMessage[0]["success"] = true;
                $successMessage[0]["content"] = "Successfully deleted user";
    
                $this->successPathVariables["message"] = json_encode($successMessage);
    
                //set success and failure paths
                $this->successPath = "/honours/webapp/view/adminArea/users/userDashboard.php";
                $this->failurePath = "/honours/webapp/view/adminArea/users/user.php";
            }
            

            return parent::delete($jsonData);
        }

        public function createUser()
        {
            //hash password
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            //user input into json object
            $data = new \stdClass();
            $data -> username = $_POST['username'];
            $data -> password = $password;
            $data -> permissionLevel = $_POST['permissionLevel'];
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully created user";

            $this->successPathVariables["message"] = json_encode($successMessage);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to create user. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/users/userDashboard.php";
            $this->failurePath = "/honours/webapp/view/adminArea/users/userDashboard.php";
 
            return parent::create($jsonData);
        }

        public function signUpUser()
        {
            //hash password
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            //user input into json object
            $data = new \stdClass();
            $data -> username = $_POST['username'];
            $data -> password = $password;
            
            if (isset($_POST["consentFormCheck"]))
            {
                //set this as experimental or control group
                $data -> permissionLevel = PermissionLevels::CONTROL;
            }
            else
            {
                //guest user
                $data -> permissionLevel = PermissionLevels::GUEST;
            }
            
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Sign up failed. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);

            //set failure path
            $this->failurePath = "/honours/webapp/view/userArea/signUp.php";

            //set success path to null, dont want to go anywhere on success
            $this->successPath = null;
 
            //create user
            $creation = parent::create($jsonData);

            if ($creation)
            {
                //login user
                $this->loginUser($_POST["username"], $_POST['password']);
            }
            else
            {
                $path = $this->constructPath($this->failurePath, $this->failurePathVariables);

                echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
            }

            //prepare error message
            $message[0]["success"] = false;
            $message[0]["content"] = "Login failed, invalid username or password";
        }

        public function updateUser()
        {
            //user input into json object
            $data = new \stdClass();
            $data -> username = $_POST['username'];
            $data -> permissionLevel = $_POST['permissionLevel'];
            $data -> userId = $_POST['userId'];
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully updated user";

            $this->successPathVariables["message"] = json_encode($successMessage);
            $this->successPathVariables["id"] = $this->validationObj->cleanInput($data->userId);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to update user. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);
            $this->failurePathVariables["id"] = $this->validationObj->cleanInput($data->userId);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/users/user.php";
            $this->failurePath = "/honours/webapp/view/adminArea/users/updateUser.php";

            return parent::update($jsonData);
        }
    }

?>