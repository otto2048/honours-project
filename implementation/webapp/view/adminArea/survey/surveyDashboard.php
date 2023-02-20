<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/SurveyQuestionModel.php");
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
        <title>Debugging Training Tool - Manage Survey Questions</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php 
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>
        
        <div class="container p-3">
            <h1>Manage Survey Questions</h1>
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

                        //get survey questions

                        $surveyQuestionModel = new SurveyQuestionModel();

                        $jsonQuestionData = $surveyQuestionModel->getAllSurveyQuestions();

                        if ($jsonQuestionData)
                        {
                            $questionData = json_decode($jsonQuestionData, JSON_INVALID_UTF8_SUBSTITUTE);

                            if (!isset($questionData["isempty"]))
                            {
                        ?>

                                <!-- view survey question table -->
                                <p class="pb-1 pt-3 mb-0">Click on column headings to sort Survey Questions by this column</p>
                                <div class="table-responsive">
                                    <table class="table tablesort tablesearch-table paginateTable" id="surveyQuestionTable">
                                        <thead>
                                            <tr>
                                                <th scope="col" data-tablesort-type="int">ID</th>
                                                <th scope="col" data-tablesort-type="string">Contents</th>
                                            </tr>
                                        </thead>
                                        <tbody class="paginateTableBody" id="surveyQuestionTableBody">
                                            <?php
                                                foreach ($questionData as $row)
                                                {
                                                    echo '<tr>';

                                                    echo '<td><u><a href="user.php?id='.$row["questionId"].'" class="moreInfoLink">'.$row["questionId"].'</a></u></td>';

                                                    echo '<td>'.$row["contents"].'</td>';
                                                    echo '</tr>';
                                                }
                                            ?>
                                        </tbody>
                                    </table>

                                    
                                </div>
                            <?php

                            }
                            else
                            {
                                echo "There are no questions";
                            }
                        }
                        else
                        {
                            echo "Failed to load question data";
                        }              
                        ?>

                </div>
                <div class="col">
                    <!-- create new question -->
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
                        <button class="btn btn-primary float-end mt-2" type="submit">Submit</button>
                    </form>
                </div>
            </div>
            




    </div>
        
        <!-- Auto tables plugin -->
        <script src="../../js/auto-sorter-filter/auto-tables.js"></script>

        <script src="../../js/setTheme.js"></script>

    </body>
</html>