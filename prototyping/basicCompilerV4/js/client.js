// Socket setup based on tutorial: https://javascript.info/websocket

import * as constants from "/honours/webapp/view/js/constants.js";

var editors = [];
var files = $(".editor");

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

    //output response into terminal

    //get active terminal
    var term = $.terminal.active();

    //output received message into terminal
    term.echo(message.value);
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
}


//tell socket that we want to compile and start the program
function startProgram()
{
    var obj = new Object();
    obj.operation = constants.OP_COMPILE;

    var filesData = [];

    console.log("editors");
     console.log(editors);

    for (var i=0; i<editors.length; i++)
    {
        console.log(editors[i].session.getValue());
        console.log("hi");
        filesData.push([files[i].getAttribute("id"), editors[i].session.getValue()]);
    }

    obj.value = filesData;

    console.log(filesData);

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