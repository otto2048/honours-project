// Socket setup based on tutorial: https://javascript.info/websocket

import * as constants from "/honours/webapp/view/js/debuggerConstants.js";
import Request from "./request.js";

var editors = [];
var files = $(".editor");

var currentFile = "main.cpp";

window.onload = preparePage();

let socket = new WebSocket("ws://192.168.17.60:8080");

//set up socket
socket.onopen = function(e) {
    console.log("Connection established with compiler");
};

socket.onmessage = function(messageEvent) {

    //handle message
    var message = JSON.parse(messageEvent.data);
    //console.log(message);

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
            addCompilationBoxMessage(message.value, "alert-info");

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
            addCompilationBoxMessage(message.value, "alert-info");

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
            clearTracker(currentFile);

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
            clearTracker(currentFile);


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
        default:
            alert(message.event + "Client operation failed. Try again?");
    }
}

socket.onclose = function(event) {
    if (event.wasClean) {
        console.log("Connection closed cleanly, code=${event.code} reason=${event.reason}");
    } else {
        console.log("Connection died");
    }
};

socket.onerror = function(error) {
    console.log("[error]");
};

function preparePage()
{
    //set up ACE editors
    setUpEditors();

    //add event listener to play button
    document.getElementById("play-btn").addEventListener("click", startProgram);

    $("#continue-btn")[0].addEventListener("click", function() {
        sendInput("continue");
    });

    $("#stop-btn")[0].addEventListener("click", function() {
        sendInput("kill");
    });

    $("#step-over-btn")[0].addEventListener("click", function() {
        sendInput("step_over");
    });

    $("#step-into-btn")[0].addEventListener("click", function() {
        sendInput("step_into");
    });

    $("#step-out-btn")[0].addEventListener("click", function() {
        sendInput("step_out");
    });

    //set up jquery terminal
    $('#code-output').terminal(function(command)
    {
        if (command !== '')
        {
            sendInput(command);
        }
    }, {
        height: 500
    });

    //clear the terminal
    clearTerminal();

    $('#increase-code-size-btn')[0].addEventListener("click", function()
    {
        changeCodeSize(5);
    });

    $('#decrease-code-size-btn')[0].addEventListener("click", function()
    {
        changeCodeSize(-5);
    });
}


//tell socket that we want to compile and start the program
function startProgram()
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

    socket.send(JSON.stringify(obj));  
}


//tell socket that we want to send some input to the program
function sendInput(input)
{
    var obj = new Request(constants.SENDER_USER);
    obj.operation = constants.OP_INPUT;
    obj.value = input;
    socket.send(JSON.stringify(obj));
}

function addCompilationBoxMessage(message, colour)
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

//change code size
function changeCodeSize(value)
{
    //change size of editor text
    var newValue = parseInt($(".CodeMirror").css('font-size'), 10) + value;

    if (newValue > 0)
    {
        $(".CodeMirror").css('font-size', newValue.toString() + "px");
    }
}

//set up the code editors for all the files
function setUpEditors()
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
                sendInput("clear_silent " + editors[i]["fileName"] + ":" + sendRow.toString());
                cm.setGutterMarker(n, "breakpoints", null);
            }
            else
            {
                editors[i]["breakpoints"].add(n + 1);
                sendInput("break_silent " + editors[i]["fileName"] + ":" + sendRow.toString());
                cm.setGutterMarker(n, "breakpoints", makeGutterDecoration("<span class='mdi mdi-circle' style='font-size:12px'></span>", "#822", "#e92929"));
            }

        });
    }(i));

    //refresh editors when user switches tabs
    $('.nav-tabs button').on('shown.bs.tab', function() {
        for (var i=0; i<editors.length; i++)
        {
            editors[i]["editor"].refresh();
        }
    }); 
    
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
function clearTerminal()
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

function clearTracker(file)
{
    for (var i=0; i<editors.length; i++)
    {
        if (editors[i]["fileName"] == file)
        {
            editors[i]["editor"].clearGutter("tracking");
            break;
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
            editors[i]["editor"].scrollIntoView({line: lineNum}, 200);

            //add a marker to the new line
            editors[i]["editor"].setGutterMarker(parseInt(lineNum) - 1, "tracking", makeGutterDecoration("<span class='mdi mdi-arrow-right-thick'></span>", "#0A12FF", "#fbff00"));
        }
    }
}

function moveTracker(newFile, lineNum)
{
    //hide current arrow
    clearTracker(currentFile);

    var oldFile = currentFile;

    currentFile = newFile;

    if (oldFile != currentFile)
    {
        //switch active file
        var start = newFile.split('.', 1)[0];
        var end = newFile.split('.').pop();

        $("#" + start + end + "File").on("shown.bs.tab", function(e)
        {
            addTracker(newFile, lineNum);
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