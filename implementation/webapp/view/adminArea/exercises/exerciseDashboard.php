<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/printErrorMessages.php");


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
        <title>Debugging Training Tool - Manage Exercises</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php 
            function getHeader()
            {
                $selected = "exerciseDashboard.php";
                include "../../navigation.php";
            }

            getHeader();

        ?>
        
        <div class="container p-3">
            <h1>Manage Exercises</h1>
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

                        //get first page of exercises

                        $pageSize = 10;
                        $pageLimit = 0;

                        $exerciseModel = new ExerciseModel();
                        $permission = new PermissionLevels();

                        $jsonExerciseData = $exerciseModel->getExercises(1, $pageSize, $pageLimit);

                        if ($jsonExerciseData)
                        {
                            $exerciseData = json_decode($jsonExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);

                            if (!isset($exerciseData["isempty"]))
                            {
                        ?>

                                <!-- view exercises table -->
                                <p class="pb-1 pt-3 mb-0">Click on column headings to sort Exercises by this column</p>
                                <div class="table-responsive">
                                    <table class="table tablesort tablesearch-table paginateTable" id="exerciseInfoTable">
                                        <thead>
                                            <tr>
                                                <th scope="col" data-tablesort-type="int">ID</th>
                                                <th scope="col" data-tablesort-type="string">Title</th>
                                                <th scope="col" data-tablesort-type="string" class="d-none d-sm-none d-md-table-cell">Description</th>
                                                <th scope="col" data-tablesort-type="string" class="d-none d-sm-none d-md-table-cell">Exercise File</th>
                                                <th scope="col" data-tablesort-type="string" class="d-none d-sm-none d-md-table-cell">Instructions File</th>
                                                <th scope="col" data-tablesort-type="string">Visibility</th>
                                                <th scope="col" data-tablesort-type="string">Availability</th>
                                            </tr>
                                        </thead>
                                        <tbody class="paginateTableBody" id="exerciseInfoTableBody">
                                            <?php

                                                //display exercise data
                                                foreach ($exerciseData as $row)
                                                {
                                                    echo '<tr>';
                                                    echo '<td>'.$row["codeId"].'</td>';

                                                    echo '<td><u><a href="exercise.php?id='.$row["codeId"].'" class="moreInfoLink">'.$row["title"].'</a></u></td>';
                                                    echo '<td class="d-none d-sm-none d-md-table-cell">'.$row["description"].'</td>';
                                                    echo '<td class="d-none d-sm-none d-md-table-cell"><u><a href="/honours/webapp/view/adminArea/exercises/exerciseFiles/'.$row["exerciseFile"].'">'.$row["exerciseFile"].'</a></u></td>';
                                                    echo '<td class="d-none d-sm-none d-md-table-cell"><u><a href="/honours/webapp/view/adminArea/exercises/exerciseFiles/'.$row["instructionsFile"].'">'.$row["instructionsFile"].'</a></u></td>';

                                                    echo '<td>';
                                                    if ($row["visible"])
                                                    {
                                                        echo "True";
                                                    }
                                                    else
                                                    {
                                                        echo "False";
                                                    }
                                                    echo '</td>';
                                                    echo '<td>'.$permission->getPermissionLevel($row["availability"]).' and down</td>';
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
                                echo "There are no exercises";
                            }
                        }
                        else
                        {
                            echo "Failed to load exercise data";
                        }                  
                        ?>

                </div>
                <div class="col">
                    <!-- create new exercise -->
                    <h2>Create a new exercise</h2>
                    <form role="form" method="POST" action="../../../controller/actionScripts/createExercise.php">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" name="title" required id="title">
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" class="form-control" name="description" id="description">
                        </div>
                        <div class="form-group">
                            <label for="exerciseFile">Exercise file location:</label>
                            <input type="text" class="form-control" name="exerciseFile" required id="exerciseFile">
                        </div>
                        <div class="form-group">
                            <label for="instructionsFile">Instructions file location:</label>
                            <input type="text" class="form-control" name="instructionsFile" id="instructionsFile">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="visible" name="visible">
                            <label class="form-check-label" for="visible">
                                Visible to users
                            </label>
                        </div>
                        <div class="form-group pt-1">
                            <label for="availability">Availability Level:</label>
                            <select name="availability" id="availability">
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