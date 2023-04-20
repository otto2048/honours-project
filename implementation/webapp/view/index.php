<!-- homepage -->
<!-- list the exercises the user has to do -->

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/printErrorMessages.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserSurveyModel.php");


    //check if the user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/userArea/signUp.php", name="_self")</script>';
    }

    if ($_SESSION["permissionLevel"] < PermissionLevels::CONTROL)
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/login.php", name="_self")</script>';
    }

    // check what task the user is on

    // task 1: exercises
        // data -> exercises
    // task 2: video and exercises
        // data -> exercises and video links
    // task 3: more exercises
        // data -> exercises
    // task 4: survey
        // data -> survey

    $exerciseModel = new ExerciseModel();
    $userModel = new UserModel();
    $userExerciseModel = new UserExerciseModel();

    $tasks = array();
    $tasksComplete = 0;

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

    function printTask($task)
    {
        ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="card-title h4 <?php if ($task->hidden) {echo "text-muted";} ?>"><?php echo $task->title; if ($task->completed) {echo "<span class='mdi mdi-checkbox-marked-circle ms-1'></span>";} ?></h2>
                    <p class="m-0 <?php if ($task->hidden) {echo "text-muted";} ?>"><?php echo $task->text ?></p>

                    <?php if (!$task->hidden) { ?>
                        <ol class="m-0">
                            <?php
                            foreach ($task->items as $item)
                            {
                                echo $item;
                            }
                            ?>
                        </ol>
                    <?php } ?>
                </div>
            </div>
        <?php
    }

    // load pretest exercises
    $jsonExercises = $exerciseModel->getAvailableExercises($_SESSION["permissionLevel"], ExerciseTypes::PRETEST, true);

    if ($jsonExercises)
    {
        $assignedExercises = array();

        //remove completed exercises
        sortExercises($assignedExercises, $jsonExercises);

        //if there are pretest exercises to be completed
        if (count($assignedExercises) > 0)
        {
            // user is on task 1
            $tasks[0] = new \stdClass();
            $tasks[0] -> title = "Task 1 - Exercises";
            $tasks[0] -> text = "Complete these exercise(s):";
            $tasks[0] -> hidden = false;
            $tasks[0] -> completed = false;
            $tasks[0] -> items = array();

            foreach ($assignedExercises as $item)
            {
                $link = '<li><a href="userArea/exercise.php?id='.$item["exercise"]["codeId"].'">'.$item["exercise"]["title"].'</a></li>';
                array_push($tasks[0] -> items, $link);
            }

            $tasks[1] = new \stdClass();
            $tasks[1] -> title = "Task 2 - Tutorials";
            $tasks[1] -> text = "Complete prior tasks to access this task";
            $tasks[1] -> items = array();
            $tasks[1] -> hidden = true;
            $tasks[1] -> completed = false;

            $tasks[2] = new \stdClass();
            $tasks[2] -> title = "Task 3 - More Exercises";
            $tasks[2] -> text = "Complete prior tasks to access this task";
            $tasks[2] -> items = array();
            $tasks[2] -> hidden = true;
            $tasks[2] -> completed = false;

            $tasks[3] = new \stdClass();
            $tasks[3] -> title = "Task 4 - Survey";
            $tasks[3] -> text = "Complete prior tasks to access this task";
            $tasks[3] -> items = array();
            $tasks[3] -> hidden = true;
            $tasks[3] -> completed = false;
        }
        else
        {
            // load posttest exercises
            $jsonExercises = $exerciseModel->getAvailableExercises($_SESSION["permissionLevel"], ExerciseTypes::POSTTEST, true);

            if ($jsonExercises)
            {
                //remove completed exercises
                sortExercises($assignedExercises, $jsonExercises);

                //if there are posttest exercises to be completed
                if (count($assignedExercises) > 0)
                {
                    // user is on task 2 and 3
                    $tasks[0] = new \stdClass();
                    $tasks[0] -> title = "Task 1 - Exercises";
                    $tasks[0] -> text = "You have completed this task";
                    $tasks[0] -> items = array();
                    $tasks[0] -> hidden = true;
                    $tasks[0] -> completed = true;

                    $tasks[1] = new \stdClass();
                    $tasks[1] -> title = "Task 2 - Tutorials";
                    $tasks[1] -> text = "Watch the following video(s):";
                    $tasks[1] -> items = array();
                    $tasks[1] -> hidden = false;
                    $tasks[1] -> completed = false;

                    array_push($tasks[1] -> items, '<li><a href="https://liveabertayac-my.sharepoint.com/:v:/g/personal/1900414_uad_ac_uk/EQekumBU3cpOuQ_y5s9wGEIBTIiJuD42rc-4IVlsQCD6DQ?e=zQp1aN" target="_blank">Debugger Tutorial</a></li>');

                    if ($_SESSION["permissionLevel"] >= PermissionLevels::EXPERIMENT)
                    {
                        array_push($tasks[1] -> items, '<li><a href="https://liveabertayac-my.sharepoint.com/:v:/g/personal/1900414_uad_ac_uk/EbHoyYXeiyxLg-2WHnIIHX8Bo2qXZyJQSDOInPq7ZGvrWg?e=HbrcgR" target="_blank">Debugging Strategy Tutorial</a></li>');
                    }

                    $tasks[2] = new \stdClass();
                    $tasks[2] -> title = "Task 3 - More Exercises";
                    $tasks[2] -> text = "Complete these exercise(s):";
                    $tasks[2] -> items = array();
                    $tasks[2] -> hidden = false;
                    $tasks[2] -> completed = false;

                    foreach ($assignedExercises as $item)
                    {
                        $link = '<li><a href="userArea/exercise.php?id='.$item["exercise"]["codeId"].'">'.$item["exercise"]["title"].'</a></li>';
                        array_push($tasks[2] -> items, $link);
                    }

                    $tasks[3] = new \stdClass();
                    $tasks[3] -> title = "Task 4 - Survey";
                    $tasks[3] -> text = "Complete prior tasks to access this task";
                    $tasks[3] -> items = array();
                    $tasks[3] -> hidden = true;
                    $tasks[3] -> completed = false;

                    $tasksComplete = 1;
                }
                else
                {
                    //check if the user has already completed the survey
                    $userSurveyModel = new UserSurveyModel();

                    $jsonData = $userSurveyModel->getUserAnswers($_SESSION["userId"]);

                    //if json data returned ok
                    if ($jsonData)
                    {
                        $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

                        //if there is results
                        if (!isset($data["isempty"]))
                        {
                            // user has finished all tasks
                            $tasks[0] = new \stdClass();
                            $tasks[0] -> title = "Task 1 - Exercises";
                            $tasks[0] -> text = "You have completed this task";
                            $tasks[0] -> items = array();
                            $tasks[0] -> hidden = true;
                            $tasks[0] -> completed = true;

                            $tasks[1] = new \stdClass();
                            $tasks[1] -> title = "Task 2 - Tutorials";
                            $tasks[1] -> text = "You have completed this task";
                            $tasks[1] -> items = array();
                            $tasks[1] -> hidden = true;
                            $tasks[1] -> completed = true;

                            $tasks[2] = new \stdClass();
                            $tasks[2] -> title = "Task 3 - More Exercises";
                            $tasks[2] -> text = "You have completed this task";
                            $tasks[2] -> items = array();
                            $tasks[2] -> hidden = true;
                            $tasks[2] -> completed = true;

                            $tasks[3] = new \stdClass();
                            $tasks[3] -> title = "Task 4 - Survey";
                            $tasks[3] -> text = "You have completed this task";
                            $tasks[3] -> items = array();
                            $tasks[3] -> hidden = true;
                            $tasks[3] -> completed = true;

                            $tasksComplete = 4;
                        }
                        else
                        {
                            // user is on task 4
                            $tasks[0] = new \stdClass();
                            $tasks[0] -> title = "Task 1 - Exercises";
                            $tasks[0] -> text = "You have completed this task";
                            $tasks[0] -> items = array();
                            $tasks[0] -> hidden = true;
                            $tasks[0] -> completed = true;

                            $tasks[1] = new \stdClass();
                            $tasks[1] -> title = "Task 2 - Tutorials";
                            $tasks[1] -> text = "You have completed this task";
                            $tasks[1] -> items = array();
                            $tasks[1] -> hidden = true;
                            $tasks[1] -> completed = true;

                            $tasks[2] = new \stdClass();
                            $tasks[2] -> title = "Task 3 - More Exercises";
                            $tasks[2] -> text = "You have completed this task";
                            $tasks[2] -> items = array();
                            $tasks[2] -> hidden = true;
                            $tasks[2] -> completed = true;

                            $tasks[3] = new \stdClass();
                            $tasks[3] -> title = "Task 4 - Survey";
                            $tasks[3] -> text = "Please complete the System Usability Scale (SUS) survey to give feedback on this tool:";
                            $tasks[3] -> items = array();
                            $tasks[3] -> hidden = false;
                            $tasks[3] -> completed = false;

                            array_push($tasks[3] -> items, '<li><a href="/honours/webapp/view/userArea/survey.php">SUS Survey</a></li>');

                            $tasksComplete = 3;
                        }
                    }
                }
            }
        }
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
            <h1>Tasks <span class="float-end h4 pt-3">Completed: <?php echo $tasksComplete ?>/4</span></h1>
            <hr>
            <?php
                if ($tasksComplete == 4)
                {
            ?>
                    <div class="alert alert-success show d-flex align-items-center" role="alert">
                        <p class="m-0">You have completed all assigned tasks</p>
                    </div>
            <?php
                }

                foreach ($tasks as $task)
                {
                    printTask($task);
                }

                if (count($tasks) == 0)
                {
                    echo "Failed to load tasks";
                }
            ?>
        </div>
        
        <script src="js/setTheme.js"></script>

    </body>
</html>