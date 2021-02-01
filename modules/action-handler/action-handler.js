$ahPage = 0;
$ahMaxPage = 6;
$ahDebounce = Date.now();

function actionHandler () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			if($rd.crz.active != "1" && Date.now()-$ahDebounce > 200){// if cruise is off and debounce timeout is over
				if ($rd.crz.btnUp == "1"){
					ahCurrentSettingCtl("+"); //increment or decrement $ahPage with limiter
				} 
				if ($rd.crz.btnDn == "1"){
					ahCurrentSettingCtl("-"); //increment or decrement $ahPage with limiter
				}
			}
		}
		ahSetPageVisibility($ahPage); //hide inactive div, highlight corresponding top icon

		actionHandler();
	}, 20);
}
actionHandler();

document.addEventListener('keydown', (event) => {
	if (event.key == 'ArrowUp' && Date.now()-$ahDebounce > 200) {
		ahCurrentSettingCtl("+");
		ahSetPageVisibility($ahPage);
	}
	if (event.key == 'ArrowDown' && Date.now()-$ahDebounce > 200) {
		ahCurrentSettingCtl("-");
		ahSetPageVisibility($ahPage);
	}
});

function ahSetPageVisibility($page){ // hides inactive div, and hilights corresponding top icon
	for (var i = 0; i < $ahMaxPage; i++) { // Loop through every page div ID
		if (i !== parseInt($page)) { // If page is not active page
			document.getElementById('page-'+i).classList.toggle("noDisplay",true); //hide inactive page
			document.getElementById('d-'+i).classList.toggle("illuminate",false);//make icon inactive
		}
	}
	document.getElementById('page-'+$page).classList.toggle("noDisplay",false); // expand active menu option
	document.getElementById('d-'+$page).classList.toggle("illuminate",true);// make icon active
}

function ahCurrentSettingCtl($var){
	$ahDebounce = Date.now();
	if ($var === "+" && $ahPage < $ahMaxPage-1) { // if current position is less than the max setting option
		$ahPage++; // then increase menu position
	}else if ($var === "-" && $ahPage > 0){
		$ahPage--;
	}
	
}