<?php
    //model class to interface with honours_user_survey table (users answers to survey questions)

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");

    class UserSurveyModel extends Model
    {
        //create all records for a survey response
        public function createData($jsonData)
        {
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $userId = $data["userId"];
            $answers = $data["answers"];

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            //start transaction
            mysqli_begin_transaction($this->conn->getConnection());

            try
            {
                //create each answer
                $this->sqlStmt = 'INSERT INTO honours_user_survey (questionId, userId, answer) VALUES (?, ?, ?)';

                foreach ($answers as $question => $answer)
                {
                    $variables = new \stdClass();
                    $variables->questionId = $question;
                    $variables->userId = $userId;
                    $variables->answer = $answer["value"];

                    $paramTypes = "iis";

                    parent::create(json_encode($variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
                }

                //determine user SUS score and insert into into their row
                $score = 0;
                foreach ($answers as $question => $answer)
                {
                    $newAnswer = 0;

                    if (intval($question) < 11)
                    {
                        if (intval($question) % 2 == 0)
                        {
                            $newAnswer = 5 - intval($answer["value"]);
                        }
                        else
                        {
                            $newAnswer = intval($answer["value"]) - 1;
                        }
                    }

                    $score += $newAnswer;
                }

                $SUS = $score * 2.5;

                $this->sqlStmt = 'UPDATE honours_user SET SUS_Score = ? WHERE userId = ?';
                $variables = new \stdClass();
                $variables->SUS_Score = $SUS;
                $variables->userId = $userId;

                $paramTypes = "di";

                parent::create(json_encode($variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);

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
                return false;
            }

            return false;
        }

        //get all the records for a user
        public function getUserAnswers($userId)
        {
            $this->sqlStmt = 'SELECT honours_user_survey.questionId, honours_user_survey.answer, honours_survey_question.contents FROM honours_user_survey INNER JOIN honours_survey_question ON honours_user_survey.questionId = honours_survey_question.questionId WHERE userId = ?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> userId = $userId;

            $paramTypes = "i";

            return parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }

        //get a certain type of answer
        public function filterUserAnswerByType($userId, $type)
        {
            $this->sqlStmt = 'SELECT honours_user_survey.questionId, honours_user_survey.answer, honours_survey_question.contents, honours_survey_question.type FROM honours_user_survey INNER JOIN honours_survey_question ON honours_user_survey.questionId = honours_survey_question.questionId WHERE userId = ? AND type = ?';

            $WHERE_variables = new \stdClass();
            $WHERE_variables -> userId = $userId;
            $WHERE_variables -> type = $type;

            $paramTypes = "ii";

            return parent::retrieve(json_encode($WHERE_variables, JSON_INVALID_UTF8_SUBSTITUTE), $paramTypes);
        }
    }
?>