<!-- homepage -->
<!-- list the exercises the user has to do -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/printErrorMessages.php");


    //check if the user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/userArea/signUp.php", name="_self")</script>';
    }

    if ($_SESSION["permissionLevel"] < PermissionLevels::CONTROL)
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/login.php", name="_self")</script>';
    }
?>

<!doctype html>

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - Homepage</title>
        <?php include "head.php"; ?>
    </head>
    <body>
        <?php
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>
        <div class="container p-3" >
            <?php
                //check for errors on this page
                if (isset($_GET["message"]))
                {
                    $message = $_GET["message"];
                
                    printErrorMessage($message);
                }
            ?>
            <h1>Your Exercises</h1>
            <hr>
            <?php

                // get the exercises the user has to do

                // get user pre test exercises
                    // if those are all complete get post test exercises and display video links
                        // if all post test exercises are complete display sus survey link

                $exerciseModel = new ExerciseModel();
                $userModel = new UserModel();
                $userExerciseModel = new UserExerciseModel();

                function sortExercises(&$completedExercises, &$assignedExercises, $jsonExercises)
                {
                    $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                    if (!isset($exercises["isempty"]))
                    {
                        foreach ($exercises as $exercise)
                        {
                            //get mark information to check if this exercise is completed
                            $markJson = $GLOBALS["userExerciseModel"]->getExerciseMark($_SESSION["userId"], $exercise["codeId"]);

                            if ($markJson)
                            {
                                $mark = json_decode($markJson, JSON_INVALID_UTF8_SUBSTITUTE);

                                if ($mark["points"] >= 0)
                                {
                                    //exercise is completed
                                    array_push($completedExercises, array("exercise"=>$exercise, "mark"=>$mark));
                                }
                                else
                                {
                                    //exercise is still to be completed
                                    $mark["points"] = 0;
                                    array_push($assignedExercises, array("exercise"=>$exercise, "mark"=>$mark));
                                }
                            }
                            else
                            {
                                array_push($assignedExercises, array("exercise"=>$exercise, "mark"=>null));
                            }
                        }
                    }
                }

                function outputAssignedExercises($assignedExercises)
                {
                    ?>
                    <h2>Assigned exercises</h2>
                    <hr>
                    <div class="row m-1">
                    <?php
                    foreach ($assignedExercises as $assignedExercise)
                    {
                    ?>
                    <div class="col-sm-4 pb-3">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title h5"><?php echo $assignedExercise["exercise"]["title"]; ?></h3>
                                <h4 class="card-subtitle mb-2 text-muted h6">Points: <?php if ($assignedExercise["mark"]) { echo $assignedExercise["mark"]["points"]."/".$assignedExercise["mark"]["total"];} else {echo "Failed to retrieve mark";} ?></h4>
                                <p class="card-text"><?php echo $assignedExercise["exercise"]["description"]; ?></p>
                                <div class="text-center">
                                    <a href="userArea/exercise.php?id=<?php echo $assignedExercise["exercise"]["codeId"]; ?>" class="btn btn-primary">Complete exercise</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    </div>
                    <?php
                }

                function outputCompletedExercises($completedExercises)
                {
                    ?>
                    <h2>Completed exercises</h2>
                    <hr>
                    <?php
                    if (count($completedExercises) == 0)
                    {
                        echo "You have no completed exercises";
                    }
                    else
                    {
                        ?>
                        <div class="row m-1">
                        <?php
                        foreach ($completedExercises as $completedExercise)
                        {
                        ?>
                        <div class="col-sm-4 pb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title h5"><?php echo $completedExercise["exercise"]["title"]; ?></h3>
                                    <h4 class="card-subtitle mb-2 text-muted h6">Points: <?php if ($completedExercise["mark"]) { echo $completedExercise["mark"]["points"]."/".$completedExercise["mark"]["total"];} else {echo "Failed to retrieve mark";} ?></h4>
                                    <p class="card-text"><?php echo $completedExercise["exercise"]["description"]; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                        </div>
                        <?php
                    }
                }

                //get pretest exercises
                $jsonExercises = $exerciseModel->getAvailableExercises($_SESSION["permissionLevel"], ExerciseTypes::PRETEST, true);

                if ($jsonExercises)
                {
                    $completedExercises = array();
                    $assignedExercises = array();

                    //sort these exercises into completed and assigned exercises
                    sortExercises($completedExercises, $assignedExercises, $jsonExercises);

                    //if there are no pre test 
                    if (count($assignedExercises) == 0)
                    {
                        //get the post test exercises
                        $jsonExercises = $exerciseModel->getAvailableExercises($_SESSION["permissionLevel"], ExerciseTypes::POSTTEST, true);

                        if ($jsonExercises)
                        {
                            //sort these exercises into completed and assigned exercises
                            sortExercises($completedExercises, $assignedExercises, $jsonExercises);

                            //if there are no post test 
                            if (count($assignedExercises) == 0)
                            {
                                ?>
                                    <div class="alert alert-danger show" role="alert">
                                        <p>You have no assigned exercises</p>
                                        <p>Please complete the SUS survey to give feedback on this tool:</p>
                                        <p>Complete the SUS survey here: <a href="/honours/webapp/view/userArea/survey.php">SUS Survey</a></p>
                                    </div>
                                <?php
                            }
                            else
                            {
                                ?>

                                <div class="alert alert-danger show" role="alert">
                                    <p>Before completing any more exercises, watch the following video(s):</p>
                                        <ol class="m-0">
                                            <li><a href="https://liveabertayac-my.sharepoint.com/:v:/g/personal/1900414_uad_ac_uk/EQekumBU3cpOuQ_y5s9wGEIBTIiJuD42rc-4IVlsQCD6DQ?e=zQp1aN" target="_blank">Debugging Tutorial</a></li>

                                            <?php
                                                if ($_SESSION["permissionLevel"] >= PermissionLevels::EXPERIMENT)
                                                {
                                            ?>
                                                    <li><a href="https://liveabertayac-my.sharepoint.com/:v:/g/personal/1900414_uad_ac_uk/EbHoyYXeiyxLg-2WHnIIHX8Bo2qXZyJQSDOInPq7ZGvrWg?e=HbrcgR" target="_blank">Debugging Strategy Tutorial</a></li>
                                            <?php
                                                }
                                            ?>
                                        </ol>
                                </div>
                                <?php

                                //output exercises
                                outputAssignedExercises($assignedExercises);
                            }

                            outputCompletedExercises($completedExercises);
                        }
                        else
                        {
                            echo "Failed to load exercises";
                        }
                    }
                    else
                    {
                        //output exercises
                        outputAssignedExercises($assignedExercises);
                        outputCompletedExercises($completedExercises);
                    }
                }
                else
                {
                    echo "Failed to load exercises";
                }
            ?>
        </div>
        
        <script src="js/setTheme.js"></script>

    </body>
</html>