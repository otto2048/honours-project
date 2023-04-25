//js for setting theme

window.onload = preparePage();

function preparePage()
{
    // get theme from local storage if it exists
    if (localStorage.getItem("theme"))
    {
        // set theme
        $("html").attr("data-bs-theme", localStorage.getItem("theme"));
    }
}