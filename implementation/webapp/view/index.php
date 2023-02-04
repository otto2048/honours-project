<!-- homepage -->
<!-- list the exercises the user has to do -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
?>

<!doctype html>

<html lang="en">
    <head>
        <title>Debugging Training Tool - Homepage</title>
        <?php include "head.php"; ?>
    </head>
    <body>
        <?php 
            function getHeader()
            {
                $selected = "index.php";
                include "navigation.php";
            }

            getHeader();

         ?>
    </body>
</html>