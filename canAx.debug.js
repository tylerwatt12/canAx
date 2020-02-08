var HAL = require('./modules/drivers/2018_Subaru_WRX_Premium.js');
var spawn = require('child_process').spawn,
candump = spawn('candump',['any']);
/*
  Loop below stores info as a nested object, socket.io can't send a whole new object set every data update due to bottlenecks. 
  A seperate loop (pushd()) is used to schedule data push events to the client every X ms.
*/
candump.stdout.on('data', function (data) {
	$pipedat = data.toString().split("\n"); // data comes in multiple lines
	$pipedat.forEach(function (e) {
		if (e !== "") { // last line is usually empty, ignore
			$o = e.split(" "); // split line data by spaces (alt to awk)
			$msgline = [$o[4],$o.slice(9).join("")]; // output in "ID# DEADBEEF12345678" format
			//console.log('\033[2J'); // cls
			$rt = HAL.addMessage($msgline[0],$msgline[1]);
		}
	});
});
function pushd() {
  setTimeout(function () {
    if (typeof $rt !== "undefined") {
      
    }
    pushd();
  }, 20);
}
pushd();