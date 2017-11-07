<?PHP
require_once("class_task.php");


class pmTask extends lingo_task
{
	protected $pmPercent;
	
	protected  $sellUnitType;
	protected $sellUnits;
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
		$this->unitsLocked = FALSE;
		$this->isSplit = "false";
		$this->printable = true;
		
		$this->pmPercent = 10;
		
		$this->sellUnitType = "Percent";
		$this->sellUnits = 0;
		
	}
	
	
	//setters
	function set_sellUnitType($new_Type)
	{
		$this->sellUnitType = $new_Type;
	}
	
	function set_sellUnits($new_Units)
	{
		$this->sellUnits=$new_Units;
	}
	
	function set_pmPercent($new_Percent)
	{
		$this->pmPercent = $new_Percent;
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
	
	function get_pmPercent()
	{
		return $this->pmPercent;
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
	
	function get_calculatedSellPricePerUnit($language)
	{
		$sumSellPrice = 0;
		
		foreach ($language->get_tasks() as $task)
		{
			if (!($task instanceof pmTask))
				$sumSellPrice += $task->get_calculatedSellPricePerUnit();
		}
		return round($sumSellPrice * ($this->pmPercent/100),2);
		
	}
	
	function get_actualSellPricePerUnit($language)
	{
		
		$cspp = $this->get_calculatedSellPricePerUnit($language);		
		$temp = (ceil($cspp * 100))/100;
		
		return $temp;
	}
	
	function get_actualSellPrice($language)
	{
		$sumSellPrice = 0;


		foreach ($language->get_tasks() as $task)
		{
			if (!($task instanceof pmTask))
				$sumSellPrice += $task->get_actualSellPrice();
		}
		
		
		if ($this->usesCustomPrice)
		{
			$retVal = ($sumSellPrice / (1-($this->customSellPrice/100))) * ($this->customSellPrice/100);
		}
		elseif ($this->get_sellUnitType() == 'Hours')
		{
			$retVal = ($this->sellUnits * $this->costPerUnit)  / (1-($this->markup / 100));
		}
		else
		{
			$retVal = ($sumSellPrice / (1-($this->pmPercent/100))) * ($this->pmPercent/100);
		}
		
	
		return round($retVal ,2);
		
	}
	
	
	function get_grossmargin($language)
	{
		$asp = $this->get_actualSellPrice($language);
		if ($asp == 0)
			$temp = 0;
		else
		{
			$cost = $this->get_cost();
			$temp = ($asp - $cost) / $asp;
			
			$temp = round($temp*100,2);
		}		
		return $temp;
	}
	
	function print_table_row($langID, $rowID, $parentLang, $editable)
	{
		if (!$editable)
		{
			//print checkbox
			print("<tr><td colspan=\"2\" align=\"right\">&nbsp;</td>");
			
			//print name
			print("<td>".$this->get_name()."</td>");
			
			//print units
			print("<td>". number_format($this->get_costUnits(),2) ."</td>");
			
			
			//print unit type
			print("<td>". $this->get_costUnitType() ."</td>");
			
			//print cost per unit
			print("<td>". $this->get_costPerUnit() ."</td>");
			
			
			//print cost
			print("<td>". number_format($this->get_cost(),2) ."</td>");
			
			
			//finish the row since we're in a PM and it calculates differently
			print("<td colspan=\"5\" style=\"background-color:#CCCCCC\">&nbsp;</td></tr>");
			
			//start a new row
			print("<tr><td colspan=\"7\" style=\"background-color:#CCCCCC\">&nbsp;</td>");
			
			
			//print markup
			print("<td ");
									
			if ($this->usesCustomPrice() == TRUE)
			{
				print("class=\"customprice\">".$this->get_customSellPrice());
			}
			else
			{
				print (">". $this->get_pmPercent());
			}
			print("</td>");
			
				
			
			//print calculated sell price per unit
			print("<td>". $this->costUnitType ."</td>");
			
			//do not print the aspp cell, since it's not valid
			print("<td>&nbsp;</td>");
			
					
			
			//print actual sell price
			print("<td>". number_format($this->get_actualSellPrice($parentLang),2) ."</td>");
			
			//print gross margin
			print("<td>".$this->get_grossmargin($parentLang)."</td></tr>");
		}
		else
		{
			//print checkbox
			print("<tr><td colspan=\"2\" align=\"right\"><input type=\"checkbox\" value=\"". $rowID ."\" id=\"print-$langID-$rowID\" name=\"print-". $langID ."\" onChange=\"changeClick($langID,$rowID);\" ");
			if ($this->get_printable() == TRUE)
				print (" checked=\"checked\" ");
			
			print("/></td>");
			
			//print name
			print("<td>".$this->get_name()."</td>");
			
			//print units
			print("<td><input ");
			if ($this->unitsLocked == TRUE)
			{
				print("class=\"noneditable\" readonly ");
			}
			else
			{
				print("onblur=\"updateUnits(this.value, 'cost', $langID, $rowID);\" ");
			}
			print(" name=\"units-". $langID ."-". $rowID ."\" type=\"text\" size=\"10\" id=\"units-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_costUnits(),2) ."\" style=\"text-align:right\"/></td>");
			
			
			//print unit type
			print("<td>". $this->get_costUnitType() ."</td>");
			
			//print cost per unit
			print("<td><input name=\"costper-". $langID ."-". $rowID ."\" type=\"text\" size=\"8\" id=\"costper-". $langID ."-". $rowID ."\" value=\"". $this->get_costPerUnit() ."\" style=\"text-align:right\" onChange=\"updateCostPer(this.value, $langID, $rowID);\" /></td>");
			
			
			//print cost
			print("<td><input class=\"noneditable\" name=\"cost-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"10\" id=\"cost-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_cost(),2) ."\" style=\"text-align:right\" /></td>");
			
			
			//finish the row since we're in a PM and it calculates differently
			print("<td colspan=\"5\" style=\"background-color:#CCCCCC\">&nbsp;</td></tr>");
			
			//start a new row
			print("<tr><td colspan=\"7\" style=\"background-color:#CCCCCC\">&nbsp;</td>");
			
			
			//print markup
			print("<td><input name=\"markup-". $langID ."-". $rowID  ."\" type=\"text\" ");
									
			if ($this->usesCustomPrice() == TRUE)
			{
				print("readonly class=\"customprice\" ");
				print(" value=\"". $this->get_customSellPrice() . "\" ");
			}
			else
			{
				print("onBlur=\"updateMarkup(this.value, $langID, $rowID);\" ");
				print (" value=\"". $this->get_pmPercent() ."\" ");
			}
			
			print("size=\"5\" id=\"markup-". $langID ."-". $rowID  ."\"  style=\"text-align:right\"/></td>");
			
				
			
			//print calculated sell price per unit
			print("<td><select name=\"pmSellUnit-". $langID ."\" id=\"pmSellUnit-". $langID ."\">");
			foreach(array_keys($this->unitTypeList) as $costType)
			{
				print("<option ");
				if ($costType == $this->sellUnitType)
				{
					print("selected ");
				}
				print ("value=\"". $costType ."\">". $costType ."</option>");
			
			
			}
			print("</select></td>");
			
			//do not print the aspp cell, since it's not valid
			print("<td>&nbsp;</td>");
			
					
			
			//print actual sell price
			print("<td><input class=\"noneditable\" name=\"asp-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"10\" id=\"asp-". $langID ."-". $rowID  ."\" value=\"". number_format($this->get_actualSellPrice($parentLang),2) ."\" style=\"text-align:right\" /></td>");
			
			//print gross margin
			print("<td><input class=\"noneditable\" id=\"GM-".$langID."-". $rowID . "\" type=\"text\" readonly size=\"8\" name=\"GM-".$langID."-". $rowID . "\" value=\"".$this->get_grossmargin($parentLang)."\" style=\"text-align:right\"/></td></tr>");	
		}
	
	}
		

}


?>