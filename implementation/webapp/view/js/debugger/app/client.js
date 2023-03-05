// Socket setup based on tutorial: https://javascript.info/websocket

import * as constants from "/honours/webapp/view/js/debugger/app/debuggerConstants.js";
import Request from "/honours/webapp/view/js/debugger/request.js";

var editors = [];
var files = $(".editor");

var currentFile = "main.cpp";
var trackingFile = null;

export let socketObj = {
    socket: null
};

export function prepareDebuggerClient()
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
}

export function on_open() 
{
    //allow user to interact with compiler, enable buttons
    var debuggerBtns = $(".on-connected");

    for (var i=0; i<debuggerBtns.length; i++)
    {
        debuggerBtns[i].disabled = false;
        debuggerBtns[i].ariaDisabled = false;
    }
}

export function on_close()
{
    var debuggerBtns = $(".on-connected");
    
    for (var i=0; i<debuggerBtns.length; i++)
    {
        debuggerBtns[i].disabled = true;
        debuggerBtns[i].ariaDisabled = true;
    }
}

export function on_message(messageEvent)
{
    //handle message
    var message = JSON.parse(messageEvent.data);
    console.log(message);

    switch(message.event)
    {
        case constants.EVENT_ON_STDOUT:
            //output response into terminal

            //get active terminal
            var term = $.terminal.active();
        
            //output received message into terminal
            term.echo(message.value);

            break;
        case constants.EVENT_ON_COMPILE_SUCCESS:
            //display compilation output
            addCompilationBoxMessage(message.value, "alert-success");

            //show debugger live controls
            $(".debugger-live-control").removeClass("d-none");

            //enable stop debugger live control
            $("#stop-btn")[0].disabled = false;
            $("#stop-btn")[0].ariaDisabled = false;

            //editor is readonly
            for (var i=0; i<editors.length; i++)
            {
                editors[i]["editor"].setOption("readOnly", true);
            }

            break;
        case constants.EVENT_ON_COMPILE_FAILURE:
            //display compilation output
            addCompilationBoxMessage(message.value, "alert-danger");
            $("#compilation-messages-box ul")[0].scrollIntoView();

            //show and enable play button
            $("#play-btn")[0].disabled = false;
            $("#play-btn")[0].ariaDisabled = false;
            $("#play-btn").show();

            break;
        case constants.EVENT_ON_PROGRAM_EXIT:
            //hide and disable debugger live controls
            $(".debugger-live-control").addClass("d-none");

            var debuggerLiveControls = $(".debugger-live-control");

            for (var i=0; i<debuggerLiveControls.length; i++)
            {
                debuggerLiveControls[i].disabled = true;
                debuggerLiveControls[i].ariaDisabled = true;
            }

            //show and enable play button
            $("#play-btn")[0].disabled = false;
            $("#play-btn")[0].ariaDisabled = false;
            $("#play-btn").show();

            //hide arrow
            clearTracker();

            //editor is editable
            for (var i=0; i<editors.length; i++)
            {
                editors[i]["editor"].setOption("readOnly", false);
            }

            break;
        case constants.EVENT_ON_BREAK:
            //enable continue button and step buttons
            $("#continue-btn")[0].disabled = false;
            $("#continue-btn")[0].ariaDisabled = false;

            var debuggerStepBtns = $(".debugger-step-control");

            for (var i=0; i<debuggerStepBtns.length; i++)
            {
                debuggerStepBtns[i].disabled = false;
                debuggerStepBtns[i].ariaDisabled = false;
            }

            //put in arrow to show where breakpoint is
            var file = message.value.split(':', 1)[0];
            var lineNum = message.value.split(':').pop();

            moveTracker(file, lineNum);

            break;
        case constants.EVENT_ON_CONTINUE:
            //enable stop debugger live control
            $("#stop-btn")[0].disabled = false;
            $("#stop-btn")[0].ariaDisabled = false;

            //disable continue and step controls
            $("#continue-btn")[0].disabled = true;
            $("#continue-btn")[0].ariaDisabled = true;

            var debuggerStepBtns = $(".debugger-step-control");

            for (var i=0; i<debuggerStepBtns.length; i++)
            {
                debuggerStepBtns[i].disabled = true;
                debuggerStepBtns[i].ariaDisabled = true;
            }

            //hide arrow
            clearTracker();


            break;
        case constants.EVENT_ON_STEP:
            //put in arrow to show where breakpoint is
            var file = message.value.split(':', 1)[0];
            var lineNum = message.value.split(':').pop();

            moveTracker(file, lineNum);

            break;
        case constants.EVENT_ON_BREAKPOINT_CHANGED:
            //clear the breakpoint at the old position
            var breaks = message.value.trim().split("\n");

            var file = breaks[0].split(':', 1)[0];
            var lineNum = breaks[0].split(':').pop();

            toggleBreakpoint(file, parseInt(lineNum));

            var file = breaks[1].split(':', 1)[0];
            var lineNum = breaks[1].split(':').pop();

            toggleBreakpoint(file, parseInt(lineNum));

            break;
        case constants.EVENT_ON_TEST_SUCCESS:
            //get number of tests succeeded
            var value = message.value.replace(/\s/g, "");

            value = value.split("DEBUGGING_TOOL_RESULT:").pop();

            console.log(value);

            const urlParams = new URLSearchParams(window.location.search);

            //mark this in the database
            $.ajax({
                type: "POST",
                url: "/honours/webapp/controller/ajaxScripts/logUserExerciseAttempt.php",
                data: {codeId: urlParams.get("id"), mark: value},
                success: function(data) {
                    if (data != 0)
                    {
                        console.log("ajax succeed");

                        $("#submitting-exercise-message")[0].innerHTML = "Successfully submitted exercise";
                        $("#spinner-exercise").hide();
                        $("#submitting-exercise-status")[0].innerHTML = "Success";

                        //take user back to homepage
                        window.open("/honours/webapp/view/index.php", name="_self");
                    }
                    else
                    {
                        console.log("ajax failed");
                        $("#submitting-exercise-message")[0].innerHTML = "Failed to submit exercise. Try again?";
                        $("#spinner-exercise").hide();
                        $("#submitting-exercise-status")[0].innerHTML = "Failed";

                    }
                }
            });
            break;

        case constants.EVENT_ON_TEST_FAILURE:
            console.log("server failed");
            $("#submitting-exercise-message")[0].innerHTML = "Failed to submit exercise. Try again?";
            $("#spinner-exercise").hide();
            $("#submitting-exercise-status")[0].innerHTML = "Failed";
            break;
        case constants.EVENT_ON_INFERIOR_EXIT:
            //add exit code to the output box
            addCompilationBoxMessage(message.value.trim(), "alert-info");

            break;
        default:
            alert(message.event + "Client operation failed. Try again?");
    }
}

