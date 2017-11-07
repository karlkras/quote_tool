<?PHP
require_once("class_task.php");


class splitTask extends lingo_task
{
	protected $sellUnitType;
	protected $sellUnits;
	protected $sellPerUnit;
	protected $unitTypeList = array();
	
	//constructor
	function __construct()
	{
		$this->name = "Default";
		$this->costUnits = 0;
		$this->costUnitType = "Hours";
		$this->costPerUnit = 0;
		$this->markupPercent = 50;
		$this->customPrice = 0;
		$this->usesCustomPrice = FALSE;
		$this->unitsLocked = TRUE;
		$this->isSplit = "false";
		$this->printable = true;

		$this->sellUnitType = "Words";
		$this->sellUnits = 0;
		$this->sellPerUnit = 0;
		
		
	}
	
	
	//setters
	function set_sellUnitType($type)
	{
		$this->sellUnitType = $type;
	}
	
	function set_sellUnits($units)
	{
		$this->sellUnits = $units;
	}
	
	function set_sellPerUnit($price)
	{
		$this->sellPerUnit = $price;
	}
	
	//getters
	function get_sellUnitType()
	{
		return $this->sellUnitType;
	}
	
	function get_sellUnits()
	{
		return $this->sellUnits;
	}
	
	function get_sellPerUnit()
	{
		return $this->sellPerUnit;
	}
	
	function get_tasks()
	{
		return $this->taskList;
	}
	
	function get_task($index)
	{
		return $this->taskList[$index];
	}
	
	function get_unitTypeList()
	{
		return $this->unitTypeList;
	}
	
	function get_unitType_at($index)
	{
		return $this->unitTypeList[$index];
	}
	
		
	//custom data functions
	function add_unitType($index, $value)
	{
		$this->unitTypeList[$index] = $value;
	}
	
	function remove_unitType_at($index)
	{
		unset($this->unitTypeList[$index]);
	}
	
	function count_unitTypes()
	{
		return count($this->unitTypeList);
	}
	
	function get_calculatedSellPricePerUnit()
	{
		$units = $this->sellUnits;
		$pricePer = $this->sellPerUnit;
		$markup = $this->markupPercent;
		
		if ($this->sellUnits == 0)
			return 0;
		else
			return (($units * $pricePer) / (1-($markup / 100))) / $units;
	}
	
	function get_actualSellPrice()
	{
		$aspp = $this->get_actualSellPricePerUnit();
		if ($this->usesCustomPrice() && ($this->get_sellUnitType() == "Hours"))
		{	//override the sell units and get words
			//$units = $this->get_costUnits
		}
			
		
		$units = $this->get_sellUnits();
		$temp = $aspp * $units;
		
		$temp = (ceil($temp * 100))/100;
		
		return $temp;
	}
	
