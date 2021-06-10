<div id="coordAidDiv">
	<div id="dmsAidDiv">
		<div>
			Lat:
			<input id="latdeg" style="width:35px;" title="Latitude Degree" />&deg;
			<input id="latmin" style="width:50px;" title="Latitude Minutes" />'
			<input id="latsec" style="width:50px;" title="Latitude Seconds" />&quot;
			<select id="latns">
				<option>N</option>
				<option>S</option>
			</select>
		</div>
		<div>
			Long:
			<input id="lngdeg" style="width:35px;" title="Longitude Degree" />&deg;
			<input id="lngmin" style="width:50px;" title="Longitude Minutes" />'
			<input id="lngsec" style="width:50px;" title="Longitude Seconds" />&quot;
			<select id="lngew">
				<option>E</option>
				<option SELECTED>W</option>
			</select>
		</div>
		<div style="margin:5px;">
			<input type="button" value="Insert Lat/Long Values" onclick="insertLatLng(this.form)" />
		</div>
	</div>
	<div id="utmAidDiv">
		Zone: <input id="utmzone" style="width:40px;" /><br/>
		East: <input id="utmeast" type="text" style="width:100px;" /><br/>
		North: <input id="utmnorth" type="text" style="width:100px;" /><br/>
		Hemisphere: <select id="hemisphere" title="Use hemisphere designator (e.g. 12N) rather than grid zone ">
			<option value="N">North</option>
			<option value="S">South</option>
		</select><br/>
		<div style="margin-top:5px;">
			<input type="button" value="Insert UTM Values" onclick="insertUtm(this.form)" />
		</div>
	</div>
	<div id="trsAidDiv">
		T<input id="township" style="width:30px;" title="Township" />
		<select id="townshipNS">
			<option>N</option>
			<option>S</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		R<input id="range" style="width:30px;" title="Range" />
		<select id="rangeEW">
			<option>E</option>
			<option>W</option>
		</select><br/>
		Sec:
		<input id="section" style="width:30px;" title="Section" />&nbsp;&nbsp;&nbsp;
		Details:
		<input id="secdetails" style="width:90px;" title="Section Details" /><br/>
		<select id="meridian" title="Meridian">
			<option value="">Meridian Selection</option>
			<option value="">----------------------------------</option>
			<option value="G-AZ">Arizona, Gila &amp; Salt River</option>
			<option value="NAAZ">Arizona, Navajo</option>
			<option value="F-AR">Arkansas, Fifth Principal</option>
			<option value="H-CA">California, Humboldt</option>
			<option value="M-CA">California, Mt. Diablo</option>
			<option value="S-CA">California, San Bernardino</option>
			<option value="NMCO">Colorado, New Mexico</option>
			<option value="SPCO">Colorado, Sixth Principal</option>
			<option value="UTCO">Colorado, Ute</option>
			<option value="B-ID">Idaho, Boise</option>
			<option value="SPKS">Kansas, Sixth Principal</option>
			<option value="F-MO">Missouri, Fifth Principal</option>
			<option value="P-MT">Montana, Principal</option>
			<option value="SPNE">Nebraska, Sixth Principal</option>
			<option value="M-NV">Nevada, Mt. Diablo</option>
			<option value="NMNM">New Mexico, New Mexico</option>
			<option value="F-ND">North Dakota, Fifth Principal</option>
			<option value="C-OK">Oklahoma, Cimarron</option>
			<option value="I-OK">Oklahoma, Indian</option>
			<option value="W-OR">Oregon, Willamette</option>
			<option value="BHSD">South Dakota, Black Hills</option>
			<option value="F-SD">South Dakota, Fifth Principal</option>
			<option value="SPSD">South Dakota, Sixth Principal</option>
			<option value="SLUT">Utah, Salt Lake</option>
			<option value="U-UT">Utah, Uinta</option>
			<option value="W-WA">Washington, Willamette</option>
			<option value="SPWY">Wyoming, Sixth Principal</option>
			<option value="WRWY">Wyoming, Wind River</option>
		</select>
		<div style="margin:5px;">
			<input type="button" value="Insert TRS Values" onclick="insertTRS(this.form)" />
		</div>
	</div>
