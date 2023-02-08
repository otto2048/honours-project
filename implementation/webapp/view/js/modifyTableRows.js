//create and delete rows in a table with ajax

window.onload = preparePage();

function preparePage() {
    //add delete event to all remove buttons
    var buttons = $(".remove");
        
    for (var i=0; i<buttons.length; i++) (function(i){
        buttons[i].onclick = function(){
                var id = $(".remove > .id")[i].innerHTML;

                deleteRow(id, $(".modifyRowsTable")[0].getAttribute("id"));
            }
    }(i));

    //add add row event to submit button in form
    $("#addRowBtn").onclick = function()
    {
        addRow($(".modifyRowsTable")[0].getAttribute("id"));
    }
}

function deleteRow(id, type)
{
    scriptURL = "";

    switch(type)
    {
        case "exerciseAnswerInfoTable":
            scriptURL = "/honours/webapp/controller/ajaxScripts/deleteExerciseAnswer.php";
            break;
        default:
            return;
    }

    $.ajax({
        url: scriptURL,
        type: "POST",
        data: {itemId: id},
        success: function(result)
        {
            if (result != 0)
            {
                //remove the row that was just deleted
                $("#row" + id).remove();

            }
            else
            {
                //add message that row failed to delete
                console.log("failed to delete");
            }
        }
    });
}

function addRow(type)
{
    scriptURL = "";

    switch(type)
    {
        case "exerciseAnswerInfoTable":
            scriptURL = "/honours/webapp/controller/ajaxScripts/addExerciseAnswer.php";
            itemData = {codeId: $("codeId")[0], input: $("#input")[0].value, inputType: $("#inputType")[0].value, output: $("#output")[0].value};
            break;
        default:
            return;
    }

    $.ajax({
        url: scriptURL,
        type: "POST",
        data: itemData,
        dataType: JSON,
        success: function(result)
        {
            if (result != 0)
            {
                //add the row
                body = $(".modifyRowsTable")[0];
                body.insertAdjacentHTML("beforeend", result);
            }
            else
            {
                //add message that row failed to add
                console.log("failed to add");
            }
        }
    });
}