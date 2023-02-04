<!-- The navigation for all the web pages -->
<!doctype html>

<html lang="en">
    <head></head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark theme-darker">
            <div class="container-fluid">
                <div class="d-flex">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Debugging Training Tool</a>
                </div>
                <!--        determining which <li> should have the selected ID-->
                <div name="selectedLink" <?php
                    if (isset($selected))
                    {
                        echo "class=".$selected;
                    }
                ?>></div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>

                        <?php 
                            require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

                            //check if user is logged in
                            if (isset($_SESSION["userId"]))
                            {
                                //check if user is an admin
                                if ($_SESSION["permissionLevel"] >= PermissionLevels::ADMIN)
                                {
                        ?>

                                    <li class="nav-item">
                                        <a class="nav-link" href="/honours/webapp/view/adminArea/userDashboard.php">Manage Users</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/honours/webapp/view/adminArea/exerciseDashboard.php">Manage Exercises</a>
                                    </li>
                       

                        <?php
                                }
                        ?>
                        
                                <li class="nav-item">
                                    <a class="nav-link" href="/honours/webapp/view/userArea/logout.php">Logout</a>
                                </li>

                        <?php
                            }
                            else
                            {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/honours/webapp/view/login.php">Login</a>
                        </li>

                        <?php
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
        <script src="../js/navigation.js"></script>
    </body>
</html>