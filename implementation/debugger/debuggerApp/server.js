//users need to be able to:
    //create a cpp file through this server
    //create an executable through this server
    //run the executable through this server, sending input and getting output

// Socket setup based on tutorial: https://javascript.info/websocket
//include required modules
const http = require('http');
const ws = require('ws');
const spawn = require('child_process').spawn;
const exec = require('child_process').exec;

const OP_CONNECTION = "CONNECTION";
const OP_INPUT = "INPUT";
const OP_COMPILE = "COMPILE";
const OP_TEST = "TEST";
const OP_LAUNCH_DEBUGGER = "DEBUGGER_LAUNCH";

const SENDER_HOST = "HOST_SERVER";
const SENDER_USER = "USER_SENDER";
const SENDER_DEBUGGER = "DEBUGGER_SENDER";

//write to files
const fs = require('fs');
const async = require('async');

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

        console.log(clientMsg);

        //check which operation client has requested
        if (clientMsg.operation == OP_INPUT)
        {
            //send input to child process
            progProcess.stdin.write(clientMsg.value + "\n");
        }
        else if (clientMsg.operation == OP_COMPILE)
        {
            //https://stackoverflow.com/questions/26413329/multiple-writefile-in-nodejs
            async.each(clientMsg.value, function(file, callback) {
                var fname = file[0];
                var content = file[1];

                fs.writeFile(fname, content, function (err)
                {
                    if (err)
                    {
                        console.log(err);
                    }
                    else
                    {
                        console.log("created file " + fname);
                    }

                    callback();
                });
            }, function (err)
            {
                if (err) {
                    // One of the iterations produced an error.
                    // All processing will now stop.
                    console.log('A file failed to process');
                }
                else {
                    console.log('All files have been processed successfully');

                    //compile program
                    var fileString = "";
                    for (var i=0; i<clientMsg.value.length; i++)
                    {
                        console.log(clientMsg.value[i][0].split('.').pop());
                        if (clientMsg.value[i][0].split('.').pop() == "cpp")
                        {
                            fileString = fileString + clientMsg.value[i][0] + " ";
                        }
                    }

                    console.log(fileString);

                    var command = "g++ " + fileString + " -o executable";

                    exec(command, function(err, stdout, stderr)
                    {
                        if (stdout)
                        {
                            //give compilation errors
                            console.log(stdout);
                        }
                        else
                        {
                            //use child process to start program
                            progProcess = spawn('./executable');

                            progProcess.stdout.on('data', function (data) {
                                console.log('stdout: ' + data.toString());
                                ws.send(data.toString());
                            });

                            progProcess.stderr.on('data', function (data) {
                                console.log('stderr: ' + data.toString());
                                ws.send(data.toString());
                            });

                            progProcess.on('exit', function (code) {
                                var data = 'Program exited with code ' + code.toString();
                                console.log();
                                ws.send(data);
                            });
                        }
                    });
                }
            });
            
        }
        else if (clientMsg.operation == OP_TEST)
        {
            //https://stackoverflow.com/questions/26413329/multiple-writefile-in-nodejs
            async.each(clientMsg.value, function(file, callback) {
                //create files for testing
                var fname = file[0];
                var content = file[1];

                fs.writeFile(fname, content, function (err)
                {
                    if (err)
                    {
                        console.log(err);
                    }
                    else
                    {
                        console.log("created file " + fname);
                    }

                    callback();
                });
            }, function (err)
            {
                if (err) {
                    // One of the iterations produced an error.
                    // All processing will now stop.
                    console.log('A file failed to process');
                }
                else {
                    console.log('All files have been processed successfully');

                    //compile program
                    var fileString = "";
                    for (var i=0; i<clientMsg.value.length; i++)
                    {
                        console.log(clientMsg.value[i][0].split('.').pop());
                        if (clientMsg.value[i][0].split('.').pop() == "cpp")
                        {
                            fileString = fileString + clientMsg.value[i][0] + " ";
                        }
                    }

                    console.log(fileString);

                    var command = "g++ -Wall -g -pthread" + fileString + " /usr/local/lib/libgtest.a -o unitTest";

                    exec(command, function(err, stdout, stderr)
                    {
                        if (stdout)
                        {
                            //give compilation errors
                            console.log(stdout);
                        }
                        else
                        {
                            //use child process to start program
                            var testProcess = spawn('./unitTest');

                            testProcess.stdout.on('data', function (data) {
                                console.log('stdout: ' + data.toString());
                                ws.send(data.toString());
                            });

                            testProcess.stderr.on('data', function (data) {
                                console.log('stderr: ' + data.toString());
                                ws.send(data.toString());
                            });

                            testProcess.on('exit', function (code) {
                                var data = 'Program exited with code ' + code.toString();
                                console.log();
                                ws.send(data);
                            });
                        }
                    });
                }
            });
        }
    });
}