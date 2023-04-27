<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/SurveyQuestionTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/AnswerTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");

    class Validation
    {
        //database data constraints
        const USERNAME_LENGTH = 50;

        const EXERCISE_TITLE_LENGTH = 100;
        const EXERCISE_DESCRIPTION_LENGTH = 250;
        const EXERCISE_EXERCISEFILE_LENGTH = 250;

        const SURVEY_QUESTION_CONTENTS = 150;
        const SURVEY_TEXT_ANSWER_LENGTH = 1000;

        const SUS_LIKERT_MIN = 1;
        const SUS_LIKERT_MAX = 5;

        const RESULT_VECTOR_MAX_LENGTH = 50;

        // validate a whole db entry
        public function validate($modelClassType, &$jsonData, &$errorMessageJson)
        {
            switch ($modelClassType)
            {
                case ModelClassTypes::USER:
                    return $this->validateUser($jsonData, $errorMessageJson);
                    break;
                case ModelClassTypes::EXERCISE:
                    return $this->validateExercise($jsonData, $errorMessageJson);
                    break;
                case ModelClassTypes::USER_EXERCISE:
                    return $this->validateUserExercise($jsonData, $errorMessageJson);
                    break;
                case ModelClassTypes::SURVEY_QUESTION:
                    return $this->validateSurveyQuestion($jsonData, $errorMessageJson);
                    break;
                case ModelClassTypes::USER_SURVEY:
                    return $this->validateUserSurveyResponse($jsonData, $errorMessageJson);
                    break;
                default:
                    return false;
            }

            return false;
        }

        // validate the primary key of db entry
        public function validatePK($modelClassType, &$jsonData)
        {
            switch ($modelClassType)
            {
                case ModelClassTypes::USER:
                    return $this->validateUserPK($jsonData);
                    break;
                case ModelClassTypes::EXERCISE:
                    return $this->validateExercisePK($jsonData);
                    break;
                case ModelClassTypes::SURVEY_QUESTION:
                    return $this->validateSurveyQuestionPK($jsonData);
                    break;
                default:
                    return false;
            }

            return false;
        }

        //sanitizing input
        public function cleanInput($input)
        {
            $retValue = trim($input);
            $retValue = stripslashes($retValue);
            $retValue = htmlspecialchars($retValue);

            return $retValue;
        }

        //validating a string in terms of a length
        public function validateString($input, $length)
        {
            if (strlen($input) <= $length)
            {
                return true;
            }

            return false;
        }
 
        //validating an integer
        public function validateInt($input)
        {
            return ctype_digit($input);
        }

        //validate likert scale answer
        public function validateLikertAnswer($input, $minVal, $maxVal)
        {
            //check if this input type is a valid int
            if (!$this->validateInt($input))
            {
                return false;
            }

            if (intval($input) > $maxVal  || intval($input) < $minVal)
            {
                return false;
            }

            return true;
        }

        //validate user answer to survey question
        private function validateUserSurveyResponse(&$jsonData, &$errorMessageJson)
        {
            $errorMessage = array();

            $userSurveyAnswer = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            //sanitize data
            $userSurveyAnswer["userId"] = $this->cleanInput($userSurveyAnswer["userId"]);

            foreach ($userSurveyAnswer["answers"] as &$a)
            {
                $a["value"] = $this->cleanInput($a["value"]);
                $a["type"] = $this->cleanInput($a["type"]);
            }

            //repack sanitized data
            $jsonData = json_encode($userSurveyAnswer, JSON_INVALID_UTF8_SUBSTITUTE);

            //validate data
            if (!$this->validateInt($userSurveyAnswer["userId"]))
            {
                $errorMessage[0]["content"] = "Invalid user id";
                $errorMessage[0]["success"] = false;
            }

            //validate each answer
            $errorCounter = 1;
            foreach ($userSurveyAnswer["answers"] as $question => $answer)
            {
                //validate question id
                if (!$this->validateInt(strval($question)))
                {
                    $errorMessage[$errorCounter]["content"] = "Invalid question id: ".$question;
                    $errorMessage[$errorCounter]["success"] = false;

                    $errorCounter++;
                }

                //validate question answer
                if ($answer["type"] == SurveyQuestionTypes::LIKERT)
                {
                    if (!$this->validateLikertAnswer($answer["value"], Validation::SUS_LIKERT_MIN, Validation::SUS_LIKERT_MAX))
                    {
                        $errorMessage[$errorCounter]["content"] = "Invalid answer: ".$answer["value"]." for question: ".$question;
                        $errorMessage[$errorCounter]["success"] = false;
    
                        $errorCounter++;
                    }
                }
                else if ($answer["type"] == SurveyQuestionTypes::TEXT)
                {
                    if (!$this->validateString($answer["value"], Validation::SURVEY_TEXT_ANSWER_LENGTH))
                    {
                        $errorMessage[$errorCounter]["content"] = "Invalid answer: ".$answer["value"]." for question: ".$question;
                        $errorMessage[$errorCounter]["success"] = false;

                        $errorCounter++;

                    }
                }
                else
                {
                    $errorMessage[$errorCounter]["content"] = "Invalid answer: ".$answer["value"]." for question: ".$question;
                    $errorMessage[$errorCounter]["success"] = false;

                    $errorCounter++;
                }
            }

            //check if we found any errors
            if (count($errorMessage) == 0)
            {
                return true;
            }

            $errorMessageJson = json_encode($errorMessage);

            return false;
        }

        //validate survey question pk
        private function validateSurveyQuestionPK(&$jsonData)
        {
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $data["questionId"] = $this->cleanInput($data["questionId"]);

            //IMPORTANT: make sure jsonData is set to the sanitized version of the data
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            return $this->validateInt($data["questionId"]);
        }

        //validate survey question
        private function validateSurveyQuestion(&$jsonData, &$errorMessageJson)
        {
            $errorMessage = array();

            $surveyQuestion = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            //sanitize data
            $surveyQuestion["questionId"] = $this->cleanInput($surveyQuestion["questionId"]);
            $surveyQuestion["contents"] = $this->cleanInput($surveyQuestion["contents"]);

            //repack sanitized data
            $jsonData = json_encode($surveyQuestion, JSON_INVALID_UTF8_SUBSTITUTE);

            //validate data
            if (!$this->validateInt($surveyQuestion["questionId"]))
            {
                $errorMessage[0]["content"] = "Invalid question id";
                $errorMessage[0]["success"] = false;
            }

            if (!$this->validateString($surveyQuestion["contents"], Validation::SURVEY_QUESTION_CONTENTS))
            {
                $errorMessage[1]["content"] = "Invalid question contents";
                $errorMessage[1]["success"] = false;
            }

            if (!$this->validateSurveyQuestionType($surveyQuestion["type"]))
            {
                $errorMessage[2]["content"] = "Invalid survey question type";
                $errorMessage[2]["success"] = false;
            }

            //check if we found any errors
            if (count($errorMessage) == 0)
            {
                return true;
            }

            $errorMessageJson = json_encode($errorMessage);

            return false;
        }

        //validate user exercise attempt
        private function validateUserExercise(&$jsonData, &$errorMessageJson)
        {
            $errorMessage = array();

            $userExercise = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            //sanitize data
            $userExercise["userId"] = $this->cleanInput($userExercise["userId"]);
            $userExercise["codeId"] = $this->cleanInput($userExercise["codeId"]);
            $userExercise["mark"] = $this->cleanInput($userExercise["mark"]);
            $userExercise["completed"] = $this->cleanInput($userExercise["completed"]);
            $userExercise["result_vector"] = $this->cleanInput($userExercise["result_vector"]);

            //repack sanitized data
            $jsonData = json_encode($userExercise, JSON_INVALID_UTF8_SUBSTITUTE);

            //validate data
            if (!$this->validateInt($userExercise["userId"]))
            {
                $errorMessage[0]["content"] = "Invalid user id";
                $errorMessage[0]["success"] = false;
            }

            if (!$this->validateInt($userExercise["codeId"]))
            {
                $errorMessage[1]["content"] = "Invalid code id";
                $errorMessage[1]["success"] = false;
            }

            if (!$this->validateInt($userExercise["mark"]))
            {
                $errorMessage[2]["content"] = "Invalid mark value";
                $errorMessage[2]["success"] = false;
            }

            if (!filter_var($userExercise["completed"], FILTER_VALIDATE_BOOLEAN))
            {
                $errorMessage[3]["content"] = "Invalid completed value";
                $errorMessage[3]["success"] = false;
            }

            if (!$this->validateString($userExercise["result_vector"], Validation::RESULT_VECTOR_MAX_LENGTH))
            {
                $errorMessage[4]["content"] = "Invalid result vector";
                $errorMessage[4]["success"] = false;
            }

            //check if we found any errors
            if (count($errorMessage) == 0)
            {
                return true;
            }

            $errorMessageJson = json_encode($errorMessage);

            return false;
        }

        //validate user object
        private function validateUser(&$jsonData, &$errorMessageJson)
        {
            $errorMessage = array();

            $user = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            //sanitize data
            if (isset($user["userId"]))
            {
                $user["userId"] = $this->cleanInput($user["userId"]);
            }

            $user["username"] = $this->cleanInput($user["username"]);
            $user["permissionLevel"] = $this->cleanInput($user["permissionLevel"]);

            //validate id if its set
            if (isset($user["userId"]))
            {
                if (!$this->validateInt($user["userId"]))
                {
                    $errorMessage[0]["content"] = "Invalid user id";
                    $errorMessage[0]["success"] = false;
                }
            }

            //validate username
            if (!$this->validateString($user["username"], Validation::USERNAME_LENGTH))
            {
                $errorMessage[1]["content"] = "Invalid username";
                $errorMessage[1]["success"] = false;
            }

            //validate permission level
            if (!$this->validateUserPermissionLevel($user["permissionLevel"]))
            {
                $errorMessage[3]["content"] = "Invalid permission level";
                $errorMessage[3]["success"] = false;
            }

            //repack sanitized data
            $jsonData = json_encode($user, JSON_INVALID_UTF8_SUBSTITUTE);

            //check if we found any errors
            if (count($errorMessage) == 0)
            {
                return true;
            }

            $errorMessageJson = json_encode($errorMessage);

            

            return false;
        }

        private function validateUserPermissionLevel($input)
        {
            //check if this permission is a valid int
            if (!$this->validateInt($input))
            {
                return false;
            }

            //check if this permission level is an actual permission level in the system
            $permissions = new PermissionLevels();

            if ($permissions->getPermissionLevel(intval($input)) == "Error finding status")
            {
                return false;
            }

            return true;
        }

        private function validateSurveyQuestionType($input)
        {
            //check if this is a valid int
            if (!$this->validateInt($input))
            {
                return false;
            }

            //check if this survey question type an actual survey question type in the system
            $permissions = new SurveyQuestionTypes();

            if ($permissions->getQuestionType(intval($input)) == "Error finding type")
            {
                return false;
            }

            return true;
        }

        private function validateInputType($input)
        {
            //check if this input type is a valid int
            if (!$this->validateInt($input))
            {
                return false;
            }

            //check if this answer type is an actual answer type in the system
            $answerTypes = new AnswerTypes();

            if ($answerTypes->getAnswerType(intval($input)) == "Error finding status")
            {
                return false;
            }

            return true;
        }

        private function validateExerciseType($input)
        {
            //check if this input type is a valid int
            if (!$this->validateInt($input))
            {
                return false;
            }

            //check if this exercise type is an actual exercise type in the system
            $exerciseTypes = new ExerciseTypes();

            if ($exerciseTypes->getExerciseType(intval($input)) == "Error finding status")
            {
                return false;
            }

            return true;
        }

        //validate user PK
        private function validateUserPK(&$jsonData)
        {
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $data["userId"] = $this->cleanInput($data["userId"]);

            //IMPORTANT: make sure jsonData is set to the sanitized version of the data
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            return $this->validateInt($data["userId"]);
        }

        //validate exercise object
        private function validateExercise(&$jsonData, &$errorMessageJson)
        {
            $errorMessage = array();

            $exercise = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            //sanitize data
            if (isset($exercise["codeId"]))
            {
                $exercise["codeId"] = $this->cleanInput($exercise["codeId"]);
            }

            $exercise["title"] = $this->cleanInput($exercise["title"]);

            if (isset($exercise["description"]))
            {
                $exercise["description"] = $this->cleanInput($exercise["description"]);
            }

            $exercise["exerciseFile"] = $this->cleanInput($exercise["exerciseFile"]);

            $exercise["visible"] = $this->cleanInput($exercise["visible"]);
            $exercise["availability"] = $this->cleanInput($exercise["availability"]);
            $exercise["time_limit"] = $this->cleanInput($exercise["time_limit"]);

            //validate id if its set
            if (isset($exercise["codeId"]))
            {
                if (!$this->validateInt($exercise["codeId"]))
                {
                    $errorMessage[0]["content"] = "Invalid exercise id";
                    $errorMessage[0]["success"] = false;
                }
            }

            //validate title
            if (!$this->validateString($exercise["title"], Validation::EXERCISE_TITLE_LENGTH))
            {
                $errorMessage[1]["content"] = "Invalid title";
                $errorMessage[1]["success"] = false;
            }

            //validate description if its set
            if (isset($exercise["description"]))
            {
                if (!$this->validateString($exercise["description"], Validation::EXERCISE_DESCRIPTION_LENGTH))
                {
                    $errorMessage[2]["content"] = "Invalid description";
                    $errorMessage[2]["success"] = false;
                }
            }

            //validate exercise file
            if (!$this->validateFileName($exercise["exerciseFile"], Validation::EXERCISE_EXERCISEFILE_LENGTH, "json"))
            {
                $errorMessage[3]["content"] = "Invalid exercise file location";
                $errorMessage[3]["success"] = false;
            }

            //validate availability
            if (!$this->validateUserPermissionLevel($exercise["availability"]))
            {
                $errorMessage[5]["content"] = "Invalid availability";
                $errorMessage[5]["success"] = false;
            }

            //validate type
            if (!$this->validateExerciseType($exercise["type"]))
            {
                $errorMessage[6]["content"] = "Invalid exercise type";
                $errorMessage[6]["success"] = false;
            }

            //validate available points
            if (!$this->validateInt($exercise["availablePoints"]))
            {
                $errorMessage[7]["content"] = "Invalid available points";
                $errorMessage[7]["success"] = false;
            }

            //validate time limit
            if (!$this->validateInt($exercise["time_limit"]))
            {
                $errorMessage[8]["content"] = "Invalid time limit";
                $errorMessage[8]["success"] = false;
            }

            //repack sanitized data
            $jsonData = json_encode($exercise, JSON_INVALID_UTF8_SUBSTITUTE);

            //check if we found any errors
            if (count($errorMessage) == 0)
            {
                return true;
            }

            $errorMessageJson = json_encode($errorMessage);

           

            return false;
        }

        private function validateFileName($input, $length, $extension)
        {
            //check length
            if (!$this->validateString($input, $length))
            {
                return false;
            }

            //check extension
            if (pathinfo($input, PATHINFO_EXTENSION) != $extension)
            {
                return false;
            }

            return true;
        }

        //validate exercise PK
        private function validateExercisePK(&$jsonData)
        {
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $data["codeId"] = $this->cleanInput($data["codeId"]);

            //IMPORTANT: make sure jsonData is set to the sanitized version of the data
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            return $this->validateInt($data["codeId"]);
        }
    }

?>