<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    class ExerciseController extends Controller
    {
        public function deleteExercise()
        {
            //Exercise input into json object
            $data = new \stdClass();
            $data -> codeId = $_GET["codeId"];
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to delete exercise. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);
            $this->failurePathVariables["id"] = $this->validationObj->cleanInput($data->codeId);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully deleted exercise";

            $this->successPathVariables["message"] = json_encode($successMessage);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/exercises/exerciseDashboard.php";
            $this->failurePath = "/honours/webapp/view/adminArea/exercises/exercise.php";

            return parent::delete($jsonData);
        }

        public function createExercise()
        {
            //exercise input into json object
            $data = new \stdClass();
            $data -> title = $_POST['title'];
            $data -> description = $_POST['description'];
            
            $data -> exerciseFile = $_POST['exerciseFile'];
            $data -> instructionsFile = $_POST['instructionsFile'];

            if (isset($_POST['visible']))
            {
                $data -> visible = true;
            }
            else
            {
                $data -> visible = false;
            }

            $data -> availability = $_POST['availability'];
            $data -> type = $_POST['type'];

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully created exercise";

            $this->successPathVariables["message"] = json_encode($successMessage);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to create exercise. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/exercises/exerciseDashboard.php";
            $this->failurePath = "/honours/webapp/view/adminArea/exercises/exerciseDashboard.php";

            return parent::create($jsonData);
        }

        public function updateExercise()
        {
            //exercise input into json object
            $data = new \stdClass();
            $data -> title = $_POST['title'];
            $data -> description = $_POST['description'];
            
            $data -> exerciseFile = $_POST['exerciseFile'];
            $data -> instructionsFile = $_POST['instructionsFile'];

            if (isset($_POST['visible']))
            {
                $data -> visible = true;
            }
            else
            {
                $data -> visible = false;
            }

            $data -> availability = $_POST['availability'];
            $data -> type = $_POST['type'];
            
            $data -> codeId = $_POST['codeId'];

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully updated exercise";

            $this->successPathVariables["message"] = json_encode($successMessage);
            $this->successPathVariables["id"] = $this->validationObj->cleanInput($data->codeId);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to update exercise. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);
            $this->failurePathVariables["id"] = $this->validationObj->cleanInput($data->codeId);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/exercises/exercise.php";
            $this->failurePath = "/honours/webapp/view/adminArea/exercises/updateExercise.php";

            return parent::update($jsonData);
        }
    }

?>