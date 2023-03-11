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
const tail = require("tail").Tail;

const OP_INPUT = "INPUT";
const OP_COMPILE = "COMPILE";
const OP_TEST = "TEST";

const EVENT_ON_BREAK = "EVENT_ON_BREAK";
const EVENT_ON_BREAK_END = "EVENT_ON_BREAK_END";
const EVENT_ON_CONTINUE = "EVENT_ON_CONTINUE";
const EVENT_ON_INFERIOR_EXIT = "EVENT_ON_PROG_EXIT";
const EVENT_ON_INFERIOR_EXIT_END = "EVENT_ON_PROG_EXIT_END";
const EVENT_ON_CONTINUE_END = "EVENT_ON_CONTINUE_END";
const EVENT_ON_STEP = "EVENT_ON_STEP";
const EVENT_ON_STEP_END = "EVENT_ON_STEP_END";
const EVENT_ON_STDOUT = 1;
const EVENT_ON_COMPILE_SUCCESS = 2;
const EVENT_ON_COMPILE_FAILURE = 3;
const EVENT_ON_PROGRAM_EXIT = 4;
const EVENT_ON_TEST_SUCCESS = 5;
const EVENT_ON_TEST_FAILURE = 6;
const EVENT_ON_BREAKPOINT_CHANGED = "EVENT_ON_BP_CHANGED";
const EVENT_ON_BREAKPOINT_CHANGED_END = "EVENT_ON_BP_CHANGED_END";
const EVENT_ON_LOCALS_DUMP = "EVENT_ON_LOCALS_DUMP";
const EVENT_ON_LOCALS_DUMP_END = "EVENT_ON_LOCALS_DUMP_END";

const SENDER_DEBUGGER = "DEBUGGER_SENDER";

const GDB_OUTPUT_STRING = "FOR_SERVER"

//write to files
const fs = require('fs');
const async = require('async');

//logging
const winston = require("winston");

