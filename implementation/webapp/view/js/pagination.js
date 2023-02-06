window.onload = preparePage();

var pageSize = 10;

function preparePage()
{
    $("#previousPageBtn").addEventListener("click", function() {
        //get latest page
        lastPage = 1;
        loadPage(lastPage - 1, pageSize);
    })
}

function loadPage(pageNum, pageSize)
{
    // load a page of results and output them in the table

    // choose ajax script based on the name of the table
}
