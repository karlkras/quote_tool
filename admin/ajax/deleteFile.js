// JavaScript Document

function deleteFile(fileID,fileName,target)
{
	var confirmDelete = confirm("Are you sure you want to delete the file "+fileName+" permanently?");
	
	if (confirmDelete == true)
	{
		var ajaxRequest = getXMLHTTPRequest();
		
		ajaxRequest.onreadystatechange = function()
		{
			if(ajaxRequest.readyState == 4)
			{
				if(ajaxRequest.status == 200)
				{
					//get the data from the server's response
					var xmlDoc = ajaxRequest.responseXML.documentElement;
					var errorFlag = xmlDoc.getElementsByTagName('errorflag')[0].childNodes[0].nodeValue;
									
					
				
					if (errorFlag == "TRUE")
					{
						errorString = "There was an error deleting your file from the server\n\n";
						errorString += xmlDoc.getElementsByTagName('errorString')[0].childNodes[0].nodeValue;
						alert(errorString);
						
					}
					else
					{
						fileName = xmlDoc.getElementsByTagName('filename')[0].childNodes[0].nodeValue;
						target = xmlDoc.getElementsByTagName('target')[0].childNodes[0].nodeValue;
						target = target.substr(7, target.length); 
						
						successString = "Successfully removed "+fileName+" from the client\n";
						alert(successString);
						window.location.assign("customPricing.php?action=edit&target="+target)
						
					}
					
					
				}
				else
				{
					//issue an error message
					alert("An error has occurred: " + ajaxRequest.statusText);
				}
			}
			
		}
		var myRandom=parseInt(Math.random()*99999999);
					
		var url="ajax/deleteFile.php?rand="+myRandom+"&id="+fileID+"&target="+target;
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	else
	{
		return false;
	}
}