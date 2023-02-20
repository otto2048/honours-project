<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserSurveyModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    class UserSurveyController extends Controller
    {
        //create survey response
        public function createSurveyResponse()
        {
            //user input into json object
            $data = new \stdClass();
            $data->answers = new \stdClass();

            foreach ($_POST as $name => $val)
            {
                if ($name != "button")
                {
                    $data -> answers -> $name = $val;
                }
            }

            $data->userId=$_SESSION["userId"];

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Survey response submitted successfully!";

            $this->successPathVariables["message"] = json_encode($successMessage);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to submit survey response. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/index.php";
            $this->failurePath = "/honours/webapp/view/userArea/survey.php";
 
            return parent::create($jsonData);
        }
    }

?>