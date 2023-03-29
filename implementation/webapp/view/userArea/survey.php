<?php
    //SUS survey for web app
    //TODO: more user feedback and explanation of survey

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/SurveyQuestionModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserSurveyModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/SurveyQuestionTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/PermissionLevels.php");


    //check if user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/userArea/signUp.php", name="_self")</script>';
    }

    if ($_SESSION["permissionLevel"] < PermissionLevels::CONTROL)
    {
        echo '<script type="text/javascript">window.open("/honours/webapp/view/login.php", name="_self")</script>';
    }

    //check if the user has already completed the survey
    $userSurveyModel = new UserSurveyModel();

    $jsonData = $userSurveyModel->getUserAnswers($_SESSION["userId"]);

    //if json data returned ok
    if ($jsonData)
    {
        $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

        //if there is results
        if (!isset($data["isempty"]))
        {
            //kick user out of this page
            $failureMessage[0]["success"] = false;
            $failureMessage[0]["content"] = "You have already submitted a survey response";

            echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php?message='.urlencode(json_encode($failureMessage)).'", name="_self")</script>';
        }
    }
    else
    {
        $failureMessage[0]["success"] = false;
        $failureMessage[0]["content"] = "Failed to load survey";

        //kick user out of this page
        echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php?message='.urlencode(json_encode($failureMessage)).'", name="_self")</script>';
    }

?>

<!doctype html>

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - SUS Survey</title>
        <?php include "../head.php"; ?>
    </head>
    <body>
        <?php
            getNavigation();
        ?>
        <div class="container p-3" >
            <div class="border rounded m-auto mt-5 mb-5 p-4 col-8 overflow-auto">
                <h1>SUS Survey</h1>

                <?php
                    //display all survey questions in form with likert answers
                    $surveyQuestionModel = new SurveyQuestionModel();

                    $jsonQuestionData = $surveyQuestionModel->getAllSurveyQuestions();

                    if ($jsonQuestionData)
                    {
                        $questionData = json_decode($jsonQuestionData, JSON_INVALID_UTF8_SUBSTITUTE);

                        if (!isset($questionData["isempty"]))
                        {
                ?>
                            <form id="form" name="form" method="post" action="../../controller/actionScripts/createSurveyResponse.php">
                                <hr>
                <?php
                            foreach ($questionData as $row)
                            {
                                if ($row["type"] == SurveyQuestionTypes::LIKERT)
                                {
                ?>
                                <div class="pb-2 pt-2">
                                    <p class="m-0">Question <?php echo $row["questionId"]; ?>: <?php echo $row["contents"] ?></p>
                                    <ul class="likert">
                                        <li>Strongly Disagree</li>
                                        <li class="likert-option">
                                            <input type="radio" name="<?php echo $row["questionId"]; ?>" value="1" aria-label="question <?php echo $row["questionId"]; ?>, strongly disagree" required/>
                                        </li>
                                        <li class="likert-option">
                                            <input type="radio" name="<?php echo $row["questionId"]; ?>" value="2" aria-label="question <?php echo $row["questionId"]; ?>, disagree" required/>
                                        </li>
                                        <li class="likert-option">
                                            <input type="radio" name="<?php echo $row["questionId"]; ?>" value="3" aria-label="question <?php echo $row["questionId"]; ?>, neither agree or disagree" required/>
                                        </li>
                                        <li class="likert-option">
                                            <input type="radio" name="<?php echo $row["questionId"]; ?>" value="4" aria-label="question <?php echo $row["questionId"]; ?>, agree" required/>
                                        </li>
                                        <li class="likert-option">
                                            <input type="radio" name="<?php echo $row["questionId"]; ?>" value="5" aria-label="question <?php echo $row["questionId"]; ?>, strongly agree" required/>
                                        </li>
                                        <li>Strongly Agree</li>
                                    </ul>
                                </div>
                                <hr>
                <?php
                                }
                                else if ($row["type"] == SurveyQuestionTypes::TEXT)
                                {
                ?>
                                    <div class="form-group">
                                        <p>Question <?php echo $row["questionId"]; ?>: <?php echo $row["contents"] ?></p>
                                        <input type="text" placeholder="Enter answer..." class="form-control" name="<?php echo $row["questionId"]; ?>" aria-label="question <?php echo $row["questionId"]; ?>" id="<?php echo $row["questionId"]; ?>">
                                    </div>
                                    <hr>
                <?php
                                }
                            }
                ?>
                                <input type="submit" class="btn btn-dark theme mt-2 float-end" name="button" value="Submit"/>
                            </form>
                <?php
                        }
                        else
                        {
                            echo "Failed to load SUS Survey";
                        }
                    }
                    else
                    {
                        echo "Failed to load SUS Survey";
                    }
                ?>
            </div>
        </div>

        <script src="../js/setTheme.js"></script>
    </body>
</html>