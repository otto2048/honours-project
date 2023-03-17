<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/ExerciseModel.php");
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
        <title>Debugging Training Tool - Create Exercise</title>
        <?php include "../../head.php"; ?>
    </head>
    <body>
        <?php
            getNavigation();
        ?>
        
        <div class="container p-3">
            <?php
                //display exercise details 
                
                //display current permission
                $permission = new PermissionLevels();
                $types = new ExerciseTypes();
            ?>
            <h1>Create Exercise - <?php echo $exerciseData[0]["title"]?></h1>

            <?php
                //check for errors on this page
                if (isset($_GET["message"]))
                {
                    $message = $_GET["message"];
                
                    printErrorMessage($message);
                }
            ?>

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
                                $optionString .= ">".$types->getExerciseType($value)."</option>";

                                echo $optionString;
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="availablePoints">Available points:</label>
                    <input type="text" class="form-control" name="availablePoints" id="availablePoints">
                </div>
                <button class="btn btn-primary float-end mt-2" type="submit">Submit</button>
            </form>
        
        </div>

        <script src="../../js/setTheme.js"></script>

    </body>
</html>