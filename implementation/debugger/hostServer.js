// manage user containers

// Socket setup based on tutorial: https://javascript.info/websocket
//include required modules
const http = require('http');
const ws = require('ws');
const wss = new ws.Server({noServer: true});
const exec = require('child_process').exec;

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
            var obj = new Object();
            obj.operation = OP_LAUNCH_DEBUGGER;
            obj.value = null;
            obj.sender = SENDER_HOST;

            var port = Math.floor(Math.random() * 10);

            port = port + 5000;

            command = "docker run -d -p " + port + ":8080 debugger_app:1.1";

            //launch a debugger container for this user
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


              });

        }

        console.log("on message");
        console.log(req.socket.remoteAddress);

        

        
    });

    ws.on('close', function()
    {
        console.log(req.socket.remoteAddress + " lost");

        
    });

    
}