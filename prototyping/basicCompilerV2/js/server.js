const http = require('http');
const ws = require('ws');
const spawn = require('child_process').spawn;

const wss = new ws.Server({noServer: true});

var progProcess;

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

function onConnect(ws) {
  ws.on('message', function (message) {
    const clientMsg = JSON.parse(message);


    if (clientMsg.operation == "PLAY")
    {
        //use child process to start program?
        progProcess = spawn('./executable');

        progProcess.stdout.on('data', function (data) {
          console.log('stdout: ' + data.toString());
          ws.send(data.toString());

        });
        
        progProcess.stderr.on('data', function (data) {
          console.log('stderr: ' + data.toString());
        });
        
        progProcess.on('exit', function (code) {
          console.log('child process exited with code ' + code.toString());
        });
    }
    else if (clientMsg.operation == "INPUT")
    {
       //send input to child process
       progProcess.stdin.write(clientMsg.value + "\n");
       //progProcess.stdin.end();
    }


    //setTimeout(() => ws.close(1000, "Bye!"), 5000);
  });
}

if (!module.parent) {
  http.createServer(accept).listen(8080);
} else {
  exports.accept = accept;
}