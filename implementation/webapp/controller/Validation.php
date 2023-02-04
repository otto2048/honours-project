<?php

    class Validation
    {
        public function validate($modelClassType, &$jsonData)
        {
            return true;
        }

        public function validatePK($modelClassType, &$jsonData)
        {
            return true;
        }

        //sanitizing input
        public function cleanInput($input)
        {
            $retValue = trim($input);
            $retValue = stripslashes($retValue);
            $retValue = htmlspecialchars($retValue);

            return $retValue;
        }
    }

?>