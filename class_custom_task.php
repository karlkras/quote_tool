<?PHP
require_once("class_task.php");


class customTask extends lingo_task
{
	protected $xyz;
	
	
	//setters
	function set_xyz($val)
	{
		$this->xyz = $val;
	}
	
	//getters
	function get_xyz()
	{
		return $this->xyz;
	}
	
	
	//custom functions
		function print_table_row($langID, $rowID, $editable)
	{
		if (!$editable)
		{
			print("<tr><td colspan=\"2\" align=\"right\">&nbsp;</td>");
			
			//print name
			print("<td>".$this->get_name()."</td>");
			
			//print units
			print("<td>". number_format($this->get_costUnits(),2) ."</td>");
			
			
			//print unit type list
			print("<td>".$this->costUnitType."</td>");
			
			//print cost per unit
			print("<td>". $this->get_costPerUnit() ."</td>");
			
			
			//print cost
			print("<td>". number_format($this->get_cost(),2) ."</td>");
			
			//print markup
			print("<td>". $this->get_markup() ."</td>");
			
			
			//print calculated sell price per unit
			print("<td>".$this->get_calculatedSellPricePerUnit() ."</td>");
			
			
			//print actual sell price per unit
			print("<td");
			if ($this->usesCustomPrice() == TRUE)
			{
				print (" class=\"customprice\" ");
			}						
			print(">" .$this->get_actualSellPricePerUnit() ."</td>");
									
			
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
				print("onblur=\"updateUnits(this.value, 'cost', $langID, $rowID);\" ");
			}
			print ("name=\"units-". $langID ."-". $rowID ."\" type=\"text\" size=\"10\" id=\"costUnits-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_costUnits(),2) ."\" style=\"text-align:right\"/></td>");
			
			
			//print unit type list
			print("<td>".$this->costUnitType."</td>");
			
			//print cost per unit
			print("<td><input name=\"costper-". $langID ."-". $rowID ."\" type=\"text\" size=\"8\" id=\"costper-". $langID ."-". $rowID ."\" value=\"". $this->get_costPerUnit() ."\" style=\"text-align:right\" onChange=\"updateCostPer(this.value, $langID, $rowID);\" /></td>");
			
			
			//print cost
			print("<td><input class=\"noneditable\" name=\"cost-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"10\" id=\"cost-". $langID ."-". $rowID ."\" value=\"". number_format($this->get_cost(),2) ."\" style=\"text-align:right\" /></td>");
			
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
			print("<td><input class=\"noneditable\" name=\"csp-". $langID ."-". $rowID ."\" type=\"text\" readonly size=\"8\" id=\"csp-". $langID ."-". $rowID  ."\" value=\"".$this->get_calculatedSellPricePerUnit() ."\" style=\"text-align:right\" /></td>");
			
			
			//print actual sell price per unit
			print("<td><input name=\"spp-". $langID ."-". $rowID ."\" type=\"text\" onKeyUp=\"updateSellPrice(this.value, $langID, $rowID);\" size=\"8\" id=\"spp-". $langID ."-". $rowID  ."\" value=\"".$this->get_actualSellPricePerUnit() ."\" style=\"text-align:right\" "); 
									
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