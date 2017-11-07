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

function replaceCommas(currString)
{
	//this function removes commas from strings

	var temp = new Array();
	var outstring = "";
	temp = currString.split(',');

	items = temp.length;

	for (var lcv=0; lcv < items; lcv++)
	{
		outstring = outstring + temp[lcv];

	}
	
	return outstring;

}

