import * as debug from "/honours/webapp/view/js/debugger/app/client.js"

export let currentVariableDataObj = {
    currentVariableData: null
};

export var visibleVariableLevels = new Map();

export var visibleVariableIds = new Set();

//display the children of this variable (the source)
export function displayWholeVariable(source)
{
    //get the source row (the parent element)
    var sourceRow = document.getElementById(source);

    var load = false;

    //find the first child of this
    for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
    {
        if (currentVariableDataObj.currentVariableData[i].source[3] == source)
        {
            //if the child is visible
            if (visibleVariableIds.has(currentVariableDataObj.currentVariableData[i].target[3]))
            {
                load = true;
            }
        }
    }

    //if we should display the children of the source element
    if (load)
    {
        sourceRow.firstChild.firstChild.classList.remove("mdi-rotate-90");
        sourceRow.firstChild.firstChild.classList.add("mdi-rotate-135");
        sourceRow.firstChild.dataset.displayed = "true";

        displayVariableDropdown(source);

        for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
        {
            //check if this element has visible children, pass each child as the source element to this function
            if (currentVariableDataObj.currentVariableData[i].source[3] == source)
            {
                if (visibleVariableIds.has(currentVariableDataObj.currentVariableData[i].target[3]))
                {
                    displayWholeVariable(currentVariableDataObj.currentVariableData[i].target[3])
                }
            }
        }
    }
    
}

export function hideVariableDropdown(source, topLevel = true) {
    var sourceRow = document.getElementById(source);

    for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
    {
        //find the children of this row
        if (currentVariableDataObj.currentVariableData[i].source[3] == source)
        {
            var childRow = document.getElementById(currentVariableDataObj.currentVariableData[i].target[3]);

            //if this row has children of its own
            if (currentVariableDataObj.currentVariableData[i].target[1] === null)
            {
                if (childRow.firstChild.dataset.displayed == "true")
                {
                    //hide children
                    hideVariableDropdown(currentVariableDataObj.currentVariableData[i].target[3], false);
                }
            }

            //remove this row
            childRow.remove();

            visibleVariableIds.delete(currentVariableDataObj.currentVariableData[i].target[3]);
        }
    }

    if (!topLevel)
    {
        sourceRow.remove();
        visibleVariableIds.delete(source);
    }

    //find parent id
    var parentId = source;

    while (!visibleVariableLevels.has(parentId))
    {
        for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
        {
            if (currentVariableDataObj.currentVariableData[i].target[3] == parentId)
            {
                if (currentVariableDataObj.currentVariableData[i].source != "top_level")
                {
                    parentId = currentVariableDataObj.currentVariableData[i].source[3];
                }
            }
        }
    }

    visibleVariableLevels.set(parentId, maxDepth(parentId));   

    console.log(visibleVariableLevels);
}

function maxDepth(parent)
{
    var childrenDepths = new Set();

    for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
    {
        //check if this element has visible children, pass each child as the source element to this function
        if (currentVariableDataObj.currentVariableData[i].source[3] == parent)
        {
            if (visibleVariableIds.has(currentVariableDataObj.currentVariableData[i].target[3]))
            {
                childrenDepths.add(maxDepth(currentVariableDataObj.currentVariableData[i].target[3]));
            }
        }
    }

    if (childrenDepths.size == 0)
    {
        return 0;
    }

    return Math.max(...childrenDepths) + 1;
}

export function displayVariableDropdown(source) {
    var sourceRow = document.getElementById(source);

    var sourceRowPadding = sourceRow.firstChild.style.paddingLeft;
    var newPadding = null;

    if (sourceRowPadding)
    {
        var sourceRowPaddingInt = parseInt(sourceRowPadding, 10);
        newPadding = sourceRowPaddingInt + 1.5;
    }

    //find parent id
    var parentId = source;

    while (!visibleVariableLevels.has(parentId))
    {
        for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
        {
            if (currentVariableDataObj.currentVariableData[i].target[3] == parentId)
            {
                if (currentVariableDataObj.currentVariableData[i].source != "top_level")
                {
                    parentId = currentVariableDataObj.currentVariableData[i].source[3];
                }
            }
        }
    }

    var elements = [];

    for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
    {
        //get the top level variables
        if (currentVariableDataObj.currentVariableData[i].source[3] == source)
        {
            elements.push(currentVariableDataObj.currentVariableData[i]);
        }
    }

    if (elements.length == 0)
    {
        var parentName;

        for (var i=0; i<currentVariableDataObj.currentVariableData.length; i++)
        {
            if (currentVariableDataObj.currentVariableData[i].target[3] == parentId)
            {
                if (currentVariableDataObj.currentVariableData[i].source == "top_level")
                {
                    parentName = currentVariableDataObj.currentVariableData[i].target[0];
                }
            }
        }

        //load the next level of variables
        var level = visibleVariableLevels.get(parentId) + 1;
        
        debug.sendInput("get_local " + parentName + " " + level);

        return;
    }

    //sort elements
    elements.sort(function(a, b) {
        if (a.target[0] < b.target[0])
        {
            return 1;
        }

        if (a.target[0] > b.target[0])
        {
            return -1;
        }

        return 0;
    });


    console.log(visibleVariableLevels);

    //display elements
    for (var i=0; i<elements.length; i++)
    {
        var tr = document.createElement("tr");
        var name = document.createElement("td");
        var value = document.createElement("td");
        var type = document.createElement("td");

        //set text
        name.textContent = elements[i].target[4];
        value.textContent = elements[i].target[1];
        type.textContent = elements[i].target[2];

        //set id on row
        tr.setAttribute("id", elements[i].target[3]);

        //set padding
        if (newPadding)
        {
            name.style.paddingLeft = newPadding + "rem";
        }
        else
        {
            name.style.paddingLeft = "1.6rem";
        }

        if (elements[i].target[1] === null)
        {
            var dropdown = document.createElement("span");
            dropdown.classList = "mdi mdi-rotate-90 mdi-triangle me-2 variable-table-triangles";
            name.prepend(dropdown);
            name.dataset.displayed = false;
            name.classList.add("variable-pointer");

            name.addEventListener("click", function()
            {
                if (this.dataset.displayed == "false")
                {
                    //change arrow orientation
                    this.firstChild.classList.remove("mdi-rotate-90");
                    this.firstChild.classList.add("mdi-rotate-135");

                    //display variables
                    displayVariableDropdown(this.parentElement.getAttribute("id"));

                    this.dataset.displayed = "true";
                }
                else
                {
                    //change arrow orientation
                    this.firstChild.classList.remove("mdi-rotate-135");
                    this.firstChild.classList.add("mdi-rotate-90");

                    //hide variables
                    hideVariableDropdown(this.parentElement.getAttribute("id"));

                    this.dataset.displayed = "false";
                }
            });
        }

        tr.append(name);
        tr.append(value);
        tr.append(type);
        sourceRow.parentNode.insertBefore(tr, sourceRow.nextSibling);

        visibleVariableIds.add(elements[i].target[3]);
    }

    visibleVariableLevels.set(parentId, maxDepth(parentId));   
}