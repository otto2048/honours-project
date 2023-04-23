<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserSurveyModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/printErrorMessages.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");

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
        <title>Debugging Training Tool - View User</title>
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
                    //get user
                    $userModel = new UserModel();
                    $userExerciseModel = new UserExerciseModel();

                    $jsonUserData = $userModel->getUserById($input);

                    if ($jsonUserData)
                    {
                
                        $userData = json_decode($jsonUserData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($userData["isempty"]))
                        {
                    ?>
                            <?php
                                //display user details 
                                
                                //display current permission
                                $permission = new PermissionLevels();
                            ?>
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h1>View User - <?php echo $userData[0]["username"]?></h1>
                                    </div>
                                    <div class="col">

                                        <button class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></button>

                                        <a href="updateUser.php?id=<?php echo $userData[0]["userId"] ?>" class="btn btn-primary ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="edit-btn">Edit <span class="mdi mdi-lead-pencil"></span></a>
                                        
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
                                    <li>User ID: <?php echo $userData[0]["userId"] ?></li>
                                    <li>Username: <?php echo $userData[0]["username"] ?></li>
                                    <li>User Group: <?php echo $permission->getPermissionLevel($userData[0]["permissionLevel"]) ?></li>
                                    <li>SUS Score: <?php echo $userData[0]["SUS_Score"]?></li>
                                </ul>

                                <div class="row">
                                    <div class="col-sm">
                                        <h1>Pre-test Exercises</h1>
                                    <hr>
                        <?php
                                // User pre test exercises

                                // get all the exercises for this type of user that are pre test exercises

                                // for each exercise, check if the user has attempted it and display their mark

                                $exerciseModel = new ExerciseModel();
                                
                                $jsonExercises = $exerciseModel->getAvailableExercises($userData[0]["permissionLevel"], ExerciseTypes::PRETEST);

                                if ($jsonExercises)
                                {
                                    $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                                    if (!isset($exercises["isempty"]))
                                    {
    ?>
                                    <p class="pb-1 pt-3 mb-0">Click on column headings to sort Exercises by this column</p>
                                    <div class="table-responsive">
                                        <table class="table tablesort tablesearch-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-tablesort-type="int">ID</th>
                                                    <th scope="col" data-tablesort-type="string">Title</th>
                                                    <th scope="col" data-tablesort-type="string">Mark</th>
                                                    <th scope="col" data-tablesort-type="string">Vector</th>
                                                    <th scope="col" data-tablesort-type="string">Visible</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                    //display user data
                                                    foreach ($exercises as $row)
                                                    {

                                                        echo '<tr>';
                                                        echo '<td>'.$row["codeId"].'</td>';
                                                        echo '<td>'.$row["title"].'</td>';
                                                        echo '<td>';

                                                        //get mark information
                                                        $markJson = $userExerciseModel->getExerciseMark($userData[0]["userId"], $row["codeId"]);

                                                        if ($markJson)
                                                        {
                                                            $mark = json_decode($markJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                                            echo $mark["points"]."/".$mark["total"]."</td>";
                                                        }
                                                        else
                                                        {
                                                            echo 'Failed to retrieve mark</td>';
                                                        }

                                                        $vectorJson = $userExerciseModel->getExerciseResultVector($userData[0]["userId"], $row["codeId"]);
                                                        echo '<td>';
                                                        if ($vectorJson)
                                                        {
                                                            $vector = json_decode($vectorJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                                            echo $vector[0]["result_vector"];
                                                        }
                                                        else
                                                        {
                                                            echo 'Failed to retrieve vector';
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
                                        echo "No pre-test exercises available for this user";
                                    }
                                }
                                else
                                {
                                    echo "Pre-test exercises failed to load";
                                }


                                // User practice test exercises
                                ?>
                                <h1>Practice Exercises</h1>
                                    <hr>
                                <?php

                                $jsonExercises = $exerciseModel->getAvailableExercises($userData[0]["permissionLevel"], ExerciseTypes::PRACTICE);

                                if ($jsonExercises)
                                {
                                    $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                                    if (!isset($exercises["isempty"]))
                                    {
                                ?>
                                    <p class="pb-1 pt-3 mb-0">Click on column headings to sort Exercises by this column</p>
                                    <div class="table-responsive">
                                        <table class="table tablesort tablesearch-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-tablesort-type="int">ID</th>
                                                    <th scope="col" data-tablesort-type="string">Title</th>
                                                    <th scope="col" data-tablesort-type="string">Mark</th>
                                                    <th scope="col" data-tablesort-type="string">Vector</th>
                                                    <th scope="col" data-tablesort-type="string">Visible</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                    //display user data
                                                    foreach ($exercises as $row)
                                                    {

                                                        echo '<tr>';
                                                        echo '<td>'.$row["codeId"].'</td>';
                                                        echo '<td>'.$row["title"].'</td>';
                                                        echo '<td>';

                                                        //get mark information
                                                        $markJson = $userExerciseModel->getExerciseMark($userData[0]["userId"], $row["codeId"]);

                                                        if ($markJson)
                                                        {
                                                            $mark = json_decode($markJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                                            echo $mark["points"]."/".$mark["total"]."</td>";
                                                        }
                                                        else
                                                        {
                                                            echo 'Failed to retrieve mark</td>';
                                                        }

                                                        $vectorJson = $userExerciseModel->getExerciseResultVector($userData[0]["userId"], $row["codeId"]);
                                                        echo '<td>';
                                                        if ($vectorJson)
                                                        {
                                                            $vector = json_decode($vectorJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                                            echo $vector[0]["result_vector"];
                                                        }
                                                        else
                                                        {
                                                            echo 'Failed to retrieve vector';
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
                                        echo "No practice exercises available for this user";
                                    }
                                }
                                else
                                {
                                    echo "Practice exercises failed to load";
                                }

                                // User post test exercises
                                ?>
                                <h1>Post-test Exercises</h1>
                                    <hr>
                                <?php

                                $jsonExercises = $exerciseModel->getAvailableExercises($userData[0]["permissionLevel"], ExerciseTypes::POSTTEST);

                                if ($jsonExercises)
                                {
                                    $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                                    if (!isset($exercises["isempty"]))
                                    {
                                ?>
                                    <p class="pb-1 pt-3 mb-0">Click on column headings to sort Exercises by this column</p>
                                    <div class="table-responsive">
                                        <table class="table tablesort tablesearch-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-tablesort-type="int">ID</th>
                                                    <th scope="col" data-tablesort-type="string">Title</th>
                                                    <th scope="col" data-tablesort-type="string">Mark</th>
                                                    <th scope="col" data-tablesort-type="string">Vector</th>
                                                    <th scope="col" data-tablesort-type="string">Visible</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                    //display exercise data
                                                    foreach ($exercises as $row)
                                                    {

                                                        echo '<tr>';
                                                        echo '<td>'.$row["codeId"].'</td>';
                                                        echo '<td>'.$row["title"].'</td>';
                                                        echo '<td>';

                                                        //get mark information
                                                        $markJson = $userExerciseModel->getExerciseMark($userData[0]["userId"], $row["codeId"]);
                                                        $vectorJson = $userExerciseModel->getExerciseResultVector($userData[0]["userId"], $row["codeId"]);

                                                        if ($markJson)
                                                        {
                                                            $mark = json_decode($markJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                                            echo $mark["points"]."/".$mark["total"]."</td>";
                                                        }
                                                        else
                                                        {
                                                            echo 'Failed to retrieve mark</td>';
                                                        }

                                                        echo '<td>';
                                                        if ($vectorJson)
                                                        {
                                                            $vector = json_decode($vectorJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                                            echo $vector[0]["result_vector"];
                                                        }
                                                        else
                                                        {
                                                            echo 'Failed to retrieve vector';
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
                                        echo "No post-test exercises available for this user";
                                    }
                                }
                                else
                                {
                                    echo "Post-test exercises failed to load";
                                }
                            ?>

                                    </div>
                                    <div class="col-sm">
                                        <h1>User Survey Answers</h1>
                                        <hr>

                                        <?php

                                            $userSurveyModel = new UserSurveyModel();

                                            $userAnswersJson = $userSurveyModel->getUserAnswers($userData[0]["userId"]);

                                            if ($userAnswersJson)
                                            {
                                                $userAnswers = json_decode($userAnswersJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                                if (!isset($userAnswers["isempty"]))
                                                {
                                                    ?>
                                                    <p class="pb-1 pt-3 mb-0">Click on column headings to sort Answers by this column</p>
                                                    <div class="table-responsive">
                                                        <table class="table tablesort tablesearch-table">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" data-tablesort-type="int">ID</th>
                                                                    <th scope="col" data-tablesort-type="string">Question</th>
                                                                    <th scope="col" data-tablesort-type="string">Answer</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php

                                                                    //display exercise data
                                                                    foreach ($userAnswers as $answer)
                                                                    {

                                                                        echo '<tr>';
                                                                        echo '<td>'.$answer["questionId"].'</td>';
                                                                        echo '<td>'.$answer["contents"].'</td>';
                                                                        echo '<td>'.$answer["answer"].'</td>';
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
                                                    echo "User has no survey answers";
                                                }
                                            }
                                            else
                                            {
                                                echo "Failed to load user survey answers";
                                            }

                                        ?>

                                    </div>
                                </div>
                            
                    <?php      
                        }
                        else
                        {
                    ?>
                            <h1>View User</h1>
                    <?php
                            echo "Failed to load user data";
                        }
                    }
                    else
                    {
                        ?>
                            <h1>View User</h1>
                    <?php
                            echo "Failed to load user data";
                        
                    }

                    
                }
                else
                {
                ?>
                        <h1>View User</h1>
                <?php
                    echo "Failed to load user data";
                }             
            ?>

        
        </div>

        <!-- delete user modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h5 modal-title">Are you sure you want to delete this user?</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <p>Confirm deletion</p>
                        <p>All user data will be lost!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-primary btn" data-bs-dismiss="modal">Close</button>
                        <a href="../../../controller/actionScripts/deleteUser.php?userId=<?php echo $input; ?>" class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto tables plugin -->
        <script src="../../js/auto-sorter-filter/auto-tables.js"></script>

        <script src="../../js/deleteConfirmation.js"></script>

        <script src="../../js/setTheme.js"></script>

    </body>
</html>