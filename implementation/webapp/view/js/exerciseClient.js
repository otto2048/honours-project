// javascript for controlling an exercise, client side

// need to launch docker container to complete exercise in

// need to handle instruction file if it exists

import * as constants from "../js/constants.js";

var editors = [];

var files = $(".editor");

window.onload = preparePage();

$(document).ready(function(){
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
    var obj = new Object();
    obj.operation = constants.OP_LAUNCH_DEBUGGER;
    obj.value = true;
    obj.sender = constants.SENDER_USER;

    socketHost.send(JSON.stringify(obj));
};

socketHost.onerror = function(error) {
    $("#debugger-load-message")[0].innerHTML = "Failed to connect to server";
    $("#spinner")[0].remove();
    $("#debugger-load-status")[0].innerHTML = "Failed";
};

socketHost.onmessage = function(event) {
    var message = JSON.parse(event.data);

    console.log(message);

    if (message.operation == constants.OP_LAUNCH_DEBUGGER)
    {
        if (message.value)
        {
            //connect to the container wss
            socket = new WebSocket("ws://192.168.17.60:" + message.value);

            socket.onopen = function(e) {
                console.log("Connection established with compiler");
            
                //allow user to interact with compiler, enable buttons
                var debuggerBtns = $(".debugger-control");

                for (var i=0; i<debuggerBtns.length; i++)
                {
                    debuggerBtns[i].disabled = false;
                    debuggerBtns[i].ariaDisabled = false;
                }

                connected = true;

                $("#debugger-load-message")[0].innerHTML = "Connected to environment";
                $("#spinner")[0].remove();
                $("#debugger-load-status")[0].innerHTML = "Success";

                $("#load-debugger-modal").modal('hide');
            };

            socket.onmessage = function(messageEvent) {

                //handle message
                var message = JSON.parse(messageEvent.data);

                if (message.operation == constants.OP_INPUT)
                {
                    //output response into terminal

                    //get active terminal
                    var term = $.terminal.active();
                
                    //output received message into terminal
                    term.echo(message.value);
                }
                else if (message.operation == constants.OP_TEST)
                {
                    console.log(message.value);
                }
                
            };

            socket.onclose = function(event) {
                console.log(event);
                if (event.wasClean) {
                    console.log("Connection closed cleanly, code=${event.code} reason=${event.reason}");
                } else {
                    console.log("Connection died");
                }
            };
            
            socket.onerror = function(error) {
                $("#debugger-load-message")[0].innerHTML = "Failed to connect to server";
                $("#spinner")[0].remove();
                $("#debugger-load-status")[0].innerHTML = "Failed";
            };
        }
        else
        {
            console.log("Failed to init compiler");

            $("#debugger-load-message")[0].innerHTML = "Failed to connect to environment";
            $("#spinner")[0].remove();
            $("#debugger-load-status")[0].innerHTML = "Failed";
        }
    }
}



function preparePage()
{
    //set up ACE editors
    setUpEditors();
    
    //add event listener to play button
    $("#play-btn")[0].addEventListener("click", function()
    {
        if (connected)
        {
            startProgram();
        }
    });

    // TODO: marking of exercise if applicable
    $("#complete-btn")[0].addEventListener("click", function()
    {
        if (connected)
        {
            testProgram();
        }
    })

    //set up jquery terminal
    $('#code-output').terminal(function(command)
    {
        if (command !== '' && connected)
        {
            sendInput(command);
        }
    }, {
        height: 500
    });
}

//tell socket that we want to compile and start the program
function startProgram()
{
    var obj = new Object();
    obj.operation = constants.OP_COMPILE;

    var filesData = [];

    for (var i=0; i<editors.length; i++)
    {
        filesData.push([files[i].getAttribute("id"), editors[i].session.getValue()]);
    }

    obj.value = filesData;

    socket.send(JSON.stringify(obj));
}

//tell socket to run unit test on program code
function testProgram()
{
    var obj = new Object();
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
        editorFiles.push([files[i].getAttribute("id"), editors[i].session.getValue()]);
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

    socket.send(JSON.stringify(obj));
}

//tell socket that we want to send some input to the program
function sendInput(input)
{
    var obj = new Object();
    obj.operation = constants.OP_INPUT;
    obj.value = input;
    socket.send(JSON.stringify(obj));
}

//set up the code editors for all the files
function setUpEditors()
{
    for (var i=0; i<files.length; i++)
    {
        editors.push(ace.edit(files[i].getAttribute("id"))); 
    }

    for (var i=0; i<editors.length; i++)
    {
        editors[i].setTheme("ace/theme/tomorrow_night_bright");
        editors[i].session.setMode("ace/mode/c_cpp");
    }
}