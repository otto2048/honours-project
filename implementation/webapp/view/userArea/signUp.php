<?php

    // sign up to the study

    // users give consent to take part in the study and have their data saved
    //      if users don't want to take part, they are given a guest account
    //      else, users are given a random username and set their own password
    //            users are assigned into the control or experimental group


    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");

    //handle starting session
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Session.php");

    //check if user is already logged in
    if (isset($_SESSION['userId']))
    {
        header("Location: ../index.php");
    }

?>

<!doctype html>

<html lang="en">
    <head>
        <title>Debugging Training Tool - Sign up</title>
        <?php include "../head.php"; ?>
    </head>
    <body>
        <?php
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>

        <div class="container">
            <div class="border border-dark rounded m-auto mt-5 p-4 col-8 overflow-auto">
                <h1 class="h2">Debugging Training Tool Sign Up:</h1>

                <h2 class="h3">Participant Information Sheet and Consent Form</h2>
                <p>Project title: Improving the Debugging Skills of a Novice Programmer through Enhanced Knowledge of Interactive Debugging (EMS6541)</p>
                <p>Researcher name(s): Elizabeth Blogg</p>

                <!-- TODO: the rest of the consent form -->

                <p><b>Consent statement: </b></p>
                <p>Abertay University attaches high priority to the ethical conduct of research. Please consider the following before indicating your consent on this form. Indicating your consent confirms that you are willing to participate in the research, however, indicating consent does not commit you to anything you do not wish to do and you are free to withdraw your participation at any time. You are indicating consent under the following assumptions:</p>

                <ul>
                    <li>I understand the contents of the participant information sheet and consent form.</li>
                    <li>I have been given the opportunity to ask questions about the research and have had them answered satisfactorily.</li>
                    <li>I understand that my participation is entirely voluntary and that I can withdraw from the research (parts of the project or the entire project) at any time without penalty and without having to provide an explanation.</li>
                    <li>I understand who has access to my data and how it will be handled at all stages of the research project.</li>
                </ul>

                <p>You can find our procedure for complaints (regarding research projects) and our privacy notice and legal basis for processing research data at: <a href="https://www.abertay.ac.uk/legal/privacy-notice-for-research-participants/">Privacy Notice for Research Participants</a></p>

                <p>Please indicate if you consent to the research: </p>
                <p><b>I consent to take part in this study conducted by Elizabeth Blogg, who intends to use my data for further research examining how to improve the debugging skills of a novice programmer</b></p>

                <?php
                    //check for errors on this page
                    if (isset($_GET["message"]))
                    {
                        $message = $_GET["message"];
                    
                        require_once("../printErrorMessages.php");

                        printErrorMessage($message);
                    }
                ?>

                <form id="form" name="form" method="post" action="../../controller/actionScripts/signUp.php">
                    <div class="form-check mb-1">
                        <label class="form-check-label" for="consentFormCheck">
                            I Consent
                        </label>
                        <input class="form-check-input" type="checkbox" name="consentFormCheck" id="consentFormCheck" checked>

                    </div>

                    <div class="form-group"> 
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" name="username" id="username" required readonly>
                    </div>
                        
                    <div id="getUserDetails">
                        <div class="form-group">
                            <label for="password">Enter Password:</label>
                            <input type="password" class="form-control" name="password" id="password" required />
                        </div>
                    </div>
                    <input type="submit" id="signUpBtn" class="btn theme-darker text-light mt-2 float-end" name="button" value="Sign up with username and password"/>
                </form>
            </div>
        </div>

        <script src="../js/signUp.js"></script>
        
    </body>
</html>