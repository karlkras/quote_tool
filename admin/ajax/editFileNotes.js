// JavaScript Document



function editFileNotes(fileID, tableName)
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
					fileNotes = xmlDoc.getElementsByTagName('fileNotes')[0].childNodes[0].nodeValue;
					fileID = xmlDoc.getElementsByTagName('fileID')[0].childNodes[0].nodeValue;
					tableID = xmlDoc.getElementsByTagName('tableName')[0].childNodes[0].nodeValue;
					commentCellID = 'fileComment-'+fileID;
					commentActionID = 'fileCommentAction-'+fileID;
					document.getElementById(commentCellID).innerHTML = "<textarea id=\"FileCommentText-"+fileID+"\" style=\"width:375px;height:75px\">"+fileNotes+"</textarea>";
					actionHTML = "<a href=\"#\" onClick=\"saveFileNotes("+ fileID+", '"+tableID+"')\" class=\"breadcrumbs\">Save</a>";
					document.getElementById(commentActionID).innerHTML = actionHTML;
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
		
	var url="ajax/editFileNotes.php?rand="+myRandom+"&id="+fileID+"&cid="+tableName;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	

}