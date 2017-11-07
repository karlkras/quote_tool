<?PHP

function makePrintable($number, $flag=0)
{
	//input:
	//      $number - the number to be formated
	//		$flag - condition for how to handle whole numbers
	//
	//output:
	//		returns number formated for printing

	if ($number == floor($number))
	{
		//we have a whole number, so process it based on flag
		if ($flag == 0)
			$temp = number_format($number);
		else
			$temp = number_format($number,$flag);
	}
	
	elseif (($number*100) != (floor($number*100)))
	{
		//we have more than two decimal places, so round at three
		$temp = number_format($number,3,".",",");
	}
	else
	{
		$temp = number_format($number,2,".",",");
	}

	return $temp;

}


?>