/*
+-------------------------------------------------------------------------------+
| Copyright (c) 2006-2007 Andrew G. Samoilov, Universal Data Solutions inc.	|
| All rights reserved.                                                  	|
|                                                                       	|
| Redistribution and use in source and binary forms, with or without    	|
| modification, are permitted provided that the following conditions    	|
| are met:                                                              	|
|                                                                       	|
| o Redistributions of source code must retain the above copyright      	|
|   notice, this list of conditions and the following disclaimer.       	|
| o Redistributions in binary form must reproduce the above copyright   	|
|   notice, this list of conditions and the following disclaimer in the 	|
|   documentation and/or other materials provided with the distribution.	|
|                                                                       	|
| THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   	|
| "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     	|
| LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR 	|
| A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  	|
| OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 	|
| SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      	|
| LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 	|
| DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY 	|
| THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   	|
| (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE 	|
| OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  	|
+-------------------------------------------------------------------------------+
*/

var cur = -1;
var selectsToHide = new Array();
var suggestValues = new Array();
var lookupValues = new Array();

var masterDetails = {
    flag : "",
    counter : 0,
    show : false
}

var isLookupError = false;
var isSetFocus = false;
var isMouseOnDiv = false;

var RollDetailsLink = {
    timeout : null,
    showPopup : function(obj,str){
        clearTimeout(this.timeout);
	if ( $('#master_details').css("visibility") == 'hidden' || ( str!=undefined && str!=masterDetails.flag ) ){
            this.timeout = setTimeout(function(){
		masterDetails.flag = str;
		masterDetails.show = true;
		masterDetails.counter++;
		//$("#master_details").html("");
		$.get(str, 
		{
		    counter: masterDetails.counter,
		    rndVal: (new Date().getTime())
		}, 
		function(txt){ 
		    var str = txt.split("counterSeparator");
		    if ( masterDetails.counter == str[1] ) { 
		    	$("#master_details").html(str[0]); 
			setLyr(obj,"master_details");
			if ( $.browser.mozilla && ( $("#master_details").get(0).offsetTop > window.innerHeight/2 + document.body.scrollTop ) ) {
			    $("#master_details").css("top", ($("#master_details").get(0).offsetTop - $("#master_details").get(0).offsetHeight) +"px");
			} else if ( $.browser.msie && ( $("#master_details").get(0).offsetTop > document.body.clientHeight/2 + document.body.scrollTop ) ) {
			    $("#master_details").css("top", ($("#master_details").get(0).offsetTop - $("#master_details").get(0).offsetHeight) +"px");
			}
			if ( masterDetails.show ) { $("#master_details").css("visibility", "visible"); }
		    }
		});				
	    },50);
        }
    },
    hidePopup : function(){
	masterDetails.show = false;
        if($('#master_details').css("visibility") == 'hidden'){
            clearTimeout(this.timeout);
        }else{
            this.timeout = setTimeout(function(){
		$("#master_details").css("visibility", "hidden");
		$("#master_details").html("");
	    },100);
        }
    }    
}

$(document).ready(function(){ 
	$("#search_suggest").mouseover(function(){ isMouseOnDiv = true; });
	$("#search_suggest").mouseout(function(){ isMouseOnDiv = false; });
	$("#ctlSearchFor").blur(function(){ if (!isMouseOnDiv) { DestroySuggestDiv(); }	});
	$("input[@name*=value_]").blur(function(){ if (!isMouseOnDiv) { DestroySuggestDiv(); } });
});

$(document).click(function(){ 
	DestroySuggestDiv();
});
		
