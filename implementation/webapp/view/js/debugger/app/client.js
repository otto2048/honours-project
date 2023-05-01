// Socket setup based on tutorial: https://javascript.info/websocket

// This file controls the connection between the client and the debugger container

import * as constants from "/honours/webapp/view/js/debugger/app/debuggerConstants.js";
import Request from "/honours/webapp/view/js/debugger/request.js";

import * as editor from "/honours/webapp/view/js/debugger/app/editors.js";

import * as locals from "/honours/webapp/view/js/debugger/app/locals.js";

import * as hostSocket from "/honours/webapp/view/js/debugger/host/client.js";

export let socketObj = {
    socket: null
};

export function initialiseEditors(breakpointFunc)
{
    editor.prepareDebuggerClient(breakpointFunc);
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

            //show debug output window
            $("#debug-output-window").removeClass("d-none");

            //enable stop debugger live control
            $("#stop-btn")[0].disabled = false;
            $("#stop-btn")[0].ariaDisabled = false;

            //set editor to readonly
            editor.toggleReadOnlyMode(true);

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

            //hide debug output window
            $("#debug-output-window").addClass("d-none");

            //make editor editable
            editor.toggleReadOnlyMode(false);

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
            editor.clearTracker();

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

            editor.moveTracker(file, lineNum);

            //load locals
            sendInput("get_top_level_locals");
            hostSocket.pingHostFunc();

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
            editor.clearTracker();


            break;
        case constants.EVENT_ON_STEP:
            //put in arrow to show where breakpoint is
            var file = message.value.split(':', 1)[0];
            var lineNum = message.value.split(':').pop();

            editor.moveTracker(file, lineNum);

            //load visible variables
            sendInput("get_top_level_locals");
            hostSocket.pingHostFunc();

            break;
        case constants.EVENT_ON_BREAKPOINT_CHANGED:
            //clear the breakpoint at the old position
            var breaks = message.value.trim().split("\n");

            var file = breaks[0].split(':', 1)[0];
            var lineNum = breaks[0].split(':').pop();

            editor.toggleBreakpoint(file, parseInt(lineNum));

            var file = breaks[1].split(':', 1)[0];
            var lineNum = breaks[1].split(':').pop();

            editor.toggleBreakpoint(file, parseInt(lineNum));

            break;
        case constants.EVENT_ON_TEST_SUCCESS:
            //get number of tests succeeded and result vector
            var value = message.value.replace(/\s/g, "");

            var points = value.split("DEBUGGING_TOOL_RESULT:").pop();

            var testVector = value.split("TEST_VECTOR:").pop().split("DEBUGGING_TOOL_RESULT:")[0];

            const urlParams = new URLSearchParams(window.location.search);

            //mark this in the database
            $.ajax({
                type: "POST",
                url: "/honours/webapp/controller/ajaxScripts/logUserExerciseAttempt.php",
                data: {codeId: urlParams.get("id"), mark: points, resultVector: testVector},
                success: function(data) {
                    if (data != 0)
                    {
                        //ajax succeeded

                        $("#submitting-exercise-message")[0].innerHTML = "Successfully submitted exercise";
                        $("#spinner-exercise").hide();
                        $("#submitting-exercise-status")[0].innerHTML = "Success";

                        //take user back to homepage
                        window.open("/honours/webapp/view/index.php", name="_self");
                    }
                    else
                    {
                        //ajax failed
                        
                        $("#submitting-exercise-message")[0].innerHTML = "Failed to submit exercise. Try again?";
                        $("#spinner-exercise").hide();
                        $("#submitting-exercise-status")[0].innerHTML = "Failed";

                    }
                }
            });
            break;
        case constants.EVENT_ON_TEST_FAILURE:
            $("#submitting-exercise-message")[0].innerHTML = "Failed to submit exercise. Try again?";
            $("#spinner-exercise").hide();
            $("#submitting-exercise-status")[0].innerHTML = "Failed";
            break;
        case constants.EVENT_ON_INFERIOR_EXIT:
            //add exit code to the output box
            addCompilationBoxMessage(message.value.trim(), "alert-info");

            break;
        case constants.EVENT_ON_LOCALS_DUMP:
            var data = JSON.parse(message.value);

            locals.displayInitialVariables(data);
            
            break;
        case constants.EVENT_ON_DUMP_LOCAL:
            var data = JSON.parse(message.value);
            
            locals.displayMoreVariableDetail(data);

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
    var writable = [];
    var readonly = [];

    //read exercise file to see what files to get
    $.ajax({
        type: "GET",
        url: $("#exerciseFileLocation")[0].innerHTML,
        dataType: "json",
        async: false,
        success: function(data) {
            writable = data.compilation.writable;
            readonly = data.compilation.readonly;
        }
    });

    writable = writable.map(x => x.split('/').pop());

    //read all the readonly files into filesData
    for (var i=0; i<readonly.length; i++)
    {
        $.ajax({
            type: "GET",
            url: "/honours/webapp/view/exerciseFiles/" + readonly[i],
            async: false,
            success: function(data) {
                filesData.push([readonly[i].split('/').pop(), data]);
            }
        });
    }

    var breakpoints = [];

    for (var i=0; i<editor.editors.length; i++)
    {
        //set breakpoints for this editor
        var doc = editor.editors[i]["editor"].getDoc();
        var lines = doc.lineCount();

        for (var j=0; j<lines; j++)
        {
            var line = doc.lineInfo(j);

            if (line.gutterMarkers)
            {
                if (line.gutterMarkers.breakpoints)
                {
                    breakpoints.push([editor.editors[i]["fileName"], j + 1]);
                }
            }
        }

        if (writable.includes(editor.files[i].getAttribute("id")))
        {
            filesData.push([editor.files[i].getAttribute("id"), editor.editors[i]["editor"].getValue()]);
        }
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

//add message to output box
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


//clear terminal
export function clearTerminal()
{
    var term = $.terminal.active();
    term.clear();
}


//tell socket to run unit test on program code
export function testProgram()
{
    var obj = new Request(constants.SENDER_USER);
    obj.operation = constants.OP_TEST;

    var filesData = [];
    var writable = [];
    var readonly = [];

    //read exercise file to see what files to get
    $.ajax({
        type: "GET",
        url: $("#exerciseFileLocation")[0].innerHTML,
        dataType: "json",
        async: false,
        success: function(data) {
            writable = data.test.writable;
            readonly = data.test.readonly;
        }
    });

    //read all the files into filesData
    for (var i=0; i<readonly.length; i++)
    {
        $.ajax({
            type: "GET",
            url: "/honours/webapp/view/exerciseFiles/" + readonly[i],
            async: false,
            success: function(data) {
                filesData.push([readonly[i].split('/').pop(), data]);
            }
        });
    }

    writable = writable.map(x => x.split('/').pop());

    for (var i=0; i<editor.editors.length; i++)
    {
        if (writable.includes(editor.files[i].getAttribute("id")))
        {
            filesData.push([editor.files[i].getAttribute("id"), editor.editors[i]["editor"].getValue()]);
        }
    }

    obj.value = filesData;

    socketObj.socket.send(JSON.stringify(obj));
}