<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");

    class ExerciseAnswerModel extends Model
    {
        //get the answers for an exercise
        public function getExerciseAnswers($codeId)
        {
            $this->sqlStmt = "SELECT * FROM honours_code_answer INNER JOIN honours_code_exercise ON codeId_fk = honours_code_exercise.codeId WHERE codeId_fk = ?";

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> codeId_fk = $codeId;

            $paramTypes = "i";

            return parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }

        //delete an answer for an exercise
        public function deleteData($jsonData)
        {
            //get the primary key passed through json data
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE|JSON_OBJECT_AS_ARRAY);

            $this->sqlStmt = 'DELETE FROM honours_code_answer WHERE codeAnswerId = ?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables->answerId = $data["answerId"];

            $paramTypes = "i";

            return parent::delete(json_encode($WHERE_variables), $paramTypes);
        }

        //add an exercise answer
        public function createData($jsonData)
        {
            $this->sqlStmt = 'INSERT INTO honours_code_answer (codeId_fk, input, inputType, output) VALUES (?, ?, ?, ?)';
            
            $paramTypes = "isis";

            return parent::create($jsonData, $paramTypes);
        }
    }

?>