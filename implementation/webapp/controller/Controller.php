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

        //url variables
        protected $successPathVariables;
        protected $failurePathVariables;

        //message json objects to add as a url variable before the user is send somewhere
        protected $successMessageJson;
        protected $failureMessageJson;

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
        }

        private function genericControllerOperation($type, $jsonData, $validated)
        {
            if (!$validated)
            {
                $path = $this->failurePath;

                //add failureMessageJson to other url variables
                $failurePathVariables .= "&?message=".urlencode($this->failureMessageJson);

                if ($this->failurePathVariables)
                {
                    $path .= $this->failurePathVariables;
                }

                echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
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
                    $path = $this->successPath;

                    //add successMessageJson to other url variables
                    $successPathVariables .= "&?message=".urlencode($this->successMessageJson);
                    
                    if ($this->successPathVariables)
                    {
                        $path .= $this->successPathVariables;
                    }

                    echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
                }
                else
                {
                    $path = $this->failurePath;

                    //add failureMessageJson to other url variables
                    $failurePathVariables .= "&?message=".urlencode($this->failureMessageJson);

                    if ($this->failurePathVariables)
                    {
                        $path .= $this->failurePathVariables;
                    }

                    echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
                }
            }  
        }

        private function prepareCreateUpdate(&$jsonData)
        {
            //sanitize and validate input
            $errorMessagesJSON = null;

            $validated = $this->validationObj->validate($this->modelObjClass, $jsonData, $errorMessagesJSON);

            if ($errorMessagesJSON)
            {
                //add error messages to failure message json
                $errorMessages = json_decode($errorMessagesJSON, JSON_INVALID_UTF8_SUBSTITUTE);

                $failureMessage = json_decode($this->failureMessageJson, JSON_INVALID_UTF8_SUBSTITUTE);

                for ($errorMessages as $error)
                {
                    array_push($failureMessage, $error);
                }

                $this->failureMessageJson = json_encode($failureMessage);
            }
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function create($jsonData)
        {
            prepareCreateUpdate($jsonData);

            $this->genericControllerOperation(Controller::CREATE_OPERATION, $jsonData, $validated);
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function update($jsonData)
        {
            prepareCreateUpdate($jsonData);

            $this->genericControllerOperation(Controller::UPDATE_OPERATION, $jsonData, $validated);
        }

        //parameters: object as json string with data labels and data values that represent the primary key of this record
        public function delete($jsonData)
        {
            //sanitize and validate primary key input
            $validated = $this->validationObj->validatePK($this->modelObjClass, $jsonData);

            $this->genericControllerOperation(Controller::DELETE_OPERATION, $jsonData, $validated);
        }
    }


?>