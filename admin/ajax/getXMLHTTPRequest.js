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