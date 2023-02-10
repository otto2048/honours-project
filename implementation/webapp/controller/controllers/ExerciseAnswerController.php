<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseAnswerModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    class ExerciseAnswerController extends Controller
    {
        public function deleteExerciseAnswer()
        {
            //Exercise input into json object
            $data = new \stdClass();
            $data -> answerId = $_POST["itemId"];
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            return parent::delete($jsonData);
        }

        public function createExerciseAnswer()
        {
            //exercise input into json object
            $data = new \stdClass();
            $data -> codeId_fk = $_POST["codeId"];
            $data -> input = $_POST["input"];
            $data -> inputType = $_POST["inputType"];
            $data -> output = $_POST["output"];

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully created exercise answer";

            $this->successPathVariables["message"] = json_encode($successMessage);
            $this->successPathVariables["id"] = $this->validationObj->cleanInput($data->codeId_fk);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to create exercise answer. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);
            $this->failurePathVariables["id"] = $this->validationObj->cleanInput($data->codeId_fk);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/exercises/exercise.php";
            $this->failurePath = "/honours/webapp/view/adminArea/exercises/exercise.php";

            return parent::create($jsonData);
        }
    }

?>