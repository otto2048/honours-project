<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/ExerciseTypes.php");

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
        <title>Debugging Training Tool - View Exercise</title>
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
                    //get exercise
                    $exerciseModel = new ExerciseModel();
                    $types = new ExerciseTypes();

                    $jsonExerciseData = $exerciseModel->getExercise($input);

                    if ($jsonExerciseData)
                    {
                
                        $exerciseData = json_decode($jsonExerciseData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($exerciseData["isempty"]))
                        {
                    ?>
                            <?php
                                //display exercise details 
                                
                                //display current permission
                                $permission = new PermissionLevels();
                            ?>
                                <h1>Update Exercise - <?php echo $exerciseData[0]["title"]?></h1>

                                <?php
                                    //check for errors on this page
                                    if (isset($_GET["message"]))
                                    {
                                        $message = $_GET["message"];
                                    
                                        printErrorMessage($message);
                                    }
                                ?>

                                <!-- update exercise -->
                                <form role="form" method="POST" action="../../../controller/actionScripts/updateExercise.php">
                                    <input type="text" name="codeId" value=<?php echo $exerciseData[0]["codeId"] ?> required hidden readonly>
                                    <div class="form-group">
                                        <label for="title">Title:</label>
                                        <input type="text" class="form-control" name="title" required id="title" value="<?php echo $exerciseData[0]["title"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <input type="text" class="form-control" name="description" id="description" value="<?php echo $exerciseData[0]["description"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="exerciseFile">Exercise file location:</label>
                                        <input type="text" class="form-control" name="exerciseFile" required id="exerciseFile" value="<?php echo $exerciseData[0]["exerciseFile"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="instructionsFile">Instructions file location:</label>
                                        <input type="text" class="form-control" name="instructionsFile" id="instructionsFile" value="<?php echo $exerciseData[0]["instructionsFile"]; ?>">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="visible" name="visible" <?php if ($exerciseData[0]["visible"]) {echo "checked";} ?>>
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

                                                    if ($value == $exerciseData[0]["availability"])
                                                    {
                                                        $optionString.='selected="selected"';
                                                    }

                                                    $optionString .= ">".$permission->getPermissionLevel($value)."</option>";

                                                    echo $optionString;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group pt-1">
                                        <label for="type">Type:</label>
                                        <select name="type" id="type">
                                            <?php
                                                $typeReflection = new \ReflectionClass("ExerciseTypes");
                                                $values = $typeReflection->getConstants();

                                                foreach ($values as $value)
                                                {
                                                    $optionString = '<option value = "';
                                                    $optionString .= $value.'"';

                                                    if ($value == $exerciseData[0]["type"])
                                                    {
                                                        $optionString.='selected="selected"';
                                                    }

                                                    $optionString .= ">".$types->getExerciseType($value)."</option>";

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
                            <h1>View Exercise</h1>
                    <?php
                            echo "Failed to load exercise data";
                        }
                    }
                    else
                    {
                        ?>
                            <h1>View Exercise</h1>
                    <?php
                            echo "Failed to load exercise data";
                    }
                }
                else
                {
                ?>
                        <h1>View Exercise</h1>
                <?php
                    echo "Failed to load exercise data";
                }             
            ?>

        
        </div>

        <script src="../../js/setTheme.js"></script>

    </body>
</html>