
function modernGauge () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			/* This section updates the GAUGE page with live data from CAN bus every 2 seconds*/
			if ($ahPage === 5) { // If page is active, draw contents
				gaugeDraw("gaugeDialBoost","gaugeBoost",Math.round($rd.ngn.boost*10)/10,-12,25,18,25,30);
				gaugeDraw("gaugeDialAccel","gaugeAccel",Math.round($rd.pdl.accel*10)/10,0,100,101,102,103);
				gaugeDraw("gaugeDialTbody","gaugeTbody",Math.round($rd.ngn.tbody*10)/10,0,100,101,102,103);
				gaugeDraw("gaugeDialBrake","gaugeBrake",Math.round($rd.pdl.brkPrs*10)/10,0,100,101,102,103);
				gaugeDraw("gaugeDialSteerAngle","gaugeSteerAngle",$rd.stng.whl,-495,495,500,501,502);
				gaugeDraw("gaugeDialSteerTq","gaugeSteerTq",$rd.stng.ttlTq,0,512,1000,1001,1002);
			}
		}
		modernGauge();
	}, 50);
}
modernGauge();