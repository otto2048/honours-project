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

//handle connections
function onConnect(ws, req) {
    ws.on('message', function (message) {

        //get message
        var message = JSON.parse(message);
        console.log(message);

        console.log(OP_LAUNCH_DEBUGGER);

        if (message.operation == OP_LAUNCH_DEBUGGER)
        {
            //create response object
            var obj = new Object();
            obj.operation = OP_LAUNCH_DEBUGGER;
            obj.value = null;
            obj.sender = SENDER_HOST;

            //generate a port for the container
            var port = generatePort(2, 5);

            if (!port)
            {
                console.log("Out of available ports");
                ws.send(JSON.stringify(obj));
                return;
            }

            //run the container
            command = "docker run -d -p " + port + ":8080 debugger_app:1.1";
            console.log(command);

            //launch a debugger container for this user
            //TODO: remove the container if it fails to launch
            exec(command, (error, stdout, stderr) => {
                if (error) {
                  console.error(`error: ${error.message}`);
                  ws.send(JSON.stringify(obj));
                  return;
                }
              
                if (stderr) {
                  console.error(`stderr: ${stderr}`);
                  ws.send(JSON.stringify(obj));
                  return;
                }
              
                console.log(`stdout:\n${stdout}`);

                //send back the port the container was launched on
                obj.value = port;

                ws.send(JSON.stringify(obj));

                var key = uuidv4();

                //add client information to clients array
                clients[key] = {websocket: ws, containerPort: port, containerId: stdout};
              });

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