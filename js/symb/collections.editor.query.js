//Query form 
function verifyQueryForm(f){
	//if(f.q_catalognumber.value == "" && f.q_othercatalognumbers.value == ""  
	//	&& f.q_recordedby.value == "" && f.q_recordnumber.value == "" && f.q_eventdate.value == ""
	//	&& f.q_recordenteredby.value == "" && f.q_processingstatus.value == "" && f.q_datelastmodified.value == "" 
	//	&& (f.q_customfield1.selectedIndex == 0 && (f.q_customvalue1.value == "" || f.q_customtype1.selectedIndex != 1)) 
	//	alert("Query form is empty! Please enter a value to query by.");
	//	return false;
	//}

	if(!verifyLeaveForm()) return false;

	var validformat1 = /^\s*[<>]{0,1}\s{0,1}\d{4}-\d{2}-\d{2}\s*$/ //Format: yyyy-mm-dd, >yyyy-mm-dd, <yyyy-mm-dd
	var validformat2 = /^\s*\d{4}-\d{2}-\d{2}\s{1,3}-\s{1,3}\d{4}-\d{2}-\d{2}\s*$/ //Format: yyyy-mm-dd - yyyy-mm-dd
	var validformat3 = /^\s*\d{4}-\d{2}-\d{2}\s{1,3}to\s{1,3}\d{4}-\d{2}-\d{2}\s*$/ //Format: yyyy-mm-dd to yyyy-mm-dd
	var validformat4 = /^\s*>{1}\s{0,1}\d{4}-\d{2}-\d{2}\s{1,3}AND\s{1,3}<{1}\s{0,1}\d{4}-\d{2}-\d{2}\s*$/i //Format: >yyyy-mm-dd AND <yyyy-mm-dd

	if(f.q_eventdate){
		var edDateStr = f.q_eventdate.value;
		if(edDateStr){
			try{
				if(!validformat1.test(edDateStr) && !validformat2.test(edDateStr) && !validformat3.test(edDateStr) && !validformat4.test(edDateStr)){
					alert("Event date must follow formats: YYYY-MM-DD, YYYY-MM-DD - YYYY-MM-DD, YYYY-MM-DD to YYYY-MM-DD, >YYYY-MM-DD, <YYYY-MM-DD, >YYYY-MM-DD AND <YYYY-MM-DD");
					return false;
				}
			}
			catch(ex){
			}
		}
	}
	
	if(f.q_datelastmodified){
		var modDateStr = f.q_datelastmodified.value;
		if(modDateStr){
			try{
				if(!validformat1.test(modDateStr) && !validformat2.test(modDateStr) && !validformat3.test(modDateStr) && !validformat4.test(edDateStr)){
					alert("Date last modified must follow formats: YYYY-MM-DD, YYYY-MM-DD - YYYY-MM-DD, YYYY-MM-DD to YYYY-MM-DD, >YYYY-MM-DD, <YYYY-MM-DD, >YYYY-MM-DD AND <YYYY-MM-DD");
					return false;
				}
			}
			catch(ex){
			}
		}
	}
	if(f.q_dateentered){
		var dateEnteredStr = f.q_dateentered.value;
		if(dateEnteredStr){
			try{
				if(!validformat1.test(dateEnteredStr) && !validformat2.test(dateEnteredStr) && !validformat3.test(dateEnteredStr) && !validformat4.test(edDateStr)){
					alert("Date entered must follow formats: YYYY-MM-DD, YYYY-MM-DD - YYYY-MM-DD, YYYY-MM-DD to YYYY-MM-DD, >YYYY-MM-DD, <YYYY-MM-DD, >YYYY-MM-DD AND <YYYY-MM-DD");
					return false;
				}
			}
			catch(ex){
			}
		}
	}

	// Check to make sure that parentheses terms for custon fields match: every open parenthesis should have a matching closed one
	if(f.q_customopenparen1){
        var open = 0;
        var closed = 0;
        if(f.q_customopenparen1.value == '(') open++;
        if(f.q_customopenparen1.value == '((') open += 2;
        if(f.q_customopenparen1.value == '(((') open += 3;
		if(f.q_customcloseparen1.value == ')' || f.q_customcloseparen1.value == '))' || f.q_customcloseparen1.value == ')))'){
            closed++;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 1 that does not have a corresponding selected open parenthesis.");
                return false;
			}
		}
        if(f.q_customopenparen2.value == '(') open++;
        if(f.q_customopenparen2.value == '((') open += 2;
        if(f.q_customopenparen2.value == '(((') open += 3;
        if(f.q_customcloseparen2.value == ')' || f.q_customcloseparen2.value == '))' || f.q_customcloseparen2.value == ')))'){
            closed++;
            if(f.q_customcloseparen2.value == '))') closed++;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 2 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen3.value == '(') open++;
        if(f.q_customopenparen3.value == '((') open += 2;
        if(f.q_customopenparen3.value == '(((') open += 3;
        if(f.q_customcloseparen3.value == ')' || f.q_customcloseparen3.value == '))' || f.q_customcloseparen3.value == ')))'){
            closed++;
            if(f.q_customcloseparen3.value == '))') closed++;
            if(f.q_customcloseparen3.value == ')))') closed += 2;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 3 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen4.value == '(') open++;
        if(f.q_customopenparen4.value == '((') open += 2;
        if(f.q_customopenparen4.value == '(((') open += 3;
        if(f.q_customcloseparen4.value == ')' || f.q_customcloseparen4.value == '))' || f.q_customcloseparen4.value == ')))'){
            closed++;
            if(f.q_customcloseparen4.value == '))') closed++;
            if(f.q_customcloseparen4.value == ')))') closed += 2;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 4 that does not have a corresponding selected open parenthesis.");
                alert(open, closed);
                return false;
            }
        }
        if(f.q_customopenparen5.value == '(') open++;
        if(f.q_customopenparen5.value == '((') open += 2;
        if(f.q_customopenparen5.value == '(((') open += 3;
        if(f.q_customcloseparen5.value == ')' || f.q_customcloseparen5.value == '))' || f.q_customcloseparen5.value == ')))'){
            closed++;
            if(f.q_customcloseparen5.value == '))') closed++;
            if(f.q_customcloseparen5.value == ')))') closed += 2;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 5 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen6.value == '(') open++;
        if(f.q_customopenparen6.value == '((') open += 2;
        if(f.q_customopenparen6.value == '(((') open += 3;
        if(f.q_customcloseparen6.value == ')' || f.q_customcloseparen6.value == '))' || f.q_customcloseparen6.value == ')))'){
            closed++;
            if(f.q_customcloseparen6.value == '))') closed++;
            if(f.q_customcloseparen6.value == ')))') closed += 2;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 6 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen7.value == '(') open++;
        if(f.q_customopenparen7.value == '((') open += 2;
        if(f.q_customcloseparen7.value == ')' || f.q_customcloseparen7.value == '))' || f.q_customcloseparen7.value == ')))'){
            closed++;
            if(f.q_customcloseparen7.value == '))') closed++;
            if(f.q_customcloseparen7.value == ')))') closed += 2;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 7 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen8.value == '(') open++;
        if(f.q_customcloseparen8.value == ')' || f.q_customcloseparen8.value == '))' || f.q_customcloseparen8.value == ')))'){
            closed++;
            if(f.q_customcloseparen8.value == '))') closed++;
            if(f.q_customcloseparen8.value == ')))') closed += 2;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 8 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(open > closed){
            alert("You have selected open parenthesis that do not have corresponding selected closed parenthesis in the Custom Fields.");
            return false;
		}
    }
	return true;
}

