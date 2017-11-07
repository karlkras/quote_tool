// JavaScript Document







function updateSellPrice(priceVal, language, id, langCount, totalWords)
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
				
				//check for rush fees
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
				
				//update the markup field
				var markupID = "markup-" + langID + "-" + taskID;
				document.getElementById(markupID).value = markup;
				
				//update the language table calculated sell price per unit
				var csppID = "csp-" + langID + "-" + taskID;
				document.getElementById(csppID).value = new_calculated_sellprice_per_word;
				
				//update the language table actual sell price per unit
				var asppID = "spp-" + langID + "-" + taskID;
				document.getElementById(asppID).value = new_actual_sellprice_per_word;
				
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
	



	

	var url="sellprice.php";
	url = url + "?spp=" + priceVal + "&lang=" + language + "&id=" + id;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);

}