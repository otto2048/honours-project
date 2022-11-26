window.onload = preparePage();

function preparePage()
{
    //set up ace editor
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/tomorrow_night_bright");
    editor.session.setMode("ace/mode/c_cpp");

    //if existing input exists, put that into editor
    if (localStorage.getItem("code-input"))
    {
        editor.setValue(localStorage.getItem("code-input"));
        editor.getSession().selection.clearSelection();
    }
    else
    {
        saveEditorInput();
    }

    //listen to change event
    editor.getSession().on("change", function () {
        saveEditorInput();
    });
}

//save the input from the ace editor
function saveEditorInput()
{
    var editor = ace.edit("editor");

    var input = document.getElementsByName("code-input")[0];

    //set hidden input field to editor contents
    input.value = editor.getSession().getValue();

    //save input into local storage
    localStorage.setItem("code-input", editor.getSession().getValue());
}