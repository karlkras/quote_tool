// JavaScript Document


function updateMarkup(markupVal, language, id)
{
	var ajaxRequest = getXMLHTTPRequest();
	
	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState == 4)
		{
			if(ajaxRequest.status == 200)
			{
				//get the data from the server's response
				var xmlDoc=ajaxRequest.responseXML.documentElement;
				
				var langID = xmlDoc.getElementsByTagName("langcode")[0].childNodes[0].nodeValue;
				var taskID = xmlDoc.getElementsByTagName("idnum")[0].childNodes[0].nodeValue;
				var markup = xmlDoc.getElementsByTagName("markup")[0].childNodes[0].nodeValue;				
				var new_calculated_sellprice_per_word = xmlDoc.getElementsByTagName("newcspp")[0].childNodes[0].nodeValue;
				var new_actual_sellprice_per_word =  xmlDoc.getElementsByTagName("newaspp")[0].childNodes[0].nodeValue;
				var new_actual_sellprice = xmlDoc.getElementsByTagName("newasp")[0].childNodes[0].nodeValue;
				var new_task_gm = xmlDoc.getElementsByTagName("newtaskgm")[0].childNodes[0].nodeValue;
				var new_language_act_sellprice_per = xmlDoc.getElementsByTagName("newlanguageaspp")[0].childNodes[0].nodeValue;
				var new_language_act_sellprice = xmlDoc.getElementsByTagName("newlanguageasp")[0].childNodes[0].nodeValue;
				var new_language_gm = xmlDoc.getElementsByTagName("newlanguagegm")[0].childNodes[0].nodeValue;
				var task_total_act_sellprice = xmlDoc.getElementsByTagName("tasktotalasp")[0].childNodes[0].nodeValue;
				var grand_total_act_sellprice = xmlDoc.getElementsByTagName("grandtotalasp")[0].childNodes[0].nodeValue;
				var grand_total_cost = xmlDoc.getElementsByTagName("grandtotalcost")[0].childNodes[0].nodeValue;
				var grand_total_gm = xmlDoc.getElementsByTagName("grandtotalgm")[0].childNodes[0].nodeValue;
				var new_pm_asp = xmlDoc.getElementsByTagName("newpmasp")[0].childNodes[0].nodeValue;
				var rushfeemarkup = xmlDoc.getElementsByTagName("rushfee")[0].childNodes[0].nodeValue;
				var discountType = xmlDoc.getElementsByTagName("discounttype")[0].childNodes[0].nodeValue;
				var discountPercent = xmlDoc.getElementsByTagName("discountpercent")[0].childNodes[0].nodeValue;
				var discountAmount = xmlDoc.getElementsByTagName("discountamount")[0].childNodes[0].nodeValue;

				grand_total_act_sellprice=grand_total_act_sellprice/1;
				
				var markupID = "markup-" + langID + "-" + taskID;
				document.getElementById(markupID).value = markup;

				if (rushfeemarkup != 0)
				{
					rushfee_amount = Math.round(grand_total_act_sellprice * rushfeemarkup*100)/100;
					
					grand_total_act_sellprice = grand_total_act_sellprice + rushfee_amount;
					grand_total_gm = ((grand_total_act_sellprice - grand_total_cost) / grand_total_act_sellprice);
					grand_total_gm = Math.round(grand_total_gm*10000)/100;
					
					document.getElementById("rushfee").innerHTML = rushfee_amount;
				}
				var discount =0;
				if (discountType == 'percent')
				{
					discount = Math.round(grand_total_act_sellprice * (discountPercent/100)*100)/100;
					grand_total_act_sellprice -= discount;
					grand_total_gm = ((grand_total_act_sellprice - grand_total_cost) / grand_total_act_sellprice);
					grand_total_gm = Math.round(grand_total_gm*10000)/100;
					
					document.getElementById("discount").innerHTML = discount;
				}
				else if(discountType == 'fixed')
				{
					grand_total_act_sellprice -= discountAmount;
					grand_total_gm = ((grand_total_act_sellprice - grand_total_cost) / grand_total_act_sellprice);
					grand_total_gm = Math.round(grand_total_gm*10000)/100;
					
					document.getElementById("discount").innerHTML = discountAmount;
				}
				
				
				
				//update the language table actual sell price per unit, unless we're updating a PM, since there is none
				if (taskID != 15)
				{
					//update the language table calculated sell price per unit
					var csppID = "csp-" + langID + "-" + taskID;
					document.getElementById(csppID).value = new_calculated_sellprice_per_word;
				
					var asppID = "spp-" + langID + "-" + taskID;
					document.getElementById(asppID).value = new_actual_sellprice_per_word;
				}
				
				//update the language table actual sell price
				var aspID = "asp-" + langID + "-" + taskID;
				document.getElementById(aspID).value = new_actual_sellprice;
				aspID = "SPT-" + langID + "-" + taskID;  //and since it's the same, update the sell price table's language asp
				document.getElementById(aspID).value = new_actual_sellprice;
				
				//update the language table gross margin
				var gmID = "GM-" + langID + "-" + taskID;
				document.getElementById(gmID).value = new_task_gm;
				
				//update the language table's sell price per WORD field
				var lang_aspp_ID = "cspTotal-" + langID;
				document.getElementById(lang_aspp_ID).value = new_language_act_sellprice_per;
				
				//update the language table actual sell price for the entire language
				var lang_asp_ID = "aspTotal-" + langID;
				document.getElementById(lang_asp_ID).value = new_language_act_sellprice;
				lang_asp_ID = "SPT_LTOTAL-" + langID;
				document.getElementById(lang_asp_ID).value = new_language_act_sellprice;
				
				
				//update the language table's language gross margin				
				var lang_total_gm_ID = "lang_total_GM-" + langID;
				document.getElementById(lang_total_gm_ID).value = new_language_gm;
				//since it's the same, update the sell price table's gm for the language too
				lang_total_gm_ID = "GM-" + langID;
				document.getElementById(lang_total_gm_ID).innerHTML = new_language_gm;
				
				//update the sell price table's task total asp
				var spt_task_total_ID = "SPT_TTOTAL-" + taskID;
				document.getElementById(spt_task_total_ID).value = task_total_act_sellprice;
				
				//update the sell price table's grand total
				document.getElementById("SPT_GTOTAL").value = grand_total_act_sellprice;
				
				//update the grand total GM
				document.getElementById('GM_Grand_Total').innerHTML = grand_total_gm;
				
				//update PM actual sell price
				var pm_asp_ID = 'asp-' + langID + "-15";
				document.getElementById(pm_asp_ID).value = new_pm_asp;
				pm_asp_ID = 'SPT-'  + langID + "-15";
				document.getElementById(pm_asp_ID).value = new_pm_asp;
				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	//make a random number to add to the url so the browser won't cache
	var myRandom=parseInt(Math.random()*99999999);
	
	var url="markup.php";
	url = url + "?markup=" + markupVal + "&lang=" + language;
	url = url + "&id=" + id ;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}

/*
function updatePM(markupVal, language, id)
{
	var ajaxRequest = getXMLHTTPRequest();
	
	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState == 4)
		{
			if(ajaxRequest.status == 200)
			{
				
			
				//get the data from the server's response
				var xmlDoc=ajaxRequest.responseXML.documentElement;
				
				var langID = xmlDoc.getElementsByTagName("langcode")[0].childNodes[0].nodeValue;
				var taskID = xmlDoc.getElementsByTagName("idnum")[0].childNodes[0].nodeValue;
				var markup = xmlDoc.getElementsByTagName("markup")[0].childNodes[0].nodeValue;				
				var new_calculated_sellprice_per_word = xmlDoc.getElementsByTagName("newcspp")[0].childNodes[0].nodeValue;
				var new_actual_sellprice_per_word =  xmlDoc.getElementsByTagName("newaspp")[0].childNodes[0].nodeValue;
				var new_actual_sellprice = xmlDoc.getElementsByTagName("newasp")[0].childNodes[0].nodeValue;
				var new_task_gm = xmlDoc.getElementsByTagName("newtaskgm")[0].childNodes[0].nodeValue;
				var new_language_act_sellprice_per = xmlDoc.getElementsByTagName("newlanguageaspp")[0].childNodes[0].nodeValue;
				var new_language_act_sellprice = xmlDoc.getElementsByTagName("newlanguageasp")[0].childNodes[0].nodeValue;
				var new_language_gm = xmlDoc.getElementsByTagName("newlanguagegm")[0].childNodes[0].nodeValue;
				var task_total_act_sellprice = xmlDoc.getElementsByTagName("tasktotalasp")[0].childNodes[0].nodeValue;
				var grand_total_act_sellprice = xmlDoc.getElementsByTagName("grandtotalasp")[0].childNodes[0].nodeValue;
				var grand_total_cost = xmlDoc.getElementsByTagName("grandtotalcost")[0].childNodes[0].nodeValue;
				var grand_total_gm = xmlDoc.getElementsByTagName("grandtotalgm")[0].childNodes[0].nodeValue;
				var new_pm_asp = xmlDoc.getElementsByTagName("newpmasp")[0].childNodes[0].nodeValue;
				var rushfeemarkup = xmlDoc.getElementsByTagName("rushfee")[0].childNodes[0].nodeValue;
				var discountType = xmlDoc.getElementsByTagName("discounttype")[0].childNodes[0].nodeValue;
				var discountPercent = xmlDoc.getElementsByTagName("discountpercent")[0].childNodes[0].nodeValue;
				var discountAmount = xmlDoc.getElementsByTagName("discountamount")[0].childNodes[0].nodeValue;

				grand_total_act_sellprice=grand_total_act_sellprice/1;

				if (rushfeemarkup != 0)
				{
					rushfee_amount = Math.round(grand_total_act_sellprice * rushfeemarkup*100)/100;
					
					grand_total_act_sellprice = grand_total_act_sellprice + rushfee_amount;
					grand_total_gm = ((grand_total_act_sellprice - grand_total_cost) / grand_total_act_sellprice);
					grand_total_gm = Math.round(grand_total_gm*10000)/100;
					
					document.getElementById("rushfee").innerHTML = rushfee_amount;
				}
				var discount =0;
				if (discountType == 'percent')
				{
					discount = Math.round(grand_total_act_sellprice * (discountPercent/100)*100)/100;
					grand_total_act_sellprice -= discount;
					grand_total_gm = ((grand_total_act_sellprice - grand_total_cost) / grand_total_act_sellprice);
					grand_total_gm = Math.round(grand_total_gm*10000)/100;
					
					document.getElementById("discount").innerHTML = discount;
				}
				else if(discountType == 'fixed')
				{
					grand_total_act_sellprice -= discountAmount;
					grand_total_gm = ((grand_total_act_sellprice - grand_total_cost) / grand_total_act_sellprice);
					grand_total_gm = Math.round(grand_total_gm*10000)/100;
					
					document.getElementById("discount").innerHTML = discountAmount;
				}
				
				//update the language table calculated sell price per unit
				var csppID = "csp-" + langID + "-" + taskID;
				document.getElementById(csppID).value = new_calculated_sellprice_per_word;

				
				//update the language table actual sell price
				var aspID = "asp-" + langID + "-" + taskID;
				document.getElementById(aspID).value = new_actual_sellprice;
				aspID = "SPT-" + langID + "-" + taskID;  //and since it's the same, update the sell price table's language asp
				document.getElementById(aspID).value = new_actual_sellprice;
				
				//update the language table gross margin
				var gmID = "GM-" + langID + "-" + taskID;
				document.getElementById(gmID).value = new_task_gm;
				
				//update the language table's sell price per WORD field
				var lang_aspp_ID = "cspTotal-" + langID;
				document.getElementById(lang_aspp_ID).value = new_language_act_sellprice_per;
				
				//update the language table actual sell price for the entire language
				var lang_asp_ID = "aspTotal-" + langID;
				document.getElementById(lang_asp_ID).value = new_language_act_sellprice;
				lang_asp_ID = "SPT_LTOTAL-" + langID;
				document.getElementById(lang_asp_ID).value = new_language_act_sellprice;
				
				
				//update the language table's language gross margin				
				var lang_total_gm_ID = "lang_total_GM-" + langID;				
				document.getElementById(lang_total_gm_ID).value = new_language_gm;
				//since it's the same, update the sell price table's gm for the language too
				lang_total_gm_ID = "GM-" + langID;
				document.getElementById(lang_total_gm_ID).innerHTML = new_language_gm;
				
				
				//update the sell price table's task total asp
				var spt_task_total_ID = "SPT_TTOTAL-" + taskID;
				document.getElementById(spt_task_total_ID).value = task_total_act_sellprice;
				
				//update the sell price table's grand total
				document.getElementById("SPT_GTOTAL").value = grand_total_act_sellprice;
				
				//update the grand total GM
				document.getElementById('GM_Grand_Total').innerHTML = grand_total_gm;
				
				
				
				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
		

	//make a random number to add to the url so the browser won't cache
	var myRandom=parseInt(Math.random()*99999999);
	
	var url="markup.php";
	url = url + "?markup=" + markupVal + "&lang=" + language;
	url = url + "&id=" + id ;
	url = url + "&key=" + myRandom;


	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}






function updateAddl(markupVal, language, id, langCount, totalWords)
{
	var ajaxRequest = getXMLHTTPRequest();
	
	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState == 4)
		{
			if(ajaxRequest.status == 200)
			{
				//get the data from the server's response
				var xmlDoc=ajaxRequest.responseXML.documentElement;
				
				var langValue = xmlDoc.getElementsByTagName("langCode")[0].childNodes[0].nodeValue;
				var idValue = xmlDoc.getElementsByTagName("idNum")[0].childNodes[0].nodeValue;
				var cspValue = xmlDoc.getElementsByTagName("CSP")[0].childNodes[0].nodeValue;
				var sppValue = xmlDoc.getElementsByTagName("SPP")[0].childNodes[0].nodeValue;
				var aspValue = xmlDoc.getElementsByTagName("ASP")[0].childNodes[0].nodeValue;
				var cspTotalValue = xmlDoc.getElementsByTagName("CSPTotal")[0].childNodes[0].nodeValue;
				var aspTotalValue = xmlDoc.getElementsByTagName("ASPTotal")[0].childNodes[0].nodeValue;
				var SPTLangValue = xmlDoc.getElementsByTagName("SPTLang")[0].childNodes[0].nodeValue;
				SPTLangValue = SPTLangValue / 1;
				var SPGT = xmlDoc.getElementsByTagName("SPGT")[0].childNodes[0].nodeValue;
				var langGM = xmlDoc.getElementsByTagName("GM")[0].childNodes[0].nodeValue;
				var languageCount = xmlDoc.getElementsByTagName("LANGCOUNT")[0].childNodes[0].nodeValue;
				

				//check for rush fees
				var rushFeeButton = document.getElementById('rushFees').value;
				switch(rushFeeButton)
				{
					case 'rf0': rushFee = 0;
								break;
					case 'rf25':	rushFee = 1.25;
									break;
					case 'rf50':	rushFee = 1.5;
									break;
					default:	rushFee = 0;
				}
				
				var cspID = "csp-" + langValue + "-" + idValue;
				document.getElementById(cspID).value = cspValue;
				
				//var sppID = "spp-" + langValue + "-" + idValue;
				//document.getElementById(sppID).value = sppValue;
				
				var aspID = "asp-" + langValue + "-" + idValue;
				var sptID = "SPT-" + langValue + "-" + idValue;
				document.getElementById(aspID).value = aspValue;
				document.getElementById(sptID).value = aspValue;
				
				var cspT_ID = "cspTotal-" + langValue;
				document.getElementById(cspT_ID).value = cspTotalValue;
				
			
				var sptlID = "SPT_TTOTAL-" + idValue;
				var currentSPT_TT = document.getElementById(sptlID).value;
				currentSPT_TT = replaceCommas(currentSPT_TT);
				document.getElementById(sptlID).value = SPTLangValue;
				
				//document.getElementById("SPT_GTOTAL").value = SPGT;
				
				//update the PM actual sell price, since it's based on all other prices
				var pmASP_ID = "asp-" + langValue + "-12";
				var pmMarkup_ID = "markup-" + langValue + "-12";
				var pmMarkup = document.getElementById(pmMarkup_ID).value;
				pmMarkup = pmMarkup/100;
				//need to get the current PM ASP to subtract from the Total ASP before the calc,
				//otherwise the numbers are skewed.
				var pmCASP = document.getElementById(pmASP_ID).value;
				
				var pmASP = Math.round(((aspTotalValue-pmCASP) / (1 - pmMarkup) * pmMarkup)*100)/100;
				document.getElementById(pmASP_ID).value = pmASP;
				
				//need to RE-update the total sell price because the PM has changed
				var aspT_ID = "aspTotal-" + langValue;
				var lang_total = "SPT_LTOTAL-" + langValue;
				aspTotalValue = aspTotalValue - pmCASP + pmASP;
				document.getElementById(aspT_ID).value = aspTotalValue;
				document.getElementById(lang_total).value = aspTotalValue;
			
				
				//update the sell price table PM asp
				var SPT_PM_ASP_ID = "SPT-" + langValue + "-12";
				document.getElementById(SPT_PM_ASP_ID).value = pmASP;
				
				//and update the spt PM total ASP too
				var newSPT_PM_TT = document.getElementById('SPT_TTOTAL-12').value;
				newSPT_PM_TT = newSPT_PM_TT - pmCASP + pmASP;
				document.getElementById('SPT_TTOTAL-12').value = newSPT_PM_TT;
				
				
				//update the PM gross margin
				var pmCost_ID = "cost-" + langValue + "-12";
				var pmCost = document.getElementById(pmCost_ID).value;
				var pmGM = Math.round(((pmASP - pmCost) / pmASP)*100);
				var pmGM_ID = "GM-" + langValue + "-12";
				document.getElementById(pmGM_ID).value = pmGM;
				
				var langGMID = "GM-" + langValue + "-" + idValue;
				document.getElementById(langGMID).value = langGM;
				
				var lang_total_gm_id = "lang_total_GM-" + langValue;
				var GMID = "GM-" + langValue;
				var costID = "costTotal-" + langValue;
				var costValue = document.getElementById(costID).value;
				var origLangGM = document.getElementById(GMID).innerHTML;
				//do some very odd looking calculations to get it to round to 2 decimal places.
				var value = (Math.round(((aspTotalValue-costValue)/aspTotalValue)*10000))/100;
				document.getElementById(lang_total_gm_id).value = value;
				document.getElementById(GMID).innerHTML = value;
				
				//update rush fees
				var orgGrandTotal = document.getElementById("SPT_GTOTAL").value;
				orgGrandTotal = replaceCommas(orgGrandTotal);
				var orgRushFee = document.getElementById("rushfee").innerHTML;
				orgRushFee = replaceCommas(orgRushFee);
				var newRushFee = 0;
			
				if (rushFee > 0)
				{

					newRushFee = (orgGrandTotal - orgRushFee - pmCASP - currentSPT_TT + SPTLangValue + pmASP) * (rushFee-1);
var string = "orgGrandTotal: " + orgGrandTotal + "\norgRushFee: " + orgRushFee;
string = string + "\npmCASP: " + pmCASP + "\ncurrentSPT_TT: " + currentSPT_TT;
string = string + "\nSPTLangValue: " + SPTLangValue + "\npmASP: " + pmASP;
alert(string);					
					document.getElementById("rushfee").innerHTML = newRushFee;
				}
				
				var GrandTotal = SP_GT - currentSPT_TT + SPTLangValue - pmCASP + pmASP - orgRushFee + newRushFee;
				document.getElementById("SPT_GTOTAL").value = GrandTotal;
				
				
				
				
				var totalCost = document.getElementById("totalCost").innerHTML;		
				totalCost = replaceCommas(totalCost);
				newTotalGM = ((GrandTotal - totalCost) / GrandTotal)*100;
				document.getElementById("GM_Grand_Total").innerHTML = newTotalGM;
				
				
				
				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	

	//make a random number to add to the url so the browser won't cache
	var myRandom=parseInt(Math.random()*99999999);
	var cspID = "csp-" + language + "-" + id;
	var csp = document.getElementById(cspID).value;
	
	var aspID = "asp-" + language + "-" + id;
	var asp = document.getElementById(aspID).value;
	var costID = "cost-" + language + "-" + id;
	var cost = document.getElementById(costID).value;
	var units = 1;
	var cspTotalID = "cspTotal-" + language;
	var cspTotal = document.getElementById(cspTotalID).value;
	var aspTotalID = "aspTotal-" + language;
	var aspTotal = document.getElementById(aspTotalID).value;
	var SPT_Lang_ID = "SPT_TTOTAL-" + id;
	var SPT_Lang = document.getElementById(SPT_Lang_ID).value;
	var SP_GT = document.getElementById("SPT_GTOTAL").value;
	
	
	var url="markup.php";
	url = url + "?markup=" + markupVal + "&lang=" + language;
	url = url + "&id=" + id + "&csp=" + csp;
	url = url + "&spp=" + csp + "&asp=" + asp;
	url = url + "&cost=" + cost + "&units=" + units;
	url = url + "&cspTotal=" + cspTotal + "&aspTotal=" + aspTotal;
	url = url + "&SPT-L=" + SPT_Lang + "&SP-GT=" +  SP_GT;
	url = url + "&langCount=" + langCount + "&totalWords=" + totalWords;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}*/