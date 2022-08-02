function displayTableView(f){
	f.action = "listtabledisplay.php";
	f.submit();
}

function cleanNumericInput(formElem){
	if(formElem.value != ""){
		var elemValue = Math.abs(parseFloat(formElem.value));
		if(!elemValue) elemValue = '';
		formElem.value = elemValue;
	}
}

function checkHarvestParamsForm(frm){
	//make sure they have filled out at least one field.
	if((frm.taxa.value.trim() == '') && (frm.country.value.trim() == '') && (frm.state.value.trim() == '') && (frm.county.value.trim() == '') &&
		(frm.local.value.trim() == '') && (frm.elevlow.value.trim() == '') && (frm.upperlat.value.trim() == '') && (frm.footprintwkt.value.trim() == '') && (frm.pointlat.value.trim() == '') &&
		(frm.collector.value.trim() == '') && (frm.collnum.value.trim() == '') && (frm.eventdate1.value.trim() == '') && (frm.catnum.value.trim() == '') &&
		(frm.typestatus.checked == false) && (frm.hasimages.checked == false) && (frm.hasgenetic.checked == false) && (frm.hascoords.checked == false)){
			//Check trait search fields if present
			if (typeof frm.SearchByTraits !== "undefined" && frm.SearchByTraits.value == "true") {
				var traitinputs = frm.elements;
				var traitselected = false;
			 	for(var i = 0; i < traitinputs.length; i++) {
					if(traitinputs[i].name.indexOf('traitid-') == 0) {
						if(traitinputs[i].type == 'checkbox' || traitinputs[i].type == 'radio') {
							if(traitinputs[i].checked == true) {
								traitselected = traitinputs[i].checked;
								break;
							}
						} else {
							if(traitinputs[i].value.trim() !== '') {
								traitselected = true;
								break;
							}
						}
					}
				}
				if(!traitselected) {
					alert("Please fill in at least one search parameter!");
					return false;
				}
			} else {
				alert("Please fill in at least one search parameter!");
				return false;
			}
	}

	if(frm.upperlat.value != '' || frm.bottomlat.value != '' || frm.leftlong.value != '' || frm.rightlong.value != ''){
		// if Lat/Long field is filled in, they all should have a value!
		if(frm.upperlat.value == '' || frm.bottomlat.value == '' || frm.leftlong.value == '' || frm.rightlong.value == ''){
			alert("Error: Please make all Lat/Long bounding box values contain a value or all are empty");
			return false;
		}

		// Check to make sure lat/longs are valid.
		if(Math.abs(frm.upperlat.value) > 90 || Math.abs(frm.bottomlat.value) > 90 || Math.abs(frm.pointlat.value) > 90){
			alert("Latitude values can not be greater than 90 or less than -90.");
			return false;
		}
		if(Math.abs(frm.leftlong.value) > 180 || Math.abs(frm.rightlong.value) > 180 || Math.abs(frm.pointlong.value) > 180){
			alert("Longitude values can not be greater than 180 or less than -180.");
			return false;
		}
		var uLat = frm.upperlat.value;
		if(frm.upperlat_NS.value == 'S') uLat = uLat * -1;
		var bLat = frm.bottomlat.value;
		if(frm.bottomlat_NS.value == 'S') bLat = bLat * -1;
		if(uLat < bLat){
			alert("Your northern latitude value is less then your southern latitude value. Please correct this.");
			return false;
		}
		var lLng = frm.leftlong.value;
		if(frm.leftlong_EW.value == 'W') lLng = lLng * -1;
		var rLng = frm.rightlong.value;
		if(frm.rightlong_EW.value == 'W') rLng = rLng * -1;
		if(lLng > rLng){
			alert("Your western longitude value is greater then your eastern longitude value. Please correct this. Note that western hemisphere longitudes in the decimal format are negitive.");
			return false;
		}
	}

	//Same with point radius fields
	if(frm.pointlat.value != '' || frm.pointlong.value != '' || frm.radius.value != ''){
		if(frm.pointlat.value == '' || frm.pointlong.value == '' || frm.radius.value == ''){
			alert("Error: Please make all Lat/Long point-radius values contain a value or all are empty");
			return false;
		}
	}

	return true;
}

