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

function editDB(editType, sourceLang, targetLang)
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
				var newValue = xmlDoc.getElementsByTagName("updatedvalue")[0].childNodes[0].nodeValue;
				var editID = xmlDoc.getElementsByTagName("editid")[0].childNodes[0].nodeValue;
				
				
			
				if (errorFlag == "TRUE")
				{
					alert("Database could not be updated\nValue: "+ newValue);
				}
				else
				{
					document.getElementById(editID).innerHTML = newValue;
				}
				
				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	var temp = "ntr";
	switch(editType)
	{
		case "newtext": temp = "ntr"; break;
		case "fuzzy": temp = "fuz"; break;
		case "match": temp = "mat"; break;
		case "trans": temp = "trans"; break;
		case "proof": temp = "proof"; break;
	}
	
	
	var editID = temp + "-" + sourceLang + "-" + targetLang;
	var oldValue = 	document.getElementById(editID).innerHTML;
	
	var value= prompt("Please enter new value",oldValue);
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url="ajax/updatedb.php";
		url = url + "?value=" + value;
		url = url + "&editType=" + editType;
		url = url + "&editID=" + editID;
		url = url + "&sourceLang=" + sourceLang;
		url = url + "&targetLang=" + targetLang;
		url = url + "&key=" + myRandom;
		
		//alert(url);
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	
	return false;

}

function deleteDB(row, sourceLangID, targetLangID, sourceLangName, targetLangName)
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
				var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
				
				if (errorFlag == 'FALSE')
					document.getElementById('table1').deleteRow(row);

				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	var myRandom=parseInt(Math.random()*99999999);
		
	var url="ajax/delete.php";
	url = url + "?row=" + row;
	url = url + "&sourceLang=" + sourceLangID;
	url = url + "&targetLang=" + targetLangID;
	url = url + "&key=" + myRandom;
	
	var confirmDel= confirm("Are you sure you want to delete the {" + sourceLangName + " => " + targetLangName + "} language pair?");
	
	if (confirmDel == true)
	{
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}


function addRow(rowCount)
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
				var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
				
				row = row/1;

				var debug_log = xmlDoc.getElementsByTagName("debug");
					if (debug_log.length > 0)
					{
						alert(debug_log[0].childNodes[0].nodeValue);
					}
				
				if (errorFlag == 'FALSE')
				{
					var srclang = xmlDoc.getElementsByTagName('srclang')[0].childNodes[0].nodeValue;
					var srclangid = xmlDoc.getElementsByTagName('srclangid')[0].childNodes[0].nodeValue;
					var tgtlang = xmlDoc.getElementsByTagName('tgtlang')[0].childNodes[0].nodeValue;
					var tgtlangid = xmlDoc.getElementsByTagName('tgtlangid')[0].childNodes[0].nodeValue;
					var newtext = xmlDoc.getElementsByTagName('newtext')[0].childNodes[0].nodeValue;
					var fuzzytext = xmlDoc.getElementsByTagName('fuzzytext')[0].childNodes[0].nodeValue;
					var matchtext = xmlDoc.getElementsByTagName('matchtext')[0].childNodes[0].nodeValue;
					var transhourly = xmlDoc.getElementsByTagName('transhourly')[0].childNodes[0].nodeValue;
					var prhourly = xmlDoc.getElementsByTagName('prhourly')[0].childNodes[0].nodeValue;
					
					var _table = document.getElementById('table1').insertRow(row+1);
					var cell0 = _table.insertCell(0);
					var cell1 = _table.insertCell(1);
					var cell2 = _table.insertCell(2);
					var cell3 = _table.insertCell(3);
					var cell4 = _table.insertCell(4);
					var cell5 = _table.insertCell(5);
					var cell6 = _table.insertCell(6);
					var cell7 = _table.insertCell(7);
					cell0.innerHTML = srclang;
					cell1.innerHTML = tgtlang;
					cellString = "<span id=\"ntr-" + srclangid + "-" + tgtlangid +"\">" + newtext;
					cellString = cellString + "</span> <a href=\"#\" onClick=\"return editDB('newtext', '" + srclangid + "', '";
					cellString = cellString + tgtlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>";
					cell2.innerHTML = cellString;
					cellString = "<span id=\"fuz-" + srclangid + "-" + tgtlangid + "\">" + fuzzytext;
					cellString = cellString + "</span> <a href=\"#\" onClick=\"return editDB('fuzzy', '" + srclangid + "', '";
					cellString = cellString + tgtlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>";
					cell3.innerHTML = cellString;
					cellString = "<span id=\"mat-" + srclangid + "-" + tgtlangid + "\">" + matchtext;
					cellString = cellString + "</span> <a href=\"#\" onClick=\"return editDB('match', '" + srclangid + "', '";
					cellString = cellString + tgtlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>";
					cell4.innerHTML = cellString;
					cellString = "<span id=\"trans-" + srclangid + "-" + tgtlangid + "\">" + transhourly;
					cellString = cellString + "</span> <a href=\"#\" onClick=\"return editDB('trans', '" + srclangid + "', '";
					cellString = cellString + tgtlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>";
					cell5.innerHTML = cellString;
					cellString = "<span id=\"proof-" + srclangid + "-" + tgtlangid + "\">" + prhourly;
					cellString = cellString + "</span> <a href=\"#\" onClick=\"return editDB('proof', '" + srclangid + "', '";
					cellString = cellString + tgtlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>";
					cell6.innerHTML = cellString;
					cellString = "<a href=\"#\" onclick=\"return deleteDB('" + row+1;
					cellString = cellString + "', '" + srclangid + "', '" + tgtlangid + "', '" + srclang + "', '" + tgtlang +"');\">";
					cellString = cellString + "<img src=\"images/delete-icon.png\" alt=\"Delete\" title=\"Delete\" border=\"0\"></a>";
					cell7.innerHTML = cellString;
				}
				else
				{
					var errortext = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
					alert("An error has occurred: " + errortext);
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
	
	var srcLang = document.getElementById('sourceL').value;
	var tgtLang = document.getElementById('targetL').value;
	var newText = document.getElementById('newtext').value;
	var fuzzyText = document.getElementById('fuzzytext').value;
	var matchText = document.getElementById('matchtext').value;
	var transHourly = document.getElementById('transHourly').value;
	var prHourly = document.getElementById('prHourly').value;
		
	var url="ajax/add.php";
	url = url + "?row=" + rowCount;
	url = url + "&srcLang=" + srcLang;
	url = url + "&tgtLang=" + tgtLang;
	if (newText != "")
		url = url + "&newText=" + newText;
	if (fuzzyText != "")
		url = url + "&fuzzyText=" + fuzzyText;
	if (matchText != "")
		url = url + "&matchText=" + matchText;
	if (transHourly != "")
		url = url + "&transH=" + transHourly;
	if (prHourly != "")
		url = url + "&prH=" + prHourly;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);

	return false;
}

