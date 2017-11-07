// JavaScript Document



function rollup(taskType, langId)
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

                var langID = xmlDoc.getElementsByTagName("langcode")[0].childNodes[0].nodeValue;
                var taskType = xmlDoc.getElementsByTagName("tasktype")[0].childNodes[0].nodeValue;
                var newValue = xmlDoc.getElementsByTagName("newvalue")[0].childNodes[0].nodeValue;


                switch (taskType)
                {
                    case 'linguistic':
                        if (newValue == "false")
                        {
                            var ID = "print-" + langID + "-0";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-1";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-2";
                            document.getElementById(ID).checked = true;
                        } else
                        {
                            var ID = "print-" + langID + "-0";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-1";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-2";
                            document.getElementById(ID).checked = false;
                        }
                        break;

                    case 'dtp':
                        if (newValue == "false")
                        {
                            var ID = "print-" + langID + "-4";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-5";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-6";
                            document.getElementById(ID).checked = true;
                        } else
                        {
                            var ID = "print-" + langID + "-4";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-5";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-6";
                            document.getElementById(ID).checked = false;
                        }
                        break;

                    case 'engineering':
                        if (newValue == "false")
                        {
                            var ID = "print-" + langID + "-7";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-8";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-9";
                            document.getElementById(ID).checked = true;
                        } else
                        {
                            var ID = "print-" + langID + "-7";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-8";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-9";
                            document.getElementById(ID).checked = false;
                        }
                        break;

                    case 'qa':
                        if (newValue == "false")
                        {
                            var ID = "print-" + langID + "-10";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-11";
                            document.getElementById(ID).checked = true;
                        } else
                        {
                            var ID = "print-" + langID + "-10";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-11";
                            document.getElementById(ID).checked = false;
                        }
                        break;

                    case 'additional':
                        if (newValue == "false")
                        {
                            var ID = "print-" + langID + "-12";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-13";
                            document.getElementById(ID).checked = true;
                            ID = "print-" + langID + "-14";
                            document.getElementById(ID).checked = true;
                        } else
                        {
                            var ID = "print-" + langID + "-12";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-13";
                            document.getElementById(ID).checked = false;
                            ID = "print-" + langID + "-14";
                            document.getElementById(ID).checked = false;
                        }
                        break;
                }


            } else
            {
                //issue an error message
                alert("An error has occurred: " + ajaxRequest.statusText);
            }
        }

    }

    //make a random number to add to the url so the browser won't cache
    var myRandom = parseInt(Math.random() * 99999999);


    var checkboxID = taskType + "-" + langId;
    var newValue = document.getElementById(checkboxID).checked;

    var url = "rollup.php";
    url = url + "?lang=" + langId;
    url = url + "&type=" + taskType;
    url = url + "&value=" + newValue;
    url = url + "&key=" + myRandom;

    ajaxRequest.open("GET", url, true);
    ajaxRequest.send(null);

}


