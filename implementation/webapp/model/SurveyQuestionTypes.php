<?php

    class SurveyQuestionTypes
    {
        const LIKERT = 1;
        const TEXT = 2;

        //get string value associated with question type
        public function getQuestionType($type)
        {
            switch($type)
            {
                case SurveyQuestionTypes::LIKERT:
                    return "Likert";
                case SurveyQuestionTypes::TEXT:
                    return "Text";
                default:
                    return "Error finding type";
            }
        }
    }

    
?>