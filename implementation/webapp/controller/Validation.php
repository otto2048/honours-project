<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    class Validation
    {
        //database data constraints
        const USERNAME_LENGTH = 50;

        public function validate($modelClassType, &$jsonData)
        {
            switch ($modelClassType)
            {
                case ModelClassTypes::USER:
                    return $this->validateUser($jsonData);
                    break;
                case ModelClassTypes::EXERCISE:
                    return $this->validateExercise($jsonData);
                    break;
                default:
                    return false;
            }

            return false;
        }

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
                default:
                    return false;
            }

            return false;
        }

        //sanitizing input
        private function cleanInput($input)
        {
            $retValue = trim($input);
            $retValue = stripslashes($retValue);
            $retValue = htmlspecialchars($retValue);

            return $retValue;
        }

        //validating a string in terms of a length
        private function validateString($input, $length)
        {
            if (strlen($input) <= $length)
            {
                return true;
            }

            return false;
        }
 
        //validating an integer
        private function validateInt($input)
        {
            return ctype_digit($input);
        }

        //validate user object
        private function validateUser(&$jsonData)
        {
            return false;
        }

        //validate user PK
        private function validateUserPK(&$jsonData)
        {
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $data["username"] = $this->cleanInput($data["username"]);

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            return $this->validateString($data["username"], Validation::USERNAME_LENGTH);
        }

        //validate exercise object
        private function validateExercise(&$jsonData)
        {
            return false;

        }

        //validate exercise PK
        private function validateExercisePK(&$jsonData)
        {
            return false;

        }
    }

?>