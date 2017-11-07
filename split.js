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
				
				if (costSell == "cost")
				{
					var costID = "cost-" + langValue + "-" + idValue;
					document.getElementById(costID).value = newCost;
				}
				
				document.getElementById("totalwords").innerHTML = totalWords;
				
				
				//call the update markup function to get the rest of the table to update
				updateMarkup(markup, langValue, idValue);
				
				
				
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
	
	
	var url="units.php";
	url = url + "?newUnits=" + newUnits + "&lang=" + language;
	url = url + "&id=" + id;
	url = url + "&costsell=" + costSell;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}


