<?php

    //handle starting session
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

    //check if user is already logged in
    if (isset($_SESSION['userId']))
    {
        header("Location: index.php");
    }
?>

<!doctype html>

<html lang="en">
    <head>
        <title>Debugging Training Tool - Login</title>
        <?php include "head.php"; ?>
    </head>
    <body>
        <?php
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>

        <div class="container">
            <div class="border border-dark rounded m-auto mt-5 p-4 col-8 overflow-auto">
                <h1 class="h2">Login:</h1>

                <hr>

                <?php
                    //check for errors on this page
                    if (isset($_GET["message"]))
                    {
                        $message = $_GET["message"];
                    
                        require_once("printErrorMessages.php");

                        printErrorMessage($message);
                    }
                ?>

                <form id="form" name="form" method="post" action="../controller/actionScripts/login.php">
                    <div class="form-group"> 
                        <label for="username">Enter Username:</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Enter Password:</label>
                        <input type="password" class="form-control" name="password" id="password" required/>
                    </div>
                    <input type="submit" class="btn theme-darker text-light mt-2 float-end" name="button" value="Login"/>
                </form>
            </div>
        </div>
    </body>
</html>