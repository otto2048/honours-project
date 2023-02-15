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

        //get the inputs for an answer
        public function getAnswerInputs($answerId)
        {
            $this->sqlStmt = "SELECT * FROM honours_values INNER JOIN honours_code_answer ON codeAnswer_fk = honours_code_answer.codeAnswerId WHERE codeAnswer_fk = ?";

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> answerId = $answerId;

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
            //sort data into answer and inputs
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $answer = $data["answer"];
            $inputs = $data["inputs"];

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            //start transaction
            mysqli_begin_transaction($this->conn->getConnection());

            try
            {
                //create answer
                $this->sqlStmt = 'INSERT INTO honours_code_answer (codeId_fk, output) VALUES (?, ?)';

                $paramTypes = "is";

                $answerId = -1;

                parent::create(json_encode($answer, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes, $null, $answerId);

                //create the inputs for the answer
                $this->sqlStmt = 'INSERT INTO honours_values (value, type, codeAnswer_fk) VALUES (?, ?, ?)';

                foreach ($inputs as $input)
                {
                    $variables = new \stdClass();
                    $variables->value = $input["value"];
                    $variables->type = $input["type"];
                    $variables->codeAnswer_fk = $answerId;

                    $paramTypes = "sii";

                    parent::create(json_encode($variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
                }

                mysqli_commit($this->conn->getConnection());

                return true;
            }
            catch (mysqli_sql_exception $exception)
            {
                mysqli_rollback($this->conn->getConnection());

                return false;
            }
            catch (Exception $e)
            {
                echo "here 2";

                return false;
            }

            return false;
        }
    }

?>