<!-- The navigation for all the web pages -->

<?php

    function getNavigation($selected = null)
    {
?>
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark theme pt-3 pb-3">
            <div class="container-fluid">
                <div class="d-flex">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a class="navbar-brand" href="/honours/webapp/view/index.php">Debugging Training Tool</a>
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
                        

                        <?php 
                            require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

                            //check if user is logged in
                            if (isset($_SESSION["userId"]))
                            {
                        ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/honours/webapp/view/index.php">Home</a>
                                </li>
                        <?php
                                //check if user is an admin
                                if ($_SESSION["permissionLevel"] >= PermissionLevels::ADMIN)
                                {
                        ?>
                                    

                                    <li class="nav-item">
                                        <a class="nav-link" href="/honours/webapp/view/adminArea/users/userDashboard.php">Manage Users</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/honours/webapp/view/adminArea/exercises/exerciseDashboard.php">Manage Exercises</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/honours/webapp/view/adminArea/survey/surveyDashboard.php">Manage Survey Questions</a>
                                    </li>

                        <?php
                                }
                        ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/honours/webapp/view/userArea/settings.php">Settings</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="/honours/webapp/view/userArea/survey.php">Survey</a>
                                </li>
                        
                                <li class="nav-item">
                                    <a class="nav-link" href="/honours/webapp/view/logout.php">Logout</a>
                                </li>

                        <?php
                            }
                            else
                            {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/honours/webapp/view/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/honours/webapp/view/userArea/signUp.php">Sign up</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/honours/webapp/view/userArea/settings.php">Settings</a>
                        </li>

                        <?php
                            }
                        ?>
                    </ul>
                </div>

                
            </div>
        </nav>
        <?php //check if user is logged in
            if (isset($_SESSION["userId"]))
            {
        ?>
                <div class="container-fluid theme-darker clearfix">
                    <p class="float-end m-0 p-2 text-light">
                        <?php
                            echo "Logged in as: ".$_SESSION["username"];
                        ?>
                    </p>
                </div>
        <?php
            } ?>
    </div>
        <script src="/honours/webapp/view/js/navigation.js"></script>
<?php
    }

?>