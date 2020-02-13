		<?php
			# Relies on visudo edits
			# www-data ALL = NOPASSWD: /sbin/shutdown, /sbin/reboot, /bin/sed, /usr/bin/candump, /bin/kill
			$redirect = NULL;
			if(@$_GET['cmd'] === "shutdown"){

				$msg = "Shutting down system";
				$cmd = 'sudo /sbin/shutdown -h now';
				$redirect = $_SERVER['HTTP_REFERER'];

			}elseif($_GET['cmd'] === "reboot"){

				$msg = "Rebooting system";
				$cmd = 'sudo /sbin/reboot';
				$redirectTimer = 25;
				$redirect = $_SERVER['HTTP_REFERER'];

			}elseif($_GET['cmd'] === "can"){ // switch data modes

				$ex = file_get_contents('/etc/rc.local');
				$redirectTimer = 25;
				$redirect = $_SERVER['HTTP_REFERER'];
				if (strpos($ex, "#sudo modprobe")) { // if file currently is using can0
					$msg = "Using VCAN0 log playback";
					$cmd = "sudo sed -i -e 's/ #sudo modprobe/ sudo modprobe/g' -e 's/ #sudo ip link/ sudo ip link/g' -e 's/ #sudo canplayer/ sudo canplayer/g' /etc/rc.local && sudo /sbin/reboot";
					
				}elseif(strpos($ex, "sudo modprobe")){ // if file currently is using vcan0
					$msg = "Using CAN0 PiCAN module";
					$cmd = "sudo sed -i -e 's/ sudo modprobe/ #sudo modprobe/g' -e 's/ sudo ip link/ #sudo ip link/g' -e 's/ sudo canplayer/ #sudo canplayer/g' /etc/rc.local && sudo /sbin/reboot";
				}

			}elseif($_GET['cmd'] === "flip"){ // emulated data mode

				$ex = file_get_contents('/boot/config.txt');
				$redirectTimer = 25;
				$redirect = $_SERVER['HTTP_REFERER'];
				if (strpos($ex, "display_rotate=0x10000")) { // if display is rotated
					$msg = "Disabling display mirror";
					$cmd = "sudo sed -i 's/display_rotate=0x10000/display_rotate=0/g' /boot/config.txt && sudo /sbin/reboot";
				}elseif(strpos($ex, "display_rotate=0")){ // if display is not rotated
					$msg = "Enabling display mirror (HUD)";
					$cmd = "sudo sed -i 's/display_rotate=0/display_rotate=0x10000/g' /boot/config.txt && sudo /sbin/reboot";
				}

			}elseif($_GET['cmd'] === "rec"){
				$msg = "Recording to /var/www/html/logs/".time().".log<br>Returning";
				shell_exec("sudo candump -l any > /dev/null 2>&1 &");
				$cmd = "Echo Recording";
				$redirectTimer = 1;
				$redirect = $_SERVER['HTTP_REFERER'];
				//to stop
				//$cmd = "ps aux | grep 'sudo candump -l any'";
				//$cmd = "sudo kill 1739";
			}elseif($_GET['cmd'] === "mode"){
				$src = parse_url($_SERVER['HTTP_REFERER']);
				$redirectTimer = 1;
				$redirect = "?page="; 
				switch ($src["query"]){
					case 'page=f1':
						$redirect .= "modern";
						break;
					case 'page=modern':
						$redirect .= "debug";
						break;
					case 'page=debug':
						$redirect .= "f1";
						break;
					default:
						$redirect .= "f1";
						break;
				}
				$msg = "Redirecting: ".$redirect;
				$cmd = "";
			}else{
				$msg = "Executing Command";
				$cmd = 'echo No Options Specified';
				
			}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<style type="text/css">
			body{
				background-color: #000;
				color: #FFF;
				font-family: sans-serif;
				text-align: center;
			}
			a{
				color: #FFF;
				text-decoration: none;
			}
		</style>
		<?php
			if ($redirect) {
				echo "<meta http-equiv='refresh' content='".$redirectTimer.";url=".$redirect."' />";
			}
		?>
	</head>
	<body>
		<h2><a href="/">Go back</a></h2>
		<?php
			echo "<h1>".$msg."</h1>";
			ob_flush();
			flush();
			//sleep(0.1);
			system($cmd);
		?>
		<h2>Command complete</h2>
	</body>
</html>