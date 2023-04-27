//js for managing theme

window.onload = preparePage();

function preparePage()
{
    var themeSelector = $("#theme");

    //set selector to selected value
    if (localStorage.getItem("theme"))
    {
        themeSelector.val(localStorage.getItem("theme"));
    }

    //handle theme selector changing
    themeSelector.on("change", function()
    {
        var changed = !this.options[this.selectedIndex].defaultSelected;

        if (changed)
        {
            //set new theme in local storage
            localStorage.setItem("theme", this.value);

            // set theme
            $("html").attr("data-bs-theme", localStorage.getItem("theme"));
        }
    });
}
