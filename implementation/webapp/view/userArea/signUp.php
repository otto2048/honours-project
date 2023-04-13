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

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - Sign up</title>
        <?php include "../head.php"; ?>
    </head>
    <body>
        <?php
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>

        <div class="container">
            <div class="border rounded m-auto mt-5 p-4 col-8 overflow-auto">
                <h1><u>Debugging Training Tool Sign Up:</u></h1>

                <h2><u>Participant Information Sheet and Consent Form</u></h2>
                <p>Project title: Improving the Debugging Skills of a Novice Programmer through Enhanced Knowledge of Interactive Debugging (EMS6541)</p>
                <p>Researcher name(s): Elizabeth Blogg</p>

                <h3><u>What is the research about?</u></h3>
                <p>This research project aims to create a training tool that improves the debugging skills of novice programmers. The training tool will do this by teaching novices how to use an interactive debugger (a tool that lets users interact with program code as it runs) effectively.</p>

                <h3><u>Do I have to take part?</u></h3>
                <p>This form has been written to help you decide if you would like to take part. It is up to you and you alone whether you wish to take part. If you do decide to take part you will be free to withdraw at any time without providing a reason and without penalty.
<br><br>The data from this survey will be fully anonymised from the outset of its collection, meaning that it will be impossible to withdraw your data if submit your answers to the survey (as you will not be able to be identified within the dataset)
<br><br>If you do not wish for your data to be stored and used within the study, you can still take part and use the training tool under a guest account. Your data will be stored temporarily (this is required for the training tool to function) but your data will be deleted when you logout from the tool</p>

                <h3><u>What will I be required to do?</u></h3>
                <p>Your participation will involve:
                    <ul>
                        <li>watching video(s) relating to the training tool</li>
                        <li>the completion of debugging exercises - you will be given some code and you must fix the bugs within the code</li>
                        <li>completing a System Usability Survey to evaluate the usability of the training tool</li>
                    </ul>
                </p>

                <h3><u>How will you handle my data?</u></h3>
                <p>Your data will be stored in an anonymised form and will only be accessible to Elizabeth Blogg and Dr Suzanne Prior. This means that nobody including the researchers could reasonably identify you within the data. Your data will be stored in a secure web server, with data fully anonymised at the earliest opportunity (i.e., at the point of collection). Your responses are treated in the strictest confidence - it will be impossible to identify individuals within a dataset when any of the research is disseminated (e.g., in publications/presentations). Abertay University acts as Data Controller (DataProtectionOfficer@abertay.ac.uk).</p>

                <h3><u>Retention of research data</u></h3>
                <p>Researchers are obliged to retain research data for up to 10 years' post-publication, however your anonymised research data may be retained indefinitely (e.g., so that researchers engage in open practice and other researchers can access their data to confirm the conclusions of published work). Consistent with our data retention policy, researchers retain consent forms for as long as we continue to hold information about a data subject and for 10 years for published research (including Research Degree thesis).</p>

                <h3><u>Researcher contact details</u></h3>
                <p>Elizabeth Blogg <br>Email: 1900414@abertay.ac.uk</p>

                <h3><u>Project supervisor contact details</u></h3>
                <p>Dr Suzanne Prior <br>Email: s.prior@abertay.ac.uk</p>

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
                        <br>
                        <p>Enter a password and take note of your login details incase you need to re-login to the training tool</p>
                        <div class="form-group">
                            <label for="password">Enter Password:</label>
                            <input type="password" class="form-control" name="password" id="password" required />
                        </div>
                    </div>
                    <input type="submit" id="signUpBtn" class="btn theme text-light mt-2 float-end" name="button" value="Sign up with username and password"/>
                </form>
            </div>
        </div>

        <script src="../js/signUp.js"></script>
        <script src="../js/setTheme.js"></script>
        
    </body>
</html>