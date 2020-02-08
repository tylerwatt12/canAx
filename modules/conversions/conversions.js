function logBig(position,min,max) {
	var minv = Math.log(min);
	var maxv = Math.log(max);
	// calculate adjustment factor
	var scale = (maxv-minv) / (max-min);
	$out = (Math.log(position)-minv) / scale + min;
	if ($out == "-Infinity") {
		return 0;
	}else{
		return $out;
	}
}
function HHMMSS($in) {
	var sec_num = parseInt($in, 10); // don't forget the second param
	var hours   = Math.floor(sec_num / 3600);
	var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
	var seconds = sec_num - (hours * 3600) - (minutes * 60);

	if (hours   < 10) {hours   = "0"+hours;}
	if (minutes < 10) {minutes = "0"+minutes;}
	if (seconds < 10) {seconds = "0"+seconds;}
	return hours+'h '+minutes+'\''+seconds+'"';
}
function roundPrec($in,$prec = 0){
	$prec = Math.pow(10,$prec);
	return Math.round($in*$prec)/$prec;
}