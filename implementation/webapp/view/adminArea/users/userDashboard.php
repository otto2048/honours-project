<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

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
            function getHeader()
            {
                $selected = "userDashboard.php";
                include "../../navigation.php";
            }

            getHeader();

        ?>
        
        <div class="container p-3">
            <h1>Manage Users</h1>
            <hr>
            <?php

                //get first page of users
                require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/UserModel.php");

                $pageSize = 10;
                $pageLimit = 0;

                $userModel = new UserModel();

                $jsonUserData = $userModel->getUsers(1, $pageSize, $pageLimit);

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
                                <th scope="col" data-tablesort-type="string" class="d-none d-sm-none d-md-table-cell">Username</th>
                                <th scope="col" data-tablesort-type="string" class="d-none d-sm-none d-md-table-cell">Container port</th>
                                <th scope="col" data-tablesort-type="string">User Group</th>
                                <th scope="col">Link to User page</th>
                            </tr>
                        </thead>
                        <tbody class="paginateTableBody" id="userInfoTableBody">
                            <?php

                                //display current permission
                                $permission = new PermissionLevels();

                                //display user data
                                foreach ($userData as $row)
                                {
                                    echo '<tr>';
                                    echo '<td>'.$row["userId"].'</td>';

                                    echo '<td><u><a href="user.php?id='.$row["id"].'" class="moreInfoLink">'.$row["username"].'</a></u></td>';
                                    echo '<td class="d-none d-sm-none d-md-table-cell">'.$row["containerPort"].'</td>';

                                    echo '<td>'.$permission->getPermissionLevel($row["permissionLevel"]).'</td>';

                                    echo '<td><a href="user.php?id='.$row["id"].'" class="btn theme-darker text-light" role="button">More info...</a></td>';
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
                    echo "Failed to load user data";
                }                  
            ?>


        <!-- create new user -->

        <!-- update user -->

        <!-- delete user -->

        
        </div>
        
        <!-- Auto tables plugin -->
        <script src="../../js/auto-sorter-filter/auto-tables.js"></script>

        <!-- Pagination -->
        <script src="../../js/pagination.js"></script>
    </body>
</html>