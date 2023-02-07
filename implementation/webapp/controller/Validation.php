<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    class Validation
    {
        //database data constraints
        const USERNAME_LENGTH = 50;

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

            //validate container port
            // if (!$this->validateInt($user["containerPort"]))
            // {
            //     $errorMessage[2]["content"] = "Invalid container port";
            //     $errorMessage[2]["success"] = false;
            // }

            //TODO: replace temp container port code once all users are being assigned a port
            if (isset($user["containerPort"]))
            {
            $user["containerPort"] = $this->cleanInput($user["containerPort"]);

                if (!$this->validateInt($user["containerPort"]))
                {
                    $errorMessage[2]["content"] = "Invalid container port";
                    $errorMessage[2]["success"] = false;
                }
            }
            

            //validate permission level
            if (!$this->validateUserPermissionLevel($user["permissionLevel"]))
            {
                $errorMessage[3]["content"] = "Invalid permission level";
                $errorMessage[3]["success"] = false;
            }

            //check if we found any errors
            if (count($errorMessage) == 0)
            {
                return true;
            }

            $errorMessageJson = json_encode($errorMessage);

            //repack sanitized data
            $jsonData = json_encode($user, JSON_INVALID_UTF8_SUBSTITUTE);

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