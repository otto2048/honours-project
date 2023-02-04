<!-- logout message displayed here -->
<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/logout.php");
?>

<!doctype html>

<html lang="en">
    <head>
        <title>Debugging Training Tool - Logout</title>
        <?php include "head.php"; ?>
    </head>
    <body>
        <?php 
            include "navigation.php";
        ?>

        <div class="container">
            <div class="border border-dark rounded m-auto mt-5 p-4 col-8">
                <h1 class="h2">Logout:</h1>
                <?php
                    if (!isset($_SESSION['userId'])) //if session variable is not set
                    {
                        echo 'You have been logged out.';
                    }
                ?>
            </div>
        </div>
    </body>
</html>