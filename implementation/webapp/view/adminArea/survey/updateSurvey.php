<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/SurveyQuestionModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/SurveyQuestionTypes.php");

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
        <title>Debugging Training Tool - Update Survey Question</title>
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
                    //get survey question
                    $surveyQuestionModel = new SurveyQuestionModel();
                    $types = new SurveyQuestionTypes();

                    $jsonQuestionData = $surveyQuestionModel->getSurveyQuestion($input);

                    if ($jsonQuestionData)
                    {
                        $questionData = json_decode($jsonQuestionData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($questionData["isempty"]))
                        {
                            //display survey question details 
                    ?>
                            <h1>Update Survey Question - <?php echo $questionData[0]["questionId"]?></h1>

                            <?php
                                //check for errors on this page
                                if (isset($_GET["message"]))
                                {
                                    $message = $_GET["message"];
                                
                                    printErrorMessage($message);
                                }
                            ?>

                            <!-- update survey question -->
                            <form role="form" method="POST" action="../../../controller/actionScripts/updateSurveyQuestion.php">
                                <input type="text" name="questionId" value=<?php echo $questionData[0]["questionId"] ?> required hidden readonly>
                                <div class="form-group">
                                    <label for="contents">Contents:</label>
                                    <input type="text" class="form-control" name="contents" required id="contents" value="<?php echo $questionData[0]["contents"] ?>">
                                </div>
                                <div class="form-group pt-1">
                                        <label for="type">Type:</label>
                                        <select name="type" id="type">
                                            <?php
                                                $typeReflection = new \ReflectionClass("SurveyQuestionTypes");
                                                $values = $typeReflection->getConstants();

                                                foreach ($values as $value)
                                                {
                                                    $optionString = '<option value = "';
                                                    $optionString .= $value.'"';

                                                    if ($value == $questionData[0]["type"])
                                                    {
                                                        $optionString.='selected="selected"';
                                                    }

                                                    $optionString .= ">".$types->getQuestionType($value)."</option>";

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
                            <h1>Update Survey Question</h1>
                    <?php
                            echo "Failed to load survey question data";
                        }
                    }
                    else
                    {
                        ?>
                            <h1>Update Survey Question</h1>
                    <?php
                            echo "Failed to load survey question data";
                    }
                }
                else
                {
                ?>
                        <h1>Update Survey Question</h1>
                <?php
                    echo "Failed to load survey question data";
                }             
            ?>

        
        </div>

        <script src="../../js/setTheme.js"></script>

    </body>
</html>