var zeroSixtyTimerStart = Date.now();
var zeroSixtyTimerReady = 0;

function modernTrip () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			/* This section updates the TRIP page with live data from CAN bus every 2 seconds*/
			if ($ahPage === 2) { // If page is active, draw contents
				document.getElementById('tripOdometer').textContent = Math.floor($rd.trip.scrTrp*10)/10;
				document.getElementById('tripMPG').textContent = Math.round($rd.fuel.avgMPG*10)/10;
				document.getElementById('tripMPH').textContent = Math.round($rd.spd.avgMPH*10)/10;
				document.getElementById('tripTimer').textContent = $rd.trip.timeDisp;

				/* ZERO TO SIXTY TIMER */
				$speed = Math.round($rd.spd.MPH);
				if ($speed === 0) {
					zeroSixtyTimerStart = Date.now(); //set timer
					zeroSixtyTimerReady = 1; //timer is ready

				}
				if ($speed < 60 && zeroSixtyTimerReady === 1) {
					document.getElementById('tripZeroSixty').textContent = (Date.now()-zeroSixtyTimerStart)/1000;
				}else if($speed >= 60){
					zeroSixtyTimerReady = 0;  //timer is not ready until speed is 0 again
				}
				/* END ZERO TO SIXTY TIMER */
			}
		}
		modernTrip();
	}, 100);
}
modernTrip();