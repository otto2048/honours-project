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
    }

?>