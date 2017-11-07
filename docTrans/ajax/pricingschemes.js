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

                //get the data from the server's response
                var xmlDoc = ajaxRequest.responseXML.documentElement;

                var error = xmlDoc.getElementsByTagName("error")[0].childNodes[0].nodeValue;
                var errString = xmlDoc.getElementsByTagName("errstring")[0].childNodes[0].nodeValue;


                if (error === 'true')
                {
                    alert('An error has occurred:\n\n' + errString);

                }
                else
                {
                    var checkKB = xmlDoc.getElementsByTagName("checkkb")[0].childNodes[0].nodeValue;
                    if (checkKB === 'true')
                    {
                        document.getElementById('specialInst').style.display = '';
                        document.getElementById('specialInst').innerHTML = xmlDoc.getElementsByTagName("company")[0].childNodes[0].nodeValue;
                        document.getElementById('specialInst').innerHTML += " has special instructions in the Knowledge Base";
                    }
                    else
                    {
                        document.getElementById('specialInst').innerHTML = "&nbsp;";
                        document.getElementById('specialInst').style.display = 'none';
                    }
                    document.getElementById('submitBtn').style.display = '';
                    document.getElementById('submit').disabled = false;
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
            document.getElementById('specialInst').style.display = '';
            document.getElementById('specialInst').innerHTML = "<img src='../images/small-loading.gif' alt='Loading...' />";
            document.getElementById('submitBtn').style.display = 'none';
            document.getElementById('submit').disabled = true;
        }
    };

    //make a random number to add to the url so the browser won't cache
    var myRandom = parseInt(Math.random() * 99999999);


    var url = "ajax/pricingschemes.php";
    url = url + "?p=" + document.getElementById('project').value;
    url = url + "&key=" + myRandom;

    //alert(url);
    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);

    return true;
}