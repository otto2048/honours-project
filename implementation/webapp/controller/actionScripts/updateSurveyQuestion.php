<?php

    //action script to update a survey question
    
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/controllers/SurveyQuestionController.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function actionUpdateSurveyQuestion()
    {
        $surveyQuestionController = new SurveyQuestionController(ModelClassTypes::SURVEY_QUESTION);

        $surveyQuestionController -> updateSurveyQuestion();
    }

    actionUpdateSurveyQuestion();
?>