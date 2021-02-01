var $showMenu =  0;
$msCurrentSetting = 0;
$page = 0;
$buttonAction = {};
function modeSwitcher () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			if($rd.crz.active != "1"){ // if cruise control is not on, allow use of buttons
				if ($showMenu === 1) {
					document.getElementById('mode-switcher').style.visibility = "visible"; // Show UI
					if ($rd.crz.btnUp == "1"){
						msCurrentSettingCtl("+");
					} 
					if ($rd.crz.btnDn == "1"){
						msCurrentSettingCtl("-");
					}
					if($rd.crz.btnCncl == "1"){
						msCurrentSettingCtl("go");
					}

					if ($msCurrentSetting > -1 && $msCurrentSetting < $msSettingMaxOption) { // Default value -1, means no options expanded.
						// Change size of selected menu item
						document.getElementById('ms-menuItem'+$msCurrentSetting).classList.toggle("ms-menuItem-large",true); // expand active menu option from the scroll wheel
					}
					for (var i = 0; i < $msSettingMaxOption; i++) { // shrink all other inactive menu options.
						if (i !== parseInt($msCurrentSetting)) { 
							document.getElementById('ms-menuItem'+i).classList.remove("ms-menuItem-large");
						}
					}
					$page = parseInt($msCurrentSetting); // set page destination key

				}else if ($showMenu === 0){
					if ($rd.crz.btnCncl == "1"){
						$showMenu = 1;
					}
				}
				 



			}
		}
		modeSwitcher();
	}, 100);
}
modeSwitcher();

function msCurrentSettingCtl($var){
	if ($var === "+" && $msCurrentSetting < $msSettingMaxOption-1) { // if current position is less than the max setting option
		$msCurrentSetting++; // then increase menu position
	}else if ($var === "-" && $msCurrentSetting > 0){
		$msCurrentSetting--;
	}else if($var === "go"){
		document.getElementById('mode-switcher').style.visibility = "hidden"; //close menu
		document.getElementById('ms-menuItem'+$page).click(); // open link specified in onClick or href
	}
	
}


function buttonAction($action,$reset=0){
	if ($reset === 1) {
		$buttonAction[$action] = 0;
	}else{
		if ($buttonAction[$action] === 0 || $buttonAction[$action] === undefined) {
			$buttonAction[$action] = 1;
		}else if($buttonAction[$action] === 1){
			$buttonAction[$action] = 0;
		}
	}
}






























