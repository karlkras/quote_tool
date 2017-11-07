// JavaScript Document



function editNotes(noteID, tableName)
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
					alert(xmlDoc.getElementsByTagName('errorString')[0].childNodes[0].nodeValue);
				}
				else
				{
					clientNotes = xmlDoc.getElementsByTagName('userNotes')[0].childNodes[0].nodeValue;
					noteID = xmlDoc.getElementsByTagName('noteID')[0].childNodes[0].nodeValue;
					tableID = xmlDoc.getElementsByTagName('tableName')[0].childNodes[0].nodeValue;
					document.getElementById("commentCell").innerHTML = "<textarea id=\"commentText\" style=\"width:375px;height:75px\">"+clientNotes+"</textarea>";
					actionHTML = "<a href=\"#\" onClick=\"saveNotes("+ noteID+", '"+tableID+"')\" class=\"breadcrumbs\">Save</a>";
					document.getElementById("commentAction").innerHTML = actionHTML;
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
		
	var url="ajax/editNotes.php?rand="+myRandom+"&id="+noteID+"&cid="+tableName;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	

}