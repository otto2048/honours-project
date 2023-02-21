window.onload = preparePage();

function preparePage()
{
    //set up ace editor
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/tomorrow_night_bright");
    editor.session.setMode("ace/mode/c_cpp");
    editor.setReadOnly(true);

    //set contents to what is stored in local storage
    editor.setValue(localStorage.getItem("code-input"));

    editor.getSession().selection.clearSelection();

}