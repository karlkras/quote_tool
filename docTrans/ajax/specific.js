// JavaScript Document


function checkSpecific(newIdValue)
{
    var ajaxCompletedState = 4;
    var ajaxStatusSuccess = 200;
    //check to see if they really want to override
    var ajaxRequest = getXMLHTTPRequest();

    ajaxRequest.onreadystatechange = function ()
    {
        if (ajaxRequest.readyState === ajaxCompletedState)
        {
            if (ajaxRequest.status === ajaxStatusSuccess)
            {
                document.getElementById('scheme').style.display = 'none';
                //get the data from the server's response
                var xmlDoc = ajaxRequest.responseXML.documentElement;

                var showOptions = xmlDoc.getElementsByTagName("showoptions")[0].childNodes[0].nodeValue;


                if (showOptions === "true")
                {
                    document.getElementById('clientspecific_div').style.display = 'inline';
                }
                else
                {
                    document.getElementById('clientspecific_div').style.display = 'none';
                }
            }
            else
            {
                //issue an error message
                alert("An error has occurred: " + ajaxRequest.statusText);
            }
        }
        else
        {
            document.getElementById('scheme').style.display = 'inline';
            document.getElementById('scheme').innerHTML = "<img src=\"ajax/ajax-loader.gif\">";
        }
    };

    //make a random number to add to the url so the browser won't cache
    var myRandom = parseInt(Math.random() * 99999999);

    var url = "ajax/specific.php";
    url = url + "?v=" + newIdValue;
    url = url + "&key=" + myRandom;

    //alert(url);
    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);
    return true;
}

