<?PHP

class lingo_task
{
	protected $name;
	protected $costUnits;
	protected $costUnitType;
	protected $costPerUnit;
	protected $markupPercent;
	protected $usesCustomPrice;
	protected $customSellPrice;
	protected $customSellUnits;
	protected $unitsLocked;
	protected $isSplit;
	protected $printable;
	
	//constructor
	function __construct()
	{
		$this->name = "Default";
		$this->costUnits = 0;
		$this->costUnitType = "Hours";
		$this->costPerUnit = 0;
		$this->markupPercent = 50;
		$this->customSellPrice = 0;
		$this->customSellType = "Hours";
		$this->usesCustomPrice = FALSE;
		$this->unitsLocked = TRUE;
		$this->isSplit = "false";
		$this->printable = true;
	}
	
	//setters
	function set_customSellType($type)
	{
		$this->customSellType = $type;
	}
	
	function set_printable($bool)
	{
		$this->printable = $bool;
	}
	
	function set_isSplit($bool)
	{
		if ($bool)
			$this->isSplit = "true";
		else
			$this->isSplit = "false";
	}
	
	function set_unitsLocked($bool)
	{
		$this->unitsLocked = $bool;
	}
	
	function set_name($new_name)
	{
		$this->name = $new_name;
	}
	
	function set_costUnits($new_units)
	{
		$this->costUnits = $new_units;
	}
	
	function set_costUnitType($new_type)
	{
		$this->costUnitType = $new_type;
	}
	
	function set_costPerUnit($new_costPer)
	{
		$this->costPerUnit = $new_costPer;
	}
	
	function set_markup($new_markup)
	{
		$this->markupPercent = $new_markup;
	}
	
	function set_customPrice($new_customPrice)
	{
		$this->customSellPrice = $new_customPrice;
		if ($new_customPrice != 0)
			$this->usesCustomPrice = TRUE;
		else
			$this->usesCustomPrice = FALSE;
	}
	
	//getters
	function get_customSellType()
	{
		return $this->customSellType;
	}
	
	function get_printable()
	{
		return $this->printable;
	}
	
	function isSplit()
	{
		return $this->isSplit;
	}
	
	function get_unitsLocked()
	{
		return $this->unitsLocked;
	}
	
	function get_name()
	{
		return $this->name;
	}
	
	function get_costUnits()
	{
		return $this->costUnits;
	}
	
	function get_costUnitType()
	{
		return $this->costUnitType;
	}
	
	function get_costPerUnit()
	{
		return $this->costPerUnit;
	}
	
	function get_markup()
	{
		return $this->markupPercent;
	}
	
	function get_customSellPrice()
	{
		return $this->customSellPrice;
	}
	
	
	
	//custom data functions
	function usesCustomPrice()
	{
		return $this->usesCustomPrice;
	}
	
	function get_cost()
	{
		$temp = $this->costPerUnit * $this->costUnits;
		$temp = round($temp,2);
	
		return ($temp);
	}
	
	function get_calculatedSellPricePerUnit()
	{
		$cost = $this->get_cost();
		$markup = $this->get_markup();
		
		if ($this->costUnits == 0)
			return 0;
		else
		{
			//check to see whether the cost includes tenth of cent
			if ( (floor($cost*100)) != ($cost*100))
				return round( ($cost / (1-($markup / 100))) / $this->costUnits, 3);
			else
				return round( ($cost / (1-($markup / 100))) / $this->costUnits, 2);
		}
	}
	
	function get_actualSellPricePerUnit()
	{
		if ($this->usesCustomPrice)
		{
			$cspp = $this->get_customSellPrice();
		}
		else
		{
			$cspp = $this->get_calculatedSellPricePerUnit();		
		}
		$temp = round($cspp,2);
		
		return $temp;
	}
	
	function get_actualSellPrice()
	{
		$aspp = $this->get_actualSellPricePerUnit();
		$units = $this->get_costUnits();
		$temp = $aspp * $units;
		
		$temp = round($temp,0);
		
		return $temp;
	}
	
	function get_grossmargin()
	{
		$asp = $this->get_actualSellPrice();
		$cost = $this->get_cost();
		
		if ($asp == 0)
			return 0;
		
		$temp = ($asp - $cost) / $asp;
		
		$temp = round($temp*100,2);
		
		return $temp;
	}
	
	function print_table_row($langID, $rowID, $editable)
	{
	
		if (!$editable)
		{
			//print checkbox
			print("<tr><td colspan=\"2\" align=\"right\">&nbsp;</td>");
			
			//print name
			print("<td>".$this->get_name()."</td>");
			
			//print units
			print("<td>". number_format($this->get_costUnits(),2) ."</td>");
			
			
			//print unit type list
			print("<td>".$this->costUnitType."</td>");
			
			//print cost per unit
			print("<td>". number_format($this->get_costPerUnit(),2) ."</td>");
			
			
			//print cost
			print("<td>". $this->get_cost() ."</td>");
			
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
			echo "<td ";
			if ($this->usesCustomPrice() == TRUE)
			{
				print (" class=\"customprice\" ");
			}
			echo ">". number_format($this->get_actualSellPricePerUnit(),2) ."</td>";
			
			
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
			print("<td>".$this->get_name()."</td>");
			
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
			print ("name=\"units-". $langID ."-". $rowID ."\" type=\"text\" size=\"10\" id=\"costUnits-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_costUnits(),2) ."\" style=\"text-align:right\" /></td>");
			
			
			//print unit type list
			print("<td>".$this->costUnitType."</td>");
			
			//print cost per unit
			print("<td><input name=\"costper-". $langID ."-". $rowID ."\" type=\"text\" size=\"8\" id=\"costper-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_costPerUnit(),2) ."\" style=\"text-align:right\" onChange=\"updateCostPer(this.value, $langID, $rowID);\" /></td>");
			
			
			//print cost
			print("<td><input class=\"noneditable\" name=\"cost-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"10\" id=\"cost-". $langID ."-". $rowID ."\" value=\"". $this->get_cost() ."\" style=\"text-align:right\" /></td>");
			
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
			
			print("size=\"5\" id=\"markup-". $langID ."-". $rowID  ."\" value=\"". $this->get_markup() ."\" style=\"text-align:right\"/></td>");
			
			
			//print calculated sell price per unit
			print("<td><input class=\"noneditable\" name=\"csp-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"8\" id=\"csp-". $langID ."-". $rowID  ."\" value=\"");
			if ( floor( $this->get_calculatedSellPricePerUnit() *100) != ($this->get_calculatedSellPricePerUnit() * 100))
				print(number_format($this->get_calculatedSellPricePerUnit() ,3));
			else
				print(number_format($this->get_calculatedSellPricePerUnit() ,2));
			  
			print("\" style=\"text-align:right\" /></td>");
			
			
			//print actual sell price per unit
			print("<td><input name=\"spp-". $langID ."-". $rowID ."\" type=\"text\" onKeyUp=\"updateSellPrice(this.value, $langID, $rowID);\" size=\"8\" id=\"spp-". $langID ."-". $rowID  ."\" value=\"". number_format($this->get_actualSellPricePerUnit(),2) ."\" style=\"text-align:right\" "); 
									
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