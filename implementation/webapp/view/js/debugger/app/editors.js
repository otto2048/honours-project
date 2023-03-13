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
            editors[i]["editor"].setOption("readOnly", true);
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
            editors[i]["editor"].setOption("readOnly", false);
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

            console.log("refresh editors");

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
            console.log("shown");

            console.log("refresh");

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
            if (editors[i]["breakpoints"].has(lineNum))
            {
                editors[i]["breakpoints"].delete(lineNum);
                editors[i]["editor"].setGutterMarker(lineNum - 1, "breakpoints", null);
            }
            else
            {
                editors[i]["breakpoints"].add(lineNum);
                editors[i]["editor"].setGutterMarker(lineNum - 1, "breakpoints", makeGutterDecoration("<span class='mdi mdi-circle' style='font-size:12px'></span>", "#822", "#e92929"));    
            }
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
            {mode: "clike", theme: "abcdef", lineNumbers: true, lineWrapping: true, foldGutter: true, gutter: true, 
            gutters: ["breakpoints", "CodeMirror-linenumbers", "tracking", "CodeMirror-foldgutter"]});

        editors.push({
            fileName: files[i].getAttribute("id"),
            fileElement: files[i].parentElement.querySelector(".CodeMirror"), 
            editor: e, 
            breakpoints: new Set(),
            editedWidth: null
        });
    }

    //set up breakpoint events
    //https://codemirror.net/5/demo/marker.html
    for (var i=0; i<editors.length; i++)
    (function(i) {
        editors[i]["editor"].on("gutterClick", function(cm, n) {
            
            var sendRow = n + 1;

            if (editors[i]["breakpoints"].has(n + 1))
            {
                editors[i]["breakpoints"].delete(n + 1);

                breakpointFunc(editors[i]["fileName"], sendRow.toString(), false);

                cm.setGutterMarker(n, "breakpoints", null);
            }
            else
            {
                editors[i]["breakpoints"].add(n + 1);

                breakpointFunc(editors[i]["fileName"], sendRow.toString(), true);

                cm.setGutterMarker(n, "breakpoints", makeGutterDecoration("<span class='mdi mdi-circle' style='font-size:12px'></span>", "#822", "#e92929"));
            }

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
            console.log("scrolling into view");

            //scroll to line
            jumpToLine(lineNum, editors[i]["editor"]);

            //add a marker to the new line
            editors[i]["editor"].setGutterMarker(parseInt(lineNum) - 1, "tracking", makeGutterDecoration("<span class='mdi mdi-arrow-right-thick'></span>", "#0A12FF", "#fbff00"));

            //keep track of the file tracker is in
            trackingFile = file;
        }
    }
}

//https://stackoverflow.com/questions/10575343/codemirror-is-it-possible-to-scroll-to-a-line-so-that-it-is-in-the-middle-of-w
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