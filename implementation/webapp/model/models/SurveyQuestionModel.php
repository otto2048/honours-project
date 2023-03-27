<?php
    //model class to interface with the honours_survey_question table (SUS survey questions)

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");

    class SurveyQuestionModel extends Model
    {
        //get survey question
        public function getSurveyQuestion($questionId)
        {
            $this->sqlStmt = 'SELECT *
            FROM honours_survey_question
            WHERE honours_survey_question.questionId=?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> questionId = $questionId;

            $paramTypes = "i";

            return parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }

        //get all survey questions
        public function getAllSurveyQuestions()
        {
            $this->sqlStmt = 'SELECT * FROM honours_survey_question ORDER BY questionId';

            return parent::retrieve();
        }

        //delete survey question
        public function deleteData($jsonData)
        {
            $this->sqlStmt = 'DELETE FROM honours_survey_question WHERE questionId = ?';
            
            $paramTypes = "i";

            return parent::delete($jsonData, $paramTypes);
        }

        //create survey question
        public function createData($jsonData)
        {
            $this->sqlStmt = 'INSERT INTO honours_survey_question (questionId, contents, type) VALUES (?, ?, ?)';

            $paramTypes = "isi";

            return parent::create($jsonData, $paramTypes);
        }

        //update survey question
        public function updateData($jsonData)
        {
            $this->sqlStmt = 'UPDATE honours_survey_question SET contents = ?, type = ? WHERE questionId = ?';

            $paramTypes = "ssi";

            return parent::update($jsonData, $paramTypes);
        }
    }

?>