<?PHP
include_once('c_defaultTask.php');

class language_task extends default_task
{
	protected $source;
	protected $target;
	
	function __construct()
	{
		$this->name = 'default task';
		$this->subCategory = 'none';
		$this->rate = 0;
		$this->unit = 'piggies';
		$this->source = 'default source';
		$this->target = 'default target';
	}
	
	function set_source($s)
	{
		$this->source = $s;
	}
	
	function get_source()
	{
		return $this->souce;
	}
	
	function set_target($t)
	{
		$this->target = $t;
	}
	
	function get_target()
	{
		return $this->target;
	}
	
}

?>