</div>
<script type="text/javascript">
	function insertLatLng(f) {
		var latDeg = document.getElementById("latdeg").value.trim();
		var latMin = document.getElementById("latmin").value.trim();
		var latSec = document.getElementById("latsec").value.trim();
		var latNS = document.getElementById("latns").value;
		var lngDeg = document.getElementById("lngdeg").value.trim();
		var lngMin = document.getElementById("lngmin").value.trim();
		var lngSec = document.getElementById("lngsec").value.trim();
		var lngEW = document.getElementById("lngew").value;
		if(latDeg && latMin && lngDeg && lngMin){
			if(latMin == "") latMin = 0;
			if(latSec == "") latSec = 0;
			if(lngMin == "") lngMin = 0;
			if(lngSec == "") lngSec = 0;
			if(isNumeric(latDeg) && isNumeric(latMin) && isNumeric(latSec) && isNumeric(lngDeg) && isNumeric(lngMin) && isNumeric(lngSec)){
				if(latDeg < 0 || latDeg > 90){
					alert("Latitude degree must be between 0 and 90 degrees");
				}
				else if(lngDeg < 0 || lngDeg > 180){
					alert("Longitude degree must be between 0 and 180 degrees");
				}
				else if(latMin < 0 || latMin > 60 || lngMin < 0 || lngMin > 60 || latSec < 0 || latSec > 60 || lngSec < 0 || lngSec > 60){
					alert("Minute and second values can only be between 0 and 60");
				}
				else{
					var vcStr = f.verbatimcoordinates.value;
					vcStr = vcStr.replace(/-*\d{2}[°\u00B0]+[NS\d\.\s\'\"-°\u00B0]+[EW;]+/g, "");
					vcStr = vcStr.replace(/^\s+|\s+$/g, "");
					vcStr = vcStr.replace(/^;|;$/g, "");
					if(vcStr != "") vcStr = vcStr + "; ";
					var dmsStr = latDeg + "\u00B0 " + latMin + "' ";
					if(latSec > 0) dmsStr += latSec + '" ';
					dmsStr += latNS + "  " + lngDeg + "\u00B0 " + lngMin + "' ";
					if(lngSec) dmsStr += lngSec + '" ';
					dmsStr += lngEW;
					f.verbatimcoordinates.value = vcStr + dmsStr;
					var latDec = parseInt(latDeg) + (parseFloat(latMin)/60) + (parseFloat(latSec)/3600);
					var lngDec = parseInt(lngDeg) + (parseFloat(lngMin)/60) + (parseFloat(lngSec)/3600);
					if(latNS == "S") latDec = latDec * -1;
					if(lngEW == "W") lngDec = lngDec * -1;
					f.decimallatitude.value = Math.round(latDec*1000000)/1000000;
					f.decimallongitude.value = Math.round(lngDec*1000000)/1000000;

					fieldChanged("decimallatitude");
					fieldChanged("decimallongitude");
					fieldChanged("verbatimcoordinates");
				}
			}
			else{
				alert("Field values must be numeric only");
			}
		}
		else{
			alert("DMS fields must contain a value");
		}
	}

	function insertUtm(f) {
		var zValue = document.getElementById("utmzone").value.trim();
		var hValue = document.getElementById("hemisphere").value;
		var eValue = document.getElementById("utmeast").value.trim();
		var nValue = document.getElementById("utmnorth").value.trim();
		if(zValue && eValue && nValue){
			if(isNumeric(eValue) && isNumeric(nValue)){
				//Remove prior UTM references from verbatimCoordinates field
				var vcStr = f.verbatimcoordinates.value;
				vcStr = vcStr.replace(/\d{2}.*\d+E\s+\d+N[;\s]*/g, "");
				vcStr = vcStr.replace(/(Northern)|(Southern)/g, "");
				vcStr = vcStr.replace(/^\s+|\s+$/g, "");
				vcStr = vcStr.replace(/^;|;$/g, "");
				//put UTM into verbatimCoordinate field
				if(vcStr != "") vcStr = vcStr + "; ";
				var utmStr = zValue;
				if(isNumeric(zValue)) utmStr = utmStr + hValue;
				utmStr = utmStr + " " + eValue + "E " + nValue + "N ";
				f.verbatimcoordinates.value = vcStr + utmStr;
				//Convert to Lat/Lng values
				var zNum = parseInt(zValue);
				if(isNumeric(zNum)){
					var latLngStr = utm2LatLng(zNum,eValue,nValue,f.geodeticdatum.value);
					var llArr = latLngStr.split(',');
					if(llArr){
						var latFact = 1;
						if(hValue == "S") latFact = -1;
						f.decimallatitude.value = latFact*Math.round(llArr[0]*1000000)/1000000;
						f.decimallongitude.value = Math.round(llArr[1]*1000000)/1000000;
					}
				}
				fieldChanged("decimallatitude");
				fieldChanged("decimallongitude");
				fieldChanged("verbatimcoordinates");
			}
			else{
				alert("Easting and northing fields must contain numeric values only");
			}
		}
		else{
			alert("Zone, Easting, and Northing fields must not be empty");
		}
	}

	function insertTRS(f) {
		var township = document.getElementById("township").value.trim();
		var townshipNS = document.getElementById("townshipNS").value.trim();
		var range = document.getElementById("range").value.trim();
		var rangeEW = document.getElementById("rangeEW").value.trim();
		var section = document.getElementById("section").value.trim();
		var secdetails = document.getElementById("secdetails").value.trim();
		var meridian = document.getElementById("meridian").value.trim();

		if(!township || !range){
			alert("Township and Range fields must have values");
			return false;
		}
		else if(!isNumeric(township)){
			alert("Numeric value expected for Township field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
			return false;
		}
		else if(!isNumeric(range)){
			alert("Numeric value expected for Range field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
			return false;
		}
		else if(!isNumeric(section)){
			alert("Numeric value expected for Section field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
			return false;
		}
		else if(section > 36){
			alert("Section field must contain a numeric value between 1-36");
			return false;
		}
		else{
			//Insert into verbatimCoordinate field
			vCoord = f.verbatimcoordinates;
			if(vCoord.value) vCoord.value = vCoord.value + "; ";
			vCoord.value = vCoord.value + "TRS: T"+township+townshipNS+" R"+range+rangeEW+" sec "+section+" "+secdetails+" "+meridian;
			fieldChanged("verbatimcoordinates");
		}
	}
</script>
