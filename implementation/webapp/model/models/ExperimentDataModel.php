<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/Model.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserSurveyModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/SurveyQuestionTypes.php");

    class ExperimentDataModel extends Model
    {
        public function getExperimentData()
        {
            // get all users
            $userModel = new UserModel();
            $exerciseModel = new ExerciseModel();
            $userExerciseModel = new UserExerciseModel();
            $userPermissions = new PermissionLevels();
            $exerciseTypes = new ExerciseTypes();

            $jsonUserData = $userModel->getAllUsers();

            $data = array();

            if ($jsonUserData)
            {
                $userData = json_decode($jsonUserData, JSON_INVALID_UTF8_SUBSTITUTE);

                if (!isset($userData["isempty"]))
                {
                    // for each user, get their pre and post test scores
                    foreach ($userData as $user)
                    {
                        // get available exercises        
                        $jsonExercises = $exerciseModel->getAvailableExercises($user["permissionLevel"], null, true);

                        if ($jsonExercises)
                        {
                            $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                            if (!isset($exercises["isempty"]))
                            {
                                foreach ($exercises as $exercise)
                                {
                                    //get mark information
                                    $markJson = $userExerciseModel->getExerciseMark($user["userId"], $exercise["codeId"]);
                                
                                    if ($markJson)
                                    {
                                        $mark = json_decode($markJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                        $obj = new \stdClass;

                                        $obj -> username = $user["username"];
                                        $obj -> permission = $userPermissions->getPermissionLevel($user["permissionLevel"]);
                                        $obj -> exercise = $exercise["codeId"];
                                        $obj -> exerciseType = $exerciseTypes->getExerciseType($exercise["type"]);
                                        $obj -> points = $mark["points"];
                                        $obj -> total = $mark["total"];

                                        array_push($data, $obj);
                                    }
                                }
                            }
                        }

                    }
                }

            }

            return json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        }

        public function getSurveyData()
        {
            $userModel = new UserModel();
            $userPermissions = new PermissionLevels();
            $exerciseTypes = new ExerciseTypes();
            $userSurveyModel = new UserSurveyModel();

            // get all users
            $jsonUserData = $userModel->getAllUsers();

            $data = array();

            if ($jsonUserData)
            {
                $userData = json_decode($jsonUserData, JSON_INVALID_UTF8_SUBSTITUTE);

                if (!isset($userData["isempty"]))
                {
                    foreach ($userData as $user)
                    {
                        // get further comments
                        $surveyJSON = $userSurveyModel->filterUserAnswerByType($user["userId"], SurveyQuestionTypes::TEXT);

                        if ($surveyJSON)
                        {
                            $surveyAnswers = json_decode($surveyJSON, JSON_INVALID_UTF8_SUBSTITUTE);

                            $obj = new \stdClass;

                            $obj -> username = $user["username"];
                            $obj -> permission = $userPermissions->getPermissionLevel($user["permissionLevel"]);
                            $obj -> sus = $user["SUS_Score"];

                            if (!isset($surveyAnswers["isempty"]))
                            {
                                $obj -> surveyTextAnswer = $surveyAnswers[0]["answer"];
                            }
                            else
                            {
                                $obj -> surveyTextAnswer = NULL;
                            }

                            array_push($data, $obj);
                        }

                    }
                }

            }

            return json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }

    $test = new ExperimentDataModel();

    $json = $test->getExperimentData();

    $actual = json_decode($json, JSON_INVALID_UTF8_SUBSTITUTE);

    echo "<pre>";
    var_dump($actual);
    echo "</pre>";

    $json = $test->getSurveyData();

    $actual = json_decode($json, JSON_INVALID_UTF8_SUBSTITUTE);

    echo "<pre>";
    var_dump($actual);
    echo "</pre>";
?>