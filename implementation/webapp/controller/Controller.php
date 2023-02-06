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

        protected $successPath;
        protected $failurePath;

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
        }

        private function genericControllerOperation($type, $jsonData, $validated)
        {
            if (!$validated)
            {
                $path = $this->failurePath;

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

                    if ($this->successPathVariables)
                    {
                        $path .= $this->successPathVariables;
                    }

                    echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
                }
                else
                {
                    $path = $this->failurePath;

                    if ($this->failurePathVariables)
                    {
                        $path .= $this->failurePathVariables;
                    }

                    echo '<script type="text/javascript">window.open("'.$path.'", name="_self")</script>';
                }
            }  
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function create($jsonData)
        {
            //sanitize and validate input
            $errorMessagesJSON = null;

            $validated = $this->validationObj->validate($this->modelObjClass, $jsonData, $errorMessagesJSON);

            if ($errorMessagesJSON)
            {
                //add error messages to failure path
                $this->failurePathVariables .= "&?message=".urlencode($errorMessagesJSON);
            }

            $this->genericControllerOperation(Controller::CREATE_OPERATION, $jsonData, $validated);
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function update($jsonData)
        {
            //sanitize and validate input
            $errorMessagesJSON = null;

            $validated = $this->validationObj->validate($this->modelObjClass, $jsonData, $errorMessagesJSON);

            if ($errorMessagesJSON)
            {
                //add error messages to failure path
                $this->failurePathVariables .= "&?message=".urlencode($errorMessagesJSON);
            }

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