<?php
    //action script to create an survey question
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/SurveyQuestionController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionCreateSurveyQuestion()
    {
        $surveyQuestionController = new SurveyQuestionController(ModelClassTypes::SURVEY_QUESTION);

        $surveyQuestionController -> createSurveyQuestion();
    }

    actionCreateSurveyQuestion();
?>