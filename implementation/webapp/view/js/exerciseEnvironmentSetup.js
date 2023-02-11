// display all of the exercise files as ACE editors

window.onload = preparePage();

function preparePage()
{
    //set up ace editor
    var editor = ace.edit("mainEditor");
    editor.setTheme("ace/theme/tomorrow_night_bright");
    editor.session.setMode("ace/mode/c_cpp");

    var editorTest = ace.edit("testEditor");
    editorTest.setTheme("ace/theme/tomorrow_night_bright");
    editorTest.session.setMode("ace/mode/c_cpp");
}