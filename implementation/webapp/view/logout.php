<!-- logout message displayed here -->
<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/logout.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/view/navigation.php");
?>

<!doctype html>

<html lang="en" data-bs-theme="dark">
    <head>
        <title>Debugging Training Tool - Logout</title>
        <?php include "head.php"; ?>
    </head>
    <body>
        <?php 
            getNavigation(basename($_SERVER['PHP_SELF']));
        ?>

        <div class="container">
            <div class="border rounded m-auto mt-5 p-4 col-8">
                <h1>Logout:</h1>
                <?php
                    if (!isset($_SESSION['userId'])) //if session variable is not set
                    {
                ?>
                        <p class="pb-3">You have been logged out.</p>
                <?php
                    }
                ?>

                <h2>Participant Debrief Form</h2>
                <p>Project title: Improving the Debugging Skills of a Novice Programmer through Enhanced Knowledge of Interactive Debugging (EMS6541)</p>
                <p>Researcher name(s): Elizabeth Blogg</p>

                <h3>Nature of research</h3>
                <p>This research project aims to create a training tool that improves the debugging skills of novice programmers. The training tool will do this by teaching novices how to use an interactive debugger (a tool that lets users interact with program code as it runs) effectively.</p>
                
                <h3>Data</h3>
                <p>Your data will be stored, shared and processed as outlined in the Participant Information Sheet and Consent form for this project (EMS6541). Your information (data) is anonymous at the point of collection, we will not be able to withdraw it after that point because we will no longer know which information (data) is yours.</p>

                <h3>Sources of support</h3>
                <p>If taking part in the research has raised any issues for you personally, you can contact Student Counselling, see<a href="https://www.abertay.ac.uk/life/student-support-and-services/counselling/"> student counselling</a></p>

                <h3>Contact</h3>
                <p>If you have any further questions you may contact the researcher or my supervisor on the details below.</p>

                <p class="mb-0"><i>Researcher Contact Details</i></p>
                <p>Elizabeth Blogg <br>Email: 1900414@abertay.ac.uk</p>

                <p class="mb-0"><i>Project Supervisor Contact Details</i></p>
                <p>Dr Suzanne Prior <br>Email: s.prior@abertay.ac.uk</p>

            </div>
        </div>

        <script src="js/setTheme.js"></script>

    </body>
</html>