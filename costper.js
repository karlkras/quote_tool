// JavaScript Document



function updateCostPer(newValue, langID, rowID)
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

				var langID = xmlDoc.getElementsByTagName("langCode")[0].childNodes[0].nodeValue;
				var taskID = xmlDoc.getElementsByTagName("idNum")[0].childNodes[0].nodeValue;
				var newunits = xmlDoc.getElementsByTagName("newunits")[0].childNodes[0].nodeValue;
				
				updateUnits(newunits, 'cost', langID, taskID)
				

				
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
	
	var url="costper.php";
	url = url + "?lang=" + langID;
	url = url + "&id=" + rowID;
	url = url + "&value=" + newValue;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}