function myEncode(value)
{
	if ( value ) {
		value = value.replace(/:/g,"%3A");
		value = value.replace(/=/g,"%3D");
		value = value.replace(/&/g,"%26");
		value = value.replace(/\//g,"%2F");
		value = value.replace(/\?/g,"%3F");
		value = value.replace(/\s/g,"%20");
	}
	return value;
}

Array.prototype.inArray = function (value,caseSensitive)
{
	var i;
	for (i=0; i < this.length; i++) {
		if (caseSensitive) {
			if (this[i] == value) { return true; }
		} else {
			if (this[i].toLowerCase() == value.toLowerCase()) { return true; }
		}
	}
	return false;
};

function DestroySuggestDiv() 
{
	cur = -1;
	isMouseOnDiv = false;
	$("#search_suggest").html("");
	$("#search_suggest").css({ visibility: "hidden"});
	$.each( selectsToHide, function(i, n){
		n.style.visibility = 'visible';
	});
	selectsToHide.splice(0,selectsToHide.length);
}

function PtInBox(oElement) 
{
	var bFlag = false;
	var el = $("#search_suggest")[0];
	var left = findPos(oElement)[0];
	var top = findPos(oElement)[1];
	var width = findPos(oElement)[2];
	var height = findPos(oElement)[3];
	
	if ( left >= el.offsetLeft && left <= (el.offsetLeft+el.offsetWidth) && top >= el.offsetTop && top <= (el.offsetTop+el.offsetHeight) ) { bFlag = true; }
	if ( (left+width) >= el.offsetLeft && (left+width) <= (el.offsetLeft+el.offsetWidth) && top >= el.offsetTop && top <= (el.offsetTop+el.offsetHeight) ) { bFlag = true; }
	if ( left >= el.offsetLeft && left <= (el.offsetLeft+el.offsetWidth) && (top+height) >= el.offsetTop && (top+height) <= (el.offsetTop+el.offsetHeight) ) { bFlag = true; }
	if ( (left+width) >= el.offsetLeft && (left+width) <= (el.offsetLeft+el.offsetWidth) && (top+height) >= el.offsetTop && (top+height) <= (el.offsetTop+el.offsetHeight) ) { bFlag = true; }
	if ( ( left <= el.offsetLeft && (left+width) >= (el.offsetLeft+el.offsetWidth) ) && ( (el.offsetTop+el.offsetHeight) >= top && el.offsetTop <= (top+height) ) ) { bFlag = true; }

	if ( bFlag ) {
		return true;
	}
	return false;
}

function setLyr(obj,lyr)
{
	var coors = findPos(obj);
	if (lyr == 'master_details') coors[0] += (coors[2] + 5);
	if (lyr == 'search_suggest') coors[1] += coors[3];
	$("#"+lyr).css("top",coors[1] + "px");
	$("#"+lyr).css("left",coors[0] + "px");
}	

function findPos(obj)
{
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		curwidth = obj.offsetWidth
		curheight = obj.offsetHeight
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop,curwidth,curheight];
}
	
function moveUp(oElement, searchType) 
{
	if($("#search_suggest").children().length>0 && cur>=-1)
	{
		cur--;
		if (cur==-2) { cur=$("#search_suggest").children().length-1; oElement.focus(); }
		for(var i=0;i<$("#search_suggest").children().length;i++)
		{
			if(i==cur)
			{
				$("#search_suggest").children().get(i).className = "suggest_link_over";
				oElement.value = suggestValues[cur].replace(/\<(\/b|b)\>/gi,"");
				if ( searchType == 'lookup' ) 
				{ 
					isLookupError = false;
					$(oElement).removeClass("highlight");
					var helement=$("#"+oElement.id.substring(8))[0];
					if ( $(helement).val() != lookupValues[cur] ) {
						$(helement).val(lookupValues[cur]);
						$(helement).change();
					}
				}
			}
			else
			{
				$("#search_suggest").children().get(i).className = "suggest_link";
			}
		}
	}
}
		
