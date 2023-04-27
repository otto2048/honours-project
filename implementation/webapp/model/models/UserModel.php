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

        // get all users
        public function getAllUsers()
        {
            $this->sqlStmt = 'SELECT honours_user.userId, honours_user.username, honours_user.permissionLevel, honours_user.SUS_Score
                FROM honours_user';

            return parent::retrieve();
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

        private function setPermissionLevel($permission, $userId)
        {
            $this->sqlStmt = 'UPDATE honours_user SET permissionLevel = ? WHERE userId = ?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables->permissionLevel = $permission;
            $WHERE_variables->userId = $userId;

            $paramTypes = "ii";

            return parent::update(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
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

            //check if the user doenst have a permission level
            $updateUser = false;

            if ($result[0]["permissionLevel"] == PermissionLevels::UNASSIGNED)
            {
                //set the permission level of this user based on their id
                if ($result[0]["userId"] % 2 == 0)
                {
                    //control user
                    $updateUser = $this->setPermissionLevel(PermissionLevels::CONTROL, $result[0]["userId"]);
                }
                else
                {
                    //experimental user
                    $updateUser = $this->setPermissionLevel(PermissionLevels::EXPERIMENT, $result[0]["userId"]);
                }
            }
            else
            {
                $updateUser = true;
            }

            //verify password
            if (password_verify($password, $result[0]["password"]) && $updateUser)
            {
                $userData = $this->getUserByUsername($username);
                return true;
            }

            return false;
        }
    }

?>