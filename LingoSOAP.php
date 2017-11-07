<?PHP

class getLinguistsByLanguages 
{
	public $arg0;
	public $arg1;
}

class company 
{
	public $id;	//int
	public $name;	//string
}

class linguistCostDetails 
{
	public $copyEdit; //double 
	public $hourly; //double 
	public $mininum; //double 
	public $sourceLanguage; //string
	public $targetLanguage; //string
	public $tr_100Match; //double
	public $tr_Help_100Match; //double
	public $tr_Help_fuzzy;	//double
	public $tr_Help_new;	//double
	public $tr_Marketing_100Match;	//double
	public $tr_Marketing_fuzzy;	//double
	public $tr_Marketing_new;	//double
	public $tr_Medical_100Match;	//double
	public $tr_Medical_fuzzy;	//double
	public $tr_Medical_new;	//double
	public $tr_Rush_100Match;	//double
	public $tr_Rush_fuzzy;	//double
	public $tr_Rush_new;	//double
	public $tr_Technical_100Match;	//double
	public $tr_Technical_fuzzy;	//double
	public $tr_Technical_new;	//double
	public $tr_UI_100Match;	//double
	public $tr_UI_fuzzy;	//double
	public $tr_UI_new;	//double
	public $tr_fuzzy;	//double
	public $tr_new;	//double
	public $trce_100Match;	//double
	public $trce_Help_100Match;	//double
	public $trce_Help_fuzzy;	//double
	public $trce_Help_new;	//double
	public $trce_Marketing_100Match;	//double
	public $trce_Marketing_fuzzy;	//double
	public $trce_Marketing_new;	//double
	public $trce_Medical_100Match;	//double
	public $trce_Medical_fuzzy;	//double
	public $trce_Medical_new;	//double
	public $trce_Rush_100Match;	//double
	public $trce_Rush_fuzzy;	//double
	public $trce_Rush_new;	//double
	public $trce_Technical_100Match;	//double
	public $trce_Technical_fuzzy;	//double
	public $trce_Technical_new;	//double
	public $trce_UI_100Match;	//double
	public $trce_UI_fuzzy;	//double
	public $trce_UI_new;	//double
	public $trce_fuzzy;	//double
	public $trce_new;	//double

}

class user 
{
	public $address;	//string
	public $address2;	//string
	public $city;	//string
	public $company; //company;
	public $email;	//string
	public $firstName;	//string
	public $id;	//int
	public $phone;	//string
	public $postalCode;	//string
	public $roles;	//string
	public $state;	//string
	public $timeZone;	//string
	public $userName;	//string
	
	function __construct()
	{
		$this->company = new company;
	}
}

class linguist 
{
	public $costDetails;	//linguistCostDetails;
	public $userInformation;	// user;
	
	function __construct()
	{
		$this->costDetails = new linguistCostDetails;
		$this->userInformation = new user;
	}
}

class getLinguistsByLanguagesResponse 
{
	 public $return;
	 
	 function __construct()
	 {
	 	$this->return = new linguist;
	 }
}



class getLinguistAgencysResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return = new agency;
	}
}

class agency 
{
	public $contactData;
	public $name;	//string
	
	function __construct()
	{
		$this->contactData = new user;
	}
}

class getLibraryTaskServiceResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return = new taskService;
	}
}

class taskService 
{
	public $billableTasks;
	public $discount;	//double
	public $linguistTasks;
	public $rushFee;	//double
	
	function __construct()
	{
		$this->billableTasks = new billableTask;
		$this->linguistTasks = new linguistTask;
	}
}

class billableTask 
{
	public $hourlyRate;	//double
	public $task;
	
	function __construct()
	{
		$this->task = new task;
	}
}

class task 
{
	public $id;	//int
	public $name;	//string
	public $plannedMinutes;	//int
	public $price;	//double
	public $projectID;	//int
	public $type;	//string
}

class linguistTask 
{
	public $targLang;	//string 
	public $task;
	public $wordCounts;
	public $wordRateDetails;
	
	function __construct()
	{
		$this->task = new task;
		$this->wordCounts = new wordCounts;
		$this->wordRateDetails = new linguistCostDetails;
	}
}

class wordCounts 
{
	public $fuzzyWords;	// int
	public $matchRepsWords;	// int
	public $newWords;	//int
}

class getInternalStandardRate 
{
	public $arg0;	//string 
}

class getInternalStandardRateResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return  = new internalCostDetails;
	}
}

class internalCostDetails 
{
	public $billHourly;	// double 
	public $costHourly;	// double 
}

class getLingoStaffResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return = new userService;
	}
}

class userService 
{
	public $projectManagers;
	public $salesReps;
	
	function __construct()
	{
		$this->projectManagers = new  user;
		$this->salesReps = new user;
	}
}

class getQuotableProjectsResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return = new project;
	}
}

class project 
{
	public $company;
	public $contact;
	public $id;	// int
	public $name;	// string
	public $sponsor;
	
	function __construct()
	{
		$this->company = new company;
		$this->contact = new user;
		$this->sponsor = new user;
	}
}

class getTaskService 
{
	public $project;
	
	function __construct()
	{
		$this->project = new project;
	}
}

class getTaskServiceResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return = new taskService;
	}
}

class getLanguageServiceResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return = new languageService;
	}
}

class languageService 
{
	public $sourceLanguages;	// string 
	public $targetLanguages;	// string 
}

class updateTaskPricing 
{
	public $taskService;
	
	function __construct()
	{
		$this->taskService = new taskService;
	}
}

class setStatus 
{
	public $arg0;
	public $arg1;	// string 
	
	function __construct()
	{
		$this->arg0 = new  project;
	}
}

class setStatusResponse 
{
	public $return;	// boolean
}

class attachFile 
{
	public $arg0;
	public $arg1;	// string
	public $arg2;	// base64Binary 
	
	function __construct()
	{
		$this->arg0 = new project;
	}
}

class attachFileResponse 
{
	public $return;	// boolean 
}

class getLinguistStandardRate 
{
	public $arg0;	// string 
	public $arg1;	// string 
}

class getLinguistStandardRateResponse 
{
	public $return;
	
	function __construct()
	{
		$this->return = new linguistCostDetails;
	}
}

class getLingoClientsResponse 
{
	 public $return;
	 
	 function __construct()
	{
		$this->return = new user;
	}
}







?>