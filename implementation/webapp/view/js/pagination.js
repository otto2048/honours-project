window.onload = preparePage();

function preparePage()
{
    //disable back button as we always load in on the first page
    $("#previousPageBtn")[0].disabled = true;
    $("#previousPageBtn")[0].ariaDisabled = true;

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

    pageMax = $("#totalPages")[0].textContent;

    //if this is the first page, disable back button
    if (page == 1)
    {
        $("#previousPageBtn")[0].disabled = true;
        $("#previousPageBtn")[0].ariaDisabled = true;

        $("#nextPageBtn")[0].disabled = false;
        $("#nextPageBtn")[0].ariaDisabled = false;
    }
    //if this is the last page, disable next button
    else if (page == pageMax)
    {
        $("#nextPageBtn")[0].disabled = true;
        $("#nextPageBtn")[0].ariaDisabled = true;

        $("#previousPageBtn")[0].disabled = false;
        $("#previousPageBtn")[0].ariaDisabled = false;
    }
    //if this is in between, make sure both buttons are enabled
    else
    {
        $("#nextPageBtn")[0].disabled = false;
        $("#nextPageBtn")[0].ariaDisabled = false;
        
        $("#previousPageBtn")[0].disabled = false;
        $("#previousPageBtn")[0].ariaDisabled = false;
    }
}
