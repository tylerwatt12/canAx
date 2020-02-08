<?php
	include('main.php');
?>
<!doctype html>
<html>
	<head>
		<title>CanAx - Debug</title>
		<script src='/js/socket.io.js'></script>
		
		<style>
			
			body {
				background-color: black;
				font-family: sans-serif;
			}
			#main{
				position: fixed;
				left:0px;
				top:0px;
				width:800px;
				height:480px;
			}
			#debug{
				column-count: 2;
				top: 20px;
				position: fixed;
			}
			pre{
				position: relative;
				font-family: monospace;
				color: white;
				font-size: 150%;
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
				$table = "<table border=1 width='100%'>";
				for (let key in $rd.d) {
					$table += "<tr><td width='150px'>"+key+"</td><td width='200px'>"+$rd.d[key]+"</td></tr>";
				}
				$table += "</table>";
					document.querySelector('#one').innerHTML = $table;
					if ($rd.crz.btnUp === "1") {
						window.scrollBy(0, 100);
					}
					if ($rd.crz.btnDn === "1") {
						window.scrollBy(0, -100);
					}
			});
		</script>
	</head>
	<body>
		<div id="main">
			<div id="debug">
				<pre><code id="one"></code></pre>
			</div>
			<?php include('modules/mode-switcher/mode-switcher.php'); ?>
		</div>
	</body>
</html>
