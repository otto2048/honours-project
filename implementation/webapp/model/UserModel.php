<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");

    class UserModel extends Model
    {
        //get user with id
        public function getUserById($userId)
        {
            $this->sqlStmt = 'SELECT *
            FROM honours_user
            WHERE honours_user.userId=?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> userId = $userId;

            $paramTypes = "i";

            return parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }

        //get user with username
        public function getUserByUsername($username)
        {
            $this->sqlStmt = 'SELECT *
            FROM honours_user
            WHERE honours_user.username=?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> username = $username;

            $paramTypes = "s";

            return parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }

        //login user
        public function loginUser(&$userData, $username, $password)
        {
            $userData = null;

            //get user details from username
            $resultJSON = $this->getUserByUsername($username);

            $result = json_decode($resultJSON, JSON_INVALID_UTF8_SUBSTITUTE|JSON_OBJECT_AS_ARRAY);

            //check if user exists
            if (isset($result["isempty"]))
            {
                return false;
            }

            $result = json_decode($resultJSON, JSON_INVALID_UTF8_SUBSTITUTE|JSON_OBJECT_AS_ARRAY);

            //verify password
            if (password_verify($password, $result[0]["password"]))
            {
                $userData = $resultJSON;
                return true;
            }

            return false;
        }
    }

?>