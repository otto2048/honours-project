// javascript for controlling an exercise, client side

// need to launch docker container to complete exercise in

// need to handle instruction file if it exists

var editors = [];

var files = $(".editor");

window.onload = preparePage();

var socket = null;

function preparePage()
{
    //set up ACE editors
    setUpEditors();

    //launch compiler container app
    launchCompiler();
    
    //add event listener to play button
    $("#play-btn")[0].addEventListener("click", startProgram);

    $("#complete-btn")[0].addEventListener("click", disconnectContainer);

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
    obj.operation = "COMPILE";

    var filesData = [];

    for (var i=0; i<editors.length; i++)
    {
        filesData.push([files[i].getAttribute("id"), editors[i].session.getValue()]);
    }

    obj.value = filesData;

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

//use ajax to launch compiler
function launchCompiler()
{
    $.ajax({
        url: "/honours/webapp/controller/ajaxScripts/launchCompiler.php",
        async: false,
        success: function(result)
        {
            if (result != 0)
            {
                //connect web socket
                socket = new WebSocket("ws://192.168.17.50:5000");

                //set up socket
                socket.onopen = function(e) {
                    console.log("Connection established");

                    //allow user to interact with compiler
                };

                socket.onmessage = function(event) {
                    //get active terminal
                    var term = $.terminal.active();

                    //output received message into terminal
                    term.echo(event.data);
                };

                socket.onclose = function(event) {

                    //clean up docker container
                    disconnectContainer();

                    if (event.wasClean) {
                        console.log("Connection closed cleanly, code=${event.code} reason=${event.reason}");
                    } else {
                        console.log("Connection died");
                    }
                };

                socket.onerror = function(error) {
                    console.log("[error]");
                };
            }
            console.log(result);
        }
    });
}

function disconnectContainer()
{
    //clean up docker container
    $.ajax({
        url: "/honours/webapp/controller/ajaxScripts/killContainer.php",
        async: false,
        success: function(result)
        {
            console.log(result);
        }
    });
}