<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");

    class ExerciseModel extends Model
    {
        //get exercise with id
        public function getExercise($codeId)
        {
            $this->sqlStmt = 'SELECT *
            FROM honours_code_exercise
            WHERE honours_code_exercise.codeId=?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> codeId = $codeId;

            $paramTypes = "i";

            return parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }

        //get all exercises
        public function getExercises($pageNum, $pageSize, &$pageLimit)
        {
            //count the total number of exercises to find the max page size
            $this->sqlStmt = 'SELECT COUNT(honours_code_exercise.codeId) as totalExercises FROM honours_code_exercise';

            $pageLimit = ceil(floatval(json_decode(parent::retrieve(), JSON_OBJECT_AS_ARRAY)[0]["totalExercises"]) / $pageSize);

            //get the page of exercises
            $this->sqlStmt = 'SELECT *
                FROM honours_code_exercise LIMIT ? OFFSET ?';

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

        //delete an exercise
        public function deleteData($jsonData)
        {
            //get the primary key passed through json data
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE|JSON_OBJECT_AS_ARRAY);

            $this->sqlStmt = 'DELETE FROM honours_code_exercise WHERE codeId = ?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables->codeId = $data["codeId"];

            $paramTypes = "i";

            return parent::delete(json_encode($WHERE_variables), $paramTypes);
        }

        //create an exercise
        public function createData($jsonData)
        {
            $this->sqlStmt = 'INSERT INTO honours_code_exercise (title, description, exerciseFile, instructionsFile, visible, availability, type) VALUES (?, ?, ?, ?, ?, ?, ?)';

            $paramTypes = "ssssiii";

            return parent::create($jsonData, $paramTypes);
        }

        //update an exercise
        public function updateData($jsonData)
        {
            $this->sqlStmt = 'UPDATE honours_code_exercise SET title = ?, description = ?, exerciseFile = ?, instructionsFile = ?, visible = ?, availability = ?, type = ? WHERE codeId = ?';

            $paramTypes = "ssssiiii";

            return parent::update($jsonData, $paramTypes);
        }
    }

?>