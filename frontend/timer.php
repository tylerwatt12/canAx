<?php include('main.php'); ?><!doctype html>
<html>
	<head>
		<title>CanAx - Timer</title>
		<script src='js/socket.io.js'></script>
		<script src='modules/conversions/conversions.js'></script>
		<style>
			
			body {
				background-color: black;
				font-family: sans-serif;
				color: white;
				margin: 0px;
			}
			body::-webkit-scrollbar {display: none;}
			#main{
				margin-left: 10px;
				margin-top: 30px;
				column-count:2;
			}
			.title{
				font-size: 150%;
				font-weight: bold;
				font-variant: small-caps;
				position: absolute;
				left: 2px;
				top: 0px;
			}
			.category{
				outline: 2px solid #00FF00;
				padding-left: 5px;
				padding-right: 5px;
				padding-top: 20px;
				
				position: fixed;
			}
			.data{font-size: 300%;}

			#cLapTime{top: 25px; left: 10px; width: 325px; height: 55px; overflow: hidden;}
			#cTripTime{top: 25px; right: 10px; width: 325px;}

			#cOdo{top: 115px; right: 10px; width: 120px; text-align: right;}
			#cLapOdo{top: 115px; left: 10px; width: 120px; text-align: right;}

			#cZeroSixty{top:205px; left: 10px; width: 160px; text-align: right;}

			#cSpeedo{top: 295px; left: 10px; width: 80px; text-align: right;}
			#cTopSpeed{bottom: 10px; left: 200px; width: 120px; text-align: right;}
			#cAvgSpeed{bottom: 10px; right: 270px; width: 120px; text-align: right;}

			#cRev{top: 295px; right: 10px; width: 140px; text-align: right;}
			#cTopRev{bottom: 10px; right: 10px; width: 140px; text-align: right;}
			#info{bottom: 10px; left: 10px; padding-bottom: 5px; padding-top: 5px; font-variant: small-caps; background-color: #222222; width: 150px; font-size: 150%; text-align: center;}
			#oemlogo{position: fixed; bottom: 100px; left: 200px; width: 300px;}
		</style>
		<!-- CONTROLLER MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="modules/mode-switcher/mode-switcher.css">
		<!-- END CONTROLLER MODULE CSS -->

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<script type="text/javascript">
			var topSpeed = 0;
			var topRev = 0;

			var zeroSixtyTimerStart = Date.now();
			var zeroSixtyTimerReady = 0;

			var lapTimerStart = Date.now();
			var lapOdoStart = "";

			var freeze = 0;

			var socket = io("<?php echo $socketIOURL; ?>");
			
			socket.on('data', function (data) {
				$rd = data;
				if (typeof $rd !== 'undefined') {
					
					if ($rd.crz.btnUp == "1"){freezeData();}
					if ($rd.crz.btnDn == "1"){clearMeters();}
					if (freeze === 0){
						if (lapOdoStart == "") {
							lapOdoStart = $rd.trip.odo;
						}
						
						/* ZERO TO SIXTY TIMER */
						$speed = Math.round($rd.spd.MPH);
						if ($speed === 0) {
							zeroSixtyTimerStart = Date.now(); //set timer
							zeroSixtyTimerReady = 1; //timer is ready

						}
						if ($speed < 60 && zeroSixtyTimerReady === 1) { // if speed is between 0 and 59
							$zeroSixtyTime = (Date.now()-zeroSixtyTimerStart)/1000;
							if ($zeroSixtyTime < 99) { // Cap zero sixty times to 99 seconds then stop.
								document.getElementById('zeroSixtyTime').textContent = $zeroSixtyTime; 
							}

						}else if($speed >= 60){ // if speed is over 60, stop timer
							zeroSixtyTimerReady = 0;  //timer is not ready until speed is 0 again
						}
						/* END ZERO TO SIXTY TIMER */
						console.log(debounce(50));
						$lapTime = (Date.now()-lapTimerStart)/1000;
						document.getElementById('lapTime').textContent = HHMMSS($lapTime)+" "+$lapTime.toString().split('.')[1];
						document.getElementById('lapOdo').textContent = roundPrec(($rd.trip.odo-lapOdoStart),3);

						document.getElementById('speedo').textContent = $speed;
						if (topSpeed < $speed) {
							topSpeed = $speed;
							document.getElementById('top-speed').textContent = topSpeed;
						}

						$rev = $rd.ngn.RPM;
						document.getElementById('rev-rpm').textContent = $rev;
						if (topRev < $rev) {
							topRev = $rev;
							document.getElementById('top-rev').textContent = topRev;
						}
						document.getElementById('avg-speed').textContent = Math.round($rd.spd.avgMPH);
						document.getElementById('odo').textContent = roundPrec($rd.trip.scrTrp,3);
						document.getElementById('trip-time').textContent = $rd.trip.timeDisp;
					}
					
					}	
			});


			function freezeData(){
				if (freeze === 0) {
					freeze = 1;
				}else if (freeze === 1){
					clearMeters();
					freeze = 0;
				}
			}
			function clearMeters(){
				lapTimerStart = Date.now();
				lapOdoStart = $rd.trip.odo;
			}
			function debounce($length){ //length in ms
				if ($start == "undefined") {
					$start = Date.now()
					return 2;
				}else{
					$currLength = Date.now()-$start;
					if ($length < $currLength) {
						return 1;
					}else{
						return 0;
					}
				}
			}
		</script>
	</head>
	<body>
		<div id="main">
			<div id="cZeroSixty" class="category">
				<div class="title">0-60</div>
				<div id="zeroSixtyTime" class="data">0</div>
			</div>
			<div id="cLapTime" class="category">
				<div class="title">LAP TIME</div>
				<div id="lapTime" class="data">0</div>
			</div>
			<div id="cLapOdo" class="category">	
				<div class="title">LAP ODO</div>
				<div id="lapOdo" class="data">0</div>
			</div>
			<div id="cSpeedo" class="category">
				<div class="title">SPD</div>
				<div id="speedo" class="data">0</div>
			</div>
			<div id="cTopSpeed" class="category">
				<div class="title">SPD MAX</div>
				<div id="top-speed" class="data">0</div>
			</div>
			<div id="cAvgSpeed" class="category">
				<div class="title">SPD AVG</div>
				<div id="avg-speed" class="data">0</div>
			</div>
			<div id="cRev" class="category">
				<div class="title">RPM</div>
				<div id="rev-rpm" class="data">0</div>
			</div>
			<div id="cTopRev" class="category">
				<div class="title">RPM MAX</div>
				<div id="top-rev" class="data">0</div>
			</div>
			<div id="cTripTime" class="category">
				<div class="title">TRIP TIME</div>
				<div id="trip-time" class="data">0</div>
			</div>
			<div id="cOdo" class="category">
				<div class="title">TRIP ODO</div>
				<div id="odo" class="data">0</div>
			</div>
			<div id="info" class="category">
				Up - Freeze<br>Down - Lap
			</div>
			<img id="oemlogo" src="img/oemlogo-white.svg">
		</div>
		<?php include('modules/mode-switcher/mode-switcher.php'); ?>
		<script type="text/javascript">
			document.getElementById('ms-titlebar').innerHTML = "<a href='#' class='ms-overlay-button' onClick=\"clearMeters();\">LAP</a><a href='#' class='ms-overlay-button' onClick=\"freezeData();\">FREEZE</a>";
		</script>
	</body>
</html>
