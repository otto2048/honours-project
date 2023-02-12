<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/printErrorMessages.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

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

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - View User</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php 
            getNavigation();
        ?>
        
        <div class="container p-3">
            
            <?php

                $validation = new Validation();

                //sanitize input
                $input = $validation->cleanInput($_GET["id"]);

                //validate input
                if ($validation->validateInt($input))
                {
                    //get user
                    $userModel = new UserModel();

                    $permission = new PermissionLevels();

                    $jsonUserData = $userModel->getUserById($input);
                
                    if ($jsonUserData)
                    {

                        $userData = json_decode($jsonUserData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($userData["isempty"]))
                        {
                    ?>
                            <?php
                                //display user details 
                                
                                
                            ?>
                                <h1>Update User - <?php echo $userData[0]["username"]?></h1>

                                <?php
                                    //check for errors on this page
                                    if (isset($_GET["message"]))
                                    {
                                        $message = $_GET["message"];
                                    
                                        printErrorMessage($message);
                                    }
                                ?>

                                <!-- update user -->
                                <form role="form" method="POST" action="../../../controller/actionScripts/updateUser.php">
                                    <input type="text" name="userId" value=<?php echo $userData[0]["userId"] ?> required hidden readonly>
                                    <div class="form-group">
                                        <label for="username">Username:</label>
                                        <input type="text" class="form-control" name="username" required id="username" value=<?php echo $userData[0]["username"] ?>>
                                    </div>
                                    <div class="form-group">
                                        <label for="containerPort">Container Port:</label>
                                        <input type="text" class="form-control" name="containerPort" id="containerPort" value=<?php echo $userData[0]["containerPort"] ?>>
                                    </div>
                                    <div class="form-group pt-1">
                                        <label for="permissionLevel">User group:</label>
                                        <select name="permissionLevel" id="permissionLevel">
                                            <?php
                                                $permissionReflection = new \ReflectionClass("PermissionLevels");
                                                $values = $permissionReflection->getConstants();

                                                foreach ($values as $value)
                                                {
                                                    $optionString = '<option value = "';
                                                    $optionString .= $value.'"';

                                                    if ($value == $userData[0]["permissionLevel"])
                                                    {
                                                        $optionString.='selected="selected"';
                                                    }

                                                    $optionString .= ">".$permission->getPermissionLevel($value)."</option>";

                                                    echo $optionString;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <button class="btn btn-primary float-end mt-2" type="submit">Submit</button>
                                </form>
                            
                        </div>

                    <?php

                        }
                        else
                        {
                    ?>
                            <h1>View User</h1>
                    <?php
                            echo "Failed to load user data";
                        }
                    }
                    else
                    {
                        ?>
                            <h1>View User</h1>
                    <?php
                            echo "Failed to load user data";
                        
                    }
                }
                else
                {
                ?>
                        <h1>View User</h1>
                <?php
                    echo "Failed to load user data";
                }             
            ?>

        
        </div>

        <script src="../../js/setTheme.js"></script>

    </body>
</html>