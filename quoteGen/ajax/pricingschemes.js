// JavaScript Document

function changeScheme()
{
    var ajaxCompletedState = 4;
    var ajaxStatusSuccess = 200;
    var ajaxRequest = getXMLHTTPRequest();

    ajaxRequest.onreadystatechange = function ()
    {
        if (ajaxRequest.readyState === ajaxCompletedState)
        {
            if (ajaxRequest.status === ajaxStatusSuccess)
            {
                document.getElementById('schemecell').innerHTML = '&nbsp;';
                //get the data from the server's response
                var xmlDoc = ajaxRequest.responseXML.documentElement;

                var error = xmlDoc.getElementsByTagName("error")[0].childNodes[0].nodeValue;
                var errString = xmlDoc.getElementsByTagName("errstring")[0].childNodes[0].nodeValue;

                if (error === 'true')
                {
                    alert('An error has occurred during the AJAX call to change pricing scheme.\nPlease contact support.\n\nMessage: ' + errString);

                }
                else
                {
                    var schemeApplied = xmlDoc.getElementsByTagName("schemeapplied")[0].childNodes[0].nodeValue;
                    document.getElementById('schemecell').innerHTML = schemeApplied;
                    var schemeType = "";
                    switch(schemeApplied){
                        case "No client pricing found":
                            schemeType = "Margin Pricing";
                            break;
                        case "LLS Pricing":
                        case "Healthcare List Pricing":
                            schemeType = schemeApplied;
                            break;
                        default:
                            schemeType = "Client-Specific Pricing";
                    }
                    $("#priceScheme").val(schemeType);

                    var pmPercent = xmlDoc.getElementsByTagName("pmpercent")[0].childNodes[0].nodeValue;
                    var customPM = xmlDoc.getElementsByTagName("customPM")[0].childNodes[0].nodeValue;
                    var customDiscount = xmlDoc.getElementsByTagName("customDiscount")[0].childNodes[0].nodeValue;

                    if (customPM === 'true')
                    {
                        document.getElementById('pmCustom').innerHTML = 'Using Custom Data from Database';
                        document.getElementById('pmPercent').readOnly = true;
                        document.getElementById('pmPercent').className = 'customprice';
                        document.getElementById('customPM').value = 'true';

                    }
                    else
                    {
                        document.getElementById('pmCustom').innerHTML = '';
                        document.getElementById('pmPercent').readOnly = false;
                        document.getElementById('pmPercent').className = '';
                        document.getElementById('customPM').value = 'false';

                    }
                    if (customDiscount === 'true')
                    {
                        document.getElementById('discountPercent').checked = true;
                        document.getElementById('discountFixed').checked = false;
                        document.getElementById('discountAmount').className = 'customprice';
                    }
                    else
                    {
                        document.getElementById('discountPercent').checked = true;
                        document.getElementById('discountFixed').checked = false;
                        document.getElementById('discountAmount').className = '';
                    }
                    document.getElementById('pmPercent').value = pmPercent;
                    document.getElementById('discountAmount').value = xmlDoc.getElementsByTagName('clientdiscount')[0].childNodes[0].nodeValue;
                }

            }
            else
            {
                //issue an error message
                alert("A Server error has occurred: " + ajaxRequest.statusText);
            }
        }
        else
        {
            document.getElementById('schemecell').innerHTML = "<img src='../images/small-loading.gif' alt='loading...' />";
        }
    };

    //make a random number to add to the url so the browser won't cache
    var myRandom = parseInt(Math.random() * 99999999);
    
    var projectId = document.getElementById('project').value;
    if(projectId == '0') {
        return;
    }

    var url = "ajax/pricingschemes.php";
    url = url + "?p=" + projectId;
    url = url + "&key=" + myRandom;

    //alert(url);
    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);

    return true;
}