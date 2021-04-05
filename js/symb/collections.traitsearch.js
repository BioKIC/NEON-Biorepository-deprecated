// The following functions enable some of the search-by-trait functionality
// added for the CCH2 TCN. Code is modified from collections.traitattr.js

$(document).ready(function() {
	setAttributeTree(null);
	$(".trianglediv").each(function(){
		var rootElem = $(this).closest("fieldset");
		var childClassArr = $(rootElem).find("div[class^=child]");
		if(childClassArr.length == 0) $(this).hide();
	});
});

function traitChanged(elem){
	var elemType =  elem.getAttribute("type");
	var elemName = elem.getAttribute("name");
	var traitID = elemName.substring(8,elemName.length-2);
	if(elem.checked == true){
    elem.checked == false;
	}
	if(!sessionStorage.attributeTree || sessionStorage.attributeTree == 0){
		$('input[name="traitid-'+traitID+'[]"]').each(function(){
			if((elemType == 'text' && elemType.value.trim() != '') || this.checked == true){
				if(sessionStorage.attributeTree == 0) $("div.child-"+this.value).show();
			}  // Expands the attribute tree if hidden, but intentionally does not re-hide
		});
	}
}

function setAttributeTree(triggerElem){
	var toggleTree = false;
	if(triggerElem) toggleTree = true;
	var treeOpen = false;
	if(sessionStorage.attributeTree && sessionStorage.attributeTree == 1) treeOpen = true;
	if(toggleTree){
		if(treeOpen) treeOpen = false;
		else treeOpen = true;
	}
	var rootElem = $("#traitdiv");
	if(triggerElem) rootElem = $(triggerElem).closest("fieldset");
	if(treeOpen){
		$(rootElem).find("div[class^=child]").show();
		$(rootElem).find(".triangledown").show();
		$(rootElem).find(".triangleright").hide();
		sessionStorage.attributeTree = 1;
	}
	else{
		$(rootElem).find("div[class^=child]").hide();
		$(rootElem).find('.triangledown').hide();
		$(rootElem).find('.triangleright').show();
		sessionStorage.attributeTree = 0;
	}
}
