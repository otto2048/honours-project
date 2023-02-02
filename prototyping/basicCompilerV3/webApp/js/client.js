//send commands to the API from button presses

// Socket setup based on tutorial: https://javascript.info/websocket

window.onload = preparePage();

let socket = new WebSocket("ws://192.168.17.50:5000");

//set up socket
socket.onopen = function(e) {
    console.log("Connection established");
};

socket.onmessage = function(event) {
    //get active terminal
    var term = $.terminal.active();

    //output received message into terminal
    term.echo(event.data);
};

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

//tell socket that we want to start playing the program
function startProgram()
{
    var obj = new Object();
    obj.operation = "COMPILE";
    obj.value = document.getElementsByName("code-input")[0].value;
    socket.send(JSON.stringify(obj));
}

//tell socket that we want to send some input to the program
function sendInput(input)
{
    var obj = new Object();
    obj.operation = "INPUT";
    obj.value = input;
    socket.send(JSON.stringify(obj));
}