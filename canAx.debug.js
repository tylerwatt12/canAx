/*
This script is launched manually via CLI to debug driver files without system reboot
*/
var driverLoc = './modules/drivers/2018_Subaru_WRX_Premium.js';
var HAL = require(driverLoc);
var spawn = require('child_process').spawn,
candump = spawn('candump',['any']);
/*
  Loop below stores info as a nested object, socket.io can't send a whole new object set every data update due to bottlenecks. 
  A seperate loop (pushd()) is used to schedule data push events to the client every X ms.
*/
console.log("canAx Debugging launched");
console.log("Loaded driver: "+driverLoc);
console.log("=======READY=======");
candump.stdout.on('data', function (data) {
	$pipedat = data.toString().split("\n"); // data comes in multiple lines
	$pipedat.forEach(function (e) {
		if (e !== "") { // last line is usually empty, ignore
			$o = e.split(" "); // split line data by spaces (alt to awk)
			$msgline = [$o[4],$o.slice(9).join("")]; // output in "ID# DEADBEEF12345678" format
			$rt = HAL.addMessage($msgline[0],$msgline[1]);
			//console.log('\033[2J'); // cls
			//console.log($rt.example.test);
		}
	});
});
//pushd is not used. All logging is sent to stdout via console.log();