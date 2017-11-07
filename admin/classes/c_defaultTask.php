<?PHP
class default_task
{
	protected $name;
	protected $subCategory;
	protected $rate;
	protected $unit;
        protected $rushRate;	
	
	function __construct()
	{
		$this->name = '';
		$this->subCategory = '';
		$this->rate = 0;
		$this->unit = '';
                $this->rushRate = 0;
	}
	
	function set_subCategory($c)
	{
		$this->subCategory = $c;
	}
	
	function get_subCategory()
	{
		return $this->subCategory;
	}
	
	function set_name($n)
	{
		$this->name = $n;
	}
	
	function get_name()
	{
		return $this->name;
	}
	
	function set_rate($r)
	{
		$this->rate = $r;
	}
	
	function get_rate()
	{
		return $this->rate;
	}
	
	function set_rushRate($r)
	{
		$this->rushRate = $r;
	}
	
	function get_rushRate()
	{
		return $this->rushRate;
	}
        
	function set_unit($u)
	{
		$this->unit = $u;
	}
	
	function get_unit()
	{
		return $this->unit;
	}
	
}


?>