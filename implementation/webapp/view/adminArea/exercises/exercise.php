<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/AnswerTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseAnswerModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/printErrorMessages.php");

    //check if the user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php", name="_self")</script>';
    }

    if ($_SESSION["permissionLevel"] < PermissionLevels::ADMIN)
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php", name="_self")</script>';
    }
?>

<!doctype html>

<html lang="en">
    <head>
        <title>Debugging Training Tool - View Exercise</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php 
            function getHeader()
            {
                include "../../navigation.php";
            }

            getHeader();

        ?>
        
        <div class="container p-3">
            
            <?php

                $validation = new Validation();

                //sanitize input
                $input = $validation->cleanInput($_GET["id"]);

                //validate input
                if ($validation->validateInt($input))
                {
                    //get exercise
                    $exerciseModel = new ExerciseModel();
                    $answerType = new AnswerTypes();
                    $exerciseType = new ExerciseTypes();

                    $jsonExerciseData = $exerciseModel->getExercise($input);

                    if ($jsonExerciseData)
                    {
                        $exerciseData = json_decode($jsonExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($exerciseData["isempty"]))
                        {
                    ?>
                            <?php
                                //display exercise details 
                                
                                //display current permission
                                $permission = new PermissionLevels();
                            ?>
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h1>View Exercise - <?php echo $exerciseData[0]["title"]?></h1>
                                    </div>
                                    <div class="col">

                                        <button class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></button>

                                        <a href="updateExercise.php?id=<?php echo $exerciseData[0]["codeId"] ?>" class="btn btn-dark ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="edit-btn">Edit <span class="mdi mdi-lead-pencil"></span></a>
                                        
                                    </div>
                                </div>

                                <?php
                                    //check for errors on this page
                                    if (isset($_GET["message"]))
                                    {
                                        $message = $_GET["message"];
                                    
                                        printErrorMessage($message);
                                    }
                                ?>

                                <hr class="mt-0">
                                <ul>
                                    <li>Exercise ID: <?php echo $exerciseData[0]["codeId"] ?></li>
                                    <li>Title: <?php echo $exerciseData[0]["title"] ?></li>

                                    <?php
                                        if ($exerciseData[0]["description"])
                                        {
                                    ?>
                                            <li>Description: <?php echo $exerciseData[0]["description"] ?></li>
                                    <?php } ?>

                                    <li><a href="/honours/webapp/view/exerciseFiles/<?php echo $exerciseData[0]["exerciseFile"]; ?>">Exercise File: <?php echo $exerciseData[0]["exerciseFile"] ?></a></li>
                                    
                                    <?php
                                        if ($exerciseData[0]["instructionsFile"])
                                        {
                                    ?>
                                            <li><a href="/honours/webapp/view/exerciseFiles/<?php echo $exerciseData[0]["instructionsFile"]; ?>">Instructions File: <?php echo $exerciseData[0]["instructionsFile"] ?></a></li>
                                    <?php } ?>

                                    <li>Visibility: <?php if ($exerciseData[0]["visible"]) {echo "True";} else {echo "False";} ?></li>

                                    <li>Availability: <?php echo $permission->getPermissionLevel($exerciseData[0]["availability"]); ?> and up</li>
                                    <li>Type: <?php echo $exerciseType->getExerciseType($exerciseData[0]["type"]); ?></li>
                                </ul>

                    <?php

                        }
                        else
                        {
                    ?>
                            <h1>View Exercise</h1>
                    <?php
                            echo "Failed to load exercise data";
                        }
                    }
                    else
                    {
                        ?>
                        <h1>View Exercise</h1>
                <?php
                        echo "Failed to load exercise data";
                    }
                ?>
                    <h1>View Exercise Answers</h1>
                    <hr>
                <?php

                    //get exercise answers
                    $exerciseAnswerModel = new ExerciseAnswerModel();

                    $jsonExerciseAnswerData = $exerciseAnswerModel->getExerciseAnswers($input);

                    if ($jsonExerciseAnswerData)
                    {
                
                        $exerciseAnswerData = json_decode($jsonExerciseAnswerData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($exerciseAnswerData["isempty"]))
                        {
                    ?>
                            <div class="table-responsive modifyRowsTable" id="exerciseAnswerInfoTable">
                                <p class="pb-1 pt-3 mb-0">Click on column headings to sort Exercise Answers by this column</p>
                                <table class="table tablesort tablesearch-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" data-tablesort-type="int">ID</th>
                                            <th scope="col" data-tablesort-type="string">Input</th>
                                            <th scope="col" data-tablesort-type="string">Input Type</th>
                                            <th scope="col" data-tablesort-type="string">Output</th>
                                            <th scope="col">Delete Answer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php


                                            //display exercise data
                                            foreach ($exerciseAnswerData as $row)
                                            {
                                                echo '<tr id="row'.$row["codeAnswerId"].'">';
                                                echo '<td>'.$row["codeAnswerId"].'</td>';
                                                echo '<td>'.$row["input"].'</td>';
                                                echo '<td>'.$answerType->getAnswerType($row["inputType"]).'</td>';
                                                echo '<td>'.$row["output"].'</td>';
                                                echo '<td><button class="btn btn-danger remove" role="button"><span class="id d-none">'.$row["codeAnswerId"].'</span>Remove</button></td>';
                                                echo '</tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                
                    <?php
                        }
                        else
                        {
                    ?>
                    <?php
                            echo "There are no answers for this exercise";
                        }

                        if ($jsonExerciseData)
                        {
                            $exerciseData = json_decode($jsonExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);

                            if (!isset($exerciseData["isempty"]))
                            {
                          
                        ?>

                            <!-- add new exercise answers -->
                            <h1>Add new Exercise Answer</h1>
                            <hr>
                            <form role="form" method="POST" action="../../../controller/actionScripts/createExerciseAnswer.php">
                                <input type="text" required hidden name="codeId" value="<?php echo $input ?>">
                                <div class="form-group">
                                    <label for="input">Input:</label>
                                    <input type="text" class="form-control" name="input" required id="input">
                                </div>
                                <div class="form-group pt-1">
                                    <label for="inputType">Input Type:</label>
                                    <select name="inputType" id="inputType">
                                        <?php
                                            $answerTypeRef = new \ReflectionClass("AnswerTypes");
                                            $values = $answerTypeRef->getConstants();

                                            foreach ($values as $value)
                                            {
                                                $optionString = '<option value = "';
                                                $optionString .= $value.'"';
                                                $optionString .= ">".$answerType->getAnswerType($value)."</option>";

                                                echo $optionString;
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="output">Output:</label>
                                    <input type="text" class="form-control" name="output" required id="output">
                                </div>
                                <button class="btn btn-dark float-end mt-2" id="addRowBtn" type="submit">Submit</button>
                            </form>

                        <?php
                            }
                        }
                    }
                    else
                    {
                        ?>
                <?php
                    echo "Failed to load exercise data";
                    }

                    
                }
                else
                {
                ?>
                        <h1>View Exercise</h1>
                <?php
                    echo "Failed to load exercise data";
                }             
            ?>

            
        
        </div>

        <!-- delete exercise modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h5 modal-title">Are you sure you want to delete this exercise?</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <p>Confirm deletion</p>
                        <p>All exercise data will be lost!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-dark btn" data-bs-dismiss="modal">Close</button>
                        <a href="../../../controller/actionScripts/deleteExercise.php?codeId=<?php echo $input; ?>" class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></a>
                    </div>
                </div>
            </div>
        </div>

        <script src="../../js/deleteConfirmation.js"></script>
        <script src="../../js/modifyTableRows.js"></script>

        <!-- Auto tables plugin -->
        <script src="../../js/auto-sorter-filter/auto-tables.js"></script>
    </body>
</html>