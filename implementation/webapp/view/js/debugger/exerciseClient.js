// javascript for controlling an exercise, client side

// need to launch docker container to complete exercise in

// need to handle instruction file if it exists

import * as constants from "../js/constants.js";

var editors = [];
var startedLaunching = false;
var launchFailed = false;

var files = $(".editor");

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

var pingHostObj = {operation: constants.OP_PING, value: null, sender: constants.SENDER_USER};

socketHost.onopen = function(e) {
    console.log("Connection established with host");

    //ask the host to launch a compiler for us
    getUsername().then(function(data)
    {
        var obj = new Object();
        obj.operation = constants.OP_LAUNCH_DEBUGGER;
        obj.value = data.username;
        obj.sender = constants.SENDER_USER;

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
            
                //allow user to interact with compiler, enable buttons
                var debuggerBtns = $(".debugger-control");

                for (var i=0; i<debuggerBtns.length; i++)
                {
                    debuggerBtns[i].disabled = false;
                    debuggerBtns[i].ariaDisabled = false;
                }

                connected = true;

                $("#spinner")[0].remove();
                $("#debugger-load-status")[0].innerHTML = "Success";

                $("#load-debugger-modal").modal('hide');
            };

            socket.onmessage = function(messageEvent) {

                //handle message
                var message = JSON.parse(messageEvent.data);
                console.log(message);

                if (message.operation == constants.OP_INPUT)
                {
                    //output response into terminal

                    //get active terminal
                    var term = $.terminal.active();
                
                    //output received message into terminal
                    term.echo(message.value);
                }
                else if (message.operation == constants.OP_COMPILE)
                {
                    if (message.value)
                    {
                        //display compilation output
                        addCompilationBoxMessage(message.value, "alert-info");
                    }
                    else
                    {
                        addCompilationBoxMessage("Compilation operation failed. Try again?.", "alert-dark");
                    }
                    
                }
                else if (message.operation == constants.OP_TEST)
                {
                    if (message.value)
                    {
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
                    }
                    else
                    {
                        console.log("server failed");
                        $("#submitting-exercise-message")[0].innerHTML = "Failed to submit exercise. Try again?";
                        $("#spinner-exercise").hide();
                        $("#submitting-exercise-status")[0].innerHTML = "Failed";
                    }
                    
                }
                
            };

            socket.onclose = function(event) {
                if (connected)
                {
                    $("#debugger-load-message")[0].innerHTML = "Environment failed.";
                    $("#debugger-load-status")[0].innerHTML = "Failed";
                    $("#load-debugger-modal").modal('show');
    
                    var debuggerBtns = $(".debugger-control");
    
                    for (var i=0; i<debuggerBtns.length; i++)
                    {
                        debuggerBtns[i].disabled = true;
                        debuggerBtns[i].ariaDisabled = true;
                    }
    
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
    //set up ACE editors
    setUpEditors();
    
    //add event listener to play button
    $("#play-btn")[0].addEventListener("click", function()
    {
        if (connected)
        {
            //clear the terminal
            clearTerminal();

            //send compilation request to server
            startProgram();

            //add a new message to compilation box to tell the user we are compiling the program
            addCompilationBoxMessage("Compiling program...", "alert-dark");
        }
    });

    $("#switch-active-session-btn")[0].addEventListener("click", function()
    {
        //close the active session
        getUsername().then(function(data)
        {
            var obj = new Object();
            obj.operation = constants.OP_MOVE_ACTIVE_SESSION;
            obj.value = data.username;
            obj.sender = constants.SENDER_USER;

            console.log(obj);

            socketHost.send(JSON.stringify(obj));

            //reload the page
            location.reload();
        });
    })

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
            testProgram();
        }
    });

    //set up jquery terminal
    $('#code-output').terminal(function(command)
    {
        if (command !== '' && connected)
        {
            sendInput(command);
        }
    }, {
        height: $('.tab-content').height()
    });

    clearTerminal();

    $('#clear-terminal-btn')[0].addEventListener("click", clearTerminal);

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

//clear terminal
function clearTerminal()
{
    var term = $.terminal.active();
    term.clear();
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
    socketHost.send(JSON.stringify(pingHostObj));    
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
    socketHost.send(JSON.stringify(pingHostObj));
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

//tell socket that we want to send some input to the program
function sendInput(input)
{
    var obj = new Object();
    obj.operation = constants.OP_INPUT;
    obj.value = input;
    socket.send(JSON.stringify(obj));
    socketHost.send(JSON.stringify(pingHostObj));
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

            var breakpoints = e.editor.session.getBreakpoints(row, 0);
            var row = e.getDocumentPosition().row;

            // If there's a breakpoint already defined, it should be removed, offering the toggle feature
            if(typeof breakpoints[row] === typeof undefined){
                e.editor.session.setBreakpoint(row);
            }else{
                e.editor.session.clearBreakpoint(row);
            }

            e.stop();
        })
        
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