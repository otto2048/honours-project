//handles stuff to do with locals

import * as debug from "/honours/webapp/view/js/debugger/app/client.js";
import * as hostSocket from "/honours/webapp/view/js/debugger/host/client.js";

var currentVariableData;

var visibleVariableLevels = new Map();

var visibleVariableIds = new Set();

//display the children of this variable (the source)
function displayWholeVariable(source)
{
    //get the source row (the parent element)
    var sourceRow = document.getElementById(source);

    var load = false;

    //find the first child of this
    for (var i=0; i<currentVariableData.length; i++)
    {
        if (currentVariableData[i].source[3] == source)
        {
            //if the child is visible
            if (visibleVariableIds.has(currentVariableData[i].target[3]))
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

        for (var i=0; i<currentVariableData.length; i++)
        {
            //check if this element has visible children, pass each child as the source element to this function
            if (currentVariableData[i].source[3] == source)
            {
                if (visibleVariableIds.has(currentVariableData[i].target[3]))
                {
                    displayWholeVariable(currentVariableData[i].target[3])
                }
            }
        }
    }
    
}

function hideVariableDropdown(source, topLevel = true) {
    var sourceRow = document.getElementById(source);

    for (var i=0; i<currentVariableData.length; i++)
    {
        //find the children of this row
        if (currentVariableData[i].source[3] == source)
        {
            var childRow = document.getElementById(currentVariableData[i].target[3]);

            //if this row has children of its own
            if (currentVariableData[i].target[1] === null)
            {
                if (childRow.firstChild.dataset.displayed == "true")
                {
                    //hide children
                    hideVariableDropdown(currentVariableData[i].target[3], false);
                }
            }

            //remove this row
            childRow.remove();

            visibleVariableIds.delete(currentVariableData[i].target[3]);
        }
    }

    if (!topLevel)
    {
        sourceRow.remove();
        visibleVariableIds.delete(source);
    }

    //find parent id
    var parentId = findInitialParent(source);

    visibleVariableLevels.set(parentId, maxDepth(parentId));
}

function maxDepth(parent)
{
    var childrenDepths = new Set();

    for (var i=0; i<currentVariableData.length; i++)
    {
        //check if this element has visible children, pass each child as the source element to this function
        if (currentVariableData[i].source[3] == parent)
        {
            if (visibleVariableIds.has(currentVariableData[i].target[3]))
            {
                childrenDepths.add(maxDepth(currentVariableData[i].target[3]));
            }
        }
    }

    if (childrenDepths.size == 0)
    {
        return 0;
    }

    return Math.max(...childrenDepths) + 1;
}

function findInitialParent(source)
{
    var parentId = source;

    while (!visibleVariableLevels.has(parentId))
    {
        for (var i=0; i<currentVariableData.length; i++)
        {
            if (currentVariableData[i].target[3] == parentId)
            {
                if (currentVariableData[i].source != "top_level")
                {
                    parentId = currentVariableData[i].source[3];
                }
            }
        }
    }

    return parentId;
}

export function displayMoreVariableDetail(data)
{
    //get id of variable to be removed
    var links = data.data.links;
    var id;

    for (var i=0; i<links.length; i++)
    {
        //get the top level variables
        if (links[i].source == "top_level")
        {
            id = links[i].target[3];
        }
    }

    //source stackoverflow (2018)
    //accessed from: https://stackoverflow.com/questions/21987909/how-to-get-the-difference-between-two-arrays-of-objects-in-javascript
    //find the elements that are not in current variables
    var newVariables = links.filter(function(objOne) {
        return !currentVariableData.some(function(objTwo) {
            return objOne.source[3] == objTwo.source[3] && objOne.target[3] == objTwo.target[3];
        });
    });

    //add links to this variable into the current links
    currentVariableData = currentVariableData.concat(newVariables);

    //check if this is refreshing variable in a new frame
    var sourceRow = document.getElementById(id);

    if (sourceRow.firstChild.dataset.displayed == "true")
    {
        var displayedVariables = [];

        for (let index = 0; index < newVariables.length; index++) {
            if (!displayedVariables.includes(newVariables[index].source[3]))
            {
                var sourceRow = document.getElementById(newVariables[index].source[3]);

                if (sourceRow)
                {
                    //if this has actually been clicked
                    if (sourceRow.firstChild.dataset.displayed == "true")
                    {
                        displayVariableDropdown(newVariables[index].source[3]);
                        displayedVariables.push(newVariables[index].source[3]);
                    }
                }
            }
        }
    }
    else
    {
        //we are displaying the whole variable
        displayWholeVariable(id);
    }
}

function sortVariables(a, b) {
    if (a.target[0] < b.target[0])
    {
        return 1;
    }

    if (a.target[0] > b.target[0])
    {
        return -1;
    }

    return 0;
}

function createVariableRow(element, padding)
{
    var tr = document.createElement("tr");
    var name = document.createElement("td");
    var value = document.createElement("td");
    var type = document.createElement("td");

    name.textContent = element.target[4];
    value.textContent = element.target[1];
    type.textContent = element.target[2];
    tr.setAttribute("id", element.target[3]);

    //set padding
    if (padding.set)
    {
        if (padding.value)
        {
            name.style.paddingLeft = padding.value + "rem";
        }
        else
        {
            name.style.paddingLeft = "1.6rem";
        }
    }

    if (element.target[1] === null)
    {
        var dropdown = document.createElement("span");
        dropdown.classList = "mdi mdi-rotate-90 mdi-triangle me-2 variable-table-triangles";
        name.prepend(dropdown);
        name.dataset.displayed = false;
        name.classList.add("variable-pointer");

        name.addEventListener("click", function(i)
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

    return tr;
}

export function displayInitialVariables(data)
{
    var links = data.data.links;

    currentVariableData = links;

    var previousVisVariables = new Map (visibleVariableLevels);

    var tableBody = $("#debug-table")[0];

    tableBody.innerHTML = "";
    visibleVariableLevels.clear();

    var elements = [];

    for (var i=0; i<currentVariableData.length; i++)
    {
        //get the top level variables
        if (currentVariableData[i].source == "top_level")
        {
            elements.push(currentVariableData[i]);
        }
    }

    //sort elements
    elements.sort(sortVariables);

    for (var i=0; i<elements.length; i++)
    {
        tableBody.append(createVariableRow(elements[i], {value: null, set: false}));

        //add to visible variables
        visibleVariableLevels.set(elements[i].target[3], 0);

        visibleVariableIds.add(elements[i].target[3]);
    }

    //find the elements that are still visible
    for (var i = 0; i < elements.length; i++)
    {
        if (previousVisVariables.has(elements[i].target[3]))
        {
            //local dump
            var level = previousVisVariables.get(elements[i].target[3]);

            if (level > 0)
            {
                debug.sendInput("get_local " + elements[i].target[3] + " " + level);

                hostSocket.pingHostFunc();
            }
            
        }
    }
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
    var parentId = findInitialParent(source);

    var elements = [];

    for (var i=0; i<currentVariableData.length; i++)
    {
        //get the top level variables
        if (currentVariableData[i].source[3] == source)
        {
            elements.push(currentVariableData[i]);
        }
    }

    if (elements.length == 0)
    {
        var parentName;

        for (var i=0; i<currentVariableData.length; i++)
        {
            if (currentVariableData[i].target[3] == parentId && currentVariableData[i].source == "top_level")
            {
                parentName = currentVariableData[i].target[0];
            }
        }

        //load the next level of variables
        var level = visibleVariableLevels.get(parentId) + 1;
        
        debug.sendInput("get_local " + parentName + " " + level);
        hostSocket.pingHostFunc();

        return;
    }

    //sort elements
    elements.sort(sortVariables);

    //display elements
    for (var i=0; i<elements.length; i++)
    {
        var tr = createVariableRow(elements[i], {value: newPadding, set: true});
        
        sourceRow.parentNode.insertBefore(tr, sourceRow.nextSibling);

        visibleVariableIds.add(elements[i].target[3]);
    }

    visibleVariableLevels.set(parentId, maxDepth(parentId));   
}