function moveDown(oElement, searchType) 
{
	if($("#search_suggest").children().length>0 && cur<($("#search_suggest").children().length))
	{
		cur++;
		for(var i=0;i<$("#search_suggest").children().length;i++)
		{
			if(i==cur)
			{
				$("#search_suggest").children().get(i).className = "suggest_link_over";
				oElement.value = suggestValues[cur].replace(/\<(\/b|b)\>/gi,"");
				if ( searchType == 'lookup' ) 
				{ 
					isLookupError = false;
					var helement=$("#"+oElement.id.substring(8))[0];
					$(oElement).removeClass("highlight");
					if ( $(helement).val() != lookupValues[cur] ) 
					{
						$(helement).val(lookupValues[cur]);
						$(helement).change();
					}
				}
			}
			else
			{
				$("#search_suggest").children().get(i).className = "suggest_link";
			}
		}
		if (cur==($("#search_suggest").children().length)) { cur=-1; oElement.focus(); }
	}
}

function suggestOver(div_value) 
{
	$("div.suggest_link_over").each(function(){
		this.className = 'suggest_link';
	}) ;
	div_value.className = 'suggest_link_over';
	cur = div_value.id.substring(10);
}

function suggestOut(div_value) 
{
	div_value.className = 'suggest_link';
}

function setSearch(inputName, value) 
{
	if (setSearch.arguments[2] == 'lookup') {
		isLookupError = false;
		var helement=$("#"+inputName.substring(8)+setSearch.arguments[4])[0];
		$("#"+inputName+setSearch.arguments[4]).removeClass("highlight");
		$("#"+inputName+setSearch.arguments[4]).val(value);
		if ( $(helement).val() != setSearch.arguments[3] )
		{
			$(helement).val(setSearch.arguments[3]);
			$(helement).change();
		}
	}
	else
		$("input[@type=text][@name="+inputName+"]").val(value);
	DestroySuggestDiv();
}

/*
function showDetailsPreview(obj, str) 
{
	detailsCounter++;
	$("#master_details").html("");
	$.get(str, 
	{
		counter: detailsCounter,
		rndVal: (new Date().getTime())
	}, 
	function(txt){ 
		var str = txt.split("counterSeparator");
		if ( detailsCounter == str[1] ) { 
			setLyr(obj,"master_details");
			$("#master_details").html(str[0]); 
			if ( $.browser.mozilla && ( $("#master_details").get(0).offsetTop > window.innerHeight/2 + document.body.scrollTop ) ) {
				$("#master_details").css("top", ($("#master_details").get(0).offsetTop - $("#master_details").get(0).offsetHeight) +"px");
			} else if ( $.browser.msie && ( $("#master_details").get(0).offsetTop > document.body.clientHeight/2 + document.body.scrollTop ) ) {
				$("#master_details").css("top", ($("#master_details").get(0).offsetTop - $("#master_details").get(0).offsetHeight) +"px");
			}
			$("#master_details").css("visibility", "visible");
		}
	});
};		

function hideDetailsPreview() 
{
	$("#master_details").css("visibility", "hidden");
	$("#master_details").html("");
};			
*/

function listenEvent(oEvent,oElement,searchType) 
{
	oEvent=window.event || oEvent;
	iKeyCode=oEvent.keyCode;

	switch(iKeyCode)
	{	
		case 38: //up arrow
			moveUp(oElement,searchType);
			break;
		case 40: //down arrow
			moveDown(oElement,searchType);
			break;
		case 13: //enter 
			//if (oElement.value != undefined || oElement.value != "") {
			//setSearch(oElement.name, oElement.value, searchType);
			DestroySuggestDiv();
			if ( searchType == 'ordinary' ) { RunSearch(); return false; } 
			if ( searchType == 'advanced' || searchType == 'advanced1' ) { document.forms[0].submit(); return false;}
			if ( searchType == 'lookup' ) { return false; } 
			//}
			break;
		case 9:
			DestroySuggestDiv();
			break;
	}
	return true;
}