	function print_table_row($langID, $rowID, $editable)
	{
	
		if (!$editable)
		{
			//print checkbox
			print("<tr><td colspan=\"2\" align=\"right\">&nbsp;</td>");
			
			//print name
			print("<td>".$this->get_name());
			
			if ($this->costUnitType != $this->sellUnitType)
			{
				print (" - Cost");
			}		
			print("</td>");
			
			//print units
			print("<td>". number_format($this->get_costUnits(),2) ."</td>");
			
			
			//print unit type list
			print("<td>". $this->costUnitType ."</td>");
			
			//print cost per unit
			print("<td>");
			if ( floor( $this->get_costPerUnit() *100) != ($this->get_costPerUnit() * 100))
			{
				print(number_format($this->get_costPerUnit(),3));
			}
			else
				print(number_format($this->get_costPerUnit(),2));
			print("</td>");
			
			
			//print cost
			print("<td>". $this->get_cost() ."</td>");
			
			//if the cost unit doesn't match the sell unit, then split the row
			if ($this->costUnitType != $this->sellUnitType)
			{
				print("<td colspan=\"5\" style=\"background-color:#CCCCCC\">&nbsp;</td></tr>\n<tr><td colspan=\"2\">&nbsp;</td><td>".$this->get_name() ." - Sell</td>");
				print("<td>". number_format($this->get_sellUnits()) ."</td>");
				print("<td>". $sellType ."</td>");
				echo "<td>". $this->sellPerUnit ."</td>";
				echo "<td>&nbsp;</td>";
			}
			
			
			
			//print markup
			print("<td>". $this->get_markup() ."</td>");
			
			
			//print calculated sell price per unit
			print("<td>");
			if ( floor( $this->get_calculatedSellPricePerUnit() *100) != ($this->get_calculatedSellPricePerUnit() * 100))
				print(number_format($this->get_calculatedSellPricePerUnit() ,3));
			else
				print(number_format($this->get_calculatedSellPricePerUnit() ,2));
			print("</td>");
			
			
			//print actual sell price per unit
			print("<td");
			if ($this->usesCustomPrice() == TRUE)
			{
				echo " class=\"customprice\" ";
			}
			echo ">";
			
			if ( floor( $this->get_actualSellPricePerUnit() *100) != ($this->get_actualSellPricePerUnit() * 100))
				print(number_format($this->get_actualSellPricePerUnit() ,3));
			else
				print(number_format($this->get_actualSellPricePerUnit() ,2));
	
			print("</td>");
			
			
			//print actual sell price
			print("<td>". number_format($this->get_actualSellPrice(),2) ."</td>");
			
			//print gross margin
			print("<td>".$this->get_grossmargin()."</td></tr>");	
		}
		else
		{
						//print checkbox
			print("<tr><td colspan=\"2\" align=\"right\"><input type=\"checkbox\" value=\"". $rowID ."\" id=\"print-$langID-$rowID\" name=\"print-". $langID ."\" onChange=\"changeClick($langID,$rowID);\" ");
			if ($this->get_printable() == TRUE)
				print (" checked=\"checked\" ");
			
			print("/></td>");
			
			//print name
			print("<td>".$this->get_name());
			
			if ($this->costUnitType != $this->sellUnitType)
			{
				print (" - Cost");
			}		
			print("</div></td>");
			
			//print units
			print("<td><input ");
			if ($this->unitsLocked == TRUE)
			{
				print("class=\"noneditable\" readonly ");
			}
			else
			{
				print("onChange=\"updateUnits(this.value, 'cost', $langID, $rowID);\" ");
			}
			print ("name=\"costUnits-". $langID ."-". $rowID ."\" type=\"text\" size=\"10\" id=\"costUnits-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_costUnits(),2) ."\" style=\"text-align:right\"/></td>");
			
			
			//print unit type list
			print("<td><select name=\"costUnitType-". $langID ."-". $rowID ."\" id=\"costUnitType-". $langID ."-". $rowID ."\" onChange=\"changeUnitType(this.value, 'cost', $langID, $rowID);\">\n");
			foreach(array_keys($this->unitTypeList) as $costType)
			{
				print("<option ");
				if ($costType == $this->costUnitType)
				{
					print("selected ");
				}
				print ("value=\"". $costType ."\">". $costType ."</option>");
			
			
			}
			print("</select></td>");
			
			//print cost per unit
			print("<td><input name=\"costper-". $langID ."-". $rowID ."\" type=\"text\" size=\"8\" id=\"costper-". $langID ."-". $rowID ."\" value=\"");
			if ( floor( $this->get_costPerUnit() *100) != ($this->get_costPerUnit() * 100))
			{
				print(number_format($this->get_costPerUnit(),3));
			}
			else
				print(number_format($this->get_costPerUnit(),2));
			print("\" style=\"text-align:right\" onChange=\"updateCostPer(this.value, $langID, $rowID);\" /></td>");
			
			
			//print cost
			print("<td><input class=\"noneditable\" name=\"cost-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"10\" id=\"cost-". $langID ."-". $rowID ."\" value=\"". $this->get_cost() ."\" style=\"text-align:right\" /></td>");
			
			//if the cost unit doesn't match the sell unit, then split the row
			if ($this->costUnitType != $this->sellUnitType)
			{
				print("<td colspan=\"5\" style=\"background-color:#CCCCCC\">&nbsp;</td></tr>\n<tr><td colspan=\"2\">&nbsp;</td><td>".$this->get_name() ." - Sell</td>");
				print("<td><input ");
				if ($this->unitsLocked == TRUE)
				{
					print("class=\"noneditable\" readonly ");
				}
				else
				{
					print("onKeyUp=\"updateUnits(this.value, 'sell', $langID, $rowID);\" ");
				}
				print ("name=\"sellUnits-". $langID ."-". $rowID ."\" type=\"text\" size=\"10\" id=\"sellUnits-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_sellUnits()) ."\" style=\"text-align:right\" /></td>");
				print("<td><select name=\"sellUnitType-". $langID ."-". $rowID ."\" id=\"sellUnitType-". $langID ."-". $rowID ."\" onChange=\"changeUnitType(this.value, 'sell', $langID, $rowID);\">\n");
				foreach(array_keys($this->unitTypeList) as $sellType)
				{
					print("<option ");
					if ($sellType == $this->sellUnitType)
					{
						print("selected ");
					}
					print ("value=\"". $sellType ."\">". $sellType ."</option>");
				
				
				}
				print("</select></td><td><input name=\"sellper-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"8\" id=\"sellper-". $langID ."-". $rowID ."\" value=\"". $this->sellPerUnit ."\" style=\"text-align:right\" /></td><td colspan=\"1\">&nbsp;</td>");
			}
			
			
			
			//print markup
			print("<td><input name=\"markup-". $langID ."-". $rowID  ."\" type=\"text\" ");
									
			if ($this->usesCustomPrice() == TRUE)
			{
				print("readonly class=\"noneditable\" ");
			}
			else
			{
				print("onKeyUp=\"updateMarkup(this.value, $langID, $rowID);\" ");
			}
			
			print("size=\"5\" id=\"markup-". $langID ."-". $rowID  ."\" value=\"". $this->get_markup() ."\" style=\"text-align:right\" /></td>");
			
			
			//print calculated sell price per unit
			print("<td><input class=\"noneditable\" name=\"csp-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"8\" id=\"csp-". $langID ."-". $rowID  ."\" value=\"");
			if ( floor( $this->get_calculatedSellPricePerUnit() *100) != ($this->get_calculatedSellPricePerUnit() * 100))
				print(number_format($this->get_calculatedSellPricePerUnit() ,3));
			else
				print(number_format($this->get_calculatedSellPricePerUnit() ,2));
			print("\" style=\"text-align:right\" /></td>");
			
			
			//print actual sell price per unit
			print("<td><input name=\"spp-". $langID ."-". $rowID ."\" type=\"text\" onKeyUp=\"updateSellPrice(this.value, $langID, $rowID);\" size=\"8\" id=\"spp-". $langID ."-". $rowID  ."\" value=\"");
			if ( floor( $this->get_actualSellPricePerUnit() *100) != ($this->get_actualSellPricePerUnit() * 100))
				print(number_format($this->get_actualSellPricePerUnit() ,3));
			else
				print(number_format($this->get_actualSellPricePerUnit() ,2));
	
			print("\" style=\"text-align:right\" "); 
									
			if ($this->usesCustomPrice() == TRUE)
			{
				print (" class=\"customprice\" ");
			}
									
			print("/></td>");
			
			
			//print actual sell price
			print("<td><input class=\"noneditable\" name=\"asp-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"10\" id=\"asp-". $langID ."-". $rowID  ."\" value=\"". number_format($this->get_actualSellPrice(),2) ."\" style=\"text-align:right\" /></td>");
			
			//print gross margin
			print("<td><input class=\"noneditable\" id=\"GM-".$langID."-". $rowID . "\" type=\"text\" readonly size=\"8\" name=\"GM-".$langID."-". $rowID . "\" value=\"".$this->get_grossmargin()."\" style=\"text-align:right\"/></td></tr>");	

		}
	
	}
		

}


?>