// JavaScript Document

function getXMLHTTPRequest()
{
    var req = false;

    try
    {
        //Opera 8.0+, firefox, safari
        req = new XMLHttpRequest();
    } catch (e)
    {
        try
        {
            //internet explorer
            req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e)
        {
            try
            {
                req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e)
            {
                //something went wront
                alert("Your browser is not compatible with this website, please upgrade");
                return false;
            }
        }
    }

    return req;
}

function addNonLanguage(clientName)
{
    var ajaxRequest = getXMLHTTPRequest();

    ajaxRequest.onreadystatechange = function ()
    {
        if (ajaxRequest.readyState == 4)
        {
            if (ajaxRequest.status == 200)
            {
                var xmlDoc = ajaxRequest.responseXML.documentElement;
                var row = xmlDoc.getElementsByTagName('index')[0].childNodes[0].nodeValue;
                var _taskName = xmlDoc.getElementsByTagName('name')[0].childNodes[0].nodeValue;
                var taskName = _taskName.replace(/\#/g, " ");
                taskName = taskName.replace(/_/g, " ");
                var taskSubCat = xmlDoc.getElementsByTagName('subcat')[0].childNodes[0].nodeValue;
                taskSubCat = taskSubCat.replace(/_/g, " ");
                var taskRate = xmlDoc.getElementsByTagName('rate')[0].childNodes[0].nodeValue;
                var taskUnits = xmlDoc.getElementsByTagName('units')[0].childNodes[0].nodeValue;
                var clientName = xmlDoc.getElementsByTagName('client')[0].childNodes[0].nodeValue;

                if (xmlDoc.getElementsByTagName('result')[0].childNodes[0].nodeValue == "ERROR")
                {
                    alert(xmlDoc.getElementsByTagName('error')[0].childNodes[0].nodeValue);
                } else
                {

                    var _table = document.getElementById('non-language-tasks').insertRow(row);
                    var cell0 = _table.insertCell(0);
                    var cell1 = _table.insertCell(1);
                    var cell2 = _table.insertCell(2);
                    var cell3 = _table.insertCell(3);
                    var cell4 = _table.insertCell(4);

                    cell0.innerHTML = taskName;
                    if (taskSubCat != "none")
                    {
                        cell0.innerHTMl = cell0.innerHTML + " " + taskSubCat;
                    }
                    cell1.innerHTML = taskRate;
                    cell2.innerHTML = taskUnits;
                    var content = "<a href=\"edit-nonlanguage.php?client=" + clientName + "&name=" + taskName.replace(/ /g, "_").replace(/\+/g, "%2B") + "&sub=" + taskSubCat.replace(/ /g, "_");
                    content = content + "&rate=" + taskRate + "&unit=" + taskUnits + "\" ><img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a>";
                    cell3.innerHTML = content;
                    //return deleteNonLanguage( this.parentNode.parentNode, '", $temp_client, "', '", $temp_name, "', '", $temp_sub, "')
                    content = "<a href=\"#\" onClick=\"return deleteNonLanguage( this.parentNode.parentNode, '" + clientName.replace(/ /g, "_") + "', '";
                    content = content + taskName.replace(/ /g, "_") + "', '" + taskSubCat.replace(/ /g, "_") + "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a>";
                    cell4.innerHTML = content;
                }


            } else
            {
                //issue an error message
                alert("An error has occurred: " + ajaxRequest.statusText);
            }
        }

    }


    var myRandom = parseInt(Math.random() * 99999999);

    var _table = document.getElementById('non-language-tasks');
    var rowIndex = _table.rows.length - 1;

    var taskName = document.forms['addnonlanguage_form'].name.value;
    var taskRate = document.forms['addnonlanguage_form'].rate.value;
    var taskUnit = document.forms['addnonlanguage_form'].units.value;
    var subCat = document.forms['addnonlanguage_form'].subcat.value;

    var url = "ajax/addNonLanguage.php";
    url = url + "?index=" + rowIndex;
    url = url + "&clientname=" + clientName;
    url = url + "&taskname=" + taskName.replace(/\+/g, "%2B").replace(/\&/g, "%26").replace(/\//g, "%2F");
    url = url + "&taskrate=" + taskRate;
    url = url + "&taskunit=" + taskUnit;
    url = url + "&subcat=" + subCat;
    url = url + "&key=" + myRandom;

    //alert( url);
    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);


    return false;
}

function deleteNonLanguage(callingObj, client, task, subTask)
{
    var confirmDel = confirm("Are you sure you want to delete this task?");

    if (confirmDel == true)
    {
        var ajaxRequest = getXMLHTTPRequest();
        ajaxRequest.onreadystatechange = function ()
        {
            if (ajaxRequest.readyState == 4)
            {
                if (ajaxRequest.status == 200)
                {
                    //get the data from the server's response
                    var xmlDoc = ajaxRequest.responseXML.documentElement;
                    var errorFlag = xmlDoc.getElementsByTagName('error')[0].childNodes[0].nodeValue;
                    var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;

                    if (errorFlag == 'FALSE')
                    {
                        document.getElementById('non-language-tasks').deleteRow(row);

                    } else
                    {
                        var errorDesc = xmlDoc.getElementsByTagName('text')[0].childNodes[0].nodeValue;
                        alert("An error has occurred:\n" + errorDesc);
                    }


                } else
                {
                    //issue an error message
                    alert("An error has occurred: " + ajaxRequest.statusText);
                }
            }

        }

        var myRandom = parseInt(Math.random() * 99999999);
        var rowIndex = callingObj.rowIndex;

        var url = "delete-nonlanguage.php";
        url = url + "?row=" + rowIndex;
        url = url + "&client=" + client.replace(/\+/g, "%2B").replace(/ /g, "_");
        url = url + "&task=" + task;
        url = url + "&sub=" + subTask;
        url = url + "&key=" + myRandom;

        //alert(url);
        ajaxRequest.open("GET", url, true);
        ajaxRequest.send(null);



    }

    return false;

}


function addLanguageTask(clientName)
{
    var ajaxRequest = getXMLHTTPRequest();

    ajaxRequest.onreadystatechange = function ()
    {
        if (ajaxRequest.readyState == 4)
        {
            if (ajaxRequest.status == 200)
            {
                var xmlDoc = ajaxRequest.responseXML.documentElement;
                var clientName = xmlDoc.getElementsByTagName('client')[0].childNodes[0].nodeValue;

                if (xmlDoc.getElementsByTagName('result')[0].childNodes[0].nodeValue == "ERROR")
                {
                    alert(xmlDoc.getElementsByTagName('error')[0].childNodes[0].nodeValue);
                } else
                {

                    window.location = "customPricing.php?action=edit&target=" + clientName;

                }


            } else
            {
                //issue an error message
                alert("An error has occurred: " + ajaxRequest.statusText);
            }
        }

    }


    var myRandom = parseInt(Math.random() * 99999999);


    var taskName = document.forms['addlanguagetask_form'].name.value;
    var taskRate = document.forms['addlanguagetask_form'].rate.value;
    var taskUnit = document.forms['addlanguagetask_form'].units.value;
    var subCat = document.forms['addlanguagetask_form'].subcat2.value;
    var srcLang = document.forms['addlanguagetask_form'].sourcelang.value;
    var tgtLang = document.forms['addlanguagetask_form'].targetlang.value;

    var url = "ajax/addLanguageTask.php";
    url = url + "?clientname=" + clientName;
    url = url + "&taskname=" + taskName.replace(/\+/g, "%2B").replace(/\&/g, "%26").replace(/\//g, "%2F");
    url = url + "&taskrate=" + taskRate;
    url = url + "&taskunit=" + taskUnit;
    url = url + "&subcat=" + subCat;
    url = url + "&src=" + srcLang.replace(/ /g, "_");
    url = url + "&tgt=" + tgtLang.replace(/ /g, "_");
    url = url + "&key=" + myRandom;

    //alert( url);
    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);


    return false;
}


function deleteLanguageTask(callingObj, client, task, subTask, srcLang, tgtLang)
{
    var confirmDel = confirm("Are you sure you want to delete this task?");

    if (confirmDel == true)
    {
        var ajaxRequest = getXMLHTTPRequest();
        ajaxRequest.onreadystatechange = function ()
        {
            if (ajaxRequest.readyState == 4)
            {
                if (ajaxRequest.status == 200)
                {
                    //get the data from the server's response
                    var xmlDoc = ajaxRequest.responseXML.documentElement;
                    var errorFlag = xmlDoc.getElementsByTagName('error')[0].childNodes[0].nodeValue;
                    var row = xmlDoc.getElementsByTagName('row')[0].childNodes[0].nodeValue;
                    var tableID = xmlDoc.getElementsByTagName('tableid')[0].childNodes[0].nodeValue;

                    if (errorFlag == 'FALSE')
                    {
                        document.getElementById(tableID).deleteRow(row);

                    } else
                    {
                        var errorDesc = xmlDoc.getElementsByTagName('text')[0].childNodes[0].nodeValue;
                        alert("An error has occurred:\n" + errorDesc);
                    }


                } else
                {
                    //issue an error message
                    alert("An error has occurred: " + ajaxRequest.statusText);
                }
            }

        }

        var myRandom = parseInt(Math.random() * 99999999);
        var rowIndex = callingObj.rowIndex;

        var table = callingObj.parentNode.parentNode.id;

        var url = "delete-language.php";
        url = url + "?row=" + rowIndex;
        url = url + "&table=" + table;
        url = url + "&client=" + client.replace(/\+/g, "%2B").replace(/ /g, "_");
        url = url + "&task=" + task;
        url = url + "&sub=" + subTask;
        url = url + "&src=" + srcLang.replace(/ /g, "_");
        url = url + "&tgt=" + tgtLang.replace(/ /g, "_");
        url = url + "&key=" + myRandom;

        //alert(url);
        ajaxRequest.open("GET", url, true);
        ajaxRequest.send(null);



    }

    return false;

}