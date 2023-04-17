// javascript for controlling an exercise, client side

// need to launch docker container to complete exercise in

// need to handle instruction file if it exists

import * as constants from "/honours/webapp/view/js/debugger/host/hostConstants.js";
import * as debug from "/honours/webapp/view/js/debugger/app/client.js"
import Request from "/honours/webapp/view/js/debugger/request.js";

var startedLaunching = false;
var launchFailed = false;

var pingHostObj = new Request(constants.SENDER_USER);
pingHostObj.value = "PING";

window.onload = preparePage();

$(document).ready(function(){
    $("#switch-active-session-btn").hide();
    $("#load-debugger-modal").modal('show');
});

//web socket to connect to the host server
let socketHost = new WebSocket("ws://44.209.40.106:8080");

//set up sockets

var connected = false;

socketHost.onopen = function(e) {
    console.log("Connection established with host");

    //ask the host to launch a compiler for us
    getUsername().then(function(data)
    {
        var obj = new Request(constants.SENDER_USER);
        obj.operation = constants.OP_LAUNCH_DEBUGGER;
        obj.value = data.username;

        console.log(obj);

        socketHost.send(JSON.stringify(obj));
    })   
};

socketHost.onerror = function(error) {
    $("#debugger-load-message")[0].innerHTML = "Failed to connect to server";
    $("#spinner")[0].remove();
    $("#debugger-load-status")[0].innerHTML = "Failed";
};

socketHost.onclose = function(event)
{
    $("#debugger-load-message")[0].innerHTML = event.reason;
    $("#debugger-load-status")[0].innerHTML = "Disconnected";
    $("#load-debugger-modal").modal('show');

    var debuggerBtns = $(".debugger-control");

    for (var i=0; i<debuggerBtns.length; i++)
    {
        debuggerBtns[i].disabled = true;
        debuggerBtns[i].ariaDisabled = true;
    }

    connected = false;
}

socketHost.onmessage = function(event) {
    var message = JSON.parse(event.data);

    console.log(message);

    if (message.operation == constants.OP_LAUNCH_DEBUGGER)
    {
        console.log(startedLaunching);

        if (message.status == constants.ENV_SUCCESS)
        {
            $("#debugger-load-message")[0].innerHTML = message.message;
            //successfully launched environment
            //connect to the container wss
            debug.socketObj.socket = new WebSocket("ws://44.209.40.106:" + message.value);

            debug.socketObj.socket.onopen = function(e) {
                console.log("Connection established with compiler");

                //set timeout for the exercise
                //source: https://stackoverflow.com/questions/1191865/code-for-a-simple-javascript-countdown-timer/1192001#1192001:~:text=21-,Here,-is%20another%20one
                var mins = 10;
                var secs = mins * 60;
                var currentSeconds = 0;
                var currentMinutes = 0;

                setTimeout(Decrement,1000); 

                function Decrement() {
                    currentMinutes = Math.floor(secs / 60);
                    currentSeconds = secs % 60;

                    //handle leading zero on seconds
                    if(currentSeconds <= 9)
                    {
                        currentSeconds = "0" + currentSeconds;
                    }
                    
                    secs--;
                    document.getElementById("timerText").innerHTML = currentMinutes + ":" + currentSeconds; //Set the element id you need the time put into.
                    
                    if(secs !== -1)
                    {
                        setTimeout(Decrement,1000);
                    }
                    else
                    {
                        //submit user answer
                        if (connected)
                        {
                            $("#submitting-exercise-message")[0].innerHTML = "Submitting exercise...";
                            $("#spinner-exercise").show();
                            $("#submitting-exercise-status")[0].innerHTML = "Submitting...";
                            $("#submit-exercise-modal").modal("show");
                
                            //submit user answer
                            debug.testProgram();
                            socketHost.send(JSON.stringify(pingHostObj));
                        }
                    }
                }

                //client on onopen function
                debug.on_open();

                connected = true;

                $("#spinner")[0].remove();
                $("#debugger-load-status")[0].innerHTML = "Success";

                $("#load-debugger-modal").modal('hide');
            };

            debug.socketObj.socket.onmessage = function(messageEvent) {

                //client on message function
                debug.on_message(messageEvent);
                
            };

            debug.socketObj.socket.onclose = function(event) {
                if (connected)
                {
                    $("#debugger-load-message")[0].innerHTML = "Environment failed.";
                    $("#debugger-load-status")[0].innerHTML = "Failed";
                    $("#load-debugger-modal").modal('show');
    
                    //client on close function
                    debug.on_close();
    
                    connected = false;
                }
            };
            
            debug.socketObj.socket.onerror = function(error) {
                $("#spinner")[0].remove();
                $("#debugger-load-status")[0].innerHTML = "Failed";
            };
        }
        else if (message.status == constants.ENV_LAUNCHING)
        {
            $("#debugger-load-message")[0].innerHTML = message.message;
            startedLaunching = true;
        }
        else if (message.status == constants.ENV_REFRESH)
        {
            if (!startedLaunching && !launchFailed)
            {
                $("#debugger-load-message")[0].innerHTML = message.message;
            }
        }
        else if (message.status == constants.ENV_FAIL)
        {
            launchFailed = true;
            $("#debugger-load-message")[0].innerHTML = message.message;

            //failed to launch environment
            console.log("Failed to init compiler");

            $("#spinner")[0].remove();
            $("#debugger-load-status")[0].innerHTML = "Failed";
        }
        else if (message.status == constants.ENV_MULTIPLE_ERROR)
        {
            launchFailed = true;
            $("#debugger-load-message")[0].innerHTML = message.message;

            $("#spinner")[0].remove();
            $("#debugger-load-status")[0].innerHTML = "Failed";

            //show switch active session button
            $("#switch-active-session-btn").show();
        }
    }
}