const logger = winston.createLogger({
    level: 'info',
    transports: [
      //
      // - Write all logs with importance level of `error` or less to `error.log`
      // - Write all logs with importance level of `info` or less to `combined.log`
      //
      new winston.transports.File({ filename: 'error.log', level: 'error' }),
      new winston.transports.File({ filename: 'combined.log' }),
    ],
  });

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

                fs.writeFile(fname, content, function (err)
                {
                    if (err)
                    {
                        logger.info(err);
                    }
                    else
                    {
                        logger.info("created file " + fname);
                    }

                    callback();
                });
            }, function (err)
            {
                if (err) {
                    // One of the iterations produced an error.
                    // All processing will now stop.
                    ws.send(JSON.stringify(obj));
                    logger.info('A file failed to process');
                }
                else {
                    logger.info('All files have been processed successfully');

                    //compile program
                    var fileString = "";
                    for (var i=0; i<clientMsg.value.filesData.length; i++)
                    {
                        if (clientMsg.value.filesData[i][0].split('.').pop() == "cpp")
                        {
                            fileString = fileString + clientMsg.value.filesData[i][0] + " ";
                        }
                    }

                    var command = "g++ -g " + fileString + " -o executable";

                    exec(command, function(err, stdout, stderr)
                    {
                        if (stderr)
                        {
                            //give compilation errors
                            // stderr = stderr.replace(' << "'+ GDB_OUTPUT_STRING + " " + PROGRAM_OUTPUT_STRING + '"', "");
                            // stderr = stderr.replace(' << " ' + PROGRAM_OUTPUT_STRING_END + '"', "");
                            obj.value = "Failed to compile\nErrors:\n" + stderr;
                            obj.event = EVENT_ON_COMPILE_FAILURE;
                            ws.send(JSON.stringify(obj));
                        }
                        else
                        {
                            //send compilation success message
                            obj.value = "Successfully compiled program \nRunning in terminal...";
                            obj.event = EVENT_ON_COMPILE_SUCCESS;
                            ws.send(JSON.stringify(obj));

                            fs.readFile('gdbinit_base', 'utf8', function(err, data)
                            {
                                if (err)
                                {
                                    logger.info(err);
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

                                                var content = "break_silent " + breakpoint[0] + ":" + breakpoint[1]+"\n";
                                
                                                fs.appendFile('.gdbinit', content, function (err)
                                                {
                                                    if (err)
                                                    {
                                                        logger.info(err);
                                                    }
                                                    else
                                                    {
                                                        logger.info("appended file ");
                                                    }
                                
                                                    callback();
                                                });
                                            }, function (err)
                                            {
                                                if (err) {
                                                    // One of the iterations produced an error.
                                                    // All processing will now stop.
                                                    ws.send(JSON.stringify(obj));
                                                    logger.info('A breakpoint failed to append');
                                                }
                                                else
                                                {
                                                    //write the rest of the file
                                                    appendFile('.gdbinit', "run > outfile").then(function() {
                                                        //launch gdb
                                                        launchGDB(obj, ws);
                                                    }).catch(function(err) {
                                                        logger.info(err);
                                                    });;
                                                }
                                            });
                                        }
                                        else
                                        {
                                            //write the rest of the file
                                            appendFile('.gdbinit', "run > outfile").then(function() {
                                                //launch gdb
                                                launchGDB(obj, ws);
                                            }).catch(function(err) {
                                                logger.info(err);
                                            });
                                        }
                                        }).catch(function(err) {
                                            logger.info(err);
                                        });
                                    }
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
                var fname_ = file[0];
                var content_ = file[1];

                fs.writeFile(fname_, content_, function (err)
                {
                    if (err)
                    {
                        logger.info(err);
                    }
                    else
                    {
                        logger.info("created file " + fname_);
                    }

                    callback();
                });
            }, function (err)
            {
                if (err) {
                    // One of the iterations produced an error.
                    // All processing will now stop.
                    obj.event = EVENT_ON_TEST_FAILURE;
                    ws.send(JSON.stringify(obj));
                    logger.info('A file failed to process');
                }
                else {
                    logger.info('All files have been processed successfully');

                    //compile program
                    var fileString_ = "";
                    for (var i=0; i<clientMsg.value.length; i++)
                    {
                        logger.info(clientMsg.value[i][0].split('.').pop());
                        if (clientMsg.value[i][0].split('.').pop() == "cpp")
                        {
                            fileString_ = fileString_ + clientMsg.value[i][0] + " ";
                        }
                    }

                    logger.info(fileString_);

                    var command_ = "g++ -Wall -g -pthread " + fileString_ + " /usr/local/lib/libgtest.a -o unitTest";

                    exec(command_, function(err, stdout, stderr)
                    {
                        if (stderr)
                        {
                            //if compilation failures, submission fails
                            obj.event = EVENT_ON_TEST_FAILURE;
                            ws.send(JSON.stringify(obj));
                        }
                        else
                        {
                            //run test program
                            exec("./unitTest --gtest_brief=1 --gtest_print_time=0", function(err, stdout, stderr)
                            {
                                if (stdout)
                                {
                                    obj.value = stdout.toString();
                                    obj.event = EVENT_ON_TEST_SUCCESS;
                                    ws.send(JSON.stringify(obj));
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

    let tailProcess = new tail("outfile");

    tailProcess.on("line", data => {
        logger.info('tail process stdout: ' + data);
        obj.value = data;
        obj.event = EVENT_ON_STDOUT;
        ws.send(JSON.stringify(obj));
    });

    tailProcess.on("error", err => {
        logger.info("tail proc err" + err);
    })

    progProcess.stdout.on('data', function (data) {
        logger.info('stdout: ' + data.toString());
        logger.info("stdout length: " + data.toString().length)

        output = data.toString();

        output = output.split(GDB_OUTPUT_STRING);

        output.forEach(element => {
            element = element.split(GDB_OUTPUT_STRING).pop();

            //check what kind of gdb event this is
            if (element.indexOf(EVENT_ON_BREAK) != -1)
            {
                //split on start string
                element = element.substring(element.indexOf(EVENT_ON_BREAK) + EVENT_ON_BREAK.length);

                //split on end string
                element = element.split(EVENT_ON_BREAK_END, 1)[0];

                //get rid of whitespace
                element = element.replace(/\s/g, "");

                //return breakpoint location
                obj.value = element;
                obj.event = EVENT_ON_BREAK;
                ws.send(JSON.stringify(obj));
            }
            else if (element.indexOf(EVENT_ON_CONTINUE) != -1)
            {
                obj.event = EVENT_ON_CONTINUE;
                ws.send(JSON.stringify(obj));
            }
            else if (element.indexOf(EVENT_ON_STEP) != -1)
            {
                //split on start string
                element = element.substring(element.indexOf(EVENT_ON_STEP) + EVENT_ON_STEP.length);

                //split on end string
                element = element.split(EVENT_ON_STEP_END, 1)[0];

                //get rid of whitespace
                element = element.replace(/\s/g, "");

                //return current location
                obj.value = element;
                obj.event = EVENT_ON_STEP;
                ws.send(JSON.stringify(obj));
            }
            else if (element.indexOf(EVENT_ON_BREAKPOINT_CHANGED) != -1)
            {
                //split on start string
                element = element.substring(element.indexOf(EVENT_ON_BREAKPOINT_CHANGED) + EVENT_ON_BREAKPOINT_CHANGED.length);

                //split on end string
                element = element.split(EVENT_ON_BREAKPOINT_CHANGED_END, 1)[0];

                obj.value = element;
                obj.event = EVENT_ON_BREAKPOINT_CHANGED;
                ws.send(JSON.stringify(obj));
            }
            //check if this is output for the user
            else if (element.indexOf(EVENT_ON_INFERIOR_EXIT) != -1)
            {
                //split on start of string
                element = element.substring(element.indexOf(EVENT_ON_INFERIOR_EXIT) + EVENT_ON_INFERIOR_EXIT.length);
                
                //split on end of string
                element = element.split(EVENT_ON_INFERIOR_EXIT_END, 1)[0];

                obj.value = element;
                obj.event = EVENT_ON_INFERIOR_EXIT;
                ws.send(JSON.stringify(obj));
            }
            else if (element.indexOf(EVENT_ON_LOCALS_DUMP) != -1)
            {
                //split on start of string
                element = element.substring(element.indexOf(EVENT_ON_LOCALS_DUMP) + EVENT_ON_LOCALS_DUMP.length);
                
                //split on end of string
                element = element.split(EVENT_ON_LOCALS_DUMP_END, 1)[0];

                obj.value = element;
                obj.event = EVENT_ON_LOCALS_DUMP;
                ws.send(JSON.stringify(obj));
            }
            
        });
    });

    progProcess.stderr.on('data', function (data) {
        logger.info('stderr: ' + data.toString());
    });

    progProcess.on('exit', function (code) {
        logger.info("exited");
        obj.event = EVENT_ON_PROGRAM_EXIT;
        logger.info(obj);
        ws.send(JSON.stringify(obj));
        progProcess = null;
        tailProcess.unwatch();
    });
}

//TODO: output program exit code

//TODO: tail only outputs a line after endl
//TODO: sometimes exit code is printed before output