/*

This script called at boot via rc.local
Example rc.local below (don't remove leading spaces from lines)
===================================================================
 sudo modprobe vcan
 sudo ip link add dev vcan0 type vcan
 sudo ip link set up vcan0
 sudo canplayer -I /var/www/html/logs/trip.log -l i -v vcan0=can0 &

cd /var/www/html
 sudo node canAx.js
exit 0
===================================================================

*/
var tcpPort = 8080;
var tcpSendFrq = 20; // how often to send updated data in ms
var driverLoc = './modules/drivers/2018_Subaru_WRX_Premium.js';
var HAL = require(driverLoc);
var spawn = require('child_process').spawn,
candump = spawn('candump',['any']);

var http = require('http'),
    fs = require('fs');

var app = http.createServer(function(req, res) {
});

/*
  Loop below stores info as a nested object, socket.io can't send a whole new object set every data update due to bottlenecks. 
  A seperate loop (pushd()) is used to schedule data push events to the client every X ms.
*/
var io = require('socket.io').listen(app);
candump.stdout.on('data', function (data) {
	$pipedat = data.toString().split("\n"); // data comes in multiple lines
	$pipedat.forEach(function (e) {
		if (e !== "") { // last line is usually empty, ignore
			$o = e.split(" "); // split line data by spaces (alt to awk)
			$msgline = [$o[4],$o.slice(9).join("")]; // output in "ID# DEADBEEF12345678" format
			$rt = HAL.addMessage($msgline[0],$msgline[1]);
		}
	});
});


/*
	Loop below sends data to front end on Apache/PHP
*/
function pushd() {
  setTimeout(function () {
    if (typeof $rt !== "undefined") {
      io.emit('data', $rt);
    }
    pushd();
  }, tcpSendFrq);
}
pushd();

app.listen(trpPort);
