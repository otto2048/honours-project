// display all of the exercise files as ACE editors

window.onload = preparePage();

function preparePage()
{
    //set up ace editor

    var files = $(".editorContainer");
    var editors = [];

    for (var i=0; i<files.length; i++)
    {
        editors.push(ace.edit(files[i].getAttribute("id"))); 
    }

    for (var i=0; i<editors.length; i++)
    {
        editors[i].setTheme("ace/theme/tomorrow_night_bright");
        editors[i].session.setMode("ace/mode/c_cpp");

        var input = $("#" + files[i].getAttribute("id")).closest("input");

        input.value = editors[i].session.getValue();

        console.log(input.value);
    }

    for (var i=0; i<editors.length; i++) (function(i) {
        //save the editor input
        editors[i].session.on("change", function(){
        var input = $("#" + files[i].getAttribute("id")).closest("input");

        input.value = editors[i].session.getValue();

        console.log(input.value);

        });
    }(i));
}