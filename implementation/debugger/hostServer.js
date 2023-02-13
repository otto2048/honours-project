// manage user containers

// Socket setup based on tutorial: https://javascript.info/websocket
//include required modules
const http = require('http');
const ws = require('ws');
const wss = new ws.Server({noServer: true});
const exec = require('child_process').exec;

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
        console.log("on message");
        console.log(req.socket.remoteAddress);

        exec("docker run -d -p 5000:8080 --name testName__ debugger_app:1.1", (error, stdout, stderr) => {
            if (error) {
              console.error(`error: ${error.message}`);
              return;
            }
          
            if (stderr) {
              console.error(`stderr: ${stderr}`);
              return;
            }
          
            console.log(`stdout:\n${stdout}`);
          });

        // console.log(path.join(__dirname, "debuggerApp"));
        
        // compose.upAll({ cwd: path.join(__dirname, "debuggerApp"), log: true }).then(
        //     () => {
        //       console.log('done')
        //     },
        //     (err) => {
        //       console.log('something went wrong:', err.message)
        //     }
        //   )
        
    });

    ws.on('close', function()
    {
        console.log(req.socket.remoteAddress + " lost");

        
    });

    
}