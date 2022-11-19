window.onload = preparePage();

function preparePage()
{
    //set up ace editor
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/tomorrow_night_bright");
    editor.session.setMode("ace/mode/c_cpp");

    saveEditorInput();

    editor.getSession().on("change", function () {
        saveEditorInput();
    });
}

function saveEditorInput()
{
    var editor = ace.edit("editor");

    var input = document.getElementsByName("code-input")[0];

    //set hidden input field to editor contents
    input.value = editor.getSession().getValue();

    //save input into local storage
    localStorage.setItem("code-input", editor.getSession().getValue());
}