function submitQueryForm(qryIndex){
	var f = document.queryform;
	if(qryIndex == 'forward' || qryIndex == 'back'){
		f.direction.value = qryIndex;
	}
	else if(qryIndex === parseInt(qryIndex)){
		f.occindex.value = qryIndex;
		f.direction.value = "";
		f.occidlist.value = "";
		f.occid.value = "";
	}
	if(verifyQueryForm(f)) f.submit();
}

function submitQueryEditor(f){
	f.action = "occurrenceeditor.php"
	f.direction.value = "";
	f.occid.value = "";
	f.occindex.value = "0"
	f.occidlist.value = "";
	if(verifyQueryForm(f)) f.submit();
	return true;
}

function submitQueryTable(f){
	f.action = "occurrencetabledisplay.php";
	f.direction.value = "";
	f.occid.value = "";
	f.occindex.value = "0"
	f.occidlist.value = "";
	if(verifyQueryForm(f)) f.submit();
	return true;
}

function setOrderBy(formObject){
	/*
	if(formObject.value != ""){
		var inputName = formObject.name;
		inputName.substring(2)
		if(formObject.form.orderby.value == "") formObject.form.orderby.value = inputName.substring(2);
	}
	*/
}

function customSelectChanged(targetSelect){
	var sourceObj = document.queryform.q_customfield1;
	var targetObj = document.queryform.q_customtype1;
	if(targetSelect == 2){
		sourceObj = document.queryform.q_customfield2;
		targetObj = document.queryform.q_customtype2;
	}
	else if(targetSelect == 3){
		sourceObj = document.queryform.q_customfield3;
		targetObj = document.queryform.q_customtype3;
	}

	else if(targetSelect == 4){
		sourceObj = document.queryform.q_customfield4;
		targetObj = document.queryform.q_customtype4;
	}

	else if(targetSelect == 5){
		sourceObj = document.queryform.q_customfield5;
		targetObj = document.queryform.q_customtype5;
	}

	else if(targetSelect == 6){
		sourceObj = document.queryform.q_customfield6;
		targetObj = document.queryform.q_customtype6;
	}

	else if(targetSelect == 7){
		sourceObj = document.queryform.q_customfield7;
		targetObj = document.queryform.q_customtype7;
	}

	else if(targetSelect == 8){
		sourceObj = document.queryform.q_customfield8;
		targetObj = document.queryform.q_customtype8;
	}

	if(sourceObj.value == "ocrFragment"){
		targetObj.value = "LIKE";
	}
}

