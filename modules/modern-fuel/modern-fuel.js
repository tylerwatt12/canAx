
function modernFuel () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			/* This section updates the FUEL page with live data from CAN bus every 500 mili-seconds*/
			if ($ahPage === 4) { // If page is active, draw contents
				document.getElementById('fuelMPG').textContent = Math.round($rd.fuel.MPG*10)/10;
				document.getElementById('fuelMPGA').textContent = Math.round($rd.fuel.avgMPG*10)/10;
				document.getElementById('fuelGPH').textContent = Math.round($rd.fuel.GPH*1000)/1000;
			}
		}
		modernFuel();
	}, 500);
}
modernFuel();