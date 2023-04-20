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
            <h1>Tasks</h1>
            <hr>
            <?php

                // get the exercises the user has to do

                // get user pre test exercises
                    // if those are all complete get post test exercises and display video links
                        // if all post test exercises are complete display sus survey link

                $exerciseModel = new ExerciseModel();
                $userModel = new UserModel();
                $userExerciseModel = new UserExerciseModel();

                function sortExercises(&$assignedExercises, $jsonExercises)
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

                                if ($mark["points"] < 0)
                                {
                                    //exercise is still to be completed
                                    array_push($assignedExercises, array("exercise"=>$exercise));
                                }
                            }
                        }
                    }
                }

                function outputAssignedExercises($assignedExercises)
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


                // get the tasks a user has to do based on where they are in the experiement (eg. pretest, video, posttest or survey)

                //get pretest exercises
                $jsonExercises = $exerciseModel->getAvailableExercises($_SESSION["permissionLevel"], ExerciseTypes::PRETEST, true);

                if ($jsonExercises)
                {
                    $assignedExercises = array();

                    //remove completed exercises
                    sortExercises($assignedExercises, $jsonExercises);

                    //if there are pretest exercises to be completed
                    if (count($assignedExercises) > 0)
                    {
                        ?>
                            <h2>Task 1</h2>
                            <p>Complete these exercise(s): </p>
                        <?php
                        outputAssignedExercises($assignedExercises);
                    }
                    else
                    {
                        //load the posttest exercises

                        //get the post test exercises
                        $jsonExercises = $exerciseModel->getAvailableExercises($_SESSION["permissionLevel"], ExerciseTypes::POSTTEST, true);

                        if ($jsonExercises)
                        {
                            //remove completed exercises
                            sortExercises($assignedExercises, $jsonExercises);

                            //if there are no post test 
                            if (count($assignedExercises) == 0)
                            {
                                ?>
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="card-title" >Task 1</h2>
                                        <div class="card-text">
                                            <p>Please complete the System Usability Scale (SUS) survey to give feedback on this tool:</p>
                                            <p>Complete the SUS survey here: <a href="/honours/webapp/view/userArea/survey.php">SUS Survey</a></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            else
                            {
                                // output video
                                ?>
                                
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h2 class="card-title h4">Task 1 - Tutorials</h2>
                                        <p class="m-0">Watch the following video(s):</p>
                                        <ol>
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
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="card-title h4">Task 2 - Exercises</h2>
                                        <p class="m-0">Complete these exercise(s): </p>
                                        <ol>
                                            <?php
                                            foreach ($assignedExercises as $assignedExercise)
                                            {
                                            ?>
                                            <li>
                                                <a href="userArea/exercise.php?id=<?php echo $assignedExercise["exercise"]["codeId"]; ?>" class="">
                                                    <?php echo $assignedExercise["exercise"]["title"]; ?>
                                                </a>
                                            </li>
                                            <?php
                                            }
                                            ?>
                                        </ol>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                    
                                    </ul>
                                </div>

                                <?php

                            }
                        }
                        else
                        {
                            echo "Failed to load tasks";
                        }
                    }
                }
                else
                {
                    echo "Failed to load tasks";
                }
            ?>
        </div>
        
        <script src="js/setTheme.js"></script>

    </body>
</html>