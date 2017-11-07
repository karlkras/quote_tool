// JavaScript Document

function checkScheme(newIdValue)
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
                //get the data from the server's response
                var xmlDoc = ajaxRequest.responseXML.documentElement;

                var showOptions = xmlDoc.getElementsByTagName("showoptions")[0].childNodes[0].nodeValue;


                if (showOptions === "true")
                {
                    document.getElementById('scheme').style.display = 'inline';
                }
                else
                {
                    document.getElementById('scheme').style.display = 'none';
                }
                document.getElementById('scheme').innerHTML = "Pricing scheme to apply: <select name=\"priceScheme\" id=\"priceScheme\">\n\t<option value=\"none\">None</option>\n\t<option value=\"LLS\">LLS Pricing</option>\n\t<option value=\"50\">50% Margin</option>\n\t<option value=\"40\">40% Margin</option>\n</select>";

                var scheme = xmlDoc.getElementsByTagName("scheme")[0].childNodes[0].nodeValue;
                var selectObj = document.getElementById('priceScheme');
                if (scheme === 'LLS Pricing')
                {
                    for (var i = 0; i < 3; i++)
                    {
                        if (selectObj.options[i].value === 'LLS')
                        {
                            selectObj.options[i].selected = true;
                        }
                    }
                }
                if (scheme === '50% Margin')
                {
                    for (var i = 0; i < 3; i++)
                    {
                        if (selectObj.options[i].value === '50')
                        {
                            selectObj.options[i].selected = true;
                        }
                    }
                }
                if (scheme === '40% Margin')
                {
                    for (var i = 0; i < 3; i++)
                    {
                        if (selectObj.options[i].value === '40')
                        {
                            selectObj.options[i].selected = true;
                        }
                    }
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
            document.getElementById('scheme').innerHTML = "&nbsp;<img src=\"ajax/ajax-loader.gif\">";
        }

    };

    //make a random number to add to the url so the browser won't cache
    var myRandom = parseInt(Math.random() * 99999999);

    var url = "ajax/scheme.php";
    url = url + "?v=" + newIdValue;
    url = url + "&key=" + myRandom;

    //alert(url);
    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);

    return true;


}

