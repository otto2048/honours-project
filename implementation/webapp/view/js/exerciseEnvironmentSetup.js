// display all of the exercise files as ACE editors

window.onload = preparePage();

function preparePage()
{
    //set up ace editor

    var files = $(".editorContainer");

    for (var i=0; i<files.length; i++)
    {
        var editor = ace.edit(files[i].getAttribute("id"));
        editor.setTheme("ace/theme/tomorrow_night_bright");
        editor.session.setMode("ace/mode/c_cpp");
    }
}