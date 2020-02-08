<?php
	include('main.php');
?>
<!doctype html>
<html>
	<head>
		<script src='/js/socket.io.js'></script>
		<link rel="stylesheet" type="text/css" href="/css/template-modern.css">
		<!-- CONTROLLER MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/mode-switcher/mode-switcher.css">
		<!-- END CONTROLLER MODULE CSS -->
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript">
			var socket = io("<?php echo $socketIOURL; ?>");
			socket.on('data', function (data) {
				$rd = data;
				$deg = ($rd.ngn.RPM)/6700*196;
				document.getElementById('rpm-gauge').style.transform = "rotate("+ $deg +"deg)";
				document.getElementById('speedo').textContent = $rd.spd.MPH;
			}); 

		</script>
	</head>
	<body>
		<div id="main">
			<!-- start needle at -112.5deg, end at 90deg total travel of ~200 -->
			<img id="piivate-logo" src="/img/logo-1.png">
			<div id="rpm-container">
					<img id="rpm-gauge" src="/img/needle.svg">
				</div>
			<div id="gauge-container">
				
				<div id="speedo-container">
					<a id="speedo">0</a>
				</div>
				<!-- img id="coolant-temp" src="/img/bottom-left.svg">
				<img id="oil-temp" src="/img/top-left.svg"-->
			</div>
			<?php include('modules/mode-switcher/mode-switcher.php'); ?>
		</div>
	</body>
</html>