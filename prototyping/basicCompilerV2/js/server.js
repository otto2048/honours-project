// Socket setup based on tutorial: https://javascript.info/websocket
//include required modules
const http = require('http');
const ws = require('ws');
const spawn = require('child_process').spawn;

const wss = new ws.Server({noServer: true});

//variable to hold child process that runs program
var progProcess;

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
function onConnect(ws) {
    ws.on('message', function (message) {
        const clientMsg = JSON.parse(message);

        //check which operation client has requested
        if (clientMsg.operation == "PLAY")
        {
            //use child process to start program
            progProcess = spawn('./executable');

            progProcess.stdout.on('data', function (data) {
                ws.send(data.toString());
            });

            progProcess.stderr.on('data', function (data) {
				ws.send(data.toString());
            });

            progProcess.on('exit', function (code) {
				var data = 'Program exited with code ' + code.toString();
				ws.send(data);
            });
        }
        else if (clientMsg.operation == "INPUT")
        {
            //send input to child process
            progProcess.stdin.write(clientMsg.value + "\n");
        }
    });
}