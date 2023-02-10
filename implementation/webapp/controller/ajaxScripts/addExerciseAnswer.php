<?php

    //TODO: move this into a Controller as the page will have to be reloaded to get the new ID

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ModelClassTypes.php");

    function addExerciseAnswer()
    {
        $validate = new Validation();

        //validate and sanitize input
        $data = new \stdClass();
        $data -> codeId_fk = $_POST["codeId"];
        $data -> input = $_POST["input"];
        $data -> inputType = $_POST["inputType"];
        $data -> output = $_POST["output"];
        $jsonData = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $errorMsgs = "";

        if ($validate->validate(ModelClassTypes::EXERCISE_ANSWER, $jsonData, $errorMsgs))
        {
            $exerciseModel = new ExerciseModel();

            $creation = $exerciseModel->addExerciseAnswer($jsonData);

            if ($creation)
            {
                //output new row in json
                $result["success"] = true;

                

                $result["content"] = "";
            }
            else
            {
                echo 0;
            }

        }
        else
        {
            echo $errorMsgs;
        }

    }

    addExerciseAnswer();
?>