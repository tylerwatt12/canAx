function pageScroller () {
	setTimeout(function () {
		if (typeof $rd !== 'undefined'){ // wait for connection to socket.io server
			if($rd.crz.active != "1"){
				if ($rd.crz.btnUp === "1") {
					window.scrollBy(0, 100);
				}
				if ($rd.crz.btnDn === "1") {
					window.scrollBy(0, -100);
				}
			}
		}
		pageScroller();
	}, 250);
}
pageScroller();