window.onload = preparePage();

function preparePage()
{
    //set up ace editor
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/tomorrow_night_bright");
    editor.session.setMode("ace/mode/c_cpp");
}