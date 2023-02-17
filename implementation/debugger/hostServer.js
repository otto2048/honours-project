// manage user containers

// Socket setup based on tutorial: https://javascript.info/websocket
//include required modules
const http = require('http');
const ws = require('ws');
const wss = new ws.Server({noServer: true});
const exec = require('child_process').exec;
const { v4: uuidv4 } = require('uuid');

const OP_CONNECTION = "CONNECTION";
const OP_INPUT = "INPUT";
const OP_COMPILE = "COMPILE";
const OP_LAUNCH_DEBUGGER = "DEBUGGER_LAUNCH";

const SENDER_HOST = "HOST_SERVER";
const SENDER_USER = "USER_SENDER";
const SENDER_DEBUGGER = "DEBUGGER_SENDER";

const CONTAINER_RUNNING = "CONTAINER_RUNNING";
const CONTAINER_STOPPING = "CONTAINER_STOPPING";

//keep track of the clients
const clients = [];

//create server
function accept(req, res) {
    // all incoming requests must be websockets
    if (!req.headers.upgrade || req.headers.upgrade.toLowerCase() != 'websocket') {
        res.end();
        return; 
    }

    // can be Connection: keep-alive, Upgrade
    if (!req.headers.connection.match(/\bupgrade\b/i)) {
        res.end();
        return;
    }

    wss.handleUpgrade(req, req.socket, Buffer.alloc(0), onConnect);
}

http.createServer(accept).listen(8080);

//https://stackoverflow.com/questions/2956966/javascript-telling-setinterval-to-only-fire-x-amount-of-times
function checkUserContainerStatus(delay, repetitions, callback, callbackFailure, callbackSuccess, value)
{
    var x = 0;
    var intervalID = setInterval(function () {

        var keys = Object.keys(clients);
        if (!keys.includes(value))
        {
            clearInterval(intervalID);

            callbackSuccess();
        }
        else if (x++ === repetitions) {
            clearInterval(intervalID);

            callbackFailure();
        }

        callback();

    }, delay);
}

//handle connections
function onConnect(ws, req) {
    ws.on('message', function (message) {

        //get message
        var message = JSON.parse(message);
        console.log(message);

        console.log(OP_LAUNCH_DEBUGGER);

        //create response object
        var obj = new Object();
        obj.operation = OP_LAUNCH_DEBUGGER;
        obj.value = null;
        obj.sender = SENDER_HOST;

        //TODO: add a warning to users if they open two windows with environment

        if (message.operation == OP_LAUNCH_DEBUGGER)
        {
            var keys = Object.keys(clients);
            console.log(keys);
            console.log(message.value);

            //if client in clients
            if (keys.includes(message.value))
            {
                console.log("keys included");
                //if container in stopping state
                if (clients[message.value].containerState == CONTAINER_STOPPING)
                {
                    //wait for container to stop
                    var launchAttempts = 10;
                    checkUserContainerStatus(5000, launchAttempts, function()
                    {
                        //callback function
                        console.log("Waiting for removal");
                    }, function() {
                        //failure function
                        //TODO: test this
                        console.log("Removal timeout");
                        ws.send(JSON.stringify(obj));
                    },
                    function()
                    {
                        console.log("Success function");
                        launchContainer(message, obj, ws);
                    }
                    , message.value);
                }
                //if container in running state
                else
                {
                    //return failure
                    ws.send(JSON.stringify(obj));
                }
            }
            else
            {
                console.log("keys not included");
                //run new container
                launchContainer(message, obj, ws);
            }
        }
    });

    ws.on('close', function()
    {
        //get client information
        var values = Object.entries(clients);
        var client = values.find(doc => doc[1].websocket === ws);

        if (client)
        {
            //clean up container for this client

            //set container state to stopping
            clients[client[0]].containerState = CONTAINER_STOPPING;

            //TODO: handle errors
            command = "docker stop " + client[1].containerId;
            exec(command, (error, stdout, stderr) => {
                if (error) {
                  console.error(`error: ${error.message}`);
                  return;
                }
              
                if (stderr) {
                  console.error(`stderr: ${stderr}`);
                  return;
                }
              
                console.log(`stdout:\n${stdout}`);

                command = "docker rm " + client[1].containerId;
                exec(command, (error, stdout, stderr) => {
                    if (error) {
                    console.error(`error: ${error.message}`);
                    return;
                    }
                
                    if (stderr) {
                    console.error(`stderr: ${stderr}`);
                    return;
                    }
                
                    console.log(`stdout:\n${stdout}`);

                    //remove reference to client
                    console.log("Deleting reference to client");
                    delete clients[client[0]];
                });
             });
            
            
        }

    });
}

//launch a container
function launchContainer(userMessage, responseObj, ws)
{
    //generate a port for the container
    var port = generatePort(2, 5);

    if (!port)
    {
        console.log("Out of available ports");
        ws.send(JSON.stringify(responseObj));
        return;
    }

    //run the container
    command = "docker run -d --name " + userMessage.value + " -p " + port + ":8080 debugger_app:1.1";
    console.log(command);

    //launch a debugger container for this user
    //TODO: remove the container if it fails to launch
    exec(command, (error, stdout, stderr) => {
        if (error) {
            console.error(`error: ${error.message}`);
            ws.send(JSON.stringify(responseObj));
            return;
        }

        if (stderr) {
            console.error(`stderr: ${stderr}`);
            ws.send(JSON.stringify(responseObj));
            return;
        }

        console.log(`stdout:\n${stdout}`);

        //send back the port the container was launched on
        responseObj.value = port;

        ws.send(JSON.stringify(responseObj));

        //add client information to clients array
        clients[userMessage.value] = {websocket: ws, containerPort: port, containerId: stdout, containerState: CONTAINER_RUNNING};
    });
}

//generate a port for the container, based on a range and the number of times to try generate a port
function generatePort(portRange, attempts)
{
    var values = Object.values(clients);

    var gen = 0;

    for (var i=0; i<attempts; i++)
    {
        gen = Math.floor(Math.random() * portRange) + 5000;
        console.log("Generated port: "+gen);

        if (!values.find(doc => doc.containerPort === gen))
        {
            return gen;
        }
    }

    return null;
}