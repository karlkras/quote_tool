<?PHP
function parseTasks($task, $rate, $rushRate, $units, &$defaultTasks, &$languageTasks)
{
	$first = strpos($task, '=');
	if ($first === false) //there is no language pair, so this is a default value
	{
		$tempTask = new default_task;
		
		//strip out sub category if available
		$subcat_start = strpos($task, "#");
		if ($subcat_start === false)
		{
			$tempTask->set_name(str_replace("_"," ",$task));
		}
		else
		{
			$subcat_stop = strpos($task,"#",$subcat_start+1);
			if ($subcat_stop === false)
				$subcat_stop = strlen($task)+2;
			$tempTask->set_name( str_replace("_"," ",substr($task,0,$subcat_start)) );
			
			$tempTask->set_subCategory( str_replace("_"," ",substr($task,$subcat_start+1, $subcat_stop-$subcat_start-1)));
			
		}
		
		$tempTask->set_rate($rate);
		$tempTask->set_unit($units);
                $tempTask->set_rushRate($rushRate);
		
		$defaultTasks[] = $tempTask;
		
							
	}
	else
	{
		$taskName = substr($task,0,$first);
		$second = strpos($task, '=',$first+1);
		if ($second === false) //there is no second language, so treat it as an error
		{
			$sourceLang = substr($task,$first+1);
			$targetLang = NULL;
			
			$tempTask = new default_task;
			$subcat_start = strpos($task, "#");
			if ($subcat_start === false)
			{
				$tempTask->set_name(str_replace("_"," ",$task));
			}
			else
			{
				$subcat_stop = strpos($task,"#",$subcat_start+1);
				if ($subcat_stop === false)
					$subcat_stop = strlen($task)+2;
				$tempTask->set_name( str_replace("_"," ",substr($task,0,$subcat_start)) );
				
				$tempTask->set_subCategory( str_replace("_"," ",substr($task,$subcat_start+1, $subcat_stop-$subcat_start-1)));
			}
			$tempTask->set_rate($rate);
			$tempTask->set_unit($units);
                        $tempTask->set_rushRate($rushRate);
			
			$defaultTasks[] = $tempTask;
			
			
		}
		else
		{
			$sourceLang = substr($task,$first+1,$second-$first-1);
			$targetLang = substr($task,$second+1);
			
			$tempTask = new language_task;
			$subcat_start = strpos($task, "#");
			if ($subcat_start === false)
			{
				$tempTask->set_name(str_replace("_"," ", substr($task,0,$first)));
			}
			else
			{
				$subcat_stop = strpos($task,"#",$subcat_start+1);
				if ($subcat_stop === false)
					$subcat_stop = strlen($task)+2;
				$tempTask->set_name( str_replace("_"," ",substr($task,0,$subcat_start)) );
				
				$tempTask->set_subCategory( str_replace("_"," ",substr($task,$subcat_start+1, $subcat_stop-$subcat_start-1)));
			}
			$tempTask->set_rate($rate);
			$tempTask->set_unit($units);
                        $tempTask->set_rushRate($rushRate);
			$tempTask->set_source($sourceLang);
			$tempTask->set_target($targetLang);
			
			$languageTasks[$sourceLang][$targetLang][] = $tempTask;
		}
	}	
	
}

?>