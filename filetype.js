// JavaScript Document

function getXMLHTTPRequest()
{
	var req = false;

	try
	{
		//Opera 8.0+, firefox, safari
		req = new XMLHttpRequest();
	}
	catch(e)
	{
		try
		{
			//internet explorer
			req = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				req = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
				//something went wront
				alert("Your browser is not compatible with this website, please upgrade");
				return false;
			}
		}
	}
	
	return req;
}

function changeFileType(docType)
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
				
				var pageRate = xmlDoc.getElementsByTagName("pagerate")[0].childNodes[0].nodeValue;
				
				document.getElementById("fmtPageHour").value = pageRate;
				
				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	var myRandom=parseInt(Math.random()*99999999);
	
	var url="filetype.php";
	url = url + "&filetype=" + docType;
	url = url + "&key=" + myRandom;
	
	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);

}