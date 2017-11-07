// JavaScript Document


function saveNotes(noteID, tableID)
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
					updateDate = xmlDoc.getElementsByTagName('date')[0].childNodes[0].nodeValue;
					updateUser = xmlDoc.getElementsByTagName('userPretty')[0].childNodes[0].nodeValue;
					
					document.getElementById("commentCell").innerHTML = clientNotes;
					actionHTML = "<a href=\"#\" onClick=\"editNotes("+ noteID+", '"+tableID+"')\" class=\"breadcrumbs\">Edit</a>";
					document.getElementById("commentAction").innerHTML = actionHTML;
					detailsHTML = "Comment by: "+updateUser+" on "+updateDate;
					document.getElementById("commentDetails").innerHTML = detailsHTML;
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
	var noteText = encodeURIComponent(document.getElementById('commentText').value);
		
	var url="ajax/saveNotes.php?rand="+myRandom+"&id="+noteID+"&cid="+tableID+"&text="+noteText;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	

}