var $showMenu =  0;
$msCurrentSetting = 0;
$page = 0;
$buttonAction = {};

document.body.onkeydown = function(e){ //keyboard function
    switch(e.keyCode){
    	case 38:
    		buttonAction("up");
    	break;
    	case 40:
    		buttonAction("down");
    	break;
    	case 77:
	    	if ($showMenu === 0){
				buttonAction("menu");
			}else if($showMenu === 1){
				buttonAction("go");
			}
    	break;
    }
};

function modeSwitcher () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			if($rd.crz.active != "1"){ // if cruise control is not on, allow use of buttons
				if ($rd.crz.btnUp == "1"){buttonAction("up");} 
				if ($rd.crz.btnDn == "1"){buttonAction("down");} 
				if ($rd.crz.btnCncl == "1" && $showMenu === 0){
					buttonAction("menu");
				}else if($rd.crz.btnCncl == "1" && $showMenu === 1){
					buttonAction("go");
				}
			}
		}
			
			if ($buttonAction.menu === 1 && $showMenu === 0) {
				$showMenu = 1;// If button was pressed, set showMenu var
				buttonAction("menu",1); //reset var
			}
			if ($showMenu === 1){// if user activated menu via touch or button (constant loop)
				document.getElementById('mode-switcher').style.visibility = "visible"; // Show UI
				if ($buttonAction.up === 1) { // If user hits + button
					msCurrentSettingCtl("+");
					buttonAction("up",1); //reset var
				}
				if ($buttonAction.down === 1) { // If user hits - button
					msCurrentSettingCtl("-");
					buttonAction("down",1); //reset var
				}
				if($buttonAction.go === 1){ // If user hits Go button
					document.getElementById('mode-switcher').style.visibility = "hidden"; //close menu
					buttonAction("go",1); //reset var
					document.getElementById('ms-menuItem'+$page).click(); // open link specified in onClick or href
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






