function preparePage()
{
    //disable ctrl+s shortcut
    //https://ej2.syncfusion.com/documentation/rich-text-editor/how-to/save/
    $(document)[0].addEventListener("keydown",function(e) {
        if(e.key === 's' && e.ctrlKey===true){
              e.preventDefault(); // to prevent default ctrl+s action
        }
      });
    
    debug.initialiseEditors(sendBreakpoint);

    //add event listener to play button
    $("#play-btn")[0].addEventListener("click", function()
    {
        if (connected)
        {
            //send compilation request to server
            debug.startProgram();
            socketHost.send(JSON.stringify(pingHostObj));    

            //add a new message to compilation box to tell the user we are compiling the program
            debug.addCompilationBoxMessage("Compiling program...", "alert-dark");
        }
    });

    $("#continue-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("continue");
            socketHost.send(JSON.stringify(pingHostObj));
        }
    });

    $("#stop-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("kill");
            socketHost.send(JSON.stringify(pingHostObj));
        }
    });

    $("#step-over-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("step_over");
            socketHost.send(JSON.stringify(pingHostObj));
        }
    });

    $("#step-into-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("step_into");
            socketHost.send(JSON.stringify(pingHostObj));
        }
    });

    $("#step-out-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("step_out");
            socketHost.send(JSON.stringify(pingHostObj));
        }
    });

    //set up jquery terminal
    $('#code-output').terminal(function(command)
    {
        if (command !== '')
        {
            debug.sendInput(command);
            socketHost.send(JSON.stringify(pingHostObj));
        }
    }, {
        height: 250
    });

    $("#complete-btn")[0].addEventListener("click", function()
    {
        if (connected)
        {
            //confirm that the user wants to submit their answer
            $("#confirm-modal").modal("show");
        }
    });

    $("#confirm-complete-btn")[0].addEventListener("click", function()
    {
        if (connected)
        {
            $("#submitting-exercise-message")[0].innerHTML = "Submitting exercise...";
            $("#spinner-exercise").show();
            $("#submitting-exercise-status")[0].innerHTML = "Submitting...";
            $("#submit-exercise-modal").modal("show");

            //submit user answer
            debug.testProgram();
            socketHost.send(JSON.stringify(pingHostObj));
        }
    });

    //clear the terminal
    debug.clearTerminal();

    $("#switch-active-session-btn")[0].addEventListener("click", function()
    {
        //close the active session
        getUsername().then(function(data)
        {
            var obj = new Request(constants.SENDER_USER);
            obj.operation = constants.OP_MOVE_ACTIVE_SESSION;
            obj.value = data.username;

            console.log(obj);

            socketHost.send(JSON.stringify(obj));

            //reload the page
            location.reload();
        });
    });

    $('#clear-terminal-btn')[0].addEventListener("click", debug.clearTerminal);

    $('#increase-code-size-btn')[0].addEventListener("click", function()
    {
        changeCodeSize(5);
    });

    $('#decrease-code-size-btn')[0].addEventListener("click", function()
    {
        changeCodeSize(-5);
    });

    $('#increase-terminal-size-btn')[0].addEventListener("click", function()
    {
        changeTerminalSize(1);
    });

    $('#decrease-terminal-size-btn')[0].addEventListener("click", function()
    {
        changeTerminalSize(-1);
    });
}

//get session info
function getUsername()
{
    return Promise.resolve($.ajax({
        url: "/honours/webapp/controller/ajaxScripts/getSessionData.php",
        async: false,
        type: "POST",
        dataType: 'json',
        success: function(result)
        {
            return result.username;
        }
    }));
}

//send a new breakpoint to the debugger
function sendBreakpoint(file, row, adding)
{
    if (connected)
    {
        if (adding)
        {
            debug.sendInput("break_silent " + file + ":" + row);
        }
        else
        {
            debug.sendInput("clear_silent " + file + ":" + row);
        }

        socketHost.send(JSON.stringify(pingHostObj));
    }
}

export function pingHostFunc() {
    socketHost.send(JSON.stringify(pingHostObj));
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

//change size of terminal text
function changeTerminalSize(value)
{
    var newValue = parseInt($(".terminal").css('--size'), 10) + value;

    if (newValue > 0)
    {
        $(".terminal").css('--size', newValue);
    }
}
