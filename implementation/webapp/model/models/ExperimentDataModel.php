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
                        // get available pretest exercises        
                        $jsonExercises = $exerciseModel->getAvailableExercises($user["permissionLevel"], ExerciseTypes::PRETEST, true);

                        if ($jsonExercises)
                        {
                            $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                            if (!isset($exercises["isempty"]))
                            {
                                foreach ($exercises as $exercise)
                                {
                                    //get mark information
                                    $markJson = $userExerciseModel->getExerciseMark($user["userId"], $exercise["codeId"]);

                                    //get result vector
                                    $resultVectorJson = $userExerciseModel->getExerciseResultVector($user["userId"], $exercise["codeId"]);
                                
                                    if ($markJson && $resultVectorJson)
                                    {
                                        $mark = json_decode($markJson, JSON_INVALID_UTF8_SUBSTITUTE);
                                        $resultVector = json_decode($resultVectorJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                        $obj = new \stdClass;

                                        $obj -> username = $user["username"];
                                        $obj -> permission = $userPermissions->getPermissionLevel($user["permissionLevel"]);
                                        $obj -> exercise = $exercise["codeId"];
                                        $obj -> exerciseType = $exerciseTypes->getExerciseType($exercise["type"]);
                                        $obj -> points = $mark["points"];
                                        $obj -> total = $mark["total"];
                                        $obj -> resultVector = $resultVector[0]["result_vector"]."_";

                                        array_push($data, $obj);
                                    }
                                }
                            }
                        }
                    }

                    foreach ($userData as $user)
                    {
                        // get available posttest exercises        
                        $jsonExercises = $exerciseModel->getAvailableExercises($user["permissionLevel"], ExerciseTypes::POSTTEST, true);

                        if ($jsonExercises)
                        {
                            $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                            if (!isset($exercises["isempty"]))
                            {
                                foreach ($exercises as $exercise)
                                {
                                    //get mark information
                                    $markJson = $userExerciseModel->getExerciseMark($user["userId"], $exercise["codeId"]);

                                    //get result vector
                                    $resultVectorJson = $userExerciseModel->getExerciseResultVector($user["userId"], $exercise["codeId"]);
                                
                                    if ($markJson && $resultVectorJson)
                                    {
                                        $mark = json_decode($markJson, JSON_INVALID_UTF8_SUBSTITUTE);
                                        $resultVector = json_decode($resultVectorJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                        $obj = new \stdClass;

                                        $obj -> username = $user["username"];
                                        $obj -> permission = $userPermissions->getPermissionLevel($user["permissionLevel"]);
                                        $obj -> exercise = $exercise["codeId"];
                                        $obj -> exerciseType = $exerciseTypes->getExerciseType($exercise["type"]);
                                        $obj -> points = $mark["points"];
                                        $obj -> total = $mark["total"];
                                        $obj -> resultVector = $resultVector[0]["result_vector"]."_";

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

    // need to change server permissions to run this code
    $test = new ExperimentDataModel();

    $json = $test->getExperimentData();

    $actual = json_decode($json, JSON_INVALID_UTF8_SUBSTITUTE);

    $fp = fopen('experiment.csv', 'w');

    fputcsv($fp, array('user','group','exercise', 'exercise type', 'points', 'total'));

    foreach ($actual as $fields) {
        fputcsv($fp,$fields);
    }

    fclose($fp);

    $json = $test->getSurveyData();

    $actual = json_decode($json, JSON_INVALID_UTF8_SUBSTITUTE);

    $fp = fopen('survey.csv', 'w');

    fputcsv($fp, array('user','group','sus', 'further comments'));

    foreach ($actual as $fields) {
        fputcsv($fp,$fields);
    }

    fclose($fp);
?>