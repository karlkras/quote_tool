<?PHP

date_default_timezone_set('America/Los_Angeles');

class estimate
{
	protected $estimateDate;
	protected $clientName;
	protected $clientType;
	protected $projectName;
	protected $projectType;
	protected $fileType;
	protected $deliverable;
	protected $deliveryDate;
	protected $rushFee;
	protected $rushFeeMultiplier;
	protected $notes;
	protected $pages;
	protected $pagesPerHour;
	protected $projectID;
	protected $requestedServices = array();
	protected $discountType;
	protected $discountPercent;
	protected $discountAmount;
	protected $dtpCoordPercent;
	protected $numberOfGraphics;
	protected $numberOfScaps;
	protected $qaCoordPercent;
	protected $projDesc;
	protected $billingTerms;
	protected $billingTermsOther;
	protected $billingCycle;
	protected $billingCycleOther;
	protected $quoteType;
	protected $sourceLanguage;
	protected $targetLanguage = array();
	protected $otherLangName;
	protected $otherLangNewTextCost;
	protected $otherLangFuzzyTextCost;
	protected $otherLangMatchTextCost;
	protected $otherLangTransHourlyCost;
	protected $otherLangPRHourlyCost;
	protected $wordCountStyle;
	protected $newText;
	protected $fuzzyText;
	protected $matchText;
	protected $totalText;
	protected $percentLeverage;
	
	
	//constructor
	function __construct()
	{
		$dateFormat = "M d, Y";
		$this->estimateDate = date($dateFormat);
		$this->deliveryDate = date($dateFormat);
		$this->representative = "Default";
		$this->clientName = "Default";
		$this->clientType = "cusClient";
		$this->projectName= "Default";
		$this->projectType = "ptDoc";
		$this->fileType = "ftWord";
		$this->deliverable = "Default";
		$this->rushFee = FALSE;
		$this->rushFeeMultiplier = 0;
		$this->notes = "none";
		$this->pages = 0;
		$this->pagesPerHour = 6;
		$this->projectID = 0;
		$this->discountType = "none";
		$this->discountPercent = 0;
		$this->discountAmount = 0;
		$this->dtpCoordPercent = 10;
		$this->qaCoordPercent = 10;
		$this->quoteType = 'qtEstimate';
		$this->sourceLanguage = "English (US)";
		
	}
	

	
	
	//setters
	function set_numberOfGraphics($num)
	{
		$this->numberOfGraphics = $num;
	}
	
	function set_numberOfScaps($num)
	{
		$this->numberOfScaps = $num;
	}
	
	function set_clientType($type)
	{
		$this->clientType = $type;
	}
	
	function set_billingTerms($terms)
	{
		$this->billingTerms = $terms;
	}
	
	function set_billingTermsOther($value)
	{
		$this->billingTermsOther = $value;
	}
	
	function set_billingCycle($terms)
	{
		$this->billingCycle = $terms;
	}
	
	function set_billingCycleOther($value)
	{
		$this->billingCycleOther = $value;
	}
	
	function set_dtpCoordPercent($percent)
	{
		$this->dtpCoordPercent = $percent;
	}
	
	function set_qaCoordPercent($percent)
	{
		$this->qaCoordPercent = $percent;
	}
	
	function set_discountType($type)
	{
		$this->discountType = $type;
	}
	
	function set_discountPercent($percent)
	{
		$this->discountPercent = $percent;
	}
	
	function set_discountAmount($amount)
	{
		$this->discountAmount = $amount;
	}
	
	function set_projectID($id)
	{
		$this->projectID = $id;
	}
	
	function set_pages($pages)
	{
		$this->pages = $pages;
	}
	
	function set_pagesPerHour($rate)
	{
		$this->pagesPerHour = $rate;
	}
	
	function set_estimateDate($date)
	{
		$this->estimateDate = $date;
	}
	
	function set_deliveryDate($date)
	{
		$this->deliveryDate = $date;
	}
	

	function set_clientName($name)
	{
		$this->clientName = $name;
	}
	
	function set_projectName($name)
	{
		$this->projectName = $name;
	}
	
	function set_projectType($type)
	{
		$this->projectType = $type;
	}
	
	function set_fileType($type)
	{
		$this->fileType = $type;
	}
	
	function set_deliverable($type)
	{
		$this->deliverable = $type;
	}
	
	function set_rushFee($bool)
	{
		$this->rushFee = $bool;
	}
	
	function set_rushFeeMultiplier($percent)
	{
		if ($percent != 0)
			$this->rushFee = TRUE;
		else
			$this->rushFee = FALSE;
			
		$this->rushFeeMultiplier = $percent;
	}
	
	function set_notes($note)
	{
		$this->notes = $note;
	}
	
