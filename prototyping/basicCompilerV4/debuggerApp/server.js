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
const Response = require('./response.js');

const OP_INPUT = "INPUT";
const OP_COMPILE = "COMPILE";
const OP_TEST = "TEST";

const EVENT_ONBREAK = 0;
const EVENT_ONSTDOUT = 1;
const EVENT_ONCOMPILE_SUCCESS = 2;
const EVENT_ONCOMPILE_FAILURE = 3;
const EVENT_ONPROGRAM_EXIT = 4;

const SENDER_DEBUGGER = "DEBUGGER_SENDER";

const PROGRAM_OUTPUT_STRING = "PROGRAM_OUTPUT ";

//write to files
const fs = require('fs');
const async = require('async');

const wss = new ws.Server({noServer: true});

//variable to hold child process that runs program
var progProcess;
var running = false;

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

        //create response object
        var obj = new Response(SENDER_DEBUGGER);

        //check which operation client has requested
        if (clientMsg.operation == OP_INPUT)
        {
            if (progProcess)
            {
                //send input to child process
                progProcess.stdin.write(clientMsg.value + "\n");
            }
        }
        else if (clientMsg.operation == OP_COMPILE)
        {
            //https://stackoverflow.com/questions/26413329/multiple-writefile-in-nodejs
            async.each(clientMsg.value.filesData, function(file, callback) {
                var fname = file[0];
                var content = file[1];

                content = content.replace(/cout/g, 'cout << "'+ PROGRAM_OUTPUT_STRING +'"');
                console.log(content);

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
                    ws.send(JSON.stringify(obj));
                    console.log('A file failed to process');
                }
                else {
                    console.log('All files have been processed successfully');

                    //compile program
                    var fileString = "";
                    for (var i=0; i<clientMsg.value.filesData.length; i++)
                    {
                        console.log(clientMsg.value.filesData[i][0].split('.').pop());
                        if (clientMsg.value.filesData[i][0].split('.').pop() == "cpp")
                        {
                            fileString = fileString + clientMsg.value.filesData[i][0] + " ";
                        }
                    }

                    console.log(fileString);

                    var command = "g++ -g " + fileString + " -o executable";

                    exec(command, function(err, stdout, stderr)
                    {
                        if (stderr)
                        {
                            //give compilation errors
                            stderr = stderr.replace(' << "' + PROGRAM_OUTPUT_STRING + '"', "");
                            obj.value = "Failed to compile\nErrors:\n" + stderr;
                            obj.event = EVENT_ONCOMPILE_FAILURE;
                            ws.send(JSON.stringify(obj));
                        }
                        else
                        {
                            //send compilation success message
                            obj.value = "Successfully compiled program \nRunning in terminal...";
                            obj.event = EVENT_ONCOMPILE_SUCCESS;
                            ws.send(JSON.stringify(obj));

                            fs.readFile('gdbinit_base', 'utf8', function(err, data)
                            {
                                if (err)
                                {
                                    console.log(err);
                                }
                                else
                                {
                                    //write the base
                                    writeFile('.gdbinit', data + "\n").then(function()
                                    {
                                        if (clientMsg.value.breakpoints.length > 0)
                                        {
                                            //write the breakpoints
                                            async.each(clientMsg.value.breakpoints, function(breakpoint, callback) {

                                                var content = "break " + breakpoint[0] + ":" + breakpoint[1]+"\n";
                                
                                                fs.appendFile('.gdbinit', content, function (err)
                                                {
                                                    if (err)
                                                    {
                                                        console.log(err);
                                                    }
                                                    else
                                                    {
                                                        console.log("appended file ");
                                                    }
                                
                                                    callback();
                                                });
                                            }, function (err)
                                            {
                                                if (err) {
                                                    // One of the iterations produced an error.
                                                    // All processing will now stop.
                                                    ws.send(JSON.stringify(obj));
                                                    console.log('A breakpoint failed to append');
                                                }
                                                else
                                                {
                                                    //write the rest of the file
                                                    appendFile('.gdbinit', "run").then(function() {
                                                        //launch gdb
                                                        launchGDB(obj, ws);
                                                    }).catch(function(err) {
                                                        console.log(err);
                                                    });;
                                                }
                                            });
                                        }
                                        else
                                        {
                                            //write the rest of the file
                                            appendFile('.gdbinit', "run").then(function() {
                                                //launch gdb
                                                launchGDB(obj, ws);
                                            }).catch(function(err) {
                                                console.log(err);
                                            });
                                        }
                                        }).catch(function(err) {
                                            console.log(err);
                                        });
                                    }
                            });
                            
                            
                        }
                    });
                }
            });
            
        }
    });
}

//https://stackoverflow.com/questions/40292837/can-multiple-fs-write-to-append-to-the-same-file-guarantee-the-order-of-executio
function writeFile(fileName, content)
{
    return new Promise((resolve, reject) =>  {
        fs.writeFile(fileName, content, (err) => {
            if (err) return reject(err);
            resolve();
        });
    });
}

function appendFile(fileName, content)
{
    return new Promise((resolve, reject) =>  {
        fs.appendFile(fileName, content, (err) => {
            if (err) return reject(err);
            resolve();
        });
    });
}

function launchGDB(obj, ws)
{
    //use child process to start program
    progProcess = spawn('gdb', ['-q', 'executable']);

    progProcess.stdout.on('data', function (data) {
        console.log('stdout: ' + data.toString());

        output = data.toString();

        if (output.indexOf(PROGRAM_OUTPUT_STRING) != -1)
        {
            //check if this is a breakpoint
            if (output.indexOf("Breakpoint") != 1)
            {
                console.log(output.indexOf("Breakpoint"));
                output = output.replace(PROGRAM_OUTPUT_STRING, "");
                obj.value = output;
                obj.event = EVENT_ONSTDOUT;
                ws.send(JSON.stringify(obj));
            }
        }
    });

    progProcess.stderr.on('data', function (data) {
        console.log('stderr: ' + data.toString());
    });

    progProcess.on('exit', function (code) {
        console.log("exited");
        obj.event = EVENT_ONPROGRAM_EXIT;
        console.log(obj);
        ws.send(JSON.stringify(obj));
        progProcess = null;
    });
}

//TODO: output program exit code