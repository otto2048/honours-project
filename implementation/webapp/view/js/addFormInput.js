//TODO: delete file?

window.onload = preparePage();

var inputCounter = 1;

function preparePage()
{
    $("#addInput")[0].addEventListener("click", addInputField);
}

function addInputField()
{
    $.ajax({
        url: "/honours/webapp/controller/ajaxScripts/loadInputField.php",
        type: "POST",
        data: {inputId: "input" + inputCounter, inputTypeId: "inputType" + inputCounter},
        success: function(result)
        {
            //add new input field to form
            rows = $("#newAnswerForm .row");
            rows[rows.length - 1].insertAdjacentHTML("afterend", result);

            $("#removeInputinput" + inputCounter)[0].addEventListener("click", removeField);

            //increase input counter
            inputCounter = inputCounter + 1;
        }
    });
}

function removeField()
{
    //remove the row that this button is in
    $(this).parent().parent().parent().remove();
}