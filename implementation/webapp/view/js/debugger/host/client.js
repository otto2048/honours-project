// javascript for controlling an exercise, client side

// need to launch docker container to complete exercise in

// need to handle instruction file if it exists

import * as constants from "/honours/webapp/view/js/debugger/host/hostConstants.js";
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

                connected = true;

                $("#spinner")[0].remove();
                $("#debugger-load-status")[0].innerHTML = "Success";

                $("#load-debugger-modal").modal('hide');
            };

            socket.onmessage = function(messageEvent) {

                //client on message function
                
            };

            socket.onclose = function(event) {
                if (connected)
                {
                    $("#debugger-load-message")[0].innerHTML = "Environment failed.";
                    $("#debugger-load-status")[0].innerHTML = "Failed";
                    $("#load-debugger-modal").modal('show');
    
                    //client on close function
    
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
    })
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
