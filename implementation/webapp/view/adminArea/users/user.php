<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    //check if the user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php", name="_self")</script>';
    }

    if ($_SESSION["permissionLevel"] < PermissionLevels::ADMIN)
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php", name="_self")</script>';
    }
?>

<!doctype html>

<html lang="en">
    <head>
        <title>Debugging Training Tool - View User</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php 
            function getHeader()
            {
                $selected = "userDashboard.php";
                include "../../navigation.php";
            }

            getHeader();

        ?>
        
        <div class="container p-3">
            <h1>View User</h1>
            <hr>
            <?php

                $validation = new Validation();

                //sanitize input
                $input = $validation->cleanInput($_GET["id"]);

                //validate input
                if ($validation->validateInt($input))
                {
                    //get user
                    $userModel = new UserModel();

                    $jsonUserData = $userModel->getUserById($input);
                
                    $userData = json_decode($jsonUserData, JSON_INVALID_UTF8_SUBSTITUTE);

                    if (!isset($userData["isempty"]))
                    {
                ?>
                        <?php
                            //display user details 
                            
                            //display current permission
                            $permission = new PermissionLevels();

                            //display user data
                        ?>

                            <h2>User: <?php echo $userData[0]["username"]?></h2>
                            <ul>
                                <li>User ID: <?php echo $userData[0]["userId"] ?></li>
                                <li>Username: <?php echo $userData[0]["username"] ?></li>
                                <li>Container port: <?php echo $userData[0]["containerPort"] ?></li>
                                <li>User Group: <?php echo $permission->getPermissionLevel($userData[0]["permissionLevel"]) ?></li>
                            </ul>
                        
                    </div>

                <?php

                    }
                    else
                    {
                        echo "Failed to load user data";
                    }
                }
                else
                {
                    echo "Failed to load user data";
                }             
            ?>

        
        </div>
    </body>
</html>