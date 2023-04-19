<!-- complete an exercise here -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserExerciseModel.php");

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
        <title>Debugging Training Tool - Exercise</title>
        <?php include "../head.php"; ?>

        <!-- jquery terminal -->
        <script src="/honours/webapp/view/js/jquery-terminal/jquery-terminal-2.35.3.js"></script>

        <!-- code mirror -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/clike/clike.js" integrity="sha512-8BriEp3cRkqmcBqIT7n59KpFSZLoLbsELo15jhB0EKac1OwlHaPBbhKcmAIpdX78n64SewuBBt3YQ3nm/6f56Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/foldcode.min.js" integrity="sha512-Q2qfEJEU257Qlqc4/5g6iKuJNnn5L0xu2D48p8WHe9YC/kLj2UfkdGD01qfxWk+XIcHsZngcA8WuKcizF8MAHA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/brace-fold.min.js" integrity="sha512-5MuaB1PVXvhsYVG0Ozb0bwauN7/D1VU4P8dwo5E/xiB9SXY+VSEhIyxt1ggYk2xaB/RKqKL7rPXpm1o1IlTQDA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/foldgutter.min.css" integrity="sha512-YwkMTlTHn8dBnwa47IF+cKsS00HPiiVhQ4DpwT1KF2gUftfFR7aefepabSPLAs6zrMyD89M3w0Ow6mQ5XJEUCw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/foldgutter.min.js" integrity="sha512-kEVEkqJPlijyiRihpbPuhIW6wkb5wcEaVsfYm/utqn8ToMspk7E2fK5UyZ2HdnJnA4/0HyQwqeKzHNuPm+zyCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/abcdef.min.css" integrity="sha512-Gzm0Fa7gFAThiSK+XOmw4e5Iou/zUMNPgyHcx+RemJUS8KeusL4DlvTM2qfP+A5mfeDexq5uOjFBz29VJP+EMA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <!-- jquery terminal styles -->
        <link rel="stylesheet" href="/honours/webapp/view/js/jquery-terminal/jquery-terminal-2.35.3.css"/>

        <!-- interact js -->
        <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

        <!-- Load d3.js -->
        <script src="https://d3js.org/d3.v4.js"></script>
    </head>
    <body>
        <?php 
            getNavigation();

            function loadExercise() {
                $validation = new Validation();
                $exerciseModel = new ExerciseModel();
                $userExerciseModel = new UserExerciseModel();

                //sanitize input
                $input = $validation->cleanInput($_GET["id"]);

                //validate input
                if ($validation->validateInt($input))
                {
                    $jsonExerciseData = $exerciseModel->getExercise($input);
                    $jsonUserExerciseData = $userExerciseModel->getExerciseMark($_SESSION["userId"], $input, true);

                    if ($jsonExerciseData && $jsonUserExerciseData)
                    {
                        $exerciseData = json_decode($jsonExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);
                        $userExerciseData = json_decode($jsonUserExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);

                        //if the exercise data has loaded
                        if (!isset($exerciseData["isempty"]) && $userExerciseData)
                        {
                            //if the exercise is visible and the user has the permissions to view the exercise and the user hasnt already completed this exercise
                            if ($exerciseData[0]["visible"] && $exerciseData[0]["availability"] <= $_SESSION["permissionLevel"] && $userExerciseData["points"] == -1)
                            {
                                return $exerciseData;
                            }
                        }
                    }

                    return null;
                }
            }
        ?>

        <div class="container p-3">
            <?php

                $exerciseData = loadExercise();

                if ($exerciseData)
                {
                    $exerciseFileJson = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$exerciseData[0]["exerciseFile"]);

                    $exerciseFile = json_decode($exerciseFileJson, JSON_OBJECT_AS_ARRAY);

                    $tabButtons = array();
                    $tabElements = array();
                    $editorContents = array();

                    ?>

                    <div class="mt-5 mb-5">
                        <h1>Exercise: <?php echo $exerciseData[0]["title"] ?></h1>
                        <p><?php echo $exerciseData[0]["description"] ?></p>
                        <hr>
                        <h2>Completing the exercise</h2>
                        <p>The exercise will require you to <b>fix bugs within the code.</b> All bugs are in the Exercise.cpp file. You can call functions from Exercise.cpp in the main.cpp file</p>
                        <p><b>Once you have completed the exercise, press the submit button below.</b> (or the exercise will submit automatically once the Time Remaining timer reaches 0)</p>
                        <br>
                        <button type="button" class="btn btn-primary debugger-control on-connected" disabled aria-disabled=true id="complete-btn">Submit</button>
                        <hr>
                        <h2>General Instructions</h2>
                        <h3>The Code</h3>
                        <p>You can view the files for this exercise in the editor below by clicking on the file names</p>
                        <p>The main.cpp file and the Exercise.cpp file can be modified, all other files are readonly</p>
                        <br>
                        <h3>Debugger output</h3>
                        <p>Output from the debugger can be seen in the Output window</p>
                        <br>
                        <h3>Program output</h3>
                        <p>Output from the program can be seen in the Program Output window. <b>Output will only appear if it is followed by a newline (std::endl)</b></p>
                        <p>Input can be sent to the program by entering it into the console within the Program Output window</p>
                        <br>
                        <h3>General environment rules</h3>
                        <p><b>The environment will time you out if you are inactive and you will lose all progress in the exercise</b></p>
                        <p>You can open the environment in multiple tabs, but you can only run code in one tab at a time</p>
                        <br>
                    </div>

                    <div class="alert alert-danger show d-flex align-items-center timerContainer" role="alert">
                        <p class="pe-1 m-0"><b>Time remaining: </b></p>
                        <p id="timerText" class="m-0">10:00</p>
                    </div>

                    <span id="exerciseFileLocation" class="d-none"><?php echo "/honours/webapp/view/exerciseFiles/".$exerciseData[0]["exerciseFile"] ?></span>

                    <div class="row pt-1 pb-1 ">
                        <div class="col-8">
                            <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control on-connected ms-1" id="play-btn"><span class="mdi mdi-play me-2"></span>Run program</button>
                            <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control d-none ms-1" id="continue-btn"><span class="mdi mdi-play me-2"></span>Continue</button>
                            <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control d-none ms-1" id="stop-btn" aria-label="Stop"><span class="mdi mdi-stop" title="stop"></span></button>
                        </div>
                        <div class="col-4">
                            <div class="float-end">
                                <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control debugger-step-control d-none ms-1" id="step-into-btn" aria-label="Step into"><span class="mdi mdi-arrow-down" title="step into"></span></button>
                                <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control debugger-step-control d-none ms-1" id="step-over-btn" aria-label="Step over"><span class="mdi mdi-arrow-down-right" title="step over"></span></button>
                                <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control debugger-step-control d-none ms-1" id="step-out-btn" aria-label="Step out"><span class="mdi mdi-arrow-up" title="step out"></span></button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <ul class="nav-tabs nav" role="tablist">
                                <?php
                                    foreach ($exerciseFile["compilation"]["writable"] as $fileName)
                                    {
                                        //get the contents of this file
                                        $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                        $pathInfo = pathinfo($fileName);

                                        ?>
                                        <li class="nav-item">
                                            <button class="nav-link tab-header <?php if ($pathInfo["filename"] == "main") {echo "active";} ?>" id="<?php  echo str_replace(".", "", $pathInfo["basename"]); ?>File" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace(".", "", $pathInfo["basename"]); ?>FileContainer" type="button" role="tab" aria-controls="<?php echo $pathInfo["basename"]; ?>" aria-selected="false"><?php echo $pathInfo["basename"]; ?></button>
                                        </li>
                                        <?php
                                    }
                                ?>
                                <?php
                                    foreach ($exerciseFile["compilation"]["readonly"] as $fileName)
                                    {
                                        //get the contents of this file
                                        $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                        $pathInfo = pathinfo($fileName);

                                        ?>
                                        <li class="nav-item">
                                            <button class="nav-link tab-header <?php if ($pathInfo["filename"] == "main") {echo "active";} ?>" id="<?php  echo str_replace(".", "", $pathInfo["basename"]); ?>File" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace(".", "", $pathInfo["basename"]); ?>FileContainer" type="button" role="tab" aria-controls="<?php echo $pathInfo["basename"]; ?>" aria-selected="false"><?php echo $pathInfo["basename"]; ?></button>
                                        </li>
                                        <?php
                                    }
                                ?>
                            </ul>
                            

                            <div class="tab-content">

                                <?php
                                    foreach ($exerciseFile["compilation"]["writable"] as $fileName)
                                    {
                                        //get the contents of this file
                                        $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                        $pathInfo = pathinfo($fileName);

                                        ?>

                                        <div class="tab-pane fade <?php if ($pathInfo["filename"] == "main") {echo "show active ";} ?>" id="<?php echo str_replace(".", "", $pathInfo["basename"]); ?>FileContainer" role="tabpanel" aria-labelledby="<?php echo $pathInfo["filename"]; ?>File">
                                            <textarea id="<?php echo $pathInfo["basename"]; ?>" class="editor resize">
<?php echo $file; ?>
                                            </textarea>
                                        </div>

                                        <?php
                                    }

                                    foreach ($exerciseFile["compilation"]["readonly"] as $fileName)
                                    {
                                        //get the contents of this file
                                        $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                        $pathInfo = pathinfo($fileName);

                                        ?>

                                        <div class="tab-pane fade <?php if ($pathInfo["filename"] == "main") {echo "show active ";} ?>" id="<?php echo str_replace(".", "", $pathInfo["basename"]); ?>FileContainer" role="tabpanel" aria-labelledby="<?php echo $pathInfo["filename"]; ?>File">
                                            <textarea id="<?php echo $pathInfo["basename"]; ?>" class="editor resize" readonly>
<?php echo $file; ?>
                                            </textarea>
                                        </div>

                                        <?php
                                    }

                                ?>
                            </div>
                            <div class="d-flex justify-content-center pb-2 pt-2">
                                <button type="button" class="btn btn-dark theme me-1" id="increase-code-size-btn">+ Increase Font Size</button>
                                <button type="button" class="btn btn-dark theme me-1" id="decrease-code-size-btn">- Decrease Font Size</button>
                            </div>
                            
                            
                        </div>
                        <!-- debug output window -->
                        <div class="col-sm d-none mt-2 border ms-sm-4 pb-3" id="debug-output-window">
                            <h2 class="h4 ps-2 pt-3">Variable states</h2>
                            <hr>
                            <div class="overflow-auto resize max-height-box table-responsive">
                                <table class="table">
                                    <thead>
                                        <th scope="col" class="w-25">Name</th>
                                        <th scope="col" class="w-25">Value</th>
                                        <th scope="col" class="w-25">Type</th>
                                    </thead>
                                    <tbody id="debug-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- compilation message output -->
                        <div class="col-sm mt-2 border pb-3 me-3">
                            <h2 class="h4 ps-2 pt-3">Output</h2>
                            <hr>
                            <p class="ps-2">See debugger output messages here</p>
                            <div class="overflow-auto resize max-height-box" id="compilation-messages-box">
                                <ul class="list-unstyled list-group">
                                    
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm mt-2 border ms-sm-3">
                            <h2 class="h4 ps-2 pt-3">Program Output</h2>
                            <hr>
                            <!-- container for terminal -->
                            <div id="code-output"></div>
                            <div class="d-flex justify-content-center mt-3 mb-3">
                                <button type="button" class="btn btn-dark theme me-1" id="increase-terminal-size-btn">+ Increase Font Size</button>
                                <button type="button" class="btn btn-dark theme me-1" id="clear-terminal-btn">Clear Terminal</button>
                                <button type="button" class="btn btn-dark theme me-1" id="decrease-terminal-size-btn">- Decrease Font Size</button>
                            </div>
                        </div>
                    </div>

                    

                    <!-- load debugger connection modal -->
                    <div class="modal fade" id="load-debugger-modal" tabindex="-1" aria-labelledby="load-debugger-modal" aria-hidden="false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h5 modal-title">Environment Status</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="modal-body">
                                    <div class="d-flex align-items-center" id="connecting">
                                        <p><strong id="debugger-load-message">Connecting to server....</strong></p>
                                        <div class="spinner-border ms-auto" role="status" aria-hidden="true" id="spinner"></div>
                                    </div>
                                    <p>Status: <span id="debugger-load-status">Loading...</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn-dark btn" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn-dark theme btn" data-bs-dismiss="modal" id="switch-active-session-btn">Switch Active Session</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- confirm submit modal -->
                    <div class="modal fade" id="confirm-modal" tabindex="-1" aria-labelledby="confirm-modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h5 modal-title">Submission Confirmation</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="modal-body">
                                    <p>Confirm submission</p>
                                    <p>Make sure you want to submit this exercise, once you submit your answer, you won't be able to attempt the exercise again</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn-secondary btn" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn-primary btn" data-bs-dismiss="modal" id="confirm-complete-btn">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- submission progress modal -->
                    <div class="modal fade" id="submit-exercise-modal" tabindex="-1" aria-labelledby="submit-exercise-modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h5 modal-title">Submitting exercise</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="modal-body">
                                    <div class="d-flex align-items-center" id="submitting">
                                        <p><strong id="submitting-exercise-message">Submitting exercise....</strong></p>
                                        <div class="spinner-border ms-auto" role="status" aria-hidden="true" id="spinner-exercise"></div>
                                    </div>
                                    <p>Status: <span id="submitting-exercise-status">Submitting...</span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn-secondary btn" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="../js/debugger/host/client.js" type="module"></script>

                    <?php
                }
                else
                {
                   echo "Failed to load exercise";
                }

            ?>

            

        </div>

        <script src="../js/setTheme.js"></script>
        <script src="../js/resizing.js"></script>

    </body>
</html>