function editClientName(id)
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
				
				if (errorFlag == 'TRUE')
					alert("An error has occurred: Could not update database entry");
				else
				{
					var value = xmlDoc.getElementsByTagName('value')[0].childNodes[0].nodeValue;
					var id = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
					var cellid = "name-" + xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
					document.getElementById(cellid).innerHTML = value;
					
					var _nameSpan = document.getElementById(cellid);
					var _tableRow = _nameSpan.parentNode.parentNode;
					
					var _table = document.getElementById('table1');
					var _rowIndex = _tableRow.rowIndex;
					var _cell = _tableRow.cells[2];
					
					_cell.innerHTML = "<a href=\"#\" onClick=\"return deleteClient('" + _rowIndex + "', '" + id + "', '" + value + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
				}

				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	
	var elementID = "name-" + id;
	var name = document.getElementById(elementID).innerHTML;
	
	var value= prompt("Please enter new value",name);
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url = "ajax/editClient.php";
		var url = url + "?id=" + id + "&oldname=" + name;
		var url = url + "&newname=" + value;
		var url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function deleteClient(ourObject, clientID, clientName)
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
				var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;

				if (errorFlag == 'FALSE')
				{
					document.getElementById('table1').deleteRow(row);
					
				}
				else
				{
					var errorDesc = xmlDoc.getElementsByTagName('errordesc')[0].childNodes[0].nodeValue;
					alert("An error has occurred:\n" + errorDesc);
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
	var rowIndex = ourObject.rowIndex;
	
	var url="ajax/deleteClient.php";
	url = url + "?row=" + rowIndex;
	url = url + "&clientid=" + clientID;
	url = url + "&clientname=" + clientName;
	url = url + "&key=" + myRandom;
 	
	var confirmDel= confirm("Are you sure you want to delete " + clientName + "?");
	
	if (confirmDel == true)
	{
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function addClient()
{
	var clientName = document.getElementById('clientName').value;
	
	if (clientName != "")
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
					row = row/1;
					var clientName = xmlDoc.getElementsByTagName('clientname')[0].childNodes[0].nodeValue;
					
					if (errorFlag == 'FALSE')
					{
						var clientID = xmlDoc.getElementsByTagName('clientid')[0].childNodes[0].nodeValue;
						clientID = clientID/1;
						
						var _table = document.getElementById('table1').insertRow(row);
						var cell0 = _table.insertCell(0);
						var cell1 = _table.insertCell(1);
						var cell2 = _table.insertCell(2);
						
						cell0.innerHTML = "<span id=\"name-" + clientID + "\">" + clientName + "</span>";
						cell1.innerHTML = "<a href=\"#\" onClick=\"return editClientName('" + clientID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						
						cell2.innerHTML = "<a href=\"#\" onClick=\"return deleteClient(this.parentNode.parentNode.parentNode, '" + clientID + "', '" + clientName + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		
		var _table = document.getElementById('table1');
		var rowIndex = _table.rows.length - 1;
		
		
		var url="ajax/addClient.php";
		url = url + "?row=" + rowIndex;
		url = url + "&name=" + clientName;
		url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	else
	{
		alert('Client name is invalid. Please try again.');
	}
	

	return false;
}



function editIEName(id)
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
				
				if (errorFlag == 'TRUE')
					alert("An error has occurred: Could not update database entry");
				else
				{
					var value = xmlDoc.getElementsByTagName('updatedvalue')[0].childNodes[0].nodeValue;
					var id = xmlDoc.getElementsByTagName('editid')[0].childNodes[0].nodeValue;
					var cellid = "name-" + id;
					document.getElementById(cellid).innerHTML = value;
					
					
				}

				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	
	var elementID = "name-" + id;
	var name = document.getElementById(elementID).innerHTML;
	
	var value= prompt("Please enter new value",name);
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url = "ajax/InternalCost.php";
		var url = url + "?edittype=name";
		var url = url + "&id=" + id ; 
		var url = url + "&value=" + value;
		var url = url + "&key=" + myRandom;
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function editIERate(id)
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
				
				if (errorFlag == 'TRUE')
					alert("An error has occurred: Could not update database entry");
				else
				{
					var value = xmlDoc.getElementsByTagName('updatedvalue')[0].childNodes[0].nodeValue;
					var id = xmlDoc.getElementsByTagName('editid')[0].childNodes[0].nodeValue;
					var cellid = "rate-" + id;
					document.getElementById(cellid).innerHTML = value;
					
					
				}

				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	
	var elementID = "rate-" + id;
	var old = document.getElementById(elementID).innerHTML;
	
	var clean_string = old.replace("$","");
	clean_string = clean_string.replace(",","");
	
	var value= prompt("Please enter new value",clean_string );
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url = "ajax/InternalCost.php";
		var url = url + "?edittype=rate";
		var url = url + "&id=" + id ; 
		var url = url + "&value=" + value;
		var url = url + "&key=" + myRandom;
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function addInternalEffort()
{
	var taskName = document.getElementById('taskName').value;
	var hourlyRate = document.getElementById('hourlyRate').value;
	
	if ((taskName != "") || (hourlyRate != ""))
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
					row = row/1;
					var taskName = xmlDoc.getElementsByTagName('name')[0].childNodes[0].nodeValue;
					
					if (errorFlag == 'FALSE')
					{
						var ID = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
						ID = ID/1;
						
						var _table = document.getElementById('table1').insertRow(row);
						var cell0 = _table.insertCell(0);
						var cell1 = _table.insertCell(1);
						var cell2 = _table.insertCell(2);
						
						cell0.innerHTML = "<span id=\"name-" + ID + "\">" + taskName + "</span><a href=\"#\" onClick=\"return editIEName('" + ID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						cell1.innerHTML = "<span id=\"rate-" + ID + "\">" + xmlDoc.getElementsByTagName('rate')[0].childNodes[0].nodeValue; + "</span><a href=\"#\" onClick=\"return editIERate('" + ID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						cell2.innerHTML = "<a href=\"#\" onClick=\"return deleteInternalEffort(this.parentNode.parentNode, '" + ID + "', '" + taskName + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		
		var _table = document.getElementById('table1');
		var rowIndex = _table.rows.length - 1;
		
		
		var url="ajax/addInternalCost.php";
		url = url + "?row=" + rowIndex;
		url = url + "&name=" + taskName;
		url = url + "&rate=" + hourlyRate;
		url = url + "&key=" + myRandom;
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	else
	{
		alert('Entry is invalid. Please try again.');
	}
	

	return false;
}


function deleteInternalEffort(ourObject, ID, Name)
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
				var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;

				if (errorFlag == 'FALSE')
				{
					document.getElementById('table1').deleteRow(row);
					
				}
				else
				{
					var errorDesc = xmlDoc.getElementsByTagName('errordesc')[0].childNodes[0].nodeValue;
					alert("An error has occurred:\n" + errorDesc);
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
	var rowIndex = ourObject.rowIndex;
	
	var url="ajax/deleteInternalCost.php";
	url = url + "?row=" + rowIndex;
	url = url + "&id=" + ID;
	url = url + "&name=" + Name;
	url = url + "&key=" + myRandom;
 	
	var confirmDel= confirm("Are you sure you want to delete " + Name + "?");
	
	if (confirmDel == true)
	{
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}


function editContact(type, id)
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
				
				if (errorFlag == 'TRUE')
					alert("An error has occurred: Could not update database entry");
				else
				{
					var value = xmlDoc.getElementsByTagName('value')[0].childNodes[0].nodeValue;
					var id = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
					var type = xmlDoc.getElementsByTagName('type')[0].childNodes[0].nodeValue;
					
					var cellid = type + "-" + id;
					document.getElementById(cellid).innerHTML = value;
					
					
				}

				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	var elementID = "";
	switch(type)
	{
		case 'name':
			elementID =  "name-" + id; break;
		case 'title':
			elementID = "title-" + id; break;
		case 'phone':
			elementID = "phone-" + id; break;
		case 'email':
			elementID = "email-" + id; break;
	}
			
	var oldVal = document.getElementById(elementID).innerHTML;
	
	var value= prompt("Please enter new value",oldVal);
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url = "ajax/editContacts.php";
		var url = url + "?type=" + type;
		var url = url + "&id=" + id ; 
		var url = url + "&value=" + value;
		var url = url + "&key=" + myRandom;
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function deleteContact(ourObject, ID)
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
				var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;

				if (errorFlag == 'FALSE')
				{
					document.getElementById('table1').deleteRow(row);
					
				}
				else
				{
					var errorDesc = xmlDoc.getElementsByTagName('errordesc')[0].childNodes[0].nodeValue;
					alert("An error has occurred:\n" + errorDesc);
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
	var rowIndex = ourObject.rowIndex;
	
	var url="ajax/deleteContacts.php";
	url = url + "?row=" + rowIndex;
	url = url + "&id=" + ID;
	url = url + "&key=" + myRandom;
 	
	var nameID = "name-" + ID;
	var confirmDel= confirm("Are you sure you want to delete " + document.getElementById(nameID).innerHTML + "?");
	
	if (confirmDel == true)
	{
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function addContact()
{
	var name = document.getElementById('name').value;
	
	if (name != "")
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
					
					if (errorFlag == 'FALSE')
					{
						var ID = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
						ID = ID/1;
						var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
						row = row/1;
						var name = xmlDoc.getElementsByTagName('name')[0].childNodes[0].nodeValue;
						var email = xmlDoc.getElementsByTagName('email')[0].childNodes[0].nodeValue;
						var phone = xmlDoc.getElementsByTagName('phone')[0].childNodes[0].nodeValue;
						var title = xmlDoc.getElementsByTagName('title')[0].childNodes[0].nodeValue;

						
						var _table = document.getElementById('table1').insertRow(row);
						var cell0 = _table.insertCell(0);
						var cell1 = _table.insertCell(1);
						var cell2 = _table.insertCell(2);
						var cell3 = _table.insertCell(3);
						var cell4 = _table.insertCell(4);
						
						cell0.innerHTML = "<span id=\"name-" + ID + "\">" + name + "</span><a href=\"#\" onClick=\"return editContact('name', '" + ID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						cell1.innerHTML = "<span id=\"title-" + ID + "\">" + title + "</span><a href=\"#\" onClick=\"return editContact('title', '" + ID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						cell2.innerHTML = "<span id=\"phone-" + ID + "\">" + phone + "</span><a href=\"#\" onClick=\"return editContact('phone', '" + ID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						cell3.innerHTML = "<span id=\"email-" + ID + "\">" + email + "</span><a href=\"#\" onClick=\"return editContact('email', '" + ID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						cell4.innerHTML = "<a href=\"#\" onClick=\"return deleteContact(this.parentNode.parentNode, '" + ID + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		
		var _table = document.getElementById('table1');
		var rowIndex = _table.rows.length - 1;
		
		var email = document.getElementById('email').value;
		var phone = document.getElementById('phone').value;
		var title = document.getElementById('title').value;
		
		var url="ajax/addContact.php";
		url = url + "?row=" + rowIndex;
		url = url + "&name=" + name;
		url = url + "&email=" + email;
		url = url + "&phone=" + phone;
		url = url + "&title=" + title;
		url = url + "&key=" + myRandom;
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	else
	{
		alert('Entry is invalid. Please try again.');
	}
	

	return false;
}

function editCustomLinguistic(editType, clientID, sourceLang, targetLang)
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
				var newValue = xmlDoc.getElementsByTagName("updatedvalue")[0].childNodes[0].nodeValue;
				var editID = xmlDoc.getElementsByTagName("editid")[0].childNodes[0].nodeValue;
				
				
			
				if (errorFlag == "TRUE")
				{
					alert("Database could not be updated\nValue: "+ newValue + "\nQuery: " + xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue);
				}
				else
				{
					document.getElementById(editID).innerHTML = newValue;
				}
				
				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	var temp = "ntr";
	switch(editType)
	{
		case "newtext": temp = "ntr"; break;
		case "fuzzy": temp = "fuz"; break;
		case "match": temp = "mat"; break;
		case "trans": temp = "trans"; break;
		case "proof": temp = "proof"; break;
	}
	
	
	var editID = temp + "-" +clientID + "-" + sourceLang + "-" + targetLang;
	var oldValue = 	document.getElementById(editID).innerHTML;
	
	var value= prompt("Please enter new value.\nTo enter NO VALUE type \"null\" (without quotes)",oldValue);
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url="ajax/editCustLing.php";
		url = url + "?value=" + value;
		url = url + "&editType=" + editType;
		url = url + "&editID=" + editID;
		url = url + "&clientID=" + clientID;
		url = url + "&sourceLang=" + sourceLang;
		url = url + "&targetLang=" + targetLang;
		url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	
	return false;

}


function addCustLing()
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
				
				if (errorFlag == 'FALSE')
				{
					
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
					row = row/1;
					
					var clientid = xmlDoc.getElementsByTagName('clientid')[0].childNodes[0].nodeValue;
					var clientname = xmlDoc.getElementsByTagName('clientname')[0].childNodes[0].nodeValue;
					var sourcelangid = xmlDoc.getElementsByTagName('sourcelangid')[0].childNodes[0].nodeValue;
					var sourcename = xmlDoc.getElementsByTagName('sourcename')[0].childNodes[0].nodeValue;
					var targetlangid = xmlDoc.getElementsByTagName('targetlangid')[0].childNodes[0].nodeValue;
					var targetname = xmlDoc.getElementsByTagName('targetname')[0].childNodes[0].nodeValue;
					var newtextrate = xmlDoc.getElementsByTagName('newtextrate')[0].childNodes[0].nodeValue;
					var fuzzytextrate = xmlDoc.getElementsByTagName('fuzzytextrate')[0].childNodes[0].nodeValue;
					var matchtextrate = xmlDoc.getElementsByTagName('matchtextrate')[0].childNodes[0].nodeValue;
					var transhourly = xmlDoc.getElementsByTagName('transhourly')[0].childNodes[0].nodeValue;
					var prhourly = xmlDoc.getElementsByTagName('prhourly')[0].childNodes[0].nodeValue;

					
					var _table = document.getElementById('table1').insertRow(row);
					var cell0 = _table.insertCell(0);
					var cell1 = _table.insertCell(1);
					var cell2 = _table.insertCell(2);
					var cell3 = _table.insertCell(3);
					var cell4 = _table.insertCell(4);
					var cell5 = _table.insertCell(5);
					var cell6 = _table.insertCell(6);
					var cell7 = _table.insertCell(7);
					var cell8 = _table.insertCell(8);
					
					cell0.innerHTML = clientname;
					cell1.innerHTML = sourcename;
					cell2.innerHTML = targetname;
					cell3.innerHTML = "<span id=\"ntr-" + clientid + "-" + sourcelangid + "-" + targetlangid + "\">" + newtextrate + "</span> <a href=\"#\" onClick=\"return editCustomLinguistic('newtext', '" + clientid + "', '" + sourcelangid + "', '" + targetlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell4.innerHTML = "<span id=\"fuz-" + clientid + "-" + sourcelangid + "-" + targetlangid + "\">" + fuzzytextrate + "</span> <a href=\"#\" onClick=\"return editCustomLinguistic('fuzzy', '" + clientid + "', '" + sourcelangid + "', '" + targetlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell5.innerHTML = "<span id=\"mat-" + clientid + "-" + sourcelangid + "-" + targetlangid + "\">" + matchtextrate + "</span> <a href=\"#\" onClick=\"return editCustomLinguistic('match', '" + clientid + "', '" + sourcelangid + "', '" + targetlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell6.innerHTML = "<span id=\"trans-" + clientid + "-" + sourcelangid + "-" + targetlangid + "\">" + transhourly + "</span> <a href=\"#\" onClick=\"return editCustomLinguistic('trans', '" + clientid + "', '" + sourcelangid + "', '" + targetlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell7.innerHTML = "<span id=\"proof-" + clientid + "-" + sourcelangid + "-" + targetlangid + "\">" + prhourly + "</span> <a href=\"#\" onClick=\"return editCustomLinguistic('proof', '" + clientid + "', '" + sourcelangid + "', '" + targetlangid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell8.innerHTML = "<a href=\"#\" onClick=\"return deleteCustLing( this.parentNode.parentNode, '" + clientid + "', '" + sourcelangid + "', '" + targetlangid + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
					
				}
				else
				{
					var errorDesc = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
					alert("An error has occurred:\n" + errorDesc);
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
	
	var _table = document.getElementById('table1');
	var rowIndex = _table.rows.length - 1;
	
	var clientid = document.getElementById('Client').value;
	var sourceLangId = document.getElementById('sourceL').value;
	var targetLangId = document.getElementById('targetL').value;
	var newTextRate = document.getElementById('newtext').value;
	var fuzzyTextRate = document.getElementById('fuzzytext').value;
	var matchTextRate = document.getElementById('matchtext').value;
	var transHourly = document.getElementById('transHourly').value;
	var prHourly = document.getElementById('prHourly').value;
	
	var url="ajax/addCustLing.php";
	url = url + "?row=" + rowIndex;
	url = url + "&clientid=" + clientid;
	url = url + "&sourceLangId=" + sourceLangId;
	url = url + "&targetLangId=" + targetLangId;
	url = url + "&newTextRate=" + newTextRate;
	url = url + "&fuzzyTextRate=" + fuzzyTextRate;
	url = url + "&matchTextRate=" + matchTextRate;
	url = url + "&transHourly=" + transHourly;
	url = url + "&prHourly=" + prHourly;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	

	return false;
}

function deleteCustLing( callingObj, clientID, srcLangID, tgtLangID)
{
	var confirmDel= confirm("Are you sure you want to delete this entry?");
	
	if (confirmDel == true)
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
	
					if (errorFlag == 'FALSE')
					{
						document.getElementById('table1').deleteRow(row);
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errordesc')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		var rowIndex = callingObj.rowIndex;
		
		var url="ajax/deleteCustLing.php";
		url = url + "?row=" + rowIndex;
		url = url + "&tgtlangid=" + tgtLangID;
		url = url + "&srclangid=" + srcLangID;
		url = url + "&clientid=" + clientID;
		url = url + "&key=" + myRandom;
 	
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function editCustomInternal(editType, clientID)
{
	
	var editID = editType + "-" +clientID;
	var oldValue = 	document.getElementById(editID).innerHTML;
	
	var value= prompt("Please enter new value.\nTo enter NO VALUE type \"null\" (without quotes)",oldValue);
	
	if (value != null)
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
					var newValue = xmlDoc.getElementsByTagName("updatedvalue")[0].childNodes[0].nodeValue;
					var editID = xmlDoc.getElementsByTagName("editid")[0].childNodes[0].nodeValue;
					
					
				
					if (errorFlag == "TRUE")
					{
						alert("Database could not be updated\nValue: "+ newValue + "\nQuery: " + xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue);
					}
					else
					{
						document.getElementById(editID).innerHTML = newValue;
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
		
		var url="ajax/editCustInt.php";
		url = url + "?value=" + value;
		url = url + "&editType=" + editType;
		url = url + "&clientID=" + clientID;
		url = url + "&editID=" + editID;
		url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	
	return false;

}

function deleteCustInt( callingObj, clientID)
{
	var confirmDel= confirm("Are you sure you want to delete this entry?");
	
	if (confirmDel == true)
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
	
					if (errorFlag == 'FALSE')
					{
						document.getElementById('table1').deleteRow(row);
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errordesc')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		var rowIndex = callingObj.rowIndex;
		
		var url="ajax/deleteCustInt.php";
		url = url + "?row=" + rowIndex;
		url = url + "&clientid=" + clientID;
		url = url + "&key=" + myRandom;
 	
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function addCustInt()
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
				
				if (errorFlag == 'FALSE')
				{
					
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
					row = row/1;
					
					var clientid = xmlDoc.getElementsByTagName('clientid')[0].childNodes[0].nodeValue;
					var clientname = xmlDoc.getElementsByTagName('clientname')[0].childNodes[0].nodeValue;
					var dtp = xmlDoc.getElementsByTagName('dtp')[0].childNodes[0].nodeValue;
					var engineering = xmlDoc.getElementsByTagName('engineering')[0].childNodes[0].nodeValue;
					var seniorengineering = xmlDoc.getElementsByTagName('seniorengineering')[0].childNodes[0].nodeValue;
					var qa = xmlDoc.getElementsByTagName('qa')[0].childNodes[0].nodeValue;
					var pmpercent = xmlDoc.getElementsByTagName('pmpercent')[0].childNodes[0].nodeValue;
					var pmhourly = xmlDoc.getElementsByTagName('pmhourly')[0].childNodes[0].nodeValue;
					
					
					var _table = document.getElementById('table1').insertRow(row);
					var cell0 = _table.insertCell(0);
					var cell1 = _table.insertCell(1);
					var cell2 = _table.insertCell(2);
					var cell3 = _table.insertCell(3);
					var cell4 = _table.insertCell(4);
					var cell5 = _table.insertCell(5);
					var cell6 = _table.insertCell(6);
					var cell7 = _table.insertCell(7);
										
					cell0.innerHTML = "<td>" + clientname + "</td>";
					cell1.innerHTML = "<span id=\"dtp-" + clientid + "\">" + dtp + "</span> <a href=\"#\" onClick=\"return editCustomInternal('dtp', '" + clientid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell2.innerHTML = "<span id=\"eng-" + clientid + "\">" + engineering + "</span> <a href=\"#\" onClick=\"return editCustomInternal('eng', '" + clientid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell3.innerHTML = "<span id=\"senEng-" + clientid + "\">" + seniorengineering + "</span> <a href=\"#\" onClick=\"return editCustomInternal('senEng', '" + clientid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell4.innerHTML = "<span id=\"qa-" + clientid + "\">" + qa + "</span> <a href=\"#\" onClick=\"return editCustomInternal('qa', '" + clientid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell5.innerHTML = "<span id=\"percent-" + clientid + "\">" + pmpercent + "</span> <a href=\"#\" onClick=\"return editCustomInternal('percent', '" + clientid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					cell6.innerHTML = "<span id=\"hourly-" + clientid + "\">" + pmhourly + "</span> <a href=\"#\" onClick=\"return editCustomInternal('hourly', '" + clientid + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
					
					cell7.innerHTML = "<a href=\"#\" onClick=\"return deleteCustInt( this.parentNode.parentNode, '" + clientid + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
					
				}
				else
				{
					var errorDesc = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
					alert("An error has occurred:\n" + errorDesc);
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
	
	var _table = document.getElementById('table1');
	var rowIndex = _table.rows.length - 1;
	
	var clientid = document.getElementById('Client').value;
	var dtp = document.getElementById('dtp').value;
	var engineering = document.getElementById('engineering').value;
	var seniorengineering = document.getElementById('seniorengineering').value;
	var qa = document.getElementById('qa').value;
	var pmpercent = document.getElementById('pmpercent').value;
	var pmhourly = document.getElementById('pmhourly').value;
	
	var url="ajax/addCustInt.php";
	url = url + "?row=" + rowIndex;
	url = url + "&clientid=" + clientid;
	url = url + "&dtp=" +dtp;
	url = url + "&engineering=" +engineering;
	url = url + "&seniorengineering=" +seniorengineering;
	url = url + "&qa=" +qa;
	url = url + "&pmpercent=" +pmpercent;
	url = url + "&pmhourly=" +pmhourly;
	url = url + "&key=" + myRandom;

	ajaxRequest.open("GET", url, true);
	ajaxRequest.send(null);
	

	return false;
}

function addSource()
{
	var langName = document.getElementById('language').value;
	
	if (langName != "")
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
					row = row/1;
					var langName = xmlDoc.getElementsByTagName('name')[0].childNodes[0].nodeValue;
					
					if (errorFlag == 'FALSE')
					{
						var langID = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
												
						var _table = document.getElementById('table1').insertRow(row);
						var cell0 = _table.insertCell(0);
						var cell1 = _table.insertCell(1);
						var cell2 = _table.insertCell(2);
						
						cell0.innerHTML = "<span id=\"name-" + langID + "\">" + langName + "</span>";
						cell1.innerHTML = "<a href=\"#\" onClick=\"return editSource('" + langID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						
						cell2.innerHTML = "<a href=\"#\" onClick=\"return deleteSource(this.parentNode.parentNode, '" + langID  + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		
		var _table = document.getElementById('table1');
		var rowIndex = _table.rows.length - 1;
		
		
		var url="ajax/addSource.php";
		url = url + "?row=" + rowIndex;
		url = url + "&name=" + langName;
		url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	else
	{
		alert('Client name is invalid. Please try again.');
	}
	

	return false;
}

function editSource(id)
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
				
				if (errorFlag == 'TRUE')
					alert("An error has occurred\n" + xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue);
				else
				{
					var value = xmlDoc.getElementsByTagName('value')[0].childNodes[0].nodeValue;
					var id = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
					var cellid = "name-" + xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
					document.getElementById(cellid).innerHTML = value;
					
					
				}

				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	
	var elementID = "name-" + id;
	var name = document.getElementById(elementID).innerHTML;
	
	var value= prompt("Please enter new value",name);
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url = "ajax/editSource.php";
		var url = url + "?id=" + id;
		var url = url + "&newname=" + value;
		var url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function deleteSource(callingObject, ID)
{
	var nameID = "name-" + ID;
	var confirmDel= confirm("Are you sure you want to delete " + document.getElementById(nameID).innerHTML + "?");
	
	if (confirmDel == true)
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
	
					if (errorFlag == 'FALSE')
					{
						document.getElementById('table1').deleteRow(row);
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errordesc')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		var rowIndex = callingObject.rowIndex;
		
		var url="ajax/deleteSource.php";
		url = url + "?row=" + rowIndex;
		url = url + "&id=" + ID;
		url = url + "&key=" + myRandom;
 	
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function editTarget(id)
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
				
				if (errorFlag == 'TRUE')
					alert("An error has occurred\n" + xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue);
				else
				{
					var value = xmlDoc.getElementsByTagName('value')[0].childNodes[0].nodeValue;
					var id = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
					var cellid = "name-" + xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
					document.getElementById(cellid).innerHTML = value;
					
					
				}

				
			}
			else
			{
				//issue an error message
				alert("An error has occurred: " + ajaxRequest.statusText);
			}
		}
		
	}
	
	
	var elementID = "name-" + id;
	var name = document.getElementById(elementID).innerHTML;
	
	var value= prompt("Please enter new value",name);
	
	if (value != null)
	{
		var myRandom=parseInt(Math.random()*99999999);
		
		var url = "ajax/editTarget.php";
		var url = url + "?id=" + id;
		var url = url + "&newname=" + value;
		var url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}

function addTarget()
{
	var langName = document.getElementById('language').value;
	
	if (langName != "")
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
					row = row/1;
					var langName = xmlDoc.getElementsByTagName('name')[0].childNodes[0].nodeValue;
					
					if (errorFlag == 'FALSE')
					{
						var langID = xmlDoc.getElementsByTagName('id')[0].childNodes[0].nodeValue;
												
						var _table = document.getElementById('table1').insertRow(row);
						var cell0 = _table.insertCell(0);
						var cell1 = _table.insertCell(1);
						var cell2 = _table.insertCell(2);
						
						cell0.innerHTML = "<span id=\"name-" + langID + "\">" + langName + "</span>";
						cell1.innerHTML = "<a href=\"#\" onClick=\"return editTarget('" + langID + "');\"><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
						
						cell2.innerHTML = "<a href=\"#\" onClick=\"return deleteTarget(this.parentNode.parentNode, '" + langID + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errortext')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		
		var _table = document.getElementById('table1');
		var rowIndex = _table.rows.length - 1;
		
		
		var url="ajax/addTarget.php";
		url = url + "?row=" + rowIndex;
		url = url + "&name=" + langName;
		url = url + "&key=" + myRandom;
		
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}
	else
	{
		alert('Client name is invalid. Please try again.');
	}
	

	return false;
}

function deleteTarget(callingObject, ID)
{
	var nameID = "name-" + ID;
	var confirmDel= confirm("Are you sure you want to delete " + document.getElementById(nameID).innerHTML + "?");
	
	if (confirmDel == true)
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
					var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
	
					if (errorFlag == 'FALSE')
					{
						document.getElementById('table1').deleteRow(row);
						
					}
					else
					{
						var errorDesc = xmlDoc.getElementsByTagName('errordesc')[0].childNodes[0].nodeValue;
						alert("An error has occurred:\n" + errorDesc);
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
		var rowIndex = callingObject.rowIndex;
		
		var url="ajax/deleteTarget.php";
		url = url + "?row=" + rowIndex;
		url = url + "&id=" + ID;
		url = url + "&key=" + myRandom;
 	
	
		ajaxRequest.open("GET", url, true);
		ajaxRequest.send(null);
	}

	return false;
}