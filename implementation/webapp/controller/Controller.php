<?php
    //include validation
    require_once("Validation.php");

    abstract class Controller
    {
        //validation object
        private $validationObj;

        //model object
        private $modelObj;
        private $modelObjClass;

        protected $successPath;
        protected $failurePath;

        const UPDATE_OPERATION = 0;
        const CREATE_OPERATION = 1;
        const DELETE_OPERATION = 2;

        public function __construct($modelClassName)
        {
            //init validation object
            $this->validationObj = new Validation();

            //init model object
            $this->modelObj = new \ReflectionClass($modelClassName);
            $this->modelObjClass = $modelClassName;

            //begin the session
            session_start();
        }

        private function genericControllerOperation($type, $jsonData, $validated)
        {
            if ($validated)
            {
                echo '<script type="text/javascript">window.open('.$successPath.', name="_self")</script>';
            }
            else
            {
                //call create in model
                switch ($type)
                {
                    case Controller::UPDATE_OPERATION:
                        $result = $this->modelObj->update($jsonData);
                        break;
                    case Controller::CREATE_OPERATION:
                        $result = $this->modelObj->create($jsonData);
                        break;
                    case Controller::DELETE_OPERATION:
                        $result = $this->modelObj->delete($jsonData);
                        break;   
                }

                if ($result)
                {
                    echo '<script type="text/javascript">window.open('.$successPath.', name="_self")</script>';
                }
                else
                {
                    echo '<script type="text/javascript">window.open('.$failurePath.', name="_self")</script>';
                }
            }  
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function create($jsonData)
        {
            //sanitize and validate input
            $validated = $this->validationObj->validate($this->modelObjClass, $jsonData);

            $this->genericControllerOperation(Controller::CREATE_OPERATION, $jsonData, $validated);
        }

        //parameters: object as json string with data labels and data values to be inserted into the database
        public function update($jsonData)
        {
            //sanitize and validate input
            $validated = $this->validationObj->validate($this->modelObjClass, $jsonData);

            $this->genericControllerOperation(Controller::UPDATE_OPERATION, $jsonData, $validated);
        }

        //parameters: object as json string with data labels and data values that represent the primary key of this record
        public function delete()
        {
            //sanitize and validate primary key input
            $validated = $this->validationObj->validatePK($this->modelObjClass, $jsonData);

            $this->genericControllerOperation(Controller::DELETE_OPERATION, $jsonData, $validated);
        }
    }


?>