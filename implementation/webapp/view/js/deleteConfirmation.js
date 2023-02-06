window.onload = preparePage();

function preparePage()
{
    //attach listener to delete modal
    var deleteBtn = document.getElementById("delete-btn");

    deleteBtn.addEventListener("click", loadModal);
}

function loadModal()
{
    //show modal
    $("#delete-modal").modal("show");
}