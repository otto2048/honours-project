<?php

    //include validation
    require_once("Validation.php");

    abstract class Controller
    {
        //validation object
        protected $validationObj;

        //model object
        protected $modelObj;
        private $modelObjClass;

        //urls to send the user to
        protected $successPath;
        protected $failurePath;

        //url variables, as objects
        protected $successPathVariables;
        protected $failurePathVariables;

        const UPDATE_OPERATION = 0;
        const CREATE_OPERATION = 1;
        const DELETE_OPERATION = 2;

        public function __construct($modelClassName)
        {
            //init validation object
            $this->validationObj = new Validation();

            //init model object
            $this->modelObj = new $modelClassName;
            $this->modelObjClass = $modelClassName;

            //begin the session
            session_start();

            //init path variables
            $this->successPathVariables = array();
            $this->failurePathVariables = array();
        }

        private function genericControllerOperation($type, $jsonData, $validated)
        {
            if (!$validated)
            {
                if ($this->failurePath)
                {
                    $path = $this->constructPath($this->failurePath, $this->failurePathVariables);
    
                    echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
                }

                return false;
            }
            else
            {
                //call create in model
                switch ($type)
                {
                    case Controller::UPDATE_OPERATION:
                        $result = $this->modelObj->updateData($jsonData);
                        break;
                    case Controller::CREATE_OPERATION:
                        $result = $this->modelObj->createData($jsonData);
                        break;
                    case Controller::DELETE_OPERATION:
                        $result = $this->modelObj->deleteData($jsonData);
                        break;   
                }

                if ($result)
                {
                    if ($this->successPath)
                    {
                        $path = $this->constructPath($this->successPath, $this->successPathVariables);
    
                        echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
                    }

                    return true;
                }
                else
                {
                    if ($this->failurePath)
                    {
                        $path = $this->constructPath($this->failurePath, $this->failurePathVariables);

                        echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
                    }

                    return false;
                }
            }  
        }

        protected function constructPath($pathUrl, $variables)
        {
            $path = $pathUrl;

            //add path variables to path
            if (count($variables))
            {
                $path .= "?";
            }
            foreach ($variables as $key => $variable)
            {
                $path .= $key."=".urlencode($variable)."&";
            }

            return $path;
        }

        private function prepareCreateUpdate(&$jsonData, &$validated)
        {
            //sanitize and validate input
            $errorMessagesJSON = null;

            $validated = $this->validationObj->validate($this->modelObjClass, $jsonData, $errorMessagesJSON);

            if ($errorMessagesJSON)
            {
                //add error messages to failure message json
                $errorMessages = json_decode($errorMessagesJSON, JSON_INVALID_UTF8_SUBSTITUTE);

                $failureMessage = json_decode($this->failurePathVariables["message"], JSON_INVALID_UTF8_SUBSTITUTE);

                foreach ($errorMessages as $error)
                {
                    array_push($failureMessage, $error);
                }

                $this->failurePathVariables["message"] = json_encode($failureMessage);
            }
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function create($jsonData)
        {
            $validated = false;
            $this->prepareCreateUpdate($jsonData, $validated);

            return $this->genericControllerOperation(Controller::CREATE_OPERATION, $jsonData, $validated);
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function update($jsonData)
        {
            $validated = false;
            $this->prepareCreateUpdate($jsonData, $validated);

            return $this->genericControllerOperation(Controller::UPDATE_OPERATION, $jsonData, $validated);
        }

        //parameters: object as json string with data labels and data values that represent the primary key of this record
        public function delete($jsonData)
        {
            //sanitize and validate primary key input
            $validated = $this->validationObj->validatePK($this->modelObjClass, $jsonData);

            return $this->genericControllerOperation(Controller::DELETE_OPERATION, $jsonData, $validated);
        }
    }


?>