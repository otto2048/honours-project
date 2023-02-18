<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

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

        //get a page of users
        public function getUsers($pageNum, $pageSize, &$pageLimit)
        {
            //count the total number of users to find the max page size
            $this->sqlStmt = 'SELECT COUNT(honours_user.userId) as totalUsers FROM honours_user';

            $pageLimit = ceil(floatval(json_decode(parent::retrieve(), JSON_OBJECT_AS_ARRAY)[0]["totalUsers"]) / $pageSize);

            //get the page of users
            $this->sqlStmt = 'SELECT honours_user.username, honours_user.userId, honours_user.permissionLevel
                FROM honours_user LIMIT ? OFFSET ?';

            $variables = new \stdClass();
            $variables -> limit = $pageSize;

            $skipValue = 0;

            if ($pageNum > 1)
            {
                $skipValue = ($pageNum - 1) * $pageSize;
            }

            $variables -> skip = $skipValue;

            $paramTypes = "ii";

            return parent::retrieve(json_encode($variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }

        //delete a user
        public function deleteData($jsonData)
        {
            //get the primary key passed through json data
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE|JSON_OBJECT_AS_ARRAY);

            //get user data
            $this->sqlStmt = 'SELECT permissionLevel FROM honours_user WHERE userId = ?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables->userId = $data["userId"];

            $paramTypes = "i";

            $userJson = parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);

            //if we fail to get user data, return false
            if (!$userJson)
            {
                return false;
            }

            $user = json_decode($userJson, JSON_INVALID_UTF8_SUBSTITUTE);

            //if the user doesnt exist, return true
            if (isset($user["isempty"]))
            {
                return true;
            }

            $permission = $user[0]["permissionLevel"];

            //check if this is an admin user, if so, do not delete
            if ($permission == PermissionLevels::ADMIN)
            {
                return false;
            }

            //delete user
            $this->sqlStmt = 'DELETE FROM honours_user WHERE userId = ?';

            return parent::delete(json_encode($WHERE_variables), $paramTypes);
        }

        //create a user
        public function createData($jsonData)
        {
            $this->sqlStmt = 'INSERT INTO honours_user (username, password, permissionLevel) VALUES (?, ?, ?)';

            $paramTypes = "ssi";

            return parent::create($jsonData, $paramTypes);
        }

        //update a user
        public function updateData($jsonData)
        {
            $this->sqlStmt = 'UPDATE honours_user SET username = ?, permissionLevel = ? WHERE userId = ?';

            $paramTypes = "sii";

            return parent::update($jsonData, $paramTypes);
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