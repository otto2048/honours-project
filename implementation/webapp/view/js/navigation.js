//set up active link on pages
window.onload = preparePage();

function preparePage() {
    var linkSel = document.getElementsByName("selectedLink") [0];
    var className = linkSel.getAttribute("class");
    
    if (className)
    {
        addSelected(className);
    }
}

function addSelected (page) {
    var link = document.getElementsByClassName("nav-link");

    for (var i=0; i<link.length; i++)
    {
        if (link[i].getAttribute("href").substring(link[i].getAttribute("href").lastIndexOf('/') + 1) == page) {
            link[i].classList.add("active");
            link[i].setAttribute("aria-current", "page");
            break;
        }
    }
}