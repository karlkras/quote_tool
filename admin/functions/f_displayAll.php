<?PHP
function displayAll()
{
/**
 *	Prints an html table of all clients in the client table
 *	@param: none
 *	@return: none
 *
 */
	
	$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
	if ($myDBConn->connect_errno)
	{
		echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
		exit;
	}
	
	
	$query = "SELECT * FROM clients ORDER BY Name";
	$result = $myDBConn->query($query) or die($myDBConn->error);
	
	if ($result->num_rows < 1)
	{
		echo "<p>No pricing schemes found</p>\n";
		if ((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin']))
		{
			echo "<p><a href=\"?action=addtable\">Add New Pricing Scheme</a></p>\n";
		}
		$result->free();
	}
	else
	{
		if ((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin']))
		{
			echo "<p class=\"centerAligned\"><a href=\"?action=addtable\">Add New Pricing Scheme</a></p>\n";
		}
		print("<table id='table1'  class='admin' border=0>\n");
		print ("<tr><th ");
		if ((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin']))
		{
			echo 'colspan=6 ';
		}
		else
		{
			echo 'colspan=3 ';
		}
		echo "class='admin'>Pricing Schemes</th></tr>\n";
		
		while ($row =  $result->fetch_assoc())
		{
			
			$tableName = $row['table_name'];
			$fiendlyName = $row['Name'];
			$realname = substr($tableName,7);
			$clientID = $row['ID'];
		
			echo "<tr><td class='admin'>", $fiendlyName, "</td>";
			echo "<td class='rightAligned' width='80px'><a href=\"?action=view&target=",$clientID,"\">View</a></td>";
			if ((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin']))
			{
				echo "<td class='rightAligned' width='80px'><a href=\"?action=import&target=",$clientID,"\">Import CSV</a></td>";
			}
			echo "<td class='rightAligned' width='75px'><a href=\"?action=export&target=",$clientID,"\">Export CSV</a></td>";
			if ((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin']))
			{
				echo "<td class='rightAligned' width='30px'><a href=\"?action=edit&target=",$realname,"\">Edit</a></td>";
				echo "<td class='rightAligned' width='50px'><a href=\"?action=drop&target=",$realname,"\" onClick=\"javascript:return confirm('Are you sure you want to delete this scheme?')\">Delete</a></td>";
			}
			echo "</tr>\n";
	
		}
		
		print("</table>\n");
		
		if ($result->num_rows > 20)
		{
			echo "<p class='centerAligned'><a href=\"?action=addtable\">Add New Pricing Scheme</a></p>\n";
		}
		$result->free();
	}
	
	$myDBConn->close();
}

?>