<?php

    //get validation instance for error messages
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    function printErrorMessage($message)
    {
        $validation = new Validation();

        //check for errors on this page
        if (isset($_GET["message"]))
        {
            $message = $_GET["message"];

            $messageobj = json_decode($message, JSON_OBJECT_AS_ARRAY);

            //output errors
            foreach ($messageobj as $message)
            {
?>
                <div class="alert <?php if ($message["success"]) {echo "alert-success";} else {echo "alert-danger";} ?> alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    
                    <?php
                        echo $validation->cleanInput($message["content"])."<br>";
                    ?>
    
                </div>
<?php
            }
        }
    }

?>