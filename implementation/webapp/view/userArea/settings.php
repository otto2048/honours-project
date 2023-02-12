<!-- user settings for web app -->

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
        <title>Debugging Training Tool - Settings</title>
        <?php include "../head.php"; ?>
    </head>
    <body>
        <?php
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>
        <div class="container p-3" >
            <h1>Your Settings</h1>
            <h2>Theme: </h2>
            <div class="form-group pt-1">
                <label for="theme">Theme:</label>
                <select name="theme" id="theme">
                    <option value="dark">Dark</option>
                    <option value="light">Light</option>
                </select>
            </div>
        </div>
    </body>
</html>