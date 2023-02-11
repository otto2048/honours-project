<!-- complete an exercise here -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

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

            <div class="row">
                <div class="col">
                    <ul class="nav-tabs nav bg-dark" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="mainFile" data-bs-toggle="tab" data-bs-target="#mainFileContainer" role="tab" type="button" aria-controls="main.cpp" aria-selected="true">main.cpp</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="testFile" data-bs-toggle="tab" data-bs-target="#testFileContainer" type="button" role="tab" aria-controls="test.cpp" aria-selected="false">test.cpp</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active editorContainer" id="mainFileContainer" role="tabpanel" aria-labelledby="mainFile">
                            <div id="mainEditor" class="editor">
                            #include &#60;iostream&#62;
            int main() {
                // Write C++ code here
                std::cout &lt;&lt; "Hello world!";

                return 0;
            }
                            </div>
                        </div>

                        <div class="tab-pane fade editorContainer" id="testFileContainer" role="tabpanel" aria-labelledby="testFile">
                            <div id="testEditor" class="editor">
                            #include &#60;iostream&#62;
            int main() {
                // Write C++ code here
                std::cout &lt;&lt; "This is a test!";

                return 0;
            }
                            </div>
                        </div>
                    </div>

                    
                </div>
                <div class="col">
                    <!-- container for terminal -->
                    <div id="code-output"></div>
                </div>
            </div>

        </div>
        <script src="../js/exerciseEnvironmentSetup.js"></script>
    </body>
</html>