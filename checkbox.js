// JavaScript Document



function changeClick(langId, taskId)
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
				var newValue = xmlDoc.getElementsByTagName("newvalue")[0].childNodes[0].nodeValue;
				

				
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
	
	var checkboxID = "print-" + langId + "-" + taskId;
	var newValue = document.getElementById(checkboxID).checked;
	
	var url="checkbox.php";
	url = url + "?lang=" + langId;
	url = url + "&id=" + taskId;
	url = url + "&value=" + newValue;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	
}


