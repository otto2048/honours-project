window.onload = preparePage();

function preparePage()
{
    //set up ace editor
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/tomorrow_night_bright");
    editor.session.setMode("ace/mode/c_cpp");

    var input = document.getElementsByName("code-input")[0];

    input.value = editor.getSession().getValue();

    editor.getSession().on("change", function () {
    input.value = editor.getSession().getValue();
    });
}