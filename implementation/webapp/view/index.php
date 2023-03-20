<!-- homepage -->
<!-- list the exercises the user has to do -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

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
            <h1>Your Exercises</h1>
            <hr>
            <?php

                // get the exercises the user has to do

                $exerciseModel = new ExerciseModel();
                $userModel = new UserModel();
                $userExerciseModel = new UserExerciseModel();

                $jsonExercises = $exerciseModel->getAvailableExercises($_SESSION["permissionLevel"], null, true);

                if ($jsonExercises)
                {
                    $exercises = json_decode($jsonExercises, JSON_INVALID_UTF8_SUBSTITUTE);

                    $completedExercises = array();
                    $assignedExercises = array();

                    if (!isset($exercises["isempty"]))
                    {
                        foreach ($exercises as $exercise)
                        {
                            //get mark information to check if this exercise is completed
                            $markJson = $userExerciseModel->getExerciseMark($_SESSION["userId"], $exercise["codeId"]);

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
                    else
                    {
                        echo "You have no exercises";
                    }

                    //output assigned exercises
                    ?>
                    <h2>Assigned exercises</h2>
                    <hr>
                    <?php
                    if (count($assignedExercises) == 0)
                    {
                        echo "You have no assigned exercises";
                    }
                    else
                    {
                        ?>
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
                    

                    //output completed exercises
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
                else
                {
                    echo "Failed to load exercises";
                }
            ?>
        </div>
        
        <script src="js/setTheme.js"></script>

    </body>
</html>