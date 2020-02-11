<?php
	include('main.php');
?>
<!doctype html>
<html>
	<head>
		<title>CanAx - Timer</title>
		<script src='/js/socket.io.js'></script>
		<script src='/modules/conversions/conversions.js'></script>
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
				padding-left: 20px;
				padding-right: 20px;
				padding-top: 20px;
				
				position: fixed;
			}
			.data{font-size: 300%;}

			#cLapTime{top: 25px; left: 10px; width: 325px;}
			#cTripTime{top: 25px; right: 10px; width: 325px;}

			#cOdo{top: 115px; right: 10px; width: 120px; text-align: right;}
			#cLapOdo{top: 115px; left: 10px; width: 120px; text-align: right;}

			#cZeroSixty{top:205px; left: 10px; width: 160px; text-align: right;}

			#cSpeedo{top: 295px; left: 10px; width: 80px; text-align: right;}
			#cTopSpeed{bottom: 10px; left: 200px; width: 80px; text-align: right;}
			#cAvgSpeed{bottom: 10px; right: 270px; width: 80px; text-align: right;}

			#cRev{top: 295px; right: 10px; width: 140px; text-align: right;}
			#cTopRev{bottom: 10px; right: 10px; width: 140px; text-align: right;}
			#info{bottom: 10px; left: 10px; padding-bottom: 20px; font-variant: small-caps; background-color: #222222;}
			#oemlogo{position: fixed; bottom: 100px; left: 200px; width: 300px;}
		</style>
		<!-- CONTROLLER MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/mode-switcher/mode-switcher.css">
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

			var socket = io("<?php echo $socketIOURL; ?>");
			
			socket.on('data', function (data) {
				$rd = data;
				if (lapOdoStart == "") {
					lapOdoStart = $rd.trip.odo;
				}

				$speed = Math.round($rd.spd.MPH);
				/* ZERO TO SIXTY TIMER */
				if ($speed === 0) {
					zeroSixtyTimerStart = Date.now(); //set timer
					zeroSixtyTimerReady = 1; //timer is ready

				}
				if ($speed < 60 && zeroSixtyTimerReady === 1) {
					document.getElementById('zeroSixtyTime').textContent = (Date.now()-zeroSixtyTimerStart)/1000;
				}else if($speed >= 60){
					zeroSixtyTimerReady = 0;  //timer is not ready until speed is 0 again
				}

				$lapTime = (Date.now()-lapTimerStart)/1000;
				HHMMSS
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
			});
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
			<img id="oemlogo" src="/img/oemlogo-white.svg">
		</div>
		<?php include('modules/mode-switcher/mode-switcher.php'); ?>
	</body>
</html>
