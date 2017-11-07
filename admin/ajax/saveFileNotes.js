// JavaScript Document


function saveFileNotes(fileID, tableID)
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
					alert("An error has occurred trying to save your comments\n"+xmlDoc.getElementsByTagName('errorString')[0].childNodes[0].nodeValue);
				}
				else
				{
					fileNotes = xmlDoc.getElementsByTagName('fileNotes')[0].childNodes[0].nodeValue;
					fileID = xmlDoc.getElementsByTagName('fileID')[0].childNodes[0].nodeValue;
					tableID = xmlDoc.getElementsByTagName('tableName')[0].childNodes[0].nodeValue;
					updateDate = xmlDoc.getElementsByTagName('date')[0].childNodes[0].nodeValue;
					updateUser = xmlDoc.getElementsByTagName('userPretty')[0].childNodes[0].nodeValue;
					
					commentCellID = 'fileComment-'+fileID;
					commentActionID = 'fileCommentAction-'+fileID;
					fileDetailsID = 'fileDetails-'+fileID;
					document.getElementById(commentCellID).innerHTML = fileNotes;
					actionHTML = "<a href=\"#\" onClick=\"editFileNotes("+ fileID+", '"+tableID+"')\" class=\"breadcrumbs\">Edit</a>";
					document.getElementById(commentActionID).innerHTML = actionHTML;
					detailsHTML = "Uploaded by "+updateUser+" on "+updateDate;
					document.getElementById(fileDetailsID).innerHTML = detailsHTML;
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
	commentTextID = 'FileCommentText-'+fileID;
	var fileText = encodeURIComponent(document.getElementById(commentTextID).value);
		
	var url="ajax/saveFileNotes.php?rand="+myRandom+"&id="+fileID+"&cid="+tableID+"&text="+fileText;


	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	

}