// Socket setup based on tutorial: https://javascript.info/websocket

import * as constants from "/honours/webapp/view/js/debuggerConstants.js";
import Request from "./request.js";

var editors = [];
var files = $(".editor");

var trackingFile = null;

window.onload = preparePage();

let socket = new WebSocket("ws://192.168.17.60:8080");

//set up socket
socket.onopen = function(e) {
    console.log("Connection established with compiler");
};

socket.onmessage = function(messageEvent) {

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
            clearTracker();

            //editor is editable
            for (var i=0; i<editors.length; i++)
            {
                editors[i]["editor"].setOption("readOnly", false);
            }

            break;
        case constants.EVENT_ON_BREAK:
            //hide arrow
            clearTracker();

            //enable continue button and step buttons
            $("#continue-btn")[0].disabled = false;
            $("#continue-btn")[0].ariaDisabled = false;

            var debuggerStepBtns = $(".debugger-step-control");

            for (var i=0; i<debuggerStepBtns.length; i++)
            {
                debuggerStepBtns[i].disabled = false;
                debuggerStepBtns[i].ariaDisabled = false;
            }


            var file = message.value.split(':', 1)[0];
            var lineNum = message.value.split(':').pop();

            //switch active file
            var start = file.split('.', 1)[0];
            var end = file.split('.').pop();
            $("#" + start + end + "File").tab("show");

            //add tracker
            addTracker(file, lineNum);

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
            //hide current arrow
            clearTracker();

            //put in arrow to show where breakpoint is
            var file = message.value.split(':', 1)[0];
            var lineNum = message.value.split(':').pop();

            //switch active file
            var start = file.split('.', 1)[0];
            var end = file.split('.').pop();
            $("#" + start + end + "File").tab("show");

            //add tracker
            addTracker(file, lineNum);

            break;
        case constants.EVENT_ON_MOVE_BREAKPOINTS:
            //clear all breakpoints in breakpoint gutters
            for (var i=0; i<editors.length; i++)
            {
                editors[i]["editor"].clearGutter("breakpoints");
                editors[i]["breakpoints"].clear();
            }

            //add breakpoints that were set into breakpoint gutters
            var breaks = message.value.split("\n");

            for (var i=0; i<breaks.length; i++)
            {
                var file = breaks[i].split(':', 1)[0];
                var lineNum = breaks[i].split(':').pop();

                addBreakpoint(file, lineNum);
            }
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
            
            var info = cm.lineInfo(n);
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
                cm.setGutterMarker(n, "breakpoints", makeBreakpoint());
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

function makeBreakpoint() {
    var marker = document.createElement("div");

    if (localStorage.getItem("theme"))
    {
        if (localStorage.getItem("theme") == "light")
        {
            marker.style.color = "#822";
        }
        else
        {
            marker.style.color = "#e92929";
        }
    }
    else
    {
        marker.style.color = "#e92929";
    }

    marker.innerHTML = "●";
    return marker;
}

function makeTracker() {
    var marker = document.createElement("div");

    marker.innerHTML = "<span class='mdi mdi-arrow-right-thick'></span>";

    if (localStorage.getItem("theme"))
    {
        if (localStorage.getItem("theme") == "light")
        {
            marker.style.color = "#0A12FF";
        }
        else
        {
            marker.style.color = "#fbff00";
        }
    }
    else
    {
        marker.style.color = "#fbff00";
    }

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
            //scroll to line
            editors[i]["editor"].scrollIntoView({line: lineNum}, 200);

            //add a marker to the new line
            editors[i]["editor"].setGutterMarker(parseInt(lineNum) - 1, "tracking", makeTracker());
            trackingFile = file;
        }
    }
}

function addBreakpoint(file, lineNum)
{
    for (var i=0; i<editors.length; i++)
    {
        if (editors[i]["fileName"] == file)
        {
            editors[i]["breakpoints"].add(parseInt(lineNum));
            editors[i]["editor"].setGutterMarker(parseInt(lineNum) - 1, "breakpoints", makeBreakpoint());
        }
    }
}