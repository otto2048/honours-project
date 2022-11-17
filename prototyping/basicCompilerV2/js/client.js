preparePage();

let socket = new WebSocket("ws://192.168.17.50:8080");

socket.onopen = function(e) {
    console.log("[open] Connection established");
};

socket.onmessage = function(event) {
    console.log(`[message] Data received from server: ${event.data}`);
};

socket.onclose = function(event) {
if (event.wasClean) {
    console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`);
} else {
    // e.g. server process killed or network down
    // event.code is usually 1006 in this case
    console.log('[close] Connection died');
}
};

socket.onerror = function(error) {
    console.log(`[error]`);
};

function preparePage()
{
    //add event listener to play button
    document.getElementById("play-btn").addEventListener("click", startProgram);
}

function startProgram()
{
    var obj = new Object();
    obj.operation = "PLAY";
    obj.value = true;
    socket.send(JSON.stringify(obj));
}