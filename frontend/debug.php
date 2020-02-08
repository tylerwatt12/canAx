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
				color: white;
			}
			#main{
				position: fixed;
				left:0px;
				top:0px;
				width:800px;
				height:480px;
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
				for (var key in $rd) { //iterate through each var group
					titleDiv = key+"-title";
					titleName = key.toUpperCase();
					dataDiv = key+"-data";
					if(document.getElementById(key)){ // if div for object already exists
						if (document.getElementById(dataDiv).style.display != "none") { // only if DIV is visible
							updateDebugData(dataDiv,$rd[key]); //update div data
						}
					}else{ //if not, create DIV
						var div = document.createElement('div');
						div.id = key;
						
						var title = document.createElement('h1');
						title.id = titleDiv;
						title.innerHTML = titleName;
						title.setAttribute("onClick", "toggleDisplay('"+dataDiv+"')");
						title.className = "debugTitle";
						div.appendChild(title); // attach title to div

						var data = document.createElement('a');
						data.id = dataDiv;
						data.style.display = "none";
						title.className = "debugData";
						div.appendChild(data); // attach data to div
						
						document.getElementById("main").appendChild(div); // attach div to main page div
					}
				}
			});

			function toggleDisplay($divID){
				if (document.getElementById($divID).style.display === "none") {
					document.getElementById($divID).style.display = "inline";
				}else{
					document.getElementById($divID).style.display = "none";
				}
			}
			function updateDebugData($div,$data){
				document.getElementById($div).innerHTML = JSON.stringify($data,null,'<br>'); //update div data
			}

		</script>
	</head>
	<body>
		<div id="main">
			<?php include('modules/mode-switcher/mode-switcher.php'); ?>
		</div>
	</body>
</html>
