window.onload = preparePage();

function preparePage()
{
    document.getElementById("previousPageBtn").addEventListener("click", function() {
        //get latest page
        latestPage = parseInt($("#pageNum")[0].textContent);

        if (latestPage > 1)
        {
            loadPage(latestPage - 1, $(".paginateTable")[0].getAttribute("id"));
        }
    });

    document.getElementById("nextPageBtn").addEventListener("click", function() {
        //get latest page

        latestPage = parseInt($("#pageNum")[0].textContent);

        totalPages = parseInt($("#totalPages")[0].textContent);

        nextPage = latestPage + 1;

        if (nextPage <= totalPages)
        {
            loadPage(nextPage, $(".paginateTable")[0].getAttribute("id"));
        }
    });
}

function loadPage(page, type)
{
    // load a page of results and output them in the table

    // choose ajax script based on the name of the table

    scriptURL = "";

    switch(type)
    {
        case "userInfoTable":
            scriptURL = "/honours/webapp/controller/ajaxScripts/loadUserPage.php";
            break;
        default:
            return;
    }

    $.ajax({
        url: scriptURL,
        type: "POST",
        data: {pageNum: page},
        success: function(result)
        {
            if (result != 0)
            {
                //output the results in the table based on the type of data
                switch(type)
                {
                    case "userInfoTable":
                        //remove current user info
                        body = $("#userInfoTableBody")[0];
                        body.textContent = '';

                        //add new table info
                        body.insertAdjacentHTML("beforeend", result);
                        break;
                    default:
                        return;
                    
                }

                //update the page number
                $("#pageNum")[0].textContent = page;

            }
        }
    });
}