	function set_projDesc($note)
	{
		$this->projDesc = $note;
	}
	
	function set_quoteType($type)
	{
		$this->quoteType = $type;
	}
	
	function set_sourceLanguage($lang)
	{
		$this->sourceLanguage = $lang;
	}
	
	function set_otherLangName($name)
	{
		$this->otherLangName = $name;
	}
	
	function set_otherLangNewTextCost($cost)
	{
		$this->otherLangNewTextCost = $cost;
	}
	
	function set_otherLangFuzzyTextCost($cost)
	{
		$this->otherLangFuzzyTextCost = $cost;
	}
	
	function set_otherLangMatchTextCost($cost)
	{
		$this->otherLangMatchTextCost = $cost;
	}
	
	function set_otherLangTransHourlyCost($cost)
	{
		$this->otherLangTransHourlyCost = $cost;
	}
	
	function set_otherLangPRHourlyCost($cost)
	{
		$this->otherLangPRHourlyCost = $cost;
	}
	
	//getters
	function get_numberOfGraphics()
	{
		return $this->numberOfGraphics;
	}
	
	function get_numberOfScaps()
	{
		return $this->numberOfScaps;
	}
	
	function get_clientType()
	{
		return $this->clientType;
	}
	
	function get_billingTerms()
	{
		return $this->billingTerms;
	}
	
	function get_billingTermsOther()
	{
		return $this->billingTermsOther;
	}
	
	function get_billingCycle()
	{
		return $this->billingCycle;
	}
	
	function get_billingCycleOther()
	{
		return $this->billingCycleOther;
	}
	
	function get_dtpCoordPercent()
	{
		return $this->dtpCoordPercent;
	}
	
	function get_qaCoordPercent()
	{
		return $this->qaCoordPercent;
	}
	
	function get_discountType()
	{
		return $this->discountType;
	}
	
	function get_discountPercent()
	{
		return $this->discountPercent;
	}
	
	function get_discountAmount()
	{
		return $this->discountAmount;
	}
	
	function get_services()
	{
		return $this->requestedServices;
	}
	
	function get_service($index)
	{
		return $this->requestedServices[$index];
	}
	function get_projectID()
	{
		return $this->projectID;
	}
	
	function get_pages()
	{
		return $this->pages;
	}
	
	function get_pagesPerHour()
	{
		return $this->pagesPerHour;
	}
	
	function get_estimateDate()
	{
		return $this->estimateDate;
	}
	
	function get_deliveryDate()
	{
		return $this->deliveryDate;
	}
	
	
	function get_clientName()
	{
		return $this->clientName;
	}
	
	function get_projectName()
	{
		return $this->projectName;
	}
	
	function get_projectType()
	{
		return $this->projectType;
	}
	
	function get_fileType()
	{
		return $this->fileType;
	}
	
	function get_deliverable()
	{
		return $this->deliverable;
	}
	
	function get_rushFee()
	{
		return $this->rushFee;
	}
	
	function get_rushFeeMultiplier()
	{			
		return $this->rushFeeMultiplier;
	}
	
	function get_notes()
	{
		return $this->notes;
	}
	
	function get_projDesc()
	{
		return $this->projDesc;
	}
	
	function get_quoteType()
	{
		return $this->quoteType;
	}
	
	function get_sourceLanguage()
	{
		return $this->sourceLanguage;
	}
	
	function get_otherLangName()
	{
		return $this->otherLangName;
	}
	
	function get_otherLangNewTextCost()
	{
		return $this->otherLangNewTextCost;
	}
	
	function get_otherLangFuzzyTextCost()
	{
		return $this->otherLangFuzzyTextCost;
	}
	
	function get_otherLangMatchTextCost()
	{
		return $this->otherLangMatchTextCost;
	}
	
	function get_otherLangTransHourlyCost()
	{
		return $this->otherLangTransHourlyCost;
	}
	
	function get_otherLangPRHourlyCost()
	{
		return $this->otherLangPRHourlyCost;
	}
	
	function get_targetLanguages()
	{
		return $this->targetLanguage;
	}
	
	function get_targetLanguage($index)
	{
		if ($index >= count($this->targetLanguage))
			return NULL;
		else
			return $this->targetLanguage[$index];
	}
	
		
	//custom data functions
	function add_service($service)
	{
		$index = count($this->requestedServices);
		$this->requestedServices[] = $service;
		
		return $index;
	}
	
	function remove_service_at($index)
	{
		unset($this->requestedServices[$index]);
		$this->requestedServices = array_values($this->requestedServices);
	}
	
	function count_services()
	{
		return count($this->requestedServices);
	}
	
	function addTargetLang($language)
	{
		$index = count($this->targetLanguage);
		$this->targetLanguage[] = $language;
		
		return $index;
	}
	
	
}


?>