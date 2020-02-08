/*
  File: 2018_Subar_WRX_Premium.js
  Author: Tyler Watthanaphand
  License: GNU GPLv3
  Notes: Driver spec file to decode CAN messages from candump into a JS object.
  Note about little endian byte swapping: Passing data into dataDecode3 function as little endian, byte swapping is done first, then offset and length functions are performed.
*/
$vinParse = [];
$avgGPHCnt = 1;
$rd = { wpr: '0',
  trns: { gear: 0, isRvse: '0' },
  esc:
   { FullAct: '0', VDCAct: '0', ESCAct: '0', TCSOff: '0', isBrk: '0' },
  pdl: { accel: 0, brkPrs: 0, PBRK: '0' },
  lok:
   { Lock: '0', UnlockDriver: '0', UnlockAll: '0', BCMAwake: '0' },
  dAjr: { TR: '0', DF: '0', PF: '0', PR: '0', DR: '0' },
  tSnl: { Left: '0', Right: '0' },
  stng: { pcnt: 0, whl: 0, tir: 0, ttlTq: 0, angTq: 0 },
  ngn: { RPM: 0, tbody: 0, isOffAccl: '1', boost: 0 },
  ltg:
   { dimPos: 0,
     dim: '0',
     DRL: '0',
     HL: '0',
     HB: '0',
     BRK: '0',
     insClstrFullBright: '0',
     fog: '0' },
  hvac:
   { system: 'off',
     dispAct: '0' },
  tmp: { oil: 0, clnt: 0 },
  crz: { btnUp: '0', btnDn: '0', btnCncl: '0', active: '0' },
  spd:
   { MPH: 0,
     avgMPH: 0,
     wssFL: 0,
     wssFR: 0,
     wssRL: 0,
     wssRR: 0,
     wssAvg: 0 },
  fuel: { avgMPG: 0, avgGPH: 0, MPG: 0, GPH: 0},
  TPMS: { FL: 0, FR: 0, RL: 0, RR: 0 },
  info: { vin: 'LOADING VIN      ', my: 2018, make: 'SUBARU', model: 'WRX', trim: 'PREMIUM', loc: 'USDM', profile : '2018_Subaru_WRX_Premium.js'},
  trip: { odo: 0, timeDisp: '0h 0\'', timeSec: 0, scrTime: Date.now(), scrTimeStart: Date.now(), scrOdo: 0, scrTrp: 0} };

$rd.d = { };
$rtnTimerArray = new Object();

