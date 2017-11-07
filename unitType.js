// JavaScript Document



function changeUnitType(newUnitType, costSell, language, id)
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
				var newUnits = xmlDoc.getElementsByTagName("newunits")[0].childNodes[0].nodeValue;
				var newCostPer = xmlDoc.getElementsByTagName("newcostper")[0].childNodes[0].nodeValue;
				var costSell = xmlDoc.getElementsByTagName("costsell")[0].childNodes[0].nodeValue;
				var newType = xmlDoc.getElementsByTagName("newtype")[0].childNodes[0].nodeValue;

				if (newType == "Pages")
				{
					newCostPer = prompt("Please enter price per page:", newCostPer);
				}

				
				var unitID = costSell + "Units-" + langValue + "-" + idValue;
				document.getElementById(unitID).value = newUnits;
				
				var costPerID = costSell + "per-" + langValue + "-" + idValue;
				document.getElementById(costPerID).value = newCostPer;
				
				//check if we're using a custom price
				var uses_custom = xmlDoc.getElementsByTagName("usescustom");
				if (uses_custom.length > 0)
				{
					var markupID = "markup-" + langValue + "-" + idValue;
					document.getElementById(markupID).readOnly = true;
					document.getElementById(markupID).setAttribute("class", "noneditable");
					var aspID = "spp-" + langValue + "-" + idValue;
					document.getElementById(aspID).readOnly = false;
					document.getElementById(aspID).setAttribute("class", "customprice");
				}
				else
				{
					var markupID = "markup-" + langValue + "-" + idValue;
					document.getElementById(markupID).readOnly = false;
					document.getElementById(markupID).setAttribute("class", "");
					var aspID = "spp-" + langValue + "-" + idValue;
					document.getElementById(aspID).readOnly = false;
					document.getElementById(aspID).setAttribute("class", "");
				}
				
				
				//call the update units function to get the rest of the table to update
				updateUnits(newUnits, costSell, langValue, idValue);
				
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
	
	
	var url="unitType.php";
	url = url + "?newType=" + newUnitType + "&lang=" + language;
	url = url + "&costSell=" + costSell;
	url = url + "&id=" + id;
	url = url + "&key=" + myRandom;
	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}


