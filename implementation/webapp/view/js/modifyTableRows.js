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