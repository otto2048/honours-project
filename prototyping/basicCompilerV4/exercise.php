<!-- complete an exercise here -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");

?>

<!doctype html>

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - Exercise</title>
        <?php include $_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/head.php"; ?>

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
    </head>
    <body>
        <?php 
            getNavigation();
        ?>

        <div class="container p-3">
            <?php

                // get exercise
                $validation = new Validation();

                //sanitize input
                $input = $validation->cleanInput($_GET["id"]);

                //validate input
                if ($validation->validateInt($input))
                {
                    //get exercise
                    $exerciseModel = new ExerciseModel();

                    $jsonExerciseData = $exerciseModel->getExercise($input);

                    if ($jsonExerciseData)
                    {
                        $exerciseData = json_decode($jsonExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($exerciseData["isempty"]))
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
                                <h2>General Instructions</h2>
                                <p>Once you have completed the exercise, press the submit button below.</p>
                                <button type="button" class="btn btn-primary debugger-control" disabled aria-disabled=true id="complete-btn">Submit</button>
                            </div>
                            
                            <span id="exerciseFileLocation" class="d-none"><?php echo "/honours/webapp/view/exerciseFiles/".$exerciseData[0]["exerciseFile"] ?></span>

                            <div class="row">
                                <div class="col-sm">
                                    <ul class="nav-tabs nav" role="tablist">
                                        <?php
                                            foreach ($exerciseFile["user_files"] as $fileName)
                                            {
                                                //get the contents of this file
                                                $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                                $pathInfo = pathinfo($fileName);

                                                ?>
                                                <li class="nav-item">
                                                    <button class="nav-link <?php if ($pathInfo["filename"] == "main") {echo "active";} ?>" id="<?php  echo str_replace(".", "", $pathInfo["basename"]); ?>File" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace(".", "", $pathInfo["basename"]); ?>FileContainer" type="button" role="tab" aria-controls="<?php echo $pathInfo["basename"]; ?>" aria-selected="false"><?php echo $pathInfo["basename"]; ?></button>
                                                </li>
                                                <?php
                                            }
                                        ?>
                                    </ul>
                                    

                                    <div class="tab-content">

                                        <?php
                                            foreach ($exerciseFile["user_files"] as $fileName)
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

                                        ?>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-dark theme me-1" id="increase-code-size-btn">+ Increase Font Size</button>
                                        <button type="button" class="btn btn-dark theme me-1" id="decrease-code-size-btn">- Decrease Font Size</button>
                                    </div>
                                    
                                    
                                </div>
                                <div class="col-sm">

                                    <div class="row  pt-1 pb-1 ">
                                        <div class="col-8">
                                            <button type="button" class="btn btn-dark theme debugger-control ms-1" id="play-btn"><span class="mdi mdi-play me-2"></span>Start Debugging</button>
                                            <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control d-none ms-1" id="continue-btn"><span class="mdi mdi-play me-2"></span>Continue</button>
                                            <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control d-none ms-1" id="pause-btn" aria-label="Pause"><span class="mdi mdi-pause" title="pause"></span></button>
                                            <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control d-none ms-1" id="stop-btn" aria-label="Stop"><span class="mdi mdi-stop" title="stop"></span></button>
                                            <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control d-none ms-1" id="restart-btn" aria-label="Restart"><span class="mdi mdi-restart" title="restart"></span></button>
                                        </div>
                                        <div class="col-4">
                                            <div class="float-end">
                                                <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control debugger-step-control d-none ms-1" id="step-into-btn" aria-label="Step into"><span class="mdi mdi-arrow-down" title="step into"></span></button>
                                                <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control debugger-step-control d-none ms-1" id="step-over-btn" aria-label="Step over"><span class="mdi mdi-arrow-down-right" title="step over"></span></button>
                                                <button type="button" disabled aria-disabled=true class="btn btn-dark theme debugger-control debugger-live-control debugger-step-control d-none ms-1" id="step-out-btn" aria-label="Step out"><span class="mdi mdi-arrow-up" title="step out"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <!-- container for terminal -->
                                    <div id="code-output"></div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-dark theme me-1" id="increase-terminal-size-btn">+ Increase Font Size</button>
                                        <button type="button" class="btn btn-dark theme me-1" id="clear-terminal-btn">Clear Terminal</button>
                                        <button type="button" class="btn btn-dark theme me-1" id="decrease-terminal-size-btn">- Decrease Font Size</button>
                                    </div>
                                    
                                </div>
                            </div>

                            <!-- compilation message output -->
                            <div class="mt-2 border pb-5 ps-1 resize">
                                <h2 class="h4 ps-2 pt-3">Compilation Output</h2>
                                <hr>
                                <p class="ps-2">See compilation messages here</p>
                                <div class="overflow-auto" id="compilation-messages-box">
                                    <ul class="list-unstyled list-group">
                                        
                                    </ul>
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

                            <script src="/honours/basicCompilerV4/js/client.js" type="module"></script>

                            <?php
                        }
                    }
                }

            ?>

            

        </div>

        
        <script src="/honours/basicCompilerV4/js/resizing.js"></script>
        <script src="/honours/webapp/view/js/setTheme.js"></script>

    </body>
</html>