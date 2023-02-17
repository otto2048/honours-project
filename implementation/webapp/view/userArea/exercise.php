<!-- complete an exercise here -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");

    //TODO: check if the user has completed this exercise already
    //check if the user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/userArea/signUp.php", name="_self")</script>';
    }
?>

<!doctype html>

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - Exercise</title>
        <?php include "../head.php"; ?>

        <!-- jquery terminal -->
        <script src="https://unpkg.com/jquery.terminal/js/jquery.terminal.min.js"></script>

        <!-- ACE editor -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js" integrity="sha512-IQmiIneKUJhTJElpHOlsrb3jpF7r54AzhCTi7BTDLiBVg0f7mrEqWVCmOeoqKv5hDdyf3rbbxBUgYf4u3O/QcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        
        <!-- jquery terminal styles -->
        <link rel="stylesheet" href="https://unpkg.com/jquery.terminal/css/jquery.terminal.min.css"/>
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
                            <button type="button" class="btn text-light theme-darker float-end debugger-control" disabled aria-disabled=true id="play-btn">Play</button>
                            <button type="button" class="btn text-light theme-darker float-end debugger-control" disabled aria-disabled=true id="complete-btn">Submit</button>
                            
                            
                            <ul class="nav-tabs nav bg-dark" role="tablist">
                                <?php
                                    foreach ($exerciseFile["user_files"] as $fileName)
                                    {
                                        //get the contents of this file
                                        $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                        $pathInfo = pathinfo($fileName);

                                        ?>
                                        <li class="nav-item">
                                            <button class="nav-link <?php if ($pathInfo["filename"] == "main") {echo "active";} ?>" id="<?php echo $pathInfo["filename"]; ?>File" data-bs-toggle="tab" data-bs-target="#<?php echo $pathInfo["basename"]; ?>FileContainer" type="button" role="tab" aria-controls="<?php echo $pathInfo["basename"]; ?>" aria-selected="false"><?php echo $pathInfo["basename"]; ?></button>
                                        </li>
                                        <?php
                                    }
                                ?>
                            </ul>

                            <span id="exerciseFileLocation" class="d-none"><?php echo "/honours/webapp/view/exerciseFiles/".$exerciseData[0]["exerciseFile"] ?></span>

                            <div class="row">
                                <div class="col">
                                    

                                    <div class="tab-content">

                                        <?php
                                            foreach ($exerciseFile["user_files"] as $fileName)
                                            {
                                                //get the contents of this file
                                                $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                                $pathInfo = pathinfo($fileName);

                                                ?>

                                                <div class="tab-pane fade <?php if ($pathInfo["filename"] == "main") {echo "show active ";} ?> editorContainer" id="<?php echo $pathInfo["basename"]; ?>FileContainer" role="tabpanel" aria-labelledby="<?php echo $pathInfo["filename"]; ?>File">
                                                    <div id="<?php echo $pathInfo["basename"]; ?>" class="editor">
<?php echo $file; ?>
                                                    </div>
                                                </div>

                                                <?php
                                            }

                                        ?>
                                    </div>

                                    
                                </div>
                                <div class="col">
                                    <!-- container for terminal -->
                                    <div id="code-output"></div>

                                    <!-- compilation message output -->
                                    <div class="mt-2 border-light border pt-5 pb-5 ps-1">
                                        <h2 class="h4">Compilation Output</h2>
                                    </div>
                                </div>
                            </div>

                            <!-- load debugger connection modal -->
                            <div class="modal fade" id="load-debugger-modal" tabindex="-1" aria-labelledby="load-debugger-modal" aria-hidden="false">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="h5 modal-title">Preparing environment...</h2>
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script src="../js/exerciseClient.js" type="module"></script>

                            <?php
                        }
                    }
                }

            ?>

            

        </div>

        <script src="../js/setTheme.js"></script>

    </body>
</html>