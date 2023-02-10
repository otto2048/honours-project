<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/model/AnswerTypes.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/honours/webapp/controller/Validation.php");

    function loadInputField()
    {
        $validate = new Validation();
        $answerType = new AnswerTypes();

        //sanitize input
        $inputId = $validate->cleanInput($_POST["inputId"]);
        $inputTypeId = $validate->cleanInput($_POST["inputTypeId"]);
?>
        <div class="row align-items-center">
            
            <div class="col-4">
                <div class="form-group">
                    <label for=<?php echo $inputId ?>>Input:</label>
                    <input type="text" class="form-control" name=<?php echo $inputId ?> required id=<?php echo $inputId ?>>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group pt-1">
                    <label for=<?php echo $inputTypeId ?>>Input Type:</label>
                    <select name=<?php echo $inputTypeId ?> id=<?php echo $inputTypeId ?>>
                        <?php
                            $answerTypeRef = new \ReflectionClass("AnswerTypes");
                            $values = $answerTypeRef->getConstants();

                            foreach ($values as $value)
                            {
                                $optionString = '<option value = "';
                                $optionString .= $value.'"';
                                $optionString .= ">".$answerType->getAnswerType($value)."</option>";

                                echo $optionString;
                            }
                        ?>
                    </select>
                <button type="button" class="btn btn-outline-dark mt-2 float-end" id="removeInput<?php echo $inputId; ?>">Remove input</button>

                </div>


            </div>
        </div>
<?php
    }

    loadInputField();

?>