//tell socket that we want to compile and start the program
export function startProgram()
{
    clearTerminal();

    var obj = new Request(constants.SENDER_USER);
    obj.operation = constants.OP_COMPILE;

    var filesData = [];

    var breakpoints = [];

    for (var i=0; i<editors.length; i++)
    {
        //set breakpoints for this editor
        var arr = Array.from(editors[i]["breakpoints"]);
        for (var j=0; j<arr.length; j++)
        {
            breakpoints.push([editors[i]["fileName"], arr[j]]);
        }

        filesData.push([files[i].getAttribute("id"), editors[i]["editor"].getValue()]);
    }

    obj.value = {"filesData":filesData, "breakpoints" : breakpoints};

    //disable and hide play button
    $("#play-btn")[0].disabled = true;
    $("#play-btn")[0].ariaDisabled = true;
    $("#play-btn").hide();

    socketObj.socket.send(JSON.stringify(obj));  
}

//tell socket that we want to send some input to the program
export function sendInput(input)
{
    var obj = new Request(constants.SENDER_USER);
    obj.operation = constants.OP_INPUT;
    obj.value = input;
    socketObj.socket.send(JSON.stringify(obj));
}

export function addCompilationBoxMessage(message, colour)
{
    var li = document.createElement("li");
    
    var alertDiv = document.createElement("div");
    alertDiv.classList = "alert " + colour + " show d-flex align-items-center";
    alertDiv.setAttribute("role", "alert");

    var alertText = document.createElement("p");
    alertText.classList = "m-0 prewrap ms-3";
    alertText.innerHTML = message;

    var alertTime = document.createElement("p");
    alertTime.classList = "m-0";
    alertTime.innerHTML = new Date().toLocaleTimeString();

    alertDiv.append(alertTime);
    alertDiv.append(alertText);

    li.append(alertDiv);

    $("#compilation-messages-box ul")[0].prepend(li);


}

//set up the code editors for all the files
export function setUpEditors(breakpointFunc)
{
    //create editors
    for (var i=0; i<files.length; i++)
    {
        editors.push({
            fileName: files[i].getAttribute("id"), 
            editor: CodeMirror.fromTextArea(files[i], 
                {mode: "clike", theme: "abcdef", lineNumbers: true, lineWrapping: true, foldGutter: true, gutter: true, 
                gutters: ["breakpoints", "CodeMirror-linenumbers", "tracking", "CodeMirror-foldgutter"]}), 
            breakpoints: new Set()
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

//clear terminal
export function clearTerminal()
{
    var term = $.terminal.active();
    term.clear();
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

function clearTracker()
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

function moveTracker(newFile, lineNum)
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
function toggleBreakpoint(file, lineNum)
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

//tell socket to run unit test on program code
export function testProgram()
{
    var obj = new Request(constants.SENDER_USER);
    obj.operation = constants.OP_TEST;

    var filesData = [];
    var editorFiles = [];
    var sysFiles = [];

    //read exercise file to see what files to get
    $.ajax({
        type: "GET",
        url: $("#exerciseFileLocation")[0].innerHTML,
        dataType: "json",
        async: false,
        success: function(data) {
            sysFiles = data.sys_files;
        }
    });

    //read all the files into filesData
    for (var i=0; i<sysFiles.length; i++)
    {
        $.ajax({
            type: "GET",
            url: "/honours/webapp/view/exerciseFiles/" + sysFiles[i],
            async: false,
            success: function(data) {
                filesData.push([sysFiles[i].split('/').pop(), data]);
            }
        });
    }

    //get the files in the editor
    for (var i=0; i<editors.length; i++)
    {
        editorFiles.push([files[i].getAttribute("id"), editors[i]["editor"].getValue()]);
    }

    //update the files if they match anything in the editor
    for (var i=0; i<filesData.length; i++)
    {
        for (var j=0; j<editorFiles.length; j++)
        {
            if (filesData[i][0] == editorFiles[j][0])
            {
                filesData[i][1] = editorFiles[j][1];
            }
        }
    }

    obj.value = filesData;

    console.log(obj);

    socketObj.socket.send(JSON.stringify(obj));
}