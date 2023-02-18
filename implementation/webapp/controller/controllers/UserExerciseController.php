<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    class UserExerciseController extends Controller
    {
        public function logUserExerciseAttempt()
        {
            //input into json object
            $data = new \stdClass();
            $data->userId = $_SESSION["userId"];
            $data->codeId = $_POST["codeId"];
            $data->mark = $_POST["mark"];
            $data->completed = true;

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Exercise submitted successfully";

            $this->successPathVariables["message"] = json_encode($successMessage);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to submit exercise. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);
 
            return parent::create($jsonData);
        }
    }

?>