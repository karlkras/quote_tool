// JavaScript Document



function updateUnits(newUnits, costSell, language, id)
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
				var newCost = xmlDoc.getElementsByTagName("newcost")[0].childNodes[0].nodeValue;
				var markup = xmlDoc.getElementsByTagName("markup")[0].childNodes[0].nodeValue;
				var totalWords = xmlDoc.getElementsByTagName("totalwords")[0].childNodes[0].nodeValue;
				var costSell = xmlDoc.getElementsByTagName("costsell")[0].childNodes[0].nodeValue;
				var sellunits = xmlDoc.getElementsByTagName("sellunits")[0].childNodes[0].nodeValue;
				var splitUnits = xmlDoc.getElementsByTagName("split")[0].childNodes[0].nodeValue;
				var task_total_cost = xmlDoc.getElementsByTagName("tasktotalcost")[0].childNodes[0].nodeValue;
				var grand_total_cost = xmlDoc.getElementsByTagName("grandtotalcost")[0].childNodes[0].nodeValue;

				if (costSell == "cost")
				{
					var costID = "cost-" + langValue + "-" + idValue;
					document.getElementById(costID).value = newCost;
					langCostID = "CT-" + langValue + "-" + idValue;
					document.getElementById(langCostID).innerHTML = newCost;
					
					var langCostID = "costTotal-" + langValue;
					document.getElementById(langCostID).value = xmlDoc.getElementsByTagName("langcost")[0].childNodes[0].nodeValue;
					langCostID = "langCostTotal-" + langValue;
					document.getElementById(langCostID).innerHTML = xmlDoc.getElementsByTagName("langcost")[0].childNodes[0].nodeValue;
					
					
					if ((idValue < 3) && (splitUnits == "true"))
					{
						var sellUnitID = "sellUnits-" + langValue + "-" + idValue;
						document.getElementById(sellUnitID).value = sellunits;
					}
					
					var taskCostID = "CT_TTOTAL-" + idValue;
					document.getElementById(taskCostID).innerHTML = task_total_cost;
					
					document.getElementById('totalCost').innerHTML = grand_total_cost;
					
				}
				
				document.getElementById("totalwords").innerHTML = totalWords;
				
				
				//call the update markup function to get the rest of the table to update
				updateMarkup(markup, langValue, idValue);
				
				
				
				//if we're doing a linguistic unit update then check to see if the cost is below the 1 hour minimum
				if ( (idValue <=2))
				{
					var below_min = xmlDoc.getElementsByTagName("belowminimum");
					if (below_min.length > 0)
					{
						alert("Warning: Linguistic cost is below 1 hour minimum rate!");
					}
					
				}
				
				
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
	if (typeof(newUnits) == 'string')	
		newUnits = replaceCommas(newUnits);
	
	var url="units.php";
	url = url + "?newUnits=" + newUnits + "&lang=" + language;
	url = url + "&id=" + id;
	url = url + "&costsell=" + costSell;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}


