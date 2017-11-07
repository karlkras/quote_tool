<?PHP


class contact
{
	protected $name;
	protected $title;
	protected $email;
	protected $phone;
	
	//constructor
	function __construct()
	{
		$name = 'Default';
		$title = 'Sales';
		$email = 'info@llts.com';
		$phone = '(503) 419-4856';
	}
	
	
	//setters
	function set_name($new_name)
	{
		$this->name = $new_name;
	}
	
	function set_title($new_title)
	{
		$this->title = $new_title;
	}
	
	function set_email($new_email)
	{
		$this->email = $new_email;
	}
	
	function set_phone($new_phone)
	{
		$this->phone = $new_phone;
	}
	
	//getters
	function get_name()
	{
		return $this->name;
	}
	
	function get_title()
	{
		return $this->title;
	}
	
	function get_email()
	{
		return $this->email;
	}
	
	function get_phone()
	{
		return $this->phone;
	}
	
		
	//custom data functions
		

}


?>