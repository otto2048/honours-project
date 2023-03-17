window.onload = preparePage();

function preparePage()
{
    $("#filesInput").change(function() {
        var files = document.getElementById("filesInput").files;

        //list the files
        //$("#filesForm")[0].innerHTML = "";

        for (var i = 0; i < files.length; i++)
        {
            $("#filesForm")[0].append(createFileDiv(files[i].name));
        }
    });
}

function createFileDiv(name)
{
    var div = document.createElement("div");

    div.innerHTML = name;

    div.append(createCheckbox("Compilation File", name.concat("_compilation")));
    div.append(createCheckbox("Test File", name.concat("_test")));
    div.append(createRadio("Writeable", name + "_readwrite", name + "_writable"))
    div.append(createRadio("Readonly", name + "_readwrite", name + "_readonly"))

    return div;
}

function createCheckbox(value, id)
{
    var check = document.createElement("div");

    check.classList = "form-check";

    var checkInput = document.createElement("input");
    checkInput.classList = "form-check-input";
    checkInput.type = "checkbox";
    checkInput.id = id;

    var checkLabel = document.createElement("label");
    checkLabel.classList = "form-check-label";
    checkLabel.setAttribute("for", checkInput.id);
    checkLabel.innerHTML = value;

    check.append(checkInput);
    check.append(checkLabel);

    return check;
}

function createRadio(value, name, id)
{
    var radio = document.createElement("div");

    radio.classList = "form-check";

    var radioInput = document.createElement("input");
    radioInput.classList = "form-check-input";
    radioInput.type = "radio";
    radioInput.name = name;
    radioInput.id = id;

    var radioLabel = document.createElement("label");
    radioLabel.classList = "form-check-label";
    radioLabel.setAttribute("for", radioInput.id);
    radioLabel.innerHTML = value;

    radio.append(radioInput);
    radio.append(radioLabel);

    return radio;
}