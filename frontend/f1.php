<?php
	include('main.php');
?>
<!doctype html>
<html>
	<head>	
		<title>CanAx - F1</title>
		<script src='/js/socket.io.js'></script>
		<link rel="stylesheet" type="text/css" href="/css/template-f1.css">
		<!-- CONVERSIONS MODULE JS -->
			<script src="/modules/conversions/conversions.js"></script>
		<!-- CONTROLLER MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/mode-switcher/mode-switcher.css">
		<!-- HVAC MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/hvac-disp/hvac-disp.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<script type="text/javascript">
			var socket = io("<?php echo $socketIOURL; ?>");
			socket.on('data', function (data) {
				$rd = data;
				setTimeout(function () {
					if ($rd.ngn.RPM > 5600) { //green
						document.getElementById("shift-1").className = "show";
					}else{
						document.getElementById("shift-1").className = "hide";
					}
					if ($rd.ngn.RPM > 6000) { //red
						document.getElementById("shift-2").className = "show";
					}else{
						document.getElementById("shift-2").className = "hide";
					}
					if ($rd.ngn.RPM > 6400) { //blue
						document.getElementById("shift-3").className = "show";
						document.getElementById("shift-light").className = "blinkShiftLight";
					}else{
						document.getElementById("shift-3").className = "hide";
						document.getElementById("shift-light").className = "blinkShiftLightOff";
					}
					if ($rd.ltg.HL == "0"){ // headlights on
						document.body.className = "dayTheme";
					}else{
						document.body.className = "nightTheme";
					}
					if ($rd.ngn.RPM > 3000 && $rd.tmp.oil < 130) { //3000 130
						document.getElementById("engine-rev-protect").className = "show";
					}else{
						document.getElementById("engine-rev-protect").className = "hide";
					}
					document.getElementById('rpm').style.height = Math.round(($rd.ngn.RPM)/6700*430)+"px";
					document.getElementById('brake').style.height = Math.round(logBig($rd.pdl.brkPrs,1,100)*4.3)+"px";
					document.getElementById('boost').textContent = Math.round($rd.ngn.boost);
					document.getElementById('tbody').style.height = Math.round(($rd.ngn.tbody)*4.3)+"px";
					document.getElementById('accel').style.height = Math.round(($rd.pdl.accel)*4.3)+"px";
					document.getElementById('speedo').textContent = Math.round($rd.spd.MPH);
					document.getElementById('gear').textContent = $rd.trns.gear;
					document.getElementById('coolant').textContent = Math.round($rd.tmp.clnt);
					document.getElementById('oil').textContent = Math.round($rd.tmp.oil);
					document.getElementById('mpg').textContent = Math.round($rd.fuel.MPG);
					document.getElementById('mga').textContent = roundPrec($rd.fuel.avgMPG,1);
					document.getElementById('gph').textContent = roundPrec($rd.fuel.GPH,2);

					document.getElementById('odo').textContent = roundPrec($rd.trip.odo,1);
					document.getElementById('trip').textContent = roundPrec($rd.trip.scrTrp,1);
					document.getElementById('trtm').textContent = $rd.trip.timeDisp;
					document.getElementById('sctm').textContent = HHMMSS($rd.trip.scrTime/1000);

					document.getElementById('ms-titlebar').textContent = $rd.info.profile+" "+$rd.info.vin;
					
				}, 50)
			}); 
		</script>
		
	</head>
	<body>
		<div id="main">
			<div id="engine-rev-protect"><img src="/img/cold-engine.svg" width="80px"></div>
			<div id="rpm" class="vChart"></div>
			<div id="rpm-gauge-label" class="gaugeLabel">RPM</div>

			<div id="tbody" class="vChart"></div>
			<div id="tbody-label" class="gaugeLabel">TB</div>

			<div id="accel" class="vChart"></div>
			<div id="accel-label" class="gaugeLabel">THR</div>

			<div id="brake" class="vChart"></div>
			<div id="brake-label" class="gaugeLabel">BRK</div>

			<div id="speedo-container">
				<a id="speedo"></a>
				<div id="speedo-label">SPD</div>
			</div>
				
			<div id="gear-container">
				<a id="gear"></a>
				<div id="gear-label">GEAR</div>
			</div>
			
			<div id="oil-container">
				<a id="oil"></a>
				<div id="oil-label">OIL</div>
			</div>

			<div id="coolant-container">
				<a id="coolant"></a>
				<div id="coolant-label">COOL</div>
			</div>

			<div id="boost-container">
				<a id="boost"></a>
				<div id="boost-label">BOOST</div>
			</div>
				
			<div id="debug-left-top-container" class="debug-text">
				Odo:<br>
				<b id="odo"></b><br>
				Lap:<br>
				<b id="trip"></b>
			</div>
			<div id="debug-right-top-container" class="debug-text">
				Trip:<br>
				<b id="trtm"></b><br>
				Lap:<br>
				<b id="sctm"></b>
			</div>
			<div id="debug-left-bottom-container" class="debug-text">
				MPG: <b id="mpg">0</b><br>
				MGA: <b id="mga">0</b><br>
				GPH: <b id="gph">0</b>
			</div>
			<div id="shift-light">
				<div id="shift-1">
					<span class="dot green-dot"></span>
					<span class="dot green-dot"></span>
					<span class="dot green-dot"></span>
					<span class="dot green-dot"></span>
					<span class="dot green-dot"></span>
				</div>
				<div id="shift-2">
					<span class="dot red-dot"></span>
					<span class="dot red-dot"></span>
					<span class="dot red-dot"></span>
					<span class="dot red-dot"></span>
					<span class="dot red-dot"></span>
				</div>
				<div id="shift-3">
					<span class="dot blue-dot"></span>
					<span class="dot blue-dot"></span>
					<span class="dot blue-dot"></span>
					<span class="dot blue-dot"></span>
					<span class="dot blue-dot"></span>
				</div>
			</div>	
		</div>
		<?php include('modules/mode-switcher/mode-switcher.php'); ?>
		<?php include('modules/hvac-disp/hvac-disp.php'); ?>
	</body>
</html>