function searchSuggest(oEvent,oElement,searchType) 
{
	oEvent=window.event || oEvent;
        var iKeyCode=oEvent.keyCode;
        var legalKeys = [8,32,46,191,192,222];
	
	switch(searchType)
	{
		case "ordinary":
			fieldForSearch = $("select#ctlSearchField").val();
			break;
		case "advanced":
			fieldForSearch = oElement.name.substring(6);
			break;
		case "advanced1":
			fieldForSearch = oElement.name.substring(7);
			break;
	}

	if ( ((iKeyCode >= 65) && (iKeyCode <= 90)) || ((iKeyCode >= 48) && (iKeyCode <= 57))
		|| ((iKeyCode >= 96) && (iKeyCode <= 111)) || legalKeys.inArray(iKeyCode,true) ) {
		cur = -1;
		$.get(SUGGEST_TABLE,
		{
			searchFor: myEncode( oElement.value ), 
			searchField: myEncode( fieldForSearch ),
			rndVal: (new Date().getTime())
		},
		function(txt){
			$("#search_suggest").html("");
			var str = txt.split("\n");
			for(i=0,j=0; i < str.length-1; i++,j++) {
				var suggest = '<div id="suggestDiv'+i+'" style="cursor:pointer;" onmouseover="suggestOver(this);" ';
				suggest += 'onmouseout="suggestOut(this);" ';
				suggest += 'onclick="setSearch(\'' + oElement.name + '\',suggestValues[' + j + '].replace(/\\<(\\/b|b)\\>/gi,\'\'));" ';
				suggest += 'class="suggest_link">' + str[i] + '</div>';
				$(suggest).appendTo("#search_suggest");
				suggestValues[j] = str[i];
			}
			if (txt) {
				$("select").each(function(){
					if ( PtInBox(this) ) {
						selectsToHide[selectsToHide.length] = this;
						this.style.visibility = 'hidden';
					}
				});
				$("#search_suggest").css({ visibility: "visible"});
			} else {
				DestroySuggestDiv();
			}
		});
	}
	setLyr(oElement,"search_suggest");
}

function showHideLookupError(oElement)
{
	if ( isLookupError ) {
		$(oElement).addClass("highlight");
	} else {
		$(oElement).removeClass("highlight");
	}
}

function lookupSuggest(oEvent,oElement, lpValue,record_id) 
{
	if(record_id!="")
		record_id="_"+record_id
	oEvent=window.event || oEvent;
	iKeyCode=oEvent.keyCode;
	var helement=$("#"+oElement.name.substring(8)+record_id)[0];
	
	if (((iKeyCode >= 65) && (iKeyCode <= 90)) || ((iKeyCode >= 48) && (iKeyCode <= 57))
		|| ((iKeyCode >= 96) && (iKeyCode <= 105)) || (iKeyCode==8) || (iKeyCode==46) || (iKeyCode==32)
		|| (iKeyCode==222)) {
		cur = -1;
		
		if (oElement.value == "") {
			DestroySuggestDiv();
			isLookupError = false;
//			$("input[@name="+oElement.name+"]").removeClass("highlight");
			$(oElement).removeClass("highlight");
			$(helement).val(""); 
			$(helement).change();
			return;
		}
		
		var cvalue="";
		if($(oElement).attr("categoryId")!=undefined)
		{
			var cElement = $("#value_"+$(oElement).attr("categoryId"));
			if($(cElement).length)
				cvalue=$(cElement).val();
		}
		$.get(SUGGEST_LOOKUP_TABLE,
		{
			searchFor: myEncode( oElement.value ), 
			searchField: myEncode( oElement.name.substring(14) ),
			lookupValue: myEncode( lpValue ),
			category : cvalue,
			rndVal: (new Date().getTime())
		},
		function(txt){
			$("#search_suggest").html("");
			var str = txt.split("\n");
			$.each( str, function(i, n){
				str[i] = unescape(n);
			})
						
			if (str.inArray(oElement.value,false)) {
				isLookupError = false;
//				$("input[@name="+oElement.name+"]").removeClass("highlight");
				$(oElement).removeClass("highlight");
				$.each( str, function(i, n){
					if((n.toLowerCase()==oElement.value.toLowerCase()) && ($(helement).val()!=str[i-1] )) { 
						$(helement).val(str[i-1]); 
						$(helement).change();
					}
				});
			} else {
				isLookupError = true;
				if ( !isSetFocus ) 
//					$("input[@name="+oElement.name+"]").addClass("highlight");
					$(oElement).addClass("highlight");
			}
			
			for(i=0,j=0; i < str.length-1; i=i+2,j++) {
				var suggest = '<div id="suggestDiv'+i+'" style="cursor:pointer;" onmouseover="javascript:suggestOver(this);" ';
				suggest += 'onmouseout="javascript:suggestOut(this);" ';
				suggest += 'onclick="javascript:setSearch(\'' + oElement.name + '\',suggestValues[' + j + '],\'lookup\',\'' + str[i] + '\',\''+record_id+'\');" ';
				suggest += 'class="suggest_link">' + str[i+1] + '</div>';
				$(suggest).appendTo("#search_suggest")
				suggestValues[j] = str[i+1];
				lookupValues[j] = str[i];
			}
			if (txt) {
				$("select").each(function(){
					if ( PtInBox(this) ) {
						selectsToHide[selectsToHide.length] = this;
						this.style.visibility = 'hidden';
					}
				});
				if ( isSetFocus ) { $("#search_suggest").css({ visibility: "visible"}); }
			} else {
				DestroySuggestDiv();
			}
		});
	}
	setLyr(oElement,"search_suggest");
}

