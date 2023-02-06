<?php
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

        //validate user object
        private function validateUser(&$jsonData)
        {
            return false;
        }

        //validate user PK
        //TODO: change this, its not using the primary key
        private function validateUserPK(&$jsonData)
        {
            $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

            $data["username"] = $this->cleanInput($data["username"]);

            //IMPORTANT: make sure jsonData is set to the sanitized version of the data
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