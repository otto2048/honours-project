<!-- complete an exercise here -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");

    //check if the user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/userArea/signUp.php", name="_self")</script>';
    }
?>

<!doctype html>

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - Homepage</title>
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
                            <div class="row">
                                <div class="col">
                                    <ul class="nav-tabs nav bg-dark" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link active" id="mainFile" data-bs-toggle="tab" data-bs-target="#mainFileContainer" role="tab" type="button" aria-controls="main.cpp" aria-selected="true">main.cpp</button>
                                        </li>
                                        <?php
                                            foreach ($exerciseFile["user_files"] as $fileName)
                                            {
                                                //get the contents of this file
                                                $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                                $pathInfo = pathinfo($fileName);

                                                ?>
                                                <li class="nav-item">
                                                    <button class="nav-link" id="<?php echo $pathInfo["filename"]; ?>File" data-bs-toggle="tab" data-bs-target="#<?php echo $pathInfo["basename"]; ?>FileContainer" type="button" role="tab" aria-controls="<?php echo $pathInfo["basename"]; ?>" aria-selected="false"><?php echo $pathInfo["basename"]; ?></button>
                                                </li>
                                                <?php
                                            }
                                        ?>
                                    </ul>

                                    <div class="tab-content">
                                        <input type="hidden" name="main.cpp">
                                        <div class="tab-pane fade show active editorContainer" id="mainFileContainer" role="tabpanel" aria-labelledby="mainFile">
                                            <div id="mainEditor" class="editor">
//ENTRY POINT
#include &#60;iostream&#62;
int main() {
    // Write C++ code here
    std::cout &lt;&lt; "Hello world!";

    return 0;
}
                                            </div>
                                        </div>

                                        <?php
                                            foreach ($exerciseFile["user_files"] as $fileName)
                                            {
                                                //get the contents of this file
                                                $file = file_get_contents($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/exerciseFiles/".$fileName);
                                                $pathInfo = pathinfo($fileName);

                                                ?>

                                                <input type="hidden" name="<?php echo $pathInfo["basename"]; ?>">
                                                <div class="tab-pane fade editorContainer" id="<?php echo $pathInfo["basename"]; ?>FileContainer" role="tabpanel" aria-labelledby="<?php echo $pathInfo["filename"]; ?>File">
                                                    <div id="<?php echo $pathInfo["filename"]; ?>Editor" class="editor">
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
                                </div>
                            </div>

                            <?php
                        }
                    }
                }

                //get user files

            ?>

            

        </div>
        <script src="../js/exerciseEnvironmentSetup.js"></script>
    </body>
</html>