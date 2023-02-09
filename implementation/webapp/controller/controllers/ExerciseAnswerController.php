<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseAnswerModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Controller.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    class ExerciseController extends Controller
    {
        public function deleteExerciseAnswer()
        {
            //Exercise input into json object
            $data = new \stdClass();
            $data -> answerId = $_POST["itemId"];
            $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

            return parent::delete($jsonData);
        }
    }

?>