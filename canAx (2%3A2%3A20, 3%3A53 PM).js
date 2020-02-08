var HAL = require('./modules/drivers/2018_Subaru_WRX_Premium.js');
var spawn = require('child_process').spawn,
candump = spawn('candump',['any']);

var http = require('http'),
    fs = require('fs');

var app = http.createServer(function(req, res) {
  res.setHeader('Access-Control-Allow-Origin', req.socket.localAddress.replace(/^.*:/, ''));
  res.setHeader('Access-Control-Request-Method', req.socket.localAddress.replace(/^.*:/, ''));
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
  res.setHeader('Access-Control-Allow-Headers', req.socket.localAddress.replace(/^.*:/, ''));
    if (req.url == "/") {
      console.log('Client load template: Debug');
      res.writeHead(200, {'Content-Type': 'text/html'});
      res.end(fs.readFileSync(__dirname + '/template.html'));
    }else if(req.url == "/f1"){
      console.log('Client load template: F1');
      res.writeHead(200, {'Content-Type': 'text/html'});
      res.end(fs.readFileSync(__dirname + '/template-f1.html'));
    }else if(req.url == "/modern"){
      console.log('Client load template: Modern');
      res.writeHead(200, {'Content-Type': 'text/html'});
      res.end(fs.readFileSync(__dirname + '/template-modern.html'));
    }else{
      console.log('Request rewrite: http://'+req.socket.localAddress.replace(/^.*:/, '')+req.url);
      res.writeHead(302, {'Location': 'http://'+req.socket.localAddress.replace(/^.*:/, '')+req.url});
      res.end();
    }
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
			//console.log('\033[2J'); // cls
			$rt = HAL.addMessage($msgline[0],$msgline[1]);
		}
	});
});



function pushd() {
  setTimeout(function () {
    if (typeof $rt !== "undefined") {
      io.emit('data', $rt);
    }
    pushd();
  }, 20);
}
pushd();

app.listen(8080);
