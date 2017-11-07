<?PHP

require_once("class_task.php");
require_once("class_pm_task.php");
require_once("class_split_task.php");
require_once("class_custom_task.php");

class lingo_languagePair
{
	protected $sourceLang;
	protected $targetLang;
	protected $newTextRate;
	protected $fuzzyTextRate;
	protected $matchTextRate;
	protected $transHourly;
	protected $prHourly;
	protected $taskList = array();
	protected $errors;
	protected $newText;
	protected $fuzzyText;
	protected $matchText;
	protected $dtpHourly;
	protected $engHourly;
	protected $rolledUpTasks = array();

	
	//constructor
	function __construct()
	{
		$this->errors = FALSE;
	}
	
	//setters
	function set_dtpHourly($rate)
	{
		$this->dtpHourly = $rate;
	}
	
	function set_engHourly($rate)
	{
		$this->engHourly = $rate;
	}
	
	function set_newText($units)
	{
		$this->newText = $units;
	}
	
	function set_fuzzyText($units)
	{
		$this->fuzzyText = $units;
	}
	
	function set_matchText($units)
	{
		$this->matchText = $units;
	}
	
	function set_error($boolean)
	{
		$this->errors = $boolean;
	}
	
	function set_sourceLang($language)
	{
		$this->sourceLang = $language;
	}
	
	function set_targetLang($language)
	{
		$this->targetLang = $language;
	}
	
	function set_newTextRate($rate)
	{
		$this->newTextRate = $rate;
	}
	
	function set_fuzzyTextRate($rate)
	{
		$this->fuzzyTextRate = $rate;
	}
	
	function set_matchTextRate($rate)
	{
		$this->matchTextRate = $rate;
	}
	
	function set_transHourly($rate)
	{
		$this->transHourly = $rate;
	}
	
	function set_prHourly($rate)
	{
		$this->prHourly = $rate;
	}
	
	function set_rolledUpTask($index, $value)
	{
		$this->rolledUpTasks[$index] = $value;
	}
	

	
	//gettters
	function get_dtpHourly()
	{
		return $this->dtpHourly;
	}
	
	function get_engHourly()
	{
		return $this->engHourly;
	}
	
	function get_newText()
	{
		return $this->newText;
	}
	
	function get_fuzzyText()
	{
		return $this->fuzzyText;
	}
	
	function get_matchText()
	{
		return $this->matchText;
	}
	
	function get_sourceLang()
	{
		return $this->sourceLang;
	}
	
	function get_targetLang()
	{
		return $this->targetLang;
	}
	
	function get_newTextRate()
	{
		return $this->newTextRate;
	}
	
	function get_fuzzyTextRate()
	{
		return $this->fuzzyTextRate;
	}
	
	function get_matchTextRate()
	{
		return $this->matchTextRate;
	}
	
	function get_transHourly()
	{
		return $this->transHourly;
	}
	
	function get_prHourly()
	{
		return $this->prHourly;
	}
	
	function get_tasks()
	{
		return $this->taskList;
	}
	
	function get_task($index)
	{
		return $this->taskList[$index];
	}
	
	function get_error()
	{
		return $this->errors;
	}
	
	function get_rolledUpTasks()
	{
		return $this->rolledUpTasks;
	}
	
	function get_rolledUpTask_at($index)
	{
		return $this->rolledUpTasks[$index];
	}
	

		
	
	//custom methods
	function remove_rolledUpTask_at($key)
	{
		unset($this->rolledUpTasks[$key]);
	}
	
	function add_task($task)
	{
		$index = count($this->taskList);
		$this->taskList[] = $task;
		
		return $index;
	}
	
	function remove_task_at($index)
	{
		unset($this->taskList[$index]);
		$this->taskList = array_values($this->taskList);
	}
	
	function count_tasks()
	{
		return count($this->taskList);
	}
	
	function total_words()
	{		
		return $this->newText + $this->fuzzyText + $this->matchText;
	}
	
	function contains_custom()
	{
		foreach ($this->taskList as $task)
		{
			if ($task->usesCustomPrice())
				return TRUE;
		}
		
		return FALSE;
	}
	
	function language_cost()
	{
		$total = 0;
		foreach($this->taskList as $task)
		{
			$total += $task->get_cost();
		}
		return $total;
	}
	
	function sellprice_per_word()
	{
		$totalWords = $this->total_words();
		$sellprice_total = 0;
		foreach($this->taskList as $task)
		{
			if ($task->get_name() == "Project Management")
				$sellprice_total += $task->get_actualSellPrice($this);
			else
				$sellprice_total += $task->get_actualSellPrice();
		}
		return round($sellprice_total/$totalWords,2);
	}
	
	function sellprice()
	{
		$sellprice_total = 0;
		foreach($this->taskList as $task)
		{
			if ($task->get_name() == "Project Management")
				$sellprice_total += $task->get_actualSellPrice($this);
			else
				$sellprice_total += $task->get_actualSellPrice();
		}
		return $sellprice_total;
	}
	
	function language_grossmargin()
	{
		$sellprice = $this->sellprice();
		$cost = $this->language_cost();
		
		return round((($sellprice - $cost) / $sellprice)*100,2);
	}
		


}


?>