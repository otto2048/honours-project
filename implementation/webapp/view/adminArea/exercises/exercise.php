<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/AnswerTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/printErrorMessages.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

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

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - View Exercise</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php 
            getNavigation();
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

                                        <a href="updateExercise.php?id=<?php echo $exerciseData[0]["codeId"] ?>" class="btn btn-primary ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="edit-btn">Edit <span class="mdi mdi-lead-pencil"></span></a>
                                        
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

                                    <li>Visibility: <?php if ($exerciseData[0]["visible"]) {echo "True";} else {echo "False";} ?></li>

                                    <li>Availability: <?php echo $permission->getPermissionLevel($exerciseData[0]["availability"]); ?> and up</li>
                                    <li>Type: <?php echo $exerciseType->getExerciseType($exerciseData[0]["type"]); ?></li>

                                    <li>Available points: <?php echo $exerciseData[0]["availablePoints"] ?></li>

                                    <li>Time limit: <?php echo $exerciseData[0]["time_limit"] ?></li>
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
            <div class="modal-dialog modal-dialog-centered">
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
                        <button type="button" class="btn-primary btn" data-bs-dismiss="modal">Close</button>
                        <a href="../../../controller/actionScripts/deleteExercise.php?codeId=<?php echo $input; ?>" class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- js includes -->
        <script src="../../js/deleteConfirmation.js"></script>
        <script src="../../js/modifyTableRows.js"></script>
        <script src="../../js/addFormInput.js"></script>

        <script src="../../js/setTheme.js"></script>

        <!-- Auto tables plugin -->
        <script src="../../js/auto-sorter-filter/auto-tables.js"></script>
    </body>
</html>