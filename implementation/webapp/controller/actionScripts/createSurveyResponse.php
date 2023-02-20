<?php
    //action script to create an survey response
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/UserSurveyController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionCreateSurveyResponse()
    {
        $surveyUserController = new UserSurveyController(ModelClassTypes::USER_SURVEY);

        $surveyUserController -> createSurveyResponse();
    }

    actionCreateSurveyResponse();
?>