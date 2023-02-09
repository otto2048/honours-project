<!-- homepage -->
<!-- list the exercises the user has to do -->

<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");

    //check if the user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/userArea/signUp.php", name="_self")</script>';
    }
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

            // get the exercises the user has to do



         ?>
    </body>
</html>