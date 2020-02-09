<?php
	include('main.php');
?>
<!doctype html>
<html>
	<head>
		<title>CanAx - Timer</title>
		<script src='/js/socket.io.js'></script>
		
		<style>
			
			body {
				background-color: black;
				font-family: sans-serif;
				color: white;
				margin: 0px;
			}
			body::-webkit-scrollbar {
				display: none;
			}
			#main{
				margin:0px;
			}
		</style>
		<!-- CONTROLLER MODULE CSS -->
			<link rel="stylesheet" type="text/css" href="/modules/mode-switcher/mode-switcher.css">
		<!-- END CONTROLLER MODULE CSS -->

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<script type="text/javascript">
			var socket = io("<?php echo $socketIOURL; ?>");
			socket.on('data', function (data) {
				$rd = data;
			});
		</script>
	</head>
	<body>
		<div id="main">
			
		</div>
		<?php include('modules/mode-switcher/mode-switcher.php'); ?>
	</body>
</html>
