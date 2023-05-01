//handles stuff to do with editors and breakpoints

export var editors = [];
export var files = $(".editor");

var currentFile = "main.cpp";
var trackingFile = null;

export function toggleReadOnlyMode(on)
{
    if (on)
    {
        //if editor has changed size, save the size and reset size
        editors.forEach(element => {
            if (element.fileElement.getAttribute("style"))
            {
                element.editedWidth = element.fileElement.style.width;
                element.fileElement.style.removeProperty("width");
            }
        });

        //editor is readonly
        for (var i=0; i<editors.length; i++)
        {
            if (!editors[i]["readOnly"])
            {
                editors[i]["editor"].setOption("readOnly", true);
            }
        }
    }
    else
    {
        //if editor has changed size, change size to how it was before program was run
        editors.forEach(element => {
            if (element.editedWidth)
            {
                element.fileElement.style.width = element.editedWidth;
                element.editedWidth = null;
            }
        });

        //editor is editable
        for (var i=0; i<editors.length; i++)
        {
            if (!editors[i]["readOnly"])
            {
                editors[i]["editor"].setOption("readOnly", false);
            }
        }
    }
}

export function prepareDebuggerClient(breakpointFunc)
{
    //keep track of the current file being displayed
    var tabs = $(".tab-header");

    for (var i=0; i<tabs.length; i++)
    (function(i) {
        tabs[i].addEventListener("click", function() {
            
            var content = tabs[i].innerText;

            currentFile = content;

            for (var j=0; j<editors.length; j++)
            {
                editors[j]["editor"].refresh();
            }

        });
    }(i));

    setUpEditors(breakpointFunc);
}

export function clearTracker()
{
    if (trackingFile)
    {
        for (var i=0; i<editors.length; i++)
        {
            if (editors[i]["fileName"] == trackingFile)
            {
                editors[i]["editor"].clearGutter("tracking");
                trackingFile = null;
                break;
            }
        }
    }
    
}

export function moveTracker(newFile, lineNum)
{
    //hide current arrow
    clearTracker();

    if (currentFile != newFile)
    {
        //switch active file
        var start = newFile.split('.', 1)[0];
        var end = newFile.split('.').pop();

        $("#" + start + end + "File").on("shown.bs.tab", function(e)
        {
            for (var j=0; j<editors.length; j++)
            {
                editors[j]["editor"].refresh();
            }

            addTracker(newFile, lineNum);
            currentFile = newFile;

            $("#" + start + end + "File").off("shown.bs.tab");
        });

        $("#" + start + end + "File").tab("show");
    }
    else
    {
        addTracker(newFile, lineNum);
    }
}

//toggle a breakpoint marker in a file manually, without gutter click event
export function toggleBreakpoint(file, lineNum)
{
    for (var i=0; i<editors.length; i++)
    {
        if (editors[i]["fileName"] == file)
        {

            var info = editors[i]["editor"].getDoc().lineInfo(lineNum - 1);

            //if some gutter markers exist for this line
            if (info.gutterMarkers)
            {
                if (info.gutterMarkers.breakpoints)
                {
                    editors[i]["editor"].setGutterMarker(lineNum - 1, "breakpoints", null);

                    return;
                }
            }
            
            editors[i]["editor"].setGutterMarker(lineNum - 1, "breakpoints", makeGutterDecoration("<span class='mdi mdi-circle' style='font-size:12px'></span>", "#822", "#e92929"));

        }
    }
}

//set up the code editors for all the files
function setUpEditors(breakpointFunc)
{
    //create editors
    for (var i=0; i<files.length; i++)
    {
        var e = CodeMirror.fromTextArea(files[i], 
            {mode: "clike", readOnly: files[i].readOnly, theme: "abcdef", lineNumbers: true, lineWrapping: true, foldGutter: true, gutter: true, 
            gutters: ["breakpoints", "CodeMirror-linenumbers", "tracking", "CodeMirror-foldgutter"]});

        var element = files[i].parentElement.querySelector(".CodeMirror");
        
        if (files[i].readOnly)
        {
            element.insertBefore(makeReadOnlyMessage(), element.firstChild);
        }

        editors.push({
            fileName: files[i].getAttribute("id"),
            fileElement: element, 
            editor: e,
            editedWidth: null,
            readOnly: files[i].readOnly
        });
    }

    //set up breakpoint events
    //source: (CodeMirror, no date)
    for (var i=0; i<editors.length; i++)
    (function(i) {
        editors[i]["editor"].on("gutterClick", function(cm, n) {
            
            var info = cm.getDoc().lineInfo(n);
            var sendRow = n + 1;

            //if some gutter markers exist for this line
            if (info.gutterMarkers)
            {
                if (info.gutterMarkers.breakpoints)
                {
                    breakpointFunc(editors[i]["fileName"], sendRow.toString(), false);

                    cm.setGutterMarker(n, "breakpoints", null);

                    return;
                }
            }
            
            breakpointFunc(editors[i]["fileName"], sendRow.toString(), true);

            cm.setGutterMarker(n, "breakpoints", makeGutterDecoration("<span class='mdi mdi-circle' style='font-size:12px'></span>", "#822", "#e92929"));

        });
    }(i));
    
    //allow user to resize editors
    $(".CodeMirror").addClass("resize");

    //check if editors should be in light mode
    if (localStorage.getItem("theme"))
    {
        // set theme
        if (localStorage.getItem("theme") == "light")
        {
            for (var i=0; i<editors.length; i++)
            {
                editors[i]["editor"].setOption("theme", "default");
            }

            return;
        }
    }
}

function addTracker(file, lineNum)
{
    for (var i=0; i<editors.length; i++)
    {
        if (editors[i]["fileName"] == file)
        {
            //scroll to line
            jumpToLine(lineNum, editors[i]["editor"]);

            //add a marker to the new line
            editors[i]["editor"].setGutterMarker(parseInt(lineNum) - 1, "tracking", makeGutterDecoration("<span class='mdi mdi-arrow-right-thick'></span>", "#0A12FF", "#fbff00"));

            //keep track of the file tracker is in
            trackingFile = file;
        }
    }
}

//source: (Grivilers, 2014)
function jumpToLine(i, editor) { 
    var t = editor.charCoords({line: i, ch: 0}, "local").top; 
    var middleHeight = editor.getScrollerElement().offsetHeight / 2; 
    editor.scrollTo(null, t - middleHeight - 5); 
}

//create dom element for gutter
function makeGutterDecoration(html, lightThemeColour, darkThemeColour) {
    var marker = document.createElement("div");
    marker.style.color = darkThemeColour;

    if (localStorage.getItem("theme"))
    {
        if (localStorage.getItem("theme") == "light")
        {
            marker.style.color = lightThemeColour;
        }
    }

    marker.innerHTML = html;
    return marker;
}

function makeReadOnlyMessage() {
    var div = document.createElement("div");
    div.innerHTML = "This file is read only (breakpoints can still be set)";
    div.classList = "readOnlyMessage";

    return div;
}