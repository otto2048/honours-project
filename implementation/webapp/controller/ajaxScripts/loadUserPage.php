<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/UserModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");

    function loadUserPage()
    {
        $validate = new Validation();

        //validate and sanitize input
        $pageNumInput = $validate->cleanInput($_POST["pageNum"]);
        $pageSizeInput = $validate->cleanInput($_POST["pageSize"]);
        
        if (!$validate->validateInt($pageNumInput) || !$validate->validateInt($pageSizeInput))
        {
            return;
        }
        
        $pageLimit = 0;
    
        $userModel = new UserModel();
    
        $jsonUserData = $userModel->getUsers(intval($pageNumInput), intval($pageSizeInput), $pageLimit);

        
        $userData = json_decode($jsonUserData, JSON_INVALID_UTF8_SUBSTITUTE);

        if (!isset($userData["isempty"]))
        {
            //display current permission
            $permission = new PermissionLevels();

            //display user data
            foreach ($userData as $row)
            {
                echo '<tr>';
                echo '<td>'.$row["userId"].'</td>';

                echo '<td><u><a href="user.php?id='.$row["userId"].'" class="moreInfoLink">'.$row["username"].'</a></u></td>';
                echo '<td class="d-none d-sm-none d-md-table-cell">'.$row["containerPort"].'</td>';

                echo '<td>'.$permission->getPermissionLevel($row["permissionLevel"]).'</td>';

                echo '</tr>';
            }
        }
        else
        {
            echo "Failed to load user data";
        }
    }

    loadUserPage();

?>