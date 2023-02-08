//create and delete rows in a table with ajax

window.onload = preparePage();

function preparePage() {
    //add delete event to all remove buttons
    var buttons = $(".remove");
        
    for (var i=0; i<buttons.length; i++) (function(i){
        buttons[i].onclick = function(){
                var id = $(".remove .id")[i].innerHTML;

                deleteRow(id);
            }
    }(i));
}

function deleteRow(answerId)
{

}