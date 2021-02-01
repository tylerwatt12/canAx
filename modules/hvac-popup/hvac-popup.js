var $hvacActTimer = 0;
var $hvacActTimerStarted = 0;
var $oldAhPage = 0;
function hvacPopup () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			/* This section makes the HVAC page pop up for 3 seconds when activity is detected on the climate control panel*/
			if($rd.hvac.dispAct === "1"){
				if ($hvacActTimerStarted === 0) { // if first time starting timer
					$oldAhPage = $ahPage; //backup previous page
				}
				$hvacActTimer = Date.now(); // start/reset timer
				$hvacActTimerStarted = 1; //set timer indicator
				$ahPage = 3; // goto HVAC Page
			}
				
			if($rd.hvac.dispAct === "0" && $hvacActTimerStarted === 1){
				var $hvacTimeElapsed = Date.now()-$hvacActTimer;
				if($hvacTimeElapsed > 3000){ // HVAC menu stays up for 3 seconds
					$ahPage = $oldAhPage; // return to old page
					$hvacActTimerStarted = 0; // stop running this loop to calculate the 
				}
			}
			/* End climate control popup panel */

			/* This section updates the HVAC page with live data from CAN bus */
			if ($ahPage === 3) { // If page is active, draw contents
				if($rd.hvac.system === "off"){
					document.getElementById('hvac-data-h-fullauto').className = "hvac-h-systemoff";
					document.getElementById('hvac-data-c-zone1').textContent = "";
					document.getElementById('hvac-data-c-zone2').textContent = "";
					document.getElementById("hvac-data-c-sync").className = "invisible";
					document.getElementById("hvac-data-v-vent").className = "invisible";
					document.getElementById("hvac-data-a-ac").className = "invisible";
					document.getElementById("hvac-data-rc-rc").className = "invisible";
					document.getElementById("hvac-data-b-rdfrst").className = "invisible";
					document.getElementById("hvac-data-fan").className = "invisible";
					var x;
					for (x = 1; x <= 7; x++) {
						document.getElementById("hvac-data-fan"+x).className = "invisible";
					}

				}else if($rd.hvac.system === "on"){
					//hvac-data-h-fullauto
					switch ($rd.hvac.auto){
						case "noAuto":
							document.getElementById("hvac-data-h-fullauto").className = "invisible";
						break;
						case "auto":
							document.getElementById("hvac-data-h-fullauto").className = "hvac-h-auto";
						break;
						case "fullAuto":
							document.getElementById("hvac-data-h-fullauto").className = "hvac-h-fullauto";
						break;
					}
					//hvac-data-c-zone1
					document.getElementById('hvac-data-c-zone1').textContent = $rd.hvac.tmp;
					//hvac-data-c-zone2
					document.getElementById('hvac-data-c-zone2').textContent = $rd.hvac.tmp; //Until a zone 2 is found, copy zone 1 (Need WRX/STi Limited)

					//hvac-data-c-sync
					document.getElementById("hvac-data-c-sync").className = "visible"; //Always on until I find CAN info

					//hvac-data-v-vent
					switch ($rd.hvac.mode){
						case "face":
							document.getElementById("hvac-data-v-vent").className = "hvac-v-h";
						break;
						case "faceFeet":
							document.getElementById("hvac-data-v-vent").className = "hvac-v-b";
						break;
						case "feet":
							document.getElementById("hvac-data-v-vent").className = "hvac-v-f";
						break;
						case "feetWindshield":
							document.getElementById("hvac-data-v-vent").className = "hvac-v-wf";
						break;
						case "windshield":
							document.getElementById("hvac-data-v-vent").className = "hvac-v-w";
						break;
					}

					//hvac-data-a-ac
					if($rd.hvac.AC === "1"){
						document.getElementById("hvac-data-a-ac").className = "visible";
					}else{
						document.getElementById("hvac-data-a-ac").className = "invisible";
					}

					//hvac-data-rc-rc
					switch ($rd.hvac.rcrc){
						case "null":
							document.getElementById("hvac-data-rc-rc").className = "invisible";
						break;
						case "recirc":
							document.getElementById("hvac-data-rc-rc").className = "hvac-rc-rc";
						break;
						case "fresh":
							document.getElementById("hvac-data-rc-rc").className = "hvac-rc-fa";
						break;
					}

					//hvac-data-b-rdfrst
					if($rd.hvac.rrDfst === "1"){
						document.getElementById("hvac-data-b-rdfrst").className = "visible";
					}else{
						document.getElementById("hvac-data-b-rdfrst").className = "invisible";
					}

					//hvac-data-fan
					document.getElementById("hvac-data-fan").className = "visible";

					//hvac-data-fan levels
					var i;
					for (i = 1; i <= $rd.hvac.fanLevel; i++) {
						document.getElementById("hvac-data-fan"+i).className = "visible";
					}
					var x;
					for (x = $rd.hvac.fanLevel+1; x <= 7; x++) {
						document.getElementById("hvac-data-fan"+x).className = "invisible";
					}
				}
			}
		}
		hvacPopup();
	}, 100);
}
hvacPopup();