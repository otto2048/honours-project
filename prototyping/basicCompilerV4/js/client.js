// Socket setup based on tutorial: https://javascript.info/websocket

import * as constants from "/honours/webapp/view/js/debuggerConstants.js";
import Request from "./request.js";

var editors = [];
var files = $(".editor");

var selectedElement = null;

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

            //enable pause, stop, and restart debugger live controls
            $("#pause-btn")[0].disabled = false;
            $("#pause-btn")[0].ariaDisabled = false;

            $("#stop-btn")[0].disabled = false;
            $("#stop-btn")[0].ariaDisabled = false;
            
            $("#restart-btn")[0].disabled = false;
            $("#restart-btn")[0].ariaDisabled = false;

            //TODO: editor is readonly

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
            $(".editor").each(function() {
                var breakpoints = $(this).find('.ace_breakpoint');

                $(breakpoints).each(function() {
                    $(this).removeClass("on_this_line");
                    selectedElement = null;
                    //$(this).css("box-shadow", "0px 0px 1px 1px #8c2424 inset");
                });
            });
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

            //disable pause button
            $("#pause-btn")[0].disabled = true;
            $("#pause-btn")[0].ariaDisabled = true;

            //put in arrow to show where breakpoint is

            var file = message.value.split(':', 1)[0];
            var lineNum = message.value.split(':').pop();

            //switch active file
            var start = file.split('.', 1)[0];
            var end = file.split('.').pop();
            $("#" + start + end + "File").tab("show");


            $(".editor").each(function() {
                if ($(this).attr("id") == file)
                {
                    var breakpoints = $(this).find('.ace_breakpoint');

                    $(breakpoints).each(function() {
                        if ($(this).text() == lineNum)
                        {
                            $(this).addClass("on_this_line");
                            selectedElement = $(this);
                            //$(this).css("box-shadow", "0px 0px 1px 1px #fbff00 inset");
                        }
                    });
                }
            });

            for (var i=0; i<editors.length; i++)
            {
                if (editors[i].container.id == file)
                {
                    editors[i].gotoLine(lineNum);
                }
            }  

            break;
        case constants.EVENT_ON_CONTINUE:
            //enable pause, stop, and restart debugger live controls
            $("#pause-btn")[0].disabled = false;
            $("#pause-btn")[0].ariaDisabled = false;

            $("#stop-btn")[0].disabled = false;
            $("#stop-btn")[0].ariaDisabled = false;
            
            $("#restart-btn")[0].disabled = false;
            $("#restart-btn")[0].ariaDisabled = false;

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
            $(".editor").each(function() {
                var breakpoints = $(this).find('.ace_breakpoint');

                $(breakpoints).each(function() {
                    $(this).removeClass("on_this_line");
                    selectedElement = null;
                    //$(this).css("box-shadow", "0px 0px 1px 1px #8c2424 inset");
                });
            });

            break;
        default:
            alert("Client operation failed. Try again?");
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
        var breakpointInstances = editors[i].session.getBreakpoints();

        for (var j=0; j<breakpointInstances.length; j++)
        {
            if (breakpointInstances[j] !== undefined)
            {
                breakpoints.push([files[i].getAttribute("id"), j + 1]);
            }
        }

        filesData.push([files[i].getAttribute("id"), editors[i].session.getValue()]);
    }

    obj.value = {"filesData":filesData, "breakpoints" : breakpoints};

    console.log(obj);

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

//set up the code editors for all the files
function setUpEditors()
{
    for (var i=0; i<files.length; i++)
    {
        editors.push(ace.edit(files[i].getAttribute("id"))); 
    }

    //set up breakpoint events
    //https://ourcodeworld.com/articles/read/1052/how-to-add-toggle-breakpoints-on-the-ace-editor-gutter
    for (var i=0; i<editors.length; i++)
    {
        editors[i].on("guttermousedown", function(e) {
            var target = e.domEvent.target;

            if (target.className.indexOf("ace_gutter-cell") == -1){
                return;
            }
            // if (!editors[i].isFocused()){
            //     return; 
            // }

            if (e.clientX > 25 + target.getBoundingClientRect().left){
                return;
            }

            var breakpoints = e.editor.session.getBreakpoints();
           
            var row = e.getDocumentPosition().row;

            // If there's a breakpoint already defined, it should be removed, offering the toggle feature
            if(typeof breakpoints[row] === typeof undefined){
                e.editor.session.setBreakpoint(row);
                var sendRow = row + 1;
                sendInput("break " + e.editor.container.id + ":" + sendRow.toString());
            }else{
                //clear any box shadow that was set by other methods
                // $(".ace_gutter-cell").each(function() {
                //     if ($(this).attr("class").indexOf("ace_breakpoint") != -1 && parseInt($(this).text()) == row + 1)
                //     {
                //         $(this).css("box-shadow", "");
                //     }
                // });

                e.editor.session.clearBreakpoint(row);

                var sendRow = row + 1;
                sendInput("clear " + e.editor.container.id + ":" + sendRow.toString());
            }

            e.stop();
        });

        
        
    }

    //check if editors should be in light mode
    if (localStorage.getItem("theme"))
    {
        // set theme
        if (localStorage.getItem("theme") == "light")
        {
            for (var i=0; i<editors.length; i++)
            {
                editors[i].session.setMode("ace/mode/c_cpp");
            }

            return;
        }
    }

    for (var i=0; i<editors.length; i++)
    {
        editors[i].setTheme("ace/theme/tomorrow_night_bright");
        editors[i].session.setMode("ace/mode/c_cpp");
    }
}

function setBreakpoint()
{
    //if program is running, send breakpoint info straight away

}

//clear terminal
function clearTerminal()
{
    var term = $.terminal.active();
    term.clear();
}