<?php
	include('main.php');
?>
<!doctype html>
<html>
	<head>
		<title>CanAx - Modern</title>
		<script src='/js/socket.io.js'></script>
		<link rel="stylesheet" type="text/css" href="/css/template-modern.css">
		<!-- CONTROLLER MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/mode-switcher/mode-switcher.css">
		<!-- END CONTROLLER MODULE CSS -->
		<!-- HVAC POPUP MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/hvac-popup/hvac-popup.css">
		<!-- END HVAC POPUP MODULE CSS -->
		<!-- TEMP MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/modern-temp/modern-temp.css">
		<!-- END TEMP MODULE CSS -->
		<!-- TRIP MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/modern-trip/modern-trip.css">
		<!-- END TRIP MODULE CSS -->
		<!-- FUEL MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/modern-fuel/modern-fuel.css">
		<!-- END FUEL MODULE CSS -->
		<!-- GAUGE MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/modern-gauge/modern-gauge.css">
		<!-- END GAUGE MODULE CSS -->
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript">
			var socket = io("<?php echo $socketIOURL; ?>");
			function idiotLightHandler($input,$elementId,$isTrue,$isFalse,$testMode = "FALSE"){ //only updates dom when changes are needed.
				var $set = "hidden";
				if ($input === $isTrue) {
					$set = "visible";
				}else if ($input === $isFalse){
					$set = "hidden";
				}

				if ($testMode === "TRUE"){
					$set = "visible";
				}
				const element = document.getElementById($elementId);
				if (element.classList.contains($set)){
					//already set, ignore
				}else{
					if ($set === "hidden") {
						element.classList.add("invisible");
						element.classList.remove("visible");
					}else if($set === "visible"){
						element.classList.add("visible");
						element.classList.remove("invisible");
					}
				}
			}
			function gaugeDraw($divGauge,$divData,$inData,$min,$max,$yellow,$orange,$red){
				//Calculate width
				var $per;
				$per = ($inData-$min)/($max-$min)*100;
				if ($per < 0) {
					$per = 0;
				}else if($per > 100){
					$per = 100;
				}
				$per = Math.round($per)+"%";
				//calculate color
				var $color;
				if ($inData < $yellow) { //set gauge color severity
					$color = "rgb(34, 85, 255)";
				}else if($inData >= $yellow && $inData < $orange){
					$color = "rgb(170, 170, 0)";
				}else if($inData >= $orange && $inData < $red){
					$color = "rgb(181, 85, 21)";
				}else if($inData >= $red){
					$color = "rgb(184, 19, 17)";
				}
				const eDivGauge = document.getElementById($divGauge);
				if (eDivGauge.style.backgroundColor === $color){
					//already set, ignore
				}else{
					document.getElementById($divGauge).style.backgroundColor = $color;
				}
				if (eDivGauge.style.width === $per){
					//already set, ignore
				}else{
					document.getElementById($divGauge).style.width = $per;
				}
				const eDivData = document.getElementById($divData);
				if (eDivData.textContent === $inData.toString()){
					//already set, ignore
				}else{
					document.getElementById($divData).textContent = $inData;
				}
			}
			var $speedoFlashMode = 0;
			var $redlineColorMode = 0;
			var $debug = 0; //DELETE ME
			socket.on('data', function (data) {	
				$rd = data;
				$deg = Math.round(($rd.ngn.RPM)/6700*170)-35;
				document.getElementById('needle').style.transform = "rotate("+ $deg +"deg)";
				document.getElementById('mph').textContent = Math.round($rd.spd.MPH);
				document.getElementById('odometer-reading').textContent = Math.floor($rd.trip.odo);
				document.getElementById('gear').textContent = $rd.trns.gear;
				document.getElementById('temp').textContent = $rd.tmp.amb+"° F";
				//	REAR CHASSIS ILLUMINATION STATUS LAMPS
				idiotLightHandler($rd.tSnl.Left,"ts-l","1","0");
				idiotLightHandler($rd.tSnl.Right,"ts-r","1","0");
				//	HEADLIGHT STATUS LAMPS
				idiotLightHandler($rd.ltg.HB,"i-hb","1","0");
				idiotLightHandler($rd.ltg.DRL,"i-runlts","1","0");
				idiotLightHandler($rd.ltg.fog,"i-fog","1","0");
				//	ELECTRONIC STABILITY CONTROL STATUS LAMPS
				idiotLightHandler($rd.esc.VDCAct,"i-trac","1","0");
				idiotLightHandler($rd.esc.FullAct,"i-escoff","0","1");
				//	PARKING BRAKE WARNING LAMP
				idiotLightHandler($rd.pdl.PBRK,"i-pbrake","1","0");
				//	CRUISE CONTROL ACTIVE LAMP
				idiotLightHandler($rd.crz.active,"i-cruiseon","1","0");
				// DRIVER SAFETY SEATBELT WARNING LAMP
				idiotLightHandler($rd.sfty.StbltDrvr,"i-seatbelt","1","0");
				//	COMBINED DOOR AJAR ALERT LAMP
				var $doorAjar = 0;
				if ($rd.dAjr.TR === "1" || $rd.dAjr.DF === "1" || $rd.dAjr.PF === "1" || $rd.dAjr.PR === "1" || $rd.dAjr.DR === "1" ) {
					$doorAjar = 1;
				}
				idiotLightHandler($doorAjar,"i-ajar","1","0");
				//	FLASH PARKING BRAKE AND SEATBELT LIGHTS WHEN MOVING
				if ($rd.spd.MPH > 14 && $speedoFlashMode != 1) {
					document.getElementById("i-pbrake").classList.add("flash");
					document.getElementById("i-seatbelt").classList.add("flash");
					$speedoFlashMode = 1;
				}else if ($rd.spd.MPH < 14 && $speedoFlashMode != 2) {
					document.getElementById("i-pbrake").classList.remove("flash");
					document.getElementById("i-seatbelt").classList.remove("flash");
					$speedoFlashMode = 2;
				}
				//	TURN SPEEDOMETER RED NEAR REDLINE
				if ($rd.ngn.RPM > 6500 && $redlineColorMode != 1){ //and is not already red
					document.getElementById("ring").classList.add("turnRed");
					document.getElementById("needle").classList.add("turnRed");
					$redlineColorMode = 1;
				}else if($rd.ngn.RPM < 6500 && $redlineColorMode != 0){
					document.getElementById("ring").classList.remove("turnRed");
					document.getElementById("needle").classList.remove("turnRed");

					$redlineColorMode = 0;
				}
				/*  ACTION HANDLER DOM EVENTS  */
				if ($ahPage === 0) {
						idiotLightHandler($rd.esc.isBrk,"car-braking","1","0");
				}
				/*  END ACTION HANDLER DOM EVENTS  */
			});
		</script>
	</head>
	<body>
		<div id="main">
			<div id="info-screen">
				<div id="info-menu">
					<img src="/img/modern/d-alert.svg" class="i-icon illuminate" id="d-0" onClick="$ahPage = 0;">
					<img src="/img/modern/d-temp.svg" class="i-icon illuminate" id="d-1" onClick="$ahPage = 1;">
					<img src="/img/modern/d-trip.svg" class="i-icon illuminate" id="d-2" onClick="$ahPage = 2;">
					<img src="/img/modern/d-hvac.svg" class="i-icon illuminate" id="d-3" onClick="$ahPage = 3;">
					<img src="/img/modern/d-fuel.svg" class="i-icon illuminate" id="d-4" onClick="$ahPage = 4;">
					<img src="/img/modern/d-gauge.svg" class="i-icon illuminate" id="d-5" onClick="$ahPage = 5;">
				</div>
				<div id="info-hr-h">
					<img src="/img/modern/dash-hilighter-h.svg">
				</div>
				<!-- PAGE CONTENTS -->
				<div id="page-0" class="info-contents noDisplay">
					<img src="/img/modern/dash-road.svg" id="roadlines">
					<div id="car-container">
						<img src="/img/modern/rear-braking.png" id="car-braking" class="car">
						<img src="/img/modern/rear.png" id="car-idle" class="car">
					</div>
				</div>
				<div id="page-1" class="info-contents noDisplay">
					<div id="tempPage">
						<div class="tempCategory">
							<div class="tempLabel">Coolant</div>
							<div class="tempGaugeProgress" id="tempDialCoolant"></div>
							<div class="tempGaugeOutline">
								<div class="tempUnit">°F</div>
								<div class="tempData" id="tempCoolant">-</div>
							</div>
						</div>
						<div class="tempCategory">
							<div class="tempLabel">Oil</div>
							<div class="tempGaugeProgress" id="tempDialOil"></div>
							<div class="tempGaugeOutline">
								<div class="tempUnit">°F</div>
								<div class="tempData" id="tempOil">-</div>
							</div>
						</div>
						<div class="tempCategory">
							<div class="tempLabel">Ambient</div>
							<div class="tempGaugeProgress" id="tempDialAmbient"></div>
							<div class="tempGaugeOutline">
								<div class="tempUnit">°F</div>							
								<div class="tempData" id="tempAmbient">-</div>
							</div>
						</div>
						<div class="tempCategory">
							<div class="tempLabel">CPU</div>
							<div class="tempGaugeProgress" id="tempDialCPU"></div>
							<div class="tempGaugeOutline">
								<div class="tempUnit">°C</div>
								<div class="tempData" id="tempCPU">-</div>
							</div>
						</div>
					</div>
				</div>
				<div id="page-2" class="info-contents noDisplay">
					<div id="tripPage">
						<div class="tripLine">
							<div class="tripLabel">Trip Odometer</div>
							<div class="tripOutline">
								<div class="tripData" id="tripOdometer">-</div>
							</div>
						</div>
						<div class="tripLine">
							<div class="tripLabel">Trip MPG</div>
							<div class="tripOutline">
								<div class="tripData" id="tripMPG">-</div>
							</div>
						</div>
						<div class="tripLine">
							<div class="tripLabel">Trip MPH</div>
							<div class="tripOutline">
								<div class="tripData" id="tripMPH">-</div>
							</div>
						</div>
						<div class="tripLine">
							<div class="tripLabel">Trip Timer</div>
							<div class="tripOutline">
								<div class="tripData" id="tripTimer">-</div>
							</div>
						</div>
						<div class="tripLine">
							<div class="tripLabel">0-60 Timer</div>
							<div class="tripOutline">
								<div class="tripData" id="tripZeroSixty">-</div>
							</div>
						</div>
					</div>
				</div>
				<div id="page-3" class="info-contents noDisplay">
					<div id="hvacClimateControl">
						<div class="hvacSectionFull">
							<img style="height: 40px;" id="hvac-data-h-fullauto" class="invisible" src="/img/modern/h-fullauto.svg">
						</div>
						<div class="hvacSectionFull">
							<div class="hvacSectionHalf hvacClimateText" id="hvac-data-c-zone1"></div>
							<div class="hvacSectionHalf hvacClimateText" id="hvac-data-c-zone2"></div>
						</div>
						<div class="hvacSectionFull">
							<div class="hvacSectionHalf">
								<img style="height: 74px;" id="hvac-data-v-vent" class="invisible" src="/img/modern/v-wf.svg">
							</div>
							<div class="hvacSectionHalf">
								<div class="hvacSectionVHalf">
									<img style="height: 30px;" id="hvac-data-c-sync" class="invisible" src="/img/modern/c-sync.svg">
								</div>
								<div class="hvacSectionVHalf">
									<img style="height: 40px;" id="hvac-data-a-ac" class="invisible" src="/img/modern/a-ac.svg">
								</div>
							</div>
						</div>
						<div class="hvacSectionFull">
							<div class="hvacSectionHalf">
								<img style="height: 50px;" id="hvac-data-rc-rc" class="invisible" src="/img/modern/rc-rc.svg">
							</div>
							<div class="hvacSectionHalf">
								<img style="height: 70px;" id="hvac-data-b-rdfrst" class="invisible" src="/img/modern/b-rdfst.svg">
							</div>
						</div>
						<div class="hvacSectionFull">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan" src="/img/modern/x-fan.svg">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan1" src="/img/modern/x-1.svg">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan2" src="/img/modern/x-2-6.svg">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan3" src="/img/modern/x-2-6.svg">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan4" src="/img/modern/x-2-6.svg">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan5" src="/img/modern/x-2-6.svg">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan6" src="/img/modern/x-2-6.svg">
							<img style="height: 30px;" class="invisible" id="hvac-data-fan7" src="/img/modern/x-7.svg">
						</div>
					</div>
				</div>
				<div id="page-4" class="info-contents noDisplay">
					<div id="fuelPage">
						<div class="fuelLine">
							<div class="fuelLabel">MILES/GAL</div>
							<div class="fuelOutline">
								<div class="fuelData" id="fuelMPG">-</div>
							</div>
						</div>
						<div class="fuelLine">
							<div class="fuelLabel">MPG/TRIP</div>
							<div class="fuelOutline">
								<div class="fuelData" id="fuelMPGA">-</div>
							</div>
						</div>
						<div class="fuelLine">
							<div class="fuelLabel">GAL/HR</div>
							<div class="fuelOutline">
								<div class="fuelData" id="fuelGPH">-</div>
							</div>
						</div>
					</div>
				</div>
				<div id="page-5" class="info-contents noDisplay">
					<div id="gaugePage">
						<div class="gaugeCategory">
							<div class="gaugeLabel">Boost</div>
							<div class="gaugeProgress" id="gaugeDialBoost"></div>
							<div class="gaugeOutline">
								<div class="gaugeUnit">PSI</div>
								<div class="gaugeData" id="gaugeBoost">-</div>
							</div>
						</div>
						<div class="gaugeCategory">
							<div class="gaugeLabel">Accel</div>
							<div class="gaugeProgress" id="gaugeDialAccel"></div>
							<div class="gaugeOutline">
								<div class="gaugeUnit">%</div>
								<div class="gaugeData" id="gaugeAccel">-</div>
							</div>
						</div>
						<div class="gaugeCategory">
							<div class="gaugeLabel">Tbody</div>
							<div class="gaugeProgress" id="gaugeDialTbody"></div>
							<div class="gaugeOutline">
								<div class="gaugeUnit">%</div>
								<div class="gaugeData" id="gaugeTbody">-</div>
							</div>
						</div>
						<div class="gaugeCategory">
							<div class="gaugeLabel">Brake</div>
							<div class="gaugeProgress" id="gaugeDialBrake"></div>
							<div class="gaugeOutline">
								<div class="gaugeUnit">%</div>
								<div class="gaugeData" id="gaugeBrake">-</div>
							</div>
						</div>
						<div class="gaugeCategory">
							<div class="gaugeLabel">SteerAngle</div>
							<div class="gaugeProgress" id="gaugeDialSteerAngle"></div>
							<div class="gaugeOutline">
								<div class="gaugeUnit">°</div>
								<div class="gaugeData" id="gaugeSteerAngle">-</div>
							</div>
						</div>
						<div class="gaugeCategory">
							<div class="gaugeLabel">SteerTq</div>
							<div class="gaugeProgress" id="gaugeDialSteerTq"></div>
							<div class="gaugeOutline">
								<div class="gaugeUnit">Nm</div>
								<div class="gaugeData" id="gaugeSteerTq">-</div>
							</div>
						</div>
					</div>
				</div>
				<!-- END PAGE CONTENTS -->
				<div id="info-hr">
					<img src="/img/modern/dash-hilighter.svg">
				</div>
				<div id="odometer-container">
					<a class="objet" id="odometer-label" onmouseenter="$debug = 1;" onmouseleave="$debug = 0;">ODO</a>
					<a class="objet" id="odometer-reading">0</a>
				</div>
			</div>
			<div id="dial-container">
				<div id="dial">
					<img  src="/img/modern/needle.svg" id="needle">
					<div id="lamp-container">
						<img src="/img/modern/i-runlts.svg" class="lamp" id="i-runlts">
						<img src="/img/modern/i-fog.svg" class="lamp" id="i-fog">
						<img src="/img/modern/i-hb.svg" class="lamp" id="i-hb">
						<img src="/img/modern/i-cruiseon.svg" class="lamp" id="i-cruiseon">
						<img src="/img/modern/i-ajar.svg" class="lamp" id="i-ajar">
						<img src="/img/modern/i-seatbelt.svg" class="lamp" id="i-seatbelt">
						<img src="/img/modern/i-trac.svg" class="lamp" id="i-trac">
						<img src="/img/modern/i-escoff.svg" class="lamp" id="i-escoff">
						<img src="/img/modern/i-pbrake.svg" class="lamp" id="i-pbrake">
						<img src="/img/modern/ts.svg" class="lamp" id="ts-l">
						<img src="/img/modern/ts.svg" class="lamp" id="ts-r">
					</div>
					
					<img  src="/img/modern/speedo-circle2.svg" id="ring">
					<div id="circle"><!-- Draws black circle --></div>
					<div id="mph-container">
						<a id="mph" class="objet textShadow">55</a><br>
						<a id="mph-label" class="objet textShadow">MPH</a>
					</div>
					<div id="gear-container">
						<a id="gear" class="objet textShadow">5</a>
					</div>
					<div id="temp-container">
						<a class="objet" id="temp">-</a>
					</div>
					<div id="time-container">
						<a class="objet" id="time">12:00AM</a>
					</div>
				</div>
			</div>
			<img src="/img/modern/speedo-footer.svg" id="screen-footer">
		</div>
		<script>
			function updateClock() {
				var date = new Date();
				var hours = date.getHours();
				var minutes = date.getMinutes();
				var ampm = hours >= 12 ? 'PM' : 'AM';
				hours = hours % 12;
				hours = hours ? hours : 12; // the hour '0' should be '12'
				minutes = minutes < 10 ? '0'+minutes : minutes;
				var strTime = hours + ':' + minutes + ampm;
				document.getElementById('time').innerHTML = strTime;
				setTimeout(updateClock, 1000);
			}
			updateClock();
		</script>
		<?php include('modules/mode-switcher/mode-switcher.php'); ?>
		<?php include('modules/action-handler/action-handler.php'); ?>
		<?php include('modules/hvac-popup/hvac-popup.php'); ?>
		<?php include('modules/modern-temp/modern-temp.php'); ?>
		<?php include('modules/modern-trip/modern-trip.php'); ?>
		<?php include('modules/modern-fuel/modern-fuel.php'); ?>
		<?php include('modules/modern-gauge/modern-gauge.php'); ?>
	</body>
</html>