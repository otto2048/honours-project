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

<html lang="en">
    <head>
        <title>Debugging Training Tool - Manage Users</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php 
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>
        
        <div class="container p-3">
            <h1>Manage Users</h1>
            <hr>

            <?php
                //check for errors on this page
                if (isset($_GET["message"]))
                {
                    $message = $_GET["message"];
                
                    printErrorMessage($message);
                }
            ?>


            <div class="row">
                <div class="col border-end">
                    <?php

                        //get first page of users

                        $pageSize = 10;
                        $pageLimit = 0;

                        $userModel = new UserModel();
                        $permission = new PermissionLevels();

                        $jsonUserData = $userModel->getUsers(1, $pageSize, $pageLimit);

                        if ($jsonUserData)
                        {
                            $userData = json_decode($jsonUserData, JSON_INVALID_UTF8_SUBSTITUTE);

                            if (!isset($userData["isempty"]))
                            {
                        ?>

                                <!-- view users table -->
                                <p class="pb-1 pt-3 mb-0">Click on column headings to sort Users by this column</p>
                                <div class="table-responsive">
                                    <table class="table tablesort tablesearch-table paginateTable" id="userInfoTable">
                                        <thead>
                                            <tr>
                                                <th scope="col" data-tablesort-type="int">ID</th>
                                                <th scope="col" data-tablesort-type="string">Username</th>
                                                <th scope="col" data-tablesort-type="string">Container port</th>
                                                <th scope="col" data-tablesort-type="string">User Group</th>
                                            </tr>
                                        </thead>
                                        <tbody class="paginateTableBody" id="userInfoTableBody">
                                            <?php

                                                //display user data
                                                foreach ($userData as $row)
                                                {
                                                    echo '<tr>';
                                                    echo '<td>'.$row["userId"].'</td>';

                                                    echo '<td><u><a href="user.php?id='.$row["userId"].'" class="moreInfoLink">'.$row["username"].'</a></u></td>';
                                                    echo '<td>'.$row["containerPort"].'</td>';

                                                    echo '<td>'.$permission->getPermissionLevel($row["permissionLevel"]).'</td>';
                                                    echo '</tr>';
                                                }
                                            ?>
                                        </tbody>
                                    </table>

                                    
                                </div>


                                <button class="btn theme-darker text-light" id="previousPageBtn">Previous page</button>
                                <button class="btn theme-darker text-light float-end" id="nextPageBtn">Next page</button>

                                <p class="text-center">Page: <span id="pageNum">1</span>/<span id="totalPages"><?php echo $pageLimit ?></span></p>

                                <p>Page Size: <span id="pageSize"><?php echo $pageSize ?></span></p>
                            <?php

                            }
                            else
                            {
                                echo "There are no users";
                            }
                        }
                        else
                        {
                            echo "Failed to load user data";
                        }              
                        ?>

                </div>
                <div class="col">
                    <!-- create new user -->
                    <h2>Create a new user</h2>
                    <form role="form" method="POST" action="../../../controller/actionScripts/createUser.php">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" name="username" required id="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" name="password" required id="password">
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
                                        $optionString .= ">".$permission->getPermissionLevel($value)."</option>";

                                        echo $optionString;
                                    }
                                ?>
                            </select>
                        </div>
                        <button class="btn btn-dark float-end mt-2" type="submit">Submit</button>
                    </form>
                </div>
            </div>
            




    </div>
        
        <!-- Auto tables plugin -->
        <script src="../../js/auto-sorter-filter/auto-tables.js"></script>

        <!-- Pagination -->
        <script src="../../js/pagination.js"></script>
    </body>
</html>