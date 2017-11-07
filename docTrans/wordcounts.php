<?PHP

function getWordTotalCount($lingTask)
{
$totalWordcount = 0;

	if ($lingTask->category == 'Non-Trados TR+CE')
		$totalWordcount = $lingTask->wordCounts->wordCount;
	else
		$totalWordcount = $lingTask->wordCounts->fuzzyWords + $lingTask->wordCounts->matchRepsWords + $lingTask->wordCounts->newWords;
		
	return $totalWordcount;

}



?>