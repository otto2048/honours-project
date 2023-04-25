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
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserSurveyModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/Task.php");


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

    // control:
        // task 1: video
        // task 2: exercise
        // task 3: exercise

        // task 4: survey

    // experimental:
        // task 1: video 
        // task 2: exercise

        // task 3: video
        // task 4: exercise

        // task 5: survey

    // create array of tasks
    $tasks = array();

    $exerciseModel = new ExerciseModel();
    $userModel = new UserModel();
    $userExerciseModel = new UserExerciseModel();

    $tasksComplete = 0;

    if ($_SESSION["permissionLevel"] == PermissionLevels::CONTROL)
    {
        $tasksToComplete = 4;
    }
    else
    {
        $tasksToComplete = 5;
    }

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
            $tasks[0] = new Task(false, false);
            $tasks[0] -> title = "Task 1 - Tutorial";
            $tasks[0] -> text = "Watch the following video(s):";
            array_push($tasks[0] -> items, '<li><a href="https://liveabertayac-my.sharepoint.com/:v:/g/personal/1900414_uad_ac_uk/ERh-ddpHpw1AvPo1p1lR7LgBOj-ikA7bSorv-6ezzb33Dg?e=ngkHQQ" target="_blank">Environment Overview</a></li>');
            array_push($tasks[0] -> items, '<li><a href="https://liveabertayac-my.sharepoint.com/:v:/g/personal/1900414_uad_ac_uk/EQekumBU3cpOuQ_y5s9wGEIBTIiJuD42rc-4IVlsQCD6DQ?e=zQp1aN" target="_blank">Debugger Tutorial</a></li>');

            $tasks[1] = new Task(false, false);
            $tasks[1] -> title = "Task 2 - Exercises";
            $tasks[1] -> text = "Complete these exercise(s):";

            foreach ($assignedExercises as $item)
            {
                $link = '<li><a href="userArea/exercise.php?id='.$item["exercise"]["codeId"].'">'.$item["exercise"]["title"].'</a></li>';
                array_push($tasks[1] -> items, $link);
            }

            // control tasks
            if ($_SESSION["permissionLevel"] == PermissionLevels::CONTROL)
            {
                $tasks[2] = new Task(true, false);
                $tasks[2] -> title = "Task 3 - Exercises";

                $tasks[3] = new Task(true, false);
                $tasks[3] -> title = "Task 4 - Survey";
            }
            // experimental tasks
            else
            {
                $tasks[2] = new Task(true, false);
                $tasks[2] -> title = "Task 3 - Tutorial";
                
                $tasks[3] = new Task(true, false);
                $tasks[3] -> title = "Task 4 - Exercises";

                $tasks[4] = new Task(true, false);
                $tasks[4] -> title = "Task 5 - Survey";
            }
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
                    $tasks[0] = new Task(true, true);
                    $tasks[0] -> title = "Task 1 - Tutorial";

                    $tasks[1] = new Task(true, true);
                    $tasks[1] -> title = "Task 2 - Exercises";

                    $posttest = new Task(false, false);
                    $posttest -> text = "Complete these exercise(s):";

                    foreach ($assignedExercises as $item)
                    {
                        $link = '<li><a href="userArea/exercise.php?id='.$item["exercise"]["codeId"].'">'.$item["exercise"]["title"].'</a></li>';
                        array_push($posttest -> items, $link);
                    }

                    // control tasks
                    if ($_SESSION["permissionLevel"] == PermissionLevels::CONTROL)
                    {
                        $tasks[2] = $posttest;
                        $tasks[2] -> title = "Task 3 - Exercises";

                        $tasks[3] = new Task(true, false);
                        $tasks[3] -> title = "Task 4 - Survey";
                    }
                    // experimental tasks
                    else
                    {
                        $tasks[2] = new Task(false, false);
                        $tasks[2] -> title = "Task 3 - Tutorial";

                        array_push($tasks[2] -> items, '<li><a href="https://liveabertayac-my.sharepoint.com/:v:/g/personal/1900414_uad_ac_uk/EbHoyYXeiyxLg-2WHnIIHX8Bo2qXZyJQSDOInPq7ZGvrWg?e=HbrcgR" target="_blank">Debugging Strategy Tutorial</a></li>');
                        
                        $tasks[3] = $posttest;
                        $tasks[3] -> title = "Task 4 - Exercises";

                        $tasks[4] = new Task(true, false);
                        $tasks[4] -> title = "Task 5 - Survey";
                    }

                    $tasksComplete = 2;
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
                            $tasks[0] = new Task(true, true);
                            $tasks[0] -> title = "Task 1 - Tutorial";

                            $tasks[1] = new Task(true, true);
                            $tasks[1] -> title = "Task 2 - Exercises";

                            $posttest = new Task(true, true);
                            $survey = new Task(true, true);

                            // control tasks
                            if ($_SESSION["permissionLevel"] == PermissionLevels::CONTROL)
                            {
                                $tasks[2] = $posttest;
                                $tasks[2] -> title = "Task 3 - Exercises";

                                $tasks[3] = $survey;
                                $tasks[3] -> title = "Task 4 - Survey";

                                $tasksComplete = 4;
                            }
                            // experimental tasks
                            else
                            {
                                $tasks[2] = new Task(true, true);
                                $tasks[2] -> title = "Task 3 - Tutorial";

                                $tasks[3] = $posttest;
                                $tasks[3] -> title = "Task 4 - Exercises";

                                $tasks[4] = $survey;
                                $tasks[4] -> title = "Task 5 - Survey";

                                $tasksComplete = 5;
                            }
                        }
                        else
                        {
                            // user is on task 4/5
                            $tasks[0] = new Task(true, true);
                            $tasks[0] -> title = "Task 1 - Tutorial";

                            $tasks[1] = new Task(true, true);
                            $tasks[1] -> title = "Task 2 - Exercises";

                            $posttest = new Task(true, true);
                            $survey = new Task(false, false);

                            // control tasks
                            if ($_SESSION["permissionLevel"] == PermissionLevels::CONTROL)
                            {
                                $tasks[2] = $posttest;
                                $tasks[2] -> title = "Task 3 - Exercises";

                                $tasks[3] = $survey;
                                $tasks[3] -> title = "Task 4 - Survey";
                                $tasks[3] -> text = "Please complete the System Usability Scale (SUS) survey to give feedback on this tool:";

                                array_push($tasks[3] -> items, '<li><a href="/honours/webapp/view/userArea/survey.php">SUS Survey</a></li>');

                                $tasksComplete = 3;
                            }
                            // experimental tasks
                            else
                            {
                                $tasks[2] = new Task(true, true);
                                $tasks[2] -> title = "Task 3 - Tutorial";

                                $tasks[3] = $posttest;
                                $tasks[3] -> title = "Task 4 - Exercises";

                                $tasks[4] = $survey;
                                $tasks[4] -> title = "Task 5 - Survey";
                                $tasks[4] -> text = "Please complete the System Usability Scale (SUS) survey to give feedback on this tool:";

                                array_push($tasks[4] -> items, '<li><a href="/honours/webapp/view/userArea/survey.php">SUS Survey</a></li>');

                                $tasksComplete = 4;
                            }
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
            <h1>Tasks <span class="float-end h4 pt-3">Completed: <?php echo $tasksComplete ?>/<?php echo $tasksToComplete ?></span></h1>
            <hr>
            <?php
                if ($tasksComplete == $tasksToComplete)
                {
            ?>
                    <div class="alert alert-success show d-flex align-items-center" role="alert">
                        <p class="m-0">You have completed all assigned tasks</p>
                    </div>
            <?php
                }

                foreach ($tasks as $task)
                {
                    echo($task);
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