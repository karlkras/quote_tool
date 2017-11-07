<?PHP
function getTotalWords($theTask)
{
$totalWords = 0;
	if (($theTask->wordCounts->newWords > 0) || ($theTask->wordCounts->fuzzyWords > 0) || ($theTask->wordCounts->matchRepsWords > 0))
	{
		$totalWords = $theTask->wordCounts->newWords + $theTask->wordCounts->fuzzyWords + $theTask->wordCounts->matchRepsWords;
	}
	else
	{
		$totalWords = $theTask->wordCounts->wordCount;
	}
	
	return $totalWords;
}
?>