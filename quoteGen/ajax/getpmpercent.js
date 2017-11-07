// JavaScript Document


function getPMPercentFromDB(newValue)
{
    var ajaxRequest = getXMLHTTPRequest();

    ajaxRequest.onreadystatechange = function ()
    {
        if (ajaxRequest.readyState === 4)
        {
            if (ajaxRequest.status === 200)
            {
                //get the data from the server's response
                var xmlDoc = ajaxRequest.responseXML.documentElement;

                var pmPercent = xmlDoc.getElementsByTagName("percent")[0].childNodes[0].nodeValue;
                var custom = xmlDoc.getElementsByTagName("custom")[0].childNodes[0].nodeValue;

                if (custom === 'true')
                {
                    document.getElementById('pmCustom').innerHTML = 'Using Custom Price from Database';
                    document.getElementById('pmPercent').readOnly = true;
                    document.getElementById('pmPercent').className = 'customprice';
                    document.getElementById('customPM').value = 'true';
                } else
                {
                    document.getElementById('pmCustom').innerHTML = '';
                    document.getElementById('pmPercent').readOnly = false;
                    document.getElementById('pmPercent').className = '';
                    document.getElementById('customPM').value = 'false';
                }
                document.getElementById('pmPercent').value = pmPercent;



            } else {
                //issue an error message
                alert("An error has occurred: " + ajaxRequest.statusText);
            }
        }

    }

    //make a random number to add to the url so the browser won't cache
    var myRandom = parseInt(Math.random() * 99999999);

    var url = "ajax/getpmpercent.php";
    url = url + "?v=" + newValue;
    url = url + "&key=" + myRandom;

    //alert(url);
    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);
}

