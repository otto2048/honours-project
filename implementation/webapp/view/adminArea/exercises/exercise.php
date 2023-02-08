<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");
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

                    $jsonExerciseData = $exerciseModel->getExercise($input);
                
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
                                <li>Description: <?php echo $exerciseData[0]["description"] ?></li>
                                <li>Exercise File: <?php echo $exerciseData[0]["exerciseFile"] ?></li>
                                <li>Instructions File: <?php echo $exerciseData[0]["instructionsFile"] ?></li>

                                <li>Visibility: <?php if ($exerciseData[0]["visible"]) {echo "True";} else {echo "False";} ?></li>

                                <li>Availability: <?php echo $permission->getPermissionLevel($exerciseData[0]["availability"]) ?> and up</li>
                            </ul>
                        
                    </div>

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
    </body>
</html>