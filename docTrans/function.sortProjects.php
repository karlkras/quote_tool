<?PHP

function sortProjects($projectList)
{
	$listLength = count($projectList);
	$idArray = array();
	
	foreach ($projectList as $project)
	{
		$idArray[] = $project->id;
	}
	sort($idArray,SORT_NUMERIC);
	
	$sortedList = array();
	foreach($idArray as $projId)
	{
		//search for matching project Id
		foreach($projectList as $project)
		{
			if ($project->id == $projId)
			{
				$sortedList[] = $project;
				break;
			}
		}
	}
	
	return $sortedList;


}
?>