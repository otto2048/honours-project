// javascript for controlling an exercise, client side

// need to launch docker container to complete exercise in

// need to handle instruction file if it exists

import * as constants from "/honours/webapp/view/js/debugger/host/hostConstants.js";
import * as debug from "/honours/webapp/view/js/debugger/app/client.js"
import Request from "/honours/webapp/view/js/debugger/request.js";

var startedLaunching = false;
var launchFailed = false;

window.onload = preparePage();

$(document).ready(function(){
    $("#switch-active-session-btn").hide();
    $("#load-debugger-modal").modal('show');
});

//web socket to connect to the host server
let socketHost = new WebSocket("ws://192.168.17.60:8080");

//set up sockets
let socket = null;

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
            socket = new WebSocket("ws://192.168.17.60:" + message.value);

            socket.onopen = function(e) {
                console.log("Connection established with compiler");

                //client on onopen function
                debug.on_open();

                connected = true;

                $("#spinner")[0].remove();
                $("#debugger-load-status")[0].innerHTML = "Success";

                $("#load-debugger-modal").modal('hide');
            };

            socket.onmessage = function(messageEvent) {

                //client on message function
                debug.on_message(messageEvent);
                
            };

            socket.onclose = function(event) {
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
            
            socket.onerror = function(error) {
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
    debug.setUpEditors();

    debug.prepareDebuggerClient();

    //add event listener to play button
    $("#play-btn")[0].addEventListener("click", function()
    {
        if (connected)
        {
            //send compilation request to server
            debug.startProgram(socket);

            //add a new message to compilation box to tell the user we are compiling the program
            debug.addCompilationBoxMessage("Compiling program...", "alert-dark");
        }
    });

    $("#continue-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("continue", socket);
        }
    });

    $("#stop-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("kill", socket);
        }
    });

    $("#step-over-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("step_over", socket);
        }
    });

    $("#step-into-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("step_into", socket);
        }
    });

    $("#step-out-btn")[0].addEventListener("click", function() {
        if (connected)
        {
            debug.sendInput("step_out", socket);
        }
    });

    //set up jquery terminal
    $('#code-output').terminal(function(command)
    {
        if (command !== '')
        {
            sendInput(command, socket);
        }
    }, {
        height: 500
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

//change code size
function changeCodeSize(value)
{
    //change size of editor text
    var newValue = parseInt($(".editor").css('font-size'), 10) + value;

    if (newValue > 0)
    {
        $(".editor").css('font-size', newValue.toString() + "px");
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