const http = require('http');
const ws = require('ws');
const { exec } = require('child_process');

const wss = new ws.Server({noServer: true});

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
        ws.send("playing program!");

        //use child process to start program?
        const progStart = exec('./executable', function (error, stdout, stderr) {
          if (error)
          {
            console.log(error.stack);
            console.log('Error code: ' + error.code);
            console.log('Signal received: ' + error.signal);
          }
          console.log('Child Process STDOUT: ' + stdout);
          console.log('Child Process STDERR: ' + stderr);

          ws.send(stdout);
          }
        )

        progStart.on('exit', function(code) {
          console.log('child process exited with exit code ' + code);
        })
    }


    setTimeout(() => ws.close(1000, "Bye!"), 5000);
  });
}

if (!module.parent) {
  http.createServer(accept).listen(8080);
} else {
  exports.accept = accept;
}