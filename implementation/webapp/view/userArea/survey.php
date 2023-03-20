<?php
    //SUS survey for web app
    //TODO: more user feedback and explanation of survey
    echo "here";

    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/SurveyQuestionModel.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/models/UserSurveyModel.php");
    echo "here 8";

    //check if user is allowed to be here
    if (!isset($_SESSION["permissionLevel"]))
    {
    echo "here 2";

        echo '<script type="text/javascript">window.open("/honours/webapp/view/userArea/signUp.php", name="_self")</script>';
    }

    if ($_SESSION["permissionLevel"] < PermissionLevels::CONTROL)
    {
    echo "here 3";

        echo '<script type="text/javascript">window.open("/honours/webapp/view/login.php", name="_self")</script>';
    }

    echo "here 7";

    //check if the user has already completed the survey
    $userSurveyModel = new UserSurveyModel();
    echo "here 6";

    $jsonData = $userSurveyModel->getUserAnswers($_SESSION["userId"]);

    //if json data returned ok
    if ($jsonData)
    {
    echo "here 4";

        $data = json_decode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE);

        //if there is results
        if (!isset($data["isempty"]))
        {
    echo "here 5";

            //kick user out of this page
            echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php", name="_self")</script>';
        }
    }
    else
    {
    echo "here 6";

        //kick user out of this page
        echo '<script type="text/javascript">window.open("/honours/webapp/view/index.php", name="_self")</script>';
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