exports.addMessage = function($ch,$dt) {
  var $startT = Date.now();
  switch ($ch) {
    case "002":
      // steering angle
      $rd.stng.pcnt = dataDecode3($dt,"sig",0,16,"b",0,0.000205,0,-1);
      $rd.stng.whl = Math.round($rd.stng.percent*485);
      $rd.stng.tir = Math.round($rd.stng.percent * (180 * 2.7) / 14.4);
      break;
    case "0D1":
      $rd.pdl.brkPrs = dataDecode3($dt,"d",16,8,"b",0,1.20481927711,0);
      $rd.spd.MPH = dataDecode3($dt,"d",16,16,"l",0,0.0349521,0);
      $rd.spd.avgMPH = $rd.trip.scrTrp/($rd.trip.scrTime/3600000);
      break;
    case "0D3":
      $escRaw = dataDecode3($dt,"b");
      $rd.esc.FullAct = $escRaw.substr(31,1);
      $rd.esc.VDCAct = $escRaw.substr(4,1);
      $rd.esc.ESCAct = $escRaw.substr(25,1);
      $rd.esc.TCSOff = $escRaw.substr(12,1);
      $rd.esc.isBrk = $escRaw.substr(18,1);
      break;
    case "0D4":
      $rd.spd.wssFL = dataDecode3($dt,"d",0,16,"l",0,0.0349521,0,-1);
      $rd.spd.wssFR = dataDecode3($dt,"d",16,16,"l",0,0.0349521,0,-1);
      $rd.spd.wssRL = dataDecode3($dt,"d",32,16,"l",0,0.0349521,0,-1);
      $rd.spd.wssRR = dataDecode3($dt,"d",48,16,"l",0,0.0349521,0,-1);
      $rd.spd.wssAvg = ($rd.spd.wssFL + $rd.spd.wssFR + $rd.spd.wssRL + $rd.spd.wssRR)/4;
      $rd.TPMS.FL = Math.round($rd.spd.wssAvg*100 - $rd.spd.wssFL*100);
      $rd.TPMS.FR = Math.round($rd.spd.wssAvg*100 - $rd.spd.wssFR*100);
      $rd.TPMS.RL = Math.round($rd.spd.wssAvg*100 - $rd.spd.wssRL*100);
      $rd.TPMS.RR = Math.round($rd.spd.wssAvg*100 - $rd.spd.wssRR*100);
      break;
    case "140":
      $rd.ngn.RPM = dataDecode3($dt,"d",35,13,"l");
      $rd.ngn.tbody = dataDecode3($dt, "d", 48, 8, "b", 0, 0.392157, 0, 0);
      $rd.ngn.isOffAccl = dataDecode3($dt, "b", 25, 1); // (Is clutch pressed)
      $rd.pdl.accel = dataDecode3($dt, "d", 0, 8, "b", 0, 0.392157, 0, 0);
      break;
    case "141":
      /* Old Gear data, doesn't work reliably
      $gData = dataDecode3($dt, "d", 53, 3);
      if($rd.trns.isRvse == "1"){
        $rd.trns.gear = "R";
      }else if($gData === 7){
        $rd.trns.gear = "N";
      }else if ($rd.ngn.isoffAccelerator !== "1" && $gData > 0) {
        $rd.trns.gear = $gData;
      }
      */
      $rd.d.rev1 = dataDecode3($dt,"d",51,13,"l"); //byte 0/1(5bits) LE
      $rd.d.rev2 = dataDecode3($dt,"d",36,12,"l"); //byte 2/3(last 4bit) LE
      $dt = dataDecode3($dt,"b");
      //$rd.pdl.isoffAccelerator = $dt.substr(40,1);
      $rd.d.D141_5 = $dt.substr(59,1);
      $rd.d.D141_6 = $dt.substr(60,1);

      break;
    case "144":
      $rd.d.D144rev4 = dataDecode3($dt,"d",16,8); // throttle based?
      $dt = dataDecode3($dt,"b");
      $rd.d.D144_2 = $dt.substr(5,1);  //button?
      $rd.d.D144_3 = $dt.substr(6,1);  //button?
      $rd.d.D144_4 = $dt.substr(49,1); //button?
      //$rd.d.D144_5 = $dt.substr(52,1); //brake pedal light
      //$rd.crz.active = $dt.substr(55,1); //cruise enabled

      $rd.crz.btnUp = $dt.substr(3,1);
      $rd.crz.btnDn = $dt.substr(4,1);
      $rd.crz.btnCncl = $dt.substr(2,1);
      break;
    case "152":
      $hl = dataDecode3($dt,"b");
      $rd.ltg.DRL = $hl.substr(61,1);
      $rd.ltg.HL = $hl.substr(60,1);
      $rd.ltg.HB = $hl.substr(59,1);
      $rd.ltg.BRK = $hl.substr(51,1);
      $rd.pdl.PBRK = $hl.substr(52,1);
      $rd.wpr = $hl.substr(57,1);
      $rd.d.D152_1 = $hl.substr(53,1); //lighting? washer spray?
      break;
    case "154": //56 bits returned
      $dt = dataDecode3($dt,"b");
      $rd.trns.isRvse = $dt.substr(54,1);
      break;
    case "280":
      $rd.d.D280_1 = dataDecode3($dt,"b",20,1);
      break;
    case "281":
      //HVAC System
      $hvr = dataDecode3($dt,"b",0,40);
      $hvacSystm = $hvr.substr(0,22); //on/off
      $hvacFan = $hvr.substr(0,3); //001-111 1-7levels
      $hvacMode = $hvr.substr(3,3);
      $hvacRecirc = $hvr.substr(8,2); // 10 Outside, 01 Recirc
      $hvacRDefst = $hvr.substr(10,1);
      $hvacACCmp = $hvr.substr(11,1);
      $hvacAuto = $hvr.substr(12,2); // 00 None, 01 Auto, 10 Full Auto
      $hvacTmp = $hvr.substr(16,6); //000000 LO, 000101 61, 100001 89, 101100 MAX (Above b33 is MAX, below b5 is LOW)

      if ($hvacSystm === "0000000000000000111111") {
        $rd.hvac.system = "off";
      }else{ // HVAC IS ON
        $rd.hvac.system = "on";
        $rd.hvac.fanLevel = binToDec($hvacFan);
        switch($hvacMode){
          case "001":
          $rd.hvac.mode = "face";
          break;
          case "010":
          $rd.hvac.mode = "faceFeet";
          break;
          case "011":
          $rd.hvac.mode = "feet";
          break;
          case "100":
          $rd.hvac.mode = "feetWindshield";
          break;
          case "101":
          $rd.hvac.mode = "windshield";
          break;
        }
        $hvacTmp = binToDec($hvacTmp);
        if ($hvacTmp > 33) {
          $rd.hvac.tmp = "HI";
        }else if($hvacTmp < 5){
          $rd.hvac.tmp = "LO";
        }else{
          $rd.hvac.tmp = $hvacTmp+56;
        }
        switch($hvacRecirc){
          case "00":
          $rd.hvac.rcrc = "null";
          break;
          case "01":
          $rd.hvac.rcrc = "recirc";
          break;
          case "10":
          $rd.hvac.rcrc = "fresh";
          break;
        }
        $rd.hvac.rrDfst = $hvacRDefst;
        $rd.hvac.AC = $hvacACCmp;
        switch($hvacAuto){
          case "00":
          $rd.hvac.auto = "noAuto";
          break;
          case "01":
          $rd.hvac.auto = "auto";
          break;
          case "10":
          $rd.hvac.auto = "fullAuto";
          break;
        }
      }
      $rd.hvac.dispAct = $hvr.substr(38,1); // IF CONTROLS WERE MODIFIED LAST 2 SECONDS
      break;
    case "282":
      $rd.d.TempFuel0 = dataDecode3($dt,"d",0,8,"b",0,1,-48); // temp, or fuel? // FIX THIS
      $rd.d.tempAmbient = dataDecode3($dt,"d",24,8,"b",0,1,-48); // temp, or fuel? Used for AC AUTO, not triple display at top
      $rd.d.tempInlet = dataDecode3($dt,"d",32,8,"b",0,1,-48); // temp, or fuel?

      $dt = dataDecode3($dt,"b");
      $rd.d.StbltDrvr = $dt.substr(47,1);
      $rd.tSnl.Left = $dt.substr(43,1);
      $rd.tSnl.Right = $dt.substr(42,1);
      break;
    case "360":
      $rd.tmp.oil = dataDecode3($dt, "d", 16, 8, "b", -40, 1.8, 32,3);
      $rd.tmp.clnt = dataDecode3($dt, "d", 24, 8, "b", -40, 1.8, 32,3);
      $rd.ngn.boost = dataDecode3($dt, "d", 32, 8, "b", 0, 0.28571428571, -14,3);
      $rd.crz.active = dataDecode3($dt,"b",43,1);
      $rd.fuel.GPH = dataDecode3($dt,"d",48,16,"l",0,0.001);
      $rd.fuel.avgGPH = (($rd.fuel.avgGPH*$avgGPHCnt)+$rd.fuel.GPH)/($avgGPHCnt+1); // used in calc of avg mpg
      $avgGPHCnt++; //increment running average counter
      if ($rd.fuel.GPH === 0) { // if using no gas
      	$rd.fuel.MPG = ($rd.spd.MPH*10);	
      }else{
      	$rd.fuel.MPG = $rd.spd.MPH/$rd.fuel.GPH;
      }
      $rd.fuel.avgMPG = $rd.spd.avgMPH/$rd.fuel.avgGPH;
      
      $dt = dataDecode3($dt,"b");
      //$rd.crz.active = $dt.substr(43,1); //Cruise On
      $rd.d.D360_3 = $dt.substr(50,1);
      //$rd.d.D360_4 = $dt.substr(51,1); //AC comp relay
      $rd.d.D360_5 = $dt.substr(54,1);
      break;
    case "361":
      $rd.d.D361_1 = dataDecode3($dt,"d",4,4);
      $gData = dataDecode3($dt,"d",4,4); // try and see if 4 bits is actually necessary. try 3?
      if($rd.trns.isRvse == "1"){
        $rd.trns.gear = "R";
      }else if($gData == "0"){
        $rd.trns.gear = "N";
      }else{
        $rd.trns.gear = $gData;
      }
      break;
    case "362":
      break;
    case "370":
      $rd.stng.ttlTq = dataDecode3($dt,"d",52,12,"l");
      $rd.stng.angTq = dataDecode3($dt,"sig",32,8,"b");
      break;
    case "371":
      break;
    case "374":
      $dt = dataDecode3($dt,"b"); //ID374:32:1 is a false ID
      //Fog Lights
      $rd.ltg.fog = $dt.substr(9,1);
      break;
    case "375":
      $dt = dataDecode3($dt,"b");
      //Instrument cluster bright on/off
      $rd.ltg.insClstrFullBright = $dt.substr(24,1); // Buggy - Signal is very hard to catch properly
      //Remote lock/unlock actions
      $rd.lok.Lock = $dt.substr(25,1);
      $rd.lok.UnlockDriver = $dt.substr(26,1);
      $rd.lok.UnlockAll = $dt.substr(27,1);
      $rd.lok.BCMAwake = $dt.substr(29,1);
      //Door Ajar
      $rd.dAjr.TR = $dt.substr(10,1);
      $rd.dAjr.DF = $dt.substr(15,1);
      $rd.dAjr.PF = $dt.substr(14,1);
      $rd.dAjr.PR = $dt.substr(13,1);
      $rd.dAjr.DR = $dt.substr(12,1);
      break;
    case "376":
      $rd.ltg.dimPos = dataDecode3($dt,"d",0,8,"b",0,1,0,0); //an analog pot with indents
      switch($rd.ltg.dimPos){ // these values might not work for every car
          case 0:
          $rd.ltg.dim = "0";
          break;
          case 33:
          $rd.ltg.dim = "1";
          break;
          case 82:
          $rd.ltg.dim = "2";
          break;
          case 125:
          $rd.ltg.dim = "3";
          break;
          case 173:
          $rd.ltg.dim = "4";
          break;
          case 250:
          $rd.ltg.dim = "5";
          break;
        }
      break;
    case "3D1":
      $rd.d.fuelCons = dataDecode3($dt,"d",51,13,"l");
      break;
    case "660":
      $rd.trip.timeSec = dataDecode3($dt,"d",48,16,"b",0,0.1,0);
      $rd.trip.timeDisp = HHMMSS($rd.trip.timeSec);
      $rd.trip.scrTime = Date.now()-$rd.trip.scrTimeStart; // time in ms since script load
      break;
    case "6D1":
      $rd.trip.odo = dataDecode3($dt,"d",40,24,"l",0,0.1,0); // current full odometer
      if ($rd.trip.scrOdo === 0) { // if script start odometer not set, set it to current
        $rd.trip.scrOdo = $rd.trip.odo;
      }
      $rd.trip.scrTrp = ($rd.trip.odo-$rd.trip.scrOdo); //miles traveled since nodejs start
      $rd.d.D6D1_2 = dataDecode3($dt,"d",8,8,"b",0,1,0); // troubleshoot this
      break;
    case "6FC":
      if ($rd.info.vin.substr(0,11) === "LOADING VIN") { //prevents this bit of code from running forever needlessly. VIN's dont change while car is running.
        $byte = dataDecode3($dt,"d",40,8,"b",0,1,0,0);
        $letter = dataDecode3($dt,"d",48,8,"b",0,1,0,0);
        $vinParse[$byte] = String.fromCharCode($letter);
        if ($vinParse.join("").length === 17) {
          $rd.info.vin = $vinParse.join("");
        }else{
          $rd.info.vin = "LOADING VIN "+$vinParse.join("").length+"/17";
        }
      }
      break;
  }
  $rtnTime = Date.now()-$startT; // get worst time for debugging
  if ($rtnTimerArray[$ch] < $rtnTime || typeof $rtnTimerArray[$ch] == "undefined") {
    $rtnTimerArray[$ch] = $rtnTime;
  }
  return $rd;
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

function dataDecode3(b, a, c, d, f, e, g, k, h) { //input,mode,offset,length,bit/little,pre,multiplier,post,avg
  a = void 0 === a ? "d" : a;
  c = void 0 === c ? 0 : c;
  d = void 0 === d ? 4 * b.length : d;
  e = void 0 === e ? 0 : e;
  g = void 0 === g ? 1 : g;
  k = void 0 === k ? 0 : k;
  h = void 0 === h ? -1 : h;
  "l" == (void 0 === f ? "b" : f) && (b = b.replace(/^(.(..)*)$/, "0$1").match(/../g).reverse().join(""));
  b = hexToBin(b).substr(c, d);
  switch(a) {
    case "sig":
      return $int = binToDec(b), $sigMax = binToDec(b.replace(/(.)/g, "1")) + 1, $int = $int >= 0.5 * $sigMax ? Math.abs($int - $sigMax + 1) : -$int, b = ($int + e) * g + k, -1 !== h && (b = Math.round(b * Math.pow(10, h || 0)) / Math.pow(10, h || 0)), b;
    case "b":
      return b;
    case "h":
      return binToHex(b);
    case "d":
      return b = (binToDec(b) + e) * g + k, -1 !== h && (b = Math.round(b * Math.pow(10, h || 0)) / Math.pow(10, h || 0)), b;
    case "hd":
      return binToHex(b).replace(/(.{2})/g, "$1 ");
    case "bd":
      return b.replace(/(.{8})/g, "$1 ");
  }
}
function logBig(b, a, c) {
  var d = Math.log(a);
  $out = (Math.log(b) - d) / ((Math.log(c) - d) / (c - a)) + a;
  return "-Infinity" == $out ? 0 : $out;
}
function logSmall(b, a, c) {
  var d = Math.log(a);
  $out = Math.exp(d + (Math.log(c) - d) / (c - a) * (b - a));
  return "-Infinity" == $out ? 0 : $out;
}
function add(b, a, c) {
  for (var d = [], f = Math.max(b.length, a.length), e = 0, g = 0; g < f || e;) {
    e = e + (g < b.length ? b[g] : 0) + (g < a.length ? a[g] : 0), d.push(e % c), e = Math.floor(e / c), g++;
  }
  return d;
}
function multiplyByNumber(b, a, c) {
  if (0 > b) {
    return null;
  }
  if (0 == b) {
    return [];
  }
  for (var d = [];;) {
    b & 1 && (d = add(d, a, c));
    b >>= 1;
    if (0 === b) {
      break;
    }
    a = add(a, a, c);
  }
  return d;
}
function parseToDigitsArray(b, a) {
  for (var c = b.split(""), d = [], f = c.length - 1; 0 <= f; f--) {
    var e = parseInt(c[f], a);
    if (isNaN(e)) {
      return null;
    }
    d.push(e);
  }
  return d;
}
function convertBase(b, a, c) {
  var d = parseToDigitsArray(b, a);
  if (null === d) {
    return null;
  }
  b = [];
  for (var f = [1], e = 0; e < d.length; e++) {
    d[e] && (b = add(b, multiplyByNumber(d[e], f, c), c)), f = multiplyByNumber(a, f, c);
  }
  a = "";
  for (e = b.length - 1; 0 <= e; e--) {
    a += b[e].toString(c);
  }
  a || "10" != c || (a = "0");
  return a;
}
function decToHex(b) {
  return (b = convertBase(b, 10, 16)) ? "0x" + b : null;
}
function decToBin(b) {
  b = String(b);
  return convertBase(b, 10, 2).padStart(8 * b.length, "0");
}
function hexToDec(b) {
  b = String(b);
  b = b.toLowerCase();
  return parseInt(convertBase(b, 16, 10));
}
function hexToBin(b) {
  b = String(b);
  "0x" === b.substring(0, 2) && (b = b.substring(2));
  b = b.toLowerCase();
  return convertBase(b, 16, 2).padStart(4 * b.length, "0");
}
function binToHex(b) {
  return convertBase(b, 2, 16).padStart(b.length / 4, "0").toUpperCase();
}
function binToDec(b) {
  return parseInt(convertBase(b, 2, 10));
}
;