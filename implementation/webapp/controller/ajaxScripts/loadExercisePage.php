<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    function loadExercisePage()
    {
        $validate = new Validation();

        //validate and sanitize input
        $pageNumInput = $validate->cleanInput($_POST["pageNum"]);
        $pageSizeInput = $validate->cleanInput($_POST["pageSize"]);
        
        if (!$validate->validateInt($pageNumInput) || !$validate->validateInt($pageSizeInput))
        {
            return;
        }
        
        $pageLimit = 0;

        $exerciseModel = new ExerciseModel();

        $jsonExerciseData = $exerciseModel->getExercises(intval($pageNumInput), intval($pageSizeInput), $pageLimit);

        if ($jsonExerciseData)
        {
            $exerciseData = json_decode($jsonExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);

            if (!isset($exerciseData["isempty"]))
            {
                //display current permission
                $permission = new PermissionLevels();
                $types = new ExerciseTypes();

                //display exercise data
                foreach ($exerciseData as $row)
                {
                    echo '<tr>';
                    echo '<td>'.$row["codeId"].'</td>';

                    echo '<td><u><a href="exercise.php?id='.$row["codeId"].'" class="moreInfoLink">'.$row["title"].'</a></u></td>';

                    echo '<td class="d-none d-sm-none d-md-table-cell">';
                    if ($row["description"])
                    {
                        echo substr($row["description"], 0, 20)."...";
                    }
                    echo '</td>';

                    echo '<td class="d-none d-sm-none d-md-table-cell"><u><a href="/honours/webapp/view/exerciseFiles/'.$row["exerciseFile"].'">'.$row["exerciseFile"].'</a></u></td>';

                    echo '<td class="d-none d-sm-none d-md-table-cell">';        
                    if ($row["instructionsFile"])
                    {
                        echo '<u><a href="/honours/webapp/view/exerciseFiles/'.$row["instructionsFile"].'">'.$row["instructionsFile"].'</a></u>';
                    }
                    echo '</td>';

                    echo '<td>';
                    if ($row["visible"])
                    {
                        echo "True";
                    }
                    else
                    {
                        echo "False";
                    }
                    echo '</td>';
                    echo '<td>'.$permission->getPermissionLevel($row["availability"]).' and up</td>';
                    echo '<td>'.$types->getExerciseType($row["type"]).'</td>';
                    echo '</tr>';
                }
            }
            else
            {
                echo "There are no exercises";
            }
        }
        else
        {
            echo "Failed to load exercise data";
        }
    }

    loadExercisePage();

?>