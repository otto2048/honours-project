<?php
    //action script to delete a survey question
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/SurveyQuestionController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionDeleteSurveyQuestion()
    {
        $surveyQuestionController = new SurveyQuestionController(ModelClassTypes::SURVEY_QUESTION);

        $surveyQuestionController -> deleteSurveyQuestion($_GET['id']);
    }

    actionDeleteSurveyQuestion();
?>