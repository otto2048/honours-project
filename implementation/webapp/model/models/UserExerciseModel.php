<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");

    class UserExerciseModel extends Model
    {
        public function createData($jsonData)
        {
            $this->sqlStmt = 'INSERT INTO honours_user_exercise (userId, codeId, mark, completed) VALUES (?, ?, ?, ?)';

            $paramTypes = "iiii";

            return parent::create($jsonData, $paramTypes);
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
            $userPoints = -1;

            if (!isset($points["isempty"]))
            {
                $userPoints = $points[0]["mark"];
            }
            
            $ret = array("points" => $userPoints, "total" => $totalPoints);

            return json_encode($ret);

        }

    }

?>