function setHarvestParamsForm(frm){
	if(sessionStorage.querystr){
		var urlVar = parseUrlVariables(sessionStorage.querystr);

		if(typeof urlVar.usethes !== 'undefined' && (urlVar.usethes == "" || urlVar.usethes == "0")){frm.usethes.checked = false;}
		if(urlVar.taxontype){frm.taxontype.value = urlVar.taxontype;}
		if(urlVar.taxa){frm.taxa.value = urlVar.taxa;}
		if(urlVar.country){
			countryStr = urlVar.country;
			countryArr = countryStr.split(";");
			if(countryArr.indexOf('USA') > -1 || countryArr.indexOf('usa') > -1) countryStr = countryArr[0];
			//if(countryStr.indexOf('United States') > -1) countryStr = 'United States';
			frm.country.value = countryStr;
		}
		if(urlVar.state){frm.state.value = urlVar.state;}
		if(urlVar.county){frm.county.value = urlVar.county;}
		if(urlVar.local){frm.local.value = urlVar.local;}
		if(urlVar.elevlow){frm.elevlow.value = urlVar.elevlow;}
		if(urlVar.elevhigh){frm.elevhigh.value = urlVar.elevhigh;}
		if(urlVar.llbound){
			var coordArr = urlVar.llbound.split(';');
			frm.upperlat.value = Math.abs(parseFloat(coordArr[0]));
			frm.bottomlat.value = Math.abs(parseFloat(coordArr[1]));
			frm.leftlong.value = Math.abs(parseFloat(coordArr[2]));
			frm.rightlong.value = Math.abs(parseFloat(coordArr[3]));
		}
		if(urlVar.footprintwkt){
			frm.footprintwkt.value = urlVar.footprintwkt;
		}
		if(urlVar.llpoint){
			var coordArr = urlVar.llpoint.split(';');
			frm.pointlat.value = Math.abs(parseFloat(coordArr[0]));
			frm.pointlong.value = Math.abs(parseFloat(coordArr[1]));
			frm.radius.value = Math.abs(parseFloat(coordArr[2]));
			if(coordArr[4] == "mi") frm.radiusunits.value = "mi";
		}
		if(urlVar.collector){frm.collector.value = urlVar.collector;}
		if(urlVar.collnum){frm.collnum.value = urlVar.collnum;}
		if(urlVar.eventdate1){frm.eventdate1.value = urlVar.eventdate1;}
		if(urlVar.eventdate2){frm.eventdate2.value = urlVar.eventdate2;}
		if(urlVar.catnum){frm.catnum.value = urlVar.catnum;}
		//if(!urlVar.othercatnum){frm.includeothercatnum.checked = false;}
		if(typeof urlVar.typestatus !== 'undefined'){frm.typestatus.checked = true;}
		if(typeof urlVar.hasimages !== 'undefined'){frm.hasimages.checked = true;}
		if(typeof urlVar.hasgenetic !== 'undefined'){frm.hasgenetic.checked = true;}
		if(typeof urlVar.hascoords !== 'undefined'){frm.hascoords.checked = true;}
		if(typeof urlVar.includecult !== 'undefined'){frm.includecult.checked = true;}
		if(urlVar.db){frm.db.value = urlVar.db;}
		for(var i in urlVar) {
			if(`${i}`.indexOf('traitid-') == 0) {
				var traitInput = document.getElementById("traitstateid-" + urlVar[i]);
				if(traitInput.type == 'checkbox' || traitInput.type == 'radio') { traitInput.checked = true; };
				// if(traitInput.type == 'select') { traitInput.value = urlVar[i]; }; // Must improve this to deal with multiple possible selections
			}
		}
	}
}

function parseUrlVariables(varStr) {
	var result = {};
	varStr.split("&").forEach(function(part) {
		if(!part) return;
		part = part.split("+").join(" ");
		var eq = part.indexOf("=");
		var key = eq>-1 ? part.substr(0,eq) : part;
		var val = eq>-1 ? decodeURIComponent(part.substr(eq+1)) : "";
		result[key] = val;
	});
	return result;
}

function resetHarvestParamsForm(f){
	sessionStorage.removeItem('querystr');
}

function openCoordAid(mapMode) {
	mapWindow=open("tools/mapcoordaid.php?mapmode="+mapMode,"polygon","resizable=0,width=900,height=630,left=20,top=20");
	if (mapWindow.opener == null) mapWindow.opener = self;
	mapWindow.focus();
}
