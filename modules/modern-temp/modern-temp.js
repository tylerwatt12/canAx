
function modernTemp () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			/* This section updates the TEMPERATURE page with live data from CAN bus every 2 seconds*/
			if ($ahPage === 1) { // If page is active, draw contents
				gaugeDraw("tempDialOil","tempOil",$rd.tmp.oil,150,250,220,235,240);
				gaugeDraw("tempDialCoolant","tempCoolant",$rd.tmp.clnt,150,250,220,235,240);
				gaugeDraw("tempDialAmbient","tempAmbient",$rd.tmp.amb,0,100,94,97,101);

				const Http = new XMLHttpRequest();
				const url='/sysctl.php?cmd=cputemp';
				Http.open("GET", url);
				Http.send();

				Http.onreadystatechange = (e) => {
					var $cpuTemp = Math.round(Http.responseText);
					gaugeDraw("tempDialCPU","tempCPU",$cpuTemp,20,90,68,75,85);
				}
			}
		}
		modernTemp();
	}, 2000);
}
modernTemp();