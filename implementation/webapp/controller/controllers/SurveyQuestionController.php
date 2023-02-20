<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/SurveyQuestionModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    class SurveyQuestionController extends Controller
    {
        public function createSurveyQuestion()
        {
            //user input
            $data = new \stdClass();
            $data -> questionId = $_POST['questionId'];
            $data -> contents = $_POST['contents'];

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully created survey question";

            $this->successPathVariables["message"] = json_encode($successMessage);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to create survey question. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/survey/surveyDashboard.php";
            $this->failurePath = "/honours/webapp/view/adminArea/survey/surveyDashboard.php";
 
            return parent::create($jsonData);
        }

        public function deleteSurveyQuestion($questionId)
        {
            //user input into json object
            $data = new \stdClass();
            $data -> questionId = $questionId;
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to delete survey question. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);
            $this->failurePathVariables["id"] = $this->validationObj->cleanInput($data->questionId);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully deleted survey question";

            $this->successPathVariables["message"] = json_encode($successMessage);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/survey/surveyDashboard.php";
            $this->failurePath = "/honours/webapp/view/adminArea/survey/survey.php";

            return parent::delete($jsonData);
        }

        public function updateSurveyQuestion()
        {
            //user input into json object
            $data = new \stdClass();
            $data -> contents = $_POST['contents'];
            $data -> questionId = $_POST['questionId'];

            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            //prepare success message
            $successMessage[0]["success"] = true;
            $successMessage[0]["content"] = "Successfully updated survey question";

            $this->successPathVariables["message"] = json_encode($successMessage);
            $this->successPathVariables["id"] = $this->validationObj->cleanInput($data->questionId);

            //prepare error message
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "Failed to update survey question. Try again?";

            $this->failurePathVariables["message"] = json_encode($failureMessage);
            $this->failurePathVariables["id"] = $this->validationObj->cleanInput($data->questionId);

            //set success and failure paths
            $this->successPath = "/honours/webapp/view/adminArea/survey/survey.php";
            $this->failurePath = "/honours/webapp/view/adminArea/survey/updateSurvey.php";

            return parent::update($jsonData);
        }
    }

?>