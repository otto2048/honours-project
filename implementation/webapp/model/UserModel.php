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

        //get a page of users
        public function getUsers($pageNum, $pageSize, &$pageLimit)
        {
            //count the total number of users to find the max page size
            $this->sqlStmt = 'SELECT COUNT(honours_user.userId) as totalUsers FROM honours_user';

            $pageLimit = ceil(floatval(json_decode(parent::retrieve(), JSON_OBJECT_AS_ARRAY)[0]["totalUsers"]) / $pageSize);

            //get the page of users
            $this->sqlStmt = 'SELECT honours_user.username, honours_user.userId, honours_user.permissionLevel, honours_user.containerPort
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

        //get a users mark for an exercise
        public function getExerciseMark($userId, $codeId)
        {
            //get the total marks available
            $this->sqlStmt = 'SELECT count(codeId_fk) as total FROM honours_code_answer WHERE codeId_fk = ?';

            $variables = new \stdClass();
            $variables -> codeId_fk = $codeId;

            $paramTypes = "i";

            $totalJson = parent::retrieve(json_encode($variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);

            if (!$totalJson)
            {
                return null;
            }

            $total = json_decode($totalJson, JSON_INVALID_UTF8_SUBSTITUTE);
            $totalPoints = 0;

            if (!isset($total["isempty"]))
            {
                $totalPoints = $total[0]["total"];
            }

            //get user points for this exercise
            $this->sqlStmt = 'SELECT mark FROM honours_user_exercise WHERE userId = ? AND codeId = ?';

            $variables = new \stdClass();
            $variables -> userId = $userId;
            $variables -> codeId = $codeId;

            $paramTypes = "ii";

            $pointsJson = parent::retrieve(json_encode($variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);

            if (!$pointsJson)
            {
                return null;
            }

            $points = json_decode($pointsJson, JSON_INVALID_UTF8_SUBSTITUTE);
            $userPoints = 0;

            if (!isset($points["isempty"]))
            {
                $userPoints = $points[0]["mark"];
            }
            
            $ret = array("points" => $userPoints, "total" => $totalPoints);

            return json_encode($ret);

        }

        //delete a user
        public function deleteData($jsonData)
        {
            //get the primary key passed through json data
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE|JSON_OBJECT_AS_ARRAY);

            $this->sqlStmt = 'DELETE FROM honours_user WHERE userId = ?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables->userId = $data["userId"];

            $paramTypes = "i";

            return parent::delete(json_encode($WHERE_variables), $paramTypes);
        }

        //create a user
        //TODO: update this once container port is sorted
        public function createData($jsonData)
        {
          //  $this->sqlStmt = 'INSERT INTO honours_user (username, password, permissionLevel, containerPort) VALUES (?, ?, ?, ?)';
            $this->sqlStmt = 'INSERT INTO honours_user (username, password, permissionLevel) VALUES (?, ?, ?)';

           // $paramTypes = "ssii";
            $paramTypes = "ssi";

            return parent::create($jsonData, $paramTypes);

            //get the last port value, add a value and add another random value to get the port value for this user - to avoid db clashes
        }

        //update a user
        public function updateData($jsonData)
        {
            $this->sqlStmt = 'UPDATE honours_user SET username = ?, permissionLevel = ?, containerPort = ? WHERE userId = ?';

            $paramTypes = "siii";

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