function preloadSelectContent(txt, selectControl, selectValue, record_id) 
{
	if(record_id!="")
		record_id="_"+record_id
	if($('#'+selectControl+record_id)[0].tagName=='SELECT')
	{
		$('#'+selectControl+record_id).get(0).options[0]=new Option(TEXT_PLEASE_SELECT,'');
		var str = txt.split('\n');
		var index = 0;
		for(i=0,j=0; i < str.length - 1; i=i+2, j++) {
			$('#'+selectControl+record_id).get(0).options[j+1]=new Option(unescape(str[i+1]),unescape(str[i]));
			if ( unescape(str[i]) == selectValue ) {index = j+1;}
		}
		$('#'+selectControl+record_id).get(0).selectedIndex = index;
		if ( j == 1 ) { $('#'+selectControl+record_id).get(0).selectedIndex = 1; }
	}
	else if(txt.length)
	{
		var str = txt.split('\n');
		if(str.length==3)
		{
			$('#'+selectControl+record_id).val(unescape(str[0]));
			$('#display_'+selectControl+record_id).val(unescape(str[1]));
		}
	}
}	

function loadSelectContent(main_field, dependent_field, record_id) 
{
	if(record_id!="")
		record_id="_"+record_id
	$.get(AUTOCOMPLETE_TABLE, 
	{
		field: myEncode( dependent_field ),
		value: myEncode( $('#value_'+main_field+record_id).val() ),
		type: 	$('#value_'+dependent_field+record_id)[0].tagName
	}, 
	function(txt){
		if($('#value_'+dependent_field+record_id)[0].tagName=='SELECT')
		{
			$('#value_'+dependent_field+record_id).html('<option value="">'+TEXT_PLEASE_SELECT+'</option>');
			var str = txt.split('\n');
			for(i=0,j=0; i < str.length - 1; i=i+2, j++) {
				$('#value_'+dependent_field+record_id).get(0).options[j+1]=new Option(unescape(str[i+1]),unescape(str[i]));
			}
			$('#value_'+dependent_field+record_id).get(0).selectedIndex = 0;
			if ( j == 1 ) { $('#value_'+dependent_field+record_id).get(0).selectedIndex = 1; }
			$('#value_'+dependent_field+record_id)[0].onchange();
		}
		else
		{
			$('#value_'+dependent_field+record_id).val("");
			$('#display_value_'+dependent_field+record_id).val("");
			if(txt.length)
			{
				var str = txt.split('\n');
				if(str.length==3)
				{
					$('#value_'+dependent_field+record_id).val(unescape(str[0]));
					$('#display_value_'+dependent_field+record_id).val(unescape(str[1]));
				}
			}
		}
	});
}