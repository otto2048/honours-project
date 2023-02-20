<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/SurveyQuestionModel.php");

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
        <title>Debugging Training Tool - View Survey Question</title>
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
                    //get survey question
                    $surveyQuestionModel = new SurveyQuestionModel();

                    $jsonQuestionData = $surveyQuestionModel->getSurveyQuestion($input);

                    if ($jsonQuestionData)
                    {
                        $questionData = json_decode($jsonQuestionData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($questionData["isempty"]))
                        {
                    ?>
                            <?php
                                //display survey question details 
                            ?>
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h1>View Survey Question - <?php echo $questionData[0]["questionId"]?></h1>
                                    </div>
                                    <div class="col">

                                        <button class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></button>

                                        <a href="updateSurvey.php?id=<?php echo $questionData[0]["questionId"] ?>" class="btn btn-primary ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="edit-btn">Edit <span class="mdi mdi-lead-pencil"></span></a>
                                        
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
                                    <li>Question ID: <?php echo $questionData[0]["questionId"] ?></li>
                                    <li>Contents: <?php echo $questionData[0]["contents"] ?></li>
                                </ul>

                    <?php

                        }
                        else
                        {
                    ?>
                            <h1>View Survey Question</h1>
                    <?php
                            echo "Failed to load Survey Question data";
                        }
                    }
                    else
                    {
                        ?>
                        <h1>View Survey Question</h1>
                <?php
                        echo "Failed to load Survey Question data";
                    }
                }
                else
                {
                ?>
                        <h1>View Survey Question</h1>
                <?php
                
                    echo "Failed to load Survey Question data";
                }             
            ?>

            
        
        </div>

        <!-- delete Survey Question modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h5 modal-title">Are you sure you want to delete this survey question?</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <p>Confirm deletion</p>
                        <p>All survey question data will be lost!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-primary btn" data-bs-dismiss="modal">Close</button>
                        <a href="../../../controller/actionScripts/deleteSurveyQuestion.php?id=<?php echo $input; ?>" class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></a>
                    </div>
                </div>
            </div>
        </div>

        <script src="../../js/deleteConfirmation.js"></script>

        <script src="../../js/setTheme.js"></script>
    </body>
</html>