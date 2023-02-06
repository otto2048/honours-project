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
                        ?>
                            <div class="row align-items-center">
                                <div class="col">
                                    <h1>View User - <?php echo $userData[0]["username"]?></h1>
                                </div>
                                <div class="col">

                                    <button class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></button>

                                    <a href="updateUser.php?id=<?php echo $userData[0]["userId"] ?>" class="btn btn-dark ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="edit-btn">Edit <span class="mdi mdi-lead-pencil"></span></a>
                                    
                                </div>
                            </div>
                            <hr class="mt-0">
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

        <!-- delete user modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h5 modal-title">Are you sure you want to delete this user?</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <p>Confirm deletion</p>
                        <p>All user data will be lost!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-dark btn" data-bs-dismiss="modal">Close</button>
                        <a href="../controller/actionScripts/deleteUser.php?SongTitle=<?php echo $song["SongTitle"]; ?>" class="btn btn-danger ps-3 pe-3 ms-1 me-1 float-end mb-1" role="button" id="delete-btn">Delete <span class="mdi mdi-trash-can"></span></a>
                    </div>
                </div>
            </div>
        </div>

        <script src="../../js/deleteConfirmation.js"></script>
    </body>
</html>