function toggleQueryForm(){
	toggle("querydiv");
	var statusDiv = document.getElementById('statusdiv');
	if(statusDiv) statusDiv.style.display = 'none';
}

function toggleCustomDiv2(){
	var f = document.queryform;
	f.q_customandor2.options[0].selected = true;
    f.q_customopenparen2.options[0].selected = true;
	f.q_customfield2.options[0].selected = true;
	f.q_customtype2.options[0].selected = true;
	f.q_customvalue2.value = "";
	f.q_customcloseparen2.options[0].selected = true;
    f.q_customandor3.options[0].selected = true;
    f.q_customopenparen3.options[0].selected = true;
	f.q_customfield3.options[0].selected = true;
	f.q_customtype3.options[0].selected = true;
	f.q_customcloseparen3.options[0].selected = true;
	f.q_customvalue3.value = "";
	document.getElementById('customdiv3').style.display = "none";
	$('#customdiv2').toggle();
}

function toggleCustomDiv3(){
	var f = document.queryform;
	f.q_customandor3.options[0].selected = true;
    f.q_customopenparen3.options[0].selected = true;
	f.q_customfield3.options[0].selected = true;
	f.q_customtype3.options[0].selected = true;
	f.q_customvalue3.value = "";
	f.q_customcloseparen3.options[0].selected = true;
    f.q_customandor4.options[0].selected = true;
    f.q_customopenparen4.options[0].selected = true;
	f.q_customfield4.options[0].selected = true;
	f.q_customtype4.options[0].selected = true;
	f.q_customcloseparen4.options[0].selected = true;
	f.q_customvalue4.value = "";
	document.getElementById('customdiv4').style.display = "none";
	$('#customdiv3').toggle();
}

function toggleCustomDiv4(){
	var f = document.queryform;
	f.q_customandor4.options[0].selected = true;
    f.q_customopenparen4.options[0].selected = true;
	f.q_customfield4.options[0].selected = true;
	f.q_customtype4.options[0].selected = true;
	f.q_customvalue4.value = "";
	f.q_customcloseparen4.options[0].selected = true;
    f.q_customandor5.options[0].selected = true;
    f.q_customopenparen5.options[0].selected = true;
	f.q_customfield5.options[0].selected = true;
	f.q_customtype5.options[0].selected = true;
	f.q_customcloseparen5.options[0].selected = true;
	f.q_customvalue5.value = "";
	document.getElementById('customdiv5').style.display = "none";
	$('#customdiv4').toggle();
}

function toggleCustomDiv5(){
	var f = document.queryform;
	f.q_customandor5.options[0].selected = true;
    f.q_customopenparen5.options[0].selected = true;
	f.q_customfield5.options[0].selected = true;
	f.q_customtype5.options[0].selected = true;
	f.q_customvalue5.value = "";
	f.q_customcloseparen5.options[0].selected = true;
    f.q_customandor6.options[0].selected = true;
    f.q_customopenparen6.options[0].selected = true;
	f.q_customfield6.options[0].selected = true;
	f.q_customtype6.options[0].selected = true;
	f.q_customcloseparen6.options[0].selected = true;
	f.q_customvalue6.value = "";
	document.getElementById('customdiv6').style.display = "none";
	$('#customdiv5').toggle();
}

function toggleCustomDiv6(){
	var f = document.queryform;
	f.q_customandor6.options[0].selected = true;
    f.q_customopenparen6.options[0].selected = true;
	f.q_customfield6.options[0].selected = true;
	f.q_customtype6.options[0].selected = true;
	f.q_customvalue6.value = "";
	f.q_customcloseparen6.options[0].selected = true;
	f.q_customandor7.options[0].selected = true;
	f.q_customopenparen7.options[0].selected = true;
	f.q_customfield7.options[0].selected = true;
	f.q_customtype7.options[0].selected = true;
	f.q_customcloseparen7.options[0].selected = true;
	f.q_customvalue7.value = "";
	document.getElementById('customdiv7').style.display = "none";
	$('#customdiv6').toggle();
}

function toggleCustomDiv7(){
	var f = document.queryform;
	f.q_customandor7.options[0].selected = true;
    f.q_customopenparen7.options[0].selected = true;
	f.q_customfield7.options[0].selected = true;
	f.q_customtype7.options[0].selected = true;
	f.q_customvalue7.value = "";
	f.q_customcloseparen7.options[0].selected = true;
	f.q_customandor8.options[0].selected = true;
	f.q_customopenparen8.options[0].selected = true;
	f.q_customfield8.options[0].selected = true;
	f.q_customtype8.options[0].selected = true;
	f.q_customcloseparen8.options[0].selected = true;
	f.q_customvalue8.value = "";
	document.getElementById('customdiv8').style.display = "none";
	$('#customdiv7').toggle();
}


function toggleCustomDiv8(){
	var f = document.queryform;
	f.q_customandor8.options[0].selected = true;
    f.q_customopenparen8.options[0].selected = true;
	f.q_customfield8.options[0].selected = true;
	f.q_customtype8.options[0].selected = true;
	f.q_customvalue8.value = "";
	f.q_customcloseparen8.options[0].selected = true;
	$('#customdiv8').toggle();
}


function toggle(target){
	var ele = document.getElementById(target);
	if(ele){
		if(ele.style.display=="none" || ele.style.display==""){
			ele.style.display="block";
  		}
	 	else {
	 		ele.style.display="none";
	 	}
	}
	else{
		var divObjs = document.getElementsByTagName("div");
	  	for (i = 0; i < divObjs.length; i++) {
	  		var divObj = divObjs[i];
	  		if(divObj.getAttribute("class") == target || divObj.getAttribute("className") == target){
				if(divObj.style.display=="none"){
					divObj.style.display="";
				}
			 	else {
			 		divObj.style.display="none";
			 	}
			}
		}
	}
}

//Misc
function verifyLeaveForm(){
	if(document.fullform && document.fullform.submitaction.disabled == false && document.fullform.submitaction.type == "submit"){
		return confirm("It appears that you didn't save your changes. Are you sure you want to leave without saving?"); 
	}
	return true;
}
