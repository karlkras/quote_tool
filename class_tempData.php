<?PHP

class tempData
{
	protected $quoteType;
	protected $estimateDate;
	protected $projectID;
	protected $representative;
	protected $projectManager;
	protected $clientType;
	protected $clientName;
	protected $prospectName;
	protected $projectType;
	protected $projectTypeOther;
	protected $fileType;
	protected $deliverable;
	protected $requestedServices = array();
	protected $sourceLanguage;
	protected $targetLanguage = array();
	protected $otherLangName;
	protected $otherLangNewTextCost;
	protected $otherLangFuzzyTextCost;
	protected $otherLangMatchTextCost;
	protected $otherLangTransHourlyCost;
	protected $otherLangPRHourlyCost;
	protected $deliveryDate;
	protected $rushFee;
	protected $discountType;
	protected $discountPercent;
	protected $discountAmount;
	protected $description;
	protected $notes;
	protected $billingTerms;
	protected $billingTermsOther;
	protected $billingCycle;
	protected $billingCycleOther;
	protected $wordCountStyle;
	protected $newText;
	protected $fuzzyText;
	protected $matchText;
	protected $totalText;
	protected $percentLeverage;
	protected $linguisticCostType;
	protected $linguisticSellType;
	protected $unitsLocked;
	protected $proofreading;
	protected $numberPages;
	protected $dtpPagesPerHour;
	protected $dtpHours;
	protected $costPerPage;
	protected $dtpCostType;
	protected $dtpSellTeyp;
	protected $numberGraphics;
	protected $graphicsPerHour;
	protected $dtpCoordPercent;
	protected $tmWork;
	protected $fileTreatment;
	protected $numberScaps;
	protected $scapsPerHour;
	protected $qaPagesPerHour;
	protected $qaHours;
	protected $qaCoordPercent;
	protected $pmPercent;
	protected $addTask1_cost;
	protected $addTask1_description;
	protected $addTask2_cost;
	protected $addTask2_description;
	protected $addTask3_cost;
	protected $addTask3_description;
	
	
	
	
	//constructor
	function __construct()
	{
		$this->quoteType = "qtEstimate";
			date_default_timezone_set('America/Los_Angeles');
			$dateFormat = "M d, Y";
		$this->todayDate = date($dateFormat);
		$this->estimateDate = $todayDate;
		$this->projectID = "";
		$this->representative = "";
		$this->projectManager = "";
		$this->clientType = "cusClient";
		$this->clientName = "";
		$this->prospectName = "";
		$this->projectType = "ptWeb";
		$this->projectTypeOther = "";
		$this->fileType = "ftWord";
		$this->deliverable = "";
		$this->sourceLanguage = "English (US)";
		$this->otherLangName = "";
		$this->otherLangNewTextCost = "";
		$this->otherLangFuzzyTextCost = "";
		$this->otherLangMatchTextCost = "";
		$this->otherLangTransHourlyCost = "";
		$this->otherLangPRHourlyCost = "";
		$this->deliveryDate = "";
		$this->rushFee = "rf0";
		$this->discountType = "percent";
		$this->discountPercent = 0;
		$this->discountAmount = 0;
		$this->description = "";
		$this->notes = "";
		$this->billingTerms = "30 days";
		$this->billingTermsOther = "";
		$this->billingCycle = 'On Delivery';
		$this->billingCycleOther = "";
		$this->wordCountStyle = 'trados';
		$this->newText = 0;
		$this->fuzzyText = 0;
		$this->matchText = 0;
		$this->totalText = 0;
		$this->percentLeverage = 0;
		$this->linguisticCostType = "Words";
		$this->linguisticSellType = "Words";
		$this->unitsLocked = TRUE;
		$this->proofreading = 0;
		$this->numberPages = 0;
		$this->dtpPagesPerHour = 6;
		$this->dtpHours = 0;
		$this->costPerPage = 0;
		$this->dtpCostType = "Hours";
		$this->dtpSellTeyp = "Hours";
		$this->numberGraphics = 0;
		$this->graphicsPerHour = 6;
		$this->dtpCoordPercent = 10;
		$this->tmWork = 0;
		$this->fileTreatment = 0;
		$this->numberScaps = 0;
		$this->scapsPerHour = 8;
		$this->qaPagesPerHour = 12;
		$this->qaHours = 0;
		$this->qaCoordPercent = 10;
		$this->pmPercent = 10;
		$this->addTask1_cost = "";
		$this->addTask1_description = "";
		$this->addTask2_cost = "";
		$this->addTask2_description = "";
		$this->addTask3_cost = "";
		$this->addTask3_description = "";
	}
	
	//setters
	function set_quoteType($type)
	{
		$this->quoteType = $type;
	}
	
	function set_estimateDate($date)
	{
		$this->estimateDate = $date;
	}
	
	function set_projectID($id)
	{
		$this->projectID = $id;
	}
	
	function set_representative($rep)
	{
		$this->representative = $rep;
	}
	
	function set_projectManager($pm)
	{
		$this->projectManager = $pm;
	}
	
	function set_clientType($type)
	{
		$this->clientType = $type;
	}
	
	function set_clientName($name)
	{
		$this->clientName = $name;
	}
	
	function set_prospectName($name)
	{
		$this->prospectName = $name;
	}
	
	function set_projectType($type)
	{
		$this->projectType = $type;
	}
	
	function set_projectTypeOther($value)
	{
		$this->projectTypeOther = $value;
	}
	
	function set_fileType($type)
	{
		$this->fileType = $type;
	}
	
	function set_deliverable($value)
	{
		$this->deliverable = $value;
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
	
	function set_deliveryDate($date)
	{
		$this->deliverDate = $date;
	}
	
	function set_rushFee($fee)
	{
		$this->rushFee = $fee;
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
	
	function set_description($desc)
	{
		$this->description = $desc;
	}
	
	function set_notes($note)
	{
		$this->notes = $note;
	}
	
	function set_billingTerms($terms)
	{
		$this->billingTerms = $terms;
	}
	
	function set_billingTermsOther($value)
	{
		$this->billingTermsOther = $value;
	}
	
	function set_billingCycle($cycle)
	{
		$this->billingCycle = $cycle;
	}
	
	function set_billingCycleOther($value)
	{
		$this->billingCycleOther = $value;
	}
	
	function set_wordCountStyle($style)
	{
		$this->wordCountStyle = $style;
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
	
	function set_totalText($units)
	{
		if ($units == NULL)
			$this->totalText = $this->newText + $this->fuzzyText + $this->matchText;
		else
			$this->totalText = $units;
			
		return $this->totalText;
	}
	
	function set_percentLeverage($percent)
	{
		$this->percentleverage = $percent;
	}
	
	function set_linguisticCostType($type)
	{
		$this->linguisticCostType = $type;
	}
	
	function set_linguisticSellType($type)
	{
		$this->linguisticSellType = $type;
	}
	
	function set_unitsLocked($bool)
	{
		if ( ($bool == TRUE) || ($bool == true) || (strtoupper($bool) == 'TRUE') )
			$this->unitsLocked = TRUE;
		else
			$this->unitsLocked = FALSE;
	}
	
	function set_proofreading($units)
	{
		$this->proofreading = $units;
	}
	
	function set_numberPages($num)
	{
		$this->numberPages = $num;
	}
	
	function set_dtpPagesPerHour($rate)
	{
		$this->dtpPagesPerHour = $rate;
	}
	
	function set_dtpHours($units)
	{
		if ($units == NULL)
			$this->dtpHours = $this->numberPages / $this->dtpPagesPerHour;
		else
			$this->dtpHours = $units;
			
		return $this->dtpHours;
	}
	
	function set_costPerPage($cost)
	{
		$this->costPerPage = $cost;
	}
	
	function set_dtpCostType($type)
	{
		$this->dtpCostType = $type;
	}
	
	function set_dtpSellType($type)
	{
		$this->dtpSellType = $type;
	}
	
	function set_numberGraphics($num)
	{
		$this->numberGraphics = $num;
	}
	
	function set_graphicsPerHour($num)
	{
		$this->graphicsPerHour = $num;
	}
	
	function set_dtpCoordPercent($percent)
	{
		$this->dtpCoordPercent = $percent;
	}
	
	function set_tmWork($units)
	{
		$this->tmWork = $units;
	}
	
	function set_fileTreatment($units)
	{
		$this->fileTreatment = $units;
	}
	
	function set_numberScaps($num)
	{
		$this->numberScaps = $num;
	}
	
	function set_scapsPerHour($num)
	{
		$this->scapsPerHour = $num;
	}
	
	function set_qaPagesPerHour($num)
	{
		$this->qaPagesPerHour = $num;
	}
	
	function set_qaHours($num)
	{
		if ($num == NULL)
			$this->qaHours = $this->numberPages / $this->qaPagesPerHour;
		else
			$this->qaHours = $num;
		
		return $this->qaHours;
	}
	
	function set_qaCoordPercent($percent)
	{
		$this->qaCoordPercent = $percent;
	}
	
	function set_pmPercent($percent)
	{
		$this->pmPercent = $percent;
	}
	
	function set_addTask1_cost($cost)
	{
		$this->addTask1_cost = $cost;
	}
	
	function set_addTask1_description($desc)
	{
		$this->addTask1_description = $desc;
	}
	
	function set_addTask2_cost($cost)
	{
		$this->addTask2_cost = $cost;
	}
	
	function set_addTask2_description($desc)
	{
		$this->addTask2_description = $desc;
	}
	
	function set_addTask3_cost($cost)
	{
		$this->addTask3_cost = $cost;
	}
	
	function set_addTask3_description($desc)
	{
		$this->addTask3_description = $desc;
	}
		
	
	//getters
	function get_quoteType()
	{
		return $this->quoteType;
	}
	
	function get_estimateDate()
	{
		return $this->estimateDate;
	}
	
	function get_projectID()
	{
		return $this->projectID;
	}
	
	function get_representative()
	{
		return $this->representative;
	}
	
	function get_projectManager()
	{
		return $this->projectManager;
	}
	
	function get_clientType()
	{
		return $this->clientType;
	}
	
	function get_clientName()
	{
		return $this->clientName;
	}
	
	function get_prospectName()
	{
		return $this->prospectName;
	}
	
	function get_projectType()
	{
		return $this->projectType;
	}
	
	function get_projectTypeOther()
	{
		return $this->projectTypeOther;
	}
	
	function get_fileType()
	{
		return $this->fileType;
	}
	
	function get_deliverable()
	{
		return $this->deliverable;
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
	
	function get_deliveryDate()
	{
		return $this->deliverDate;
	}
	
	function get_rushFee()
	{
		return $this->rushFee;
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
	
	function get_description()
	{
		return $this->description;
	}
	
	function get_notes()
	{
		return $this->notes;
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
	
	function get_wordCountStyle()
	{
		return $this->wordCountStyle;
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
	
	function get_totalText()
	{
		return $this->totalText;
	}
	
	function get_percentLeverage()
	{
		return $this->percentleverage;
	}
	
	function get_linguisticCostType()
	{
		return $this->linguisticCostType;
	}
	
	function get_linguisticSellType()
	{
		return $this->linguisticSellType;
	}
	
	function get_unitsLocked()
	{
		return $this->unitsLocked;
	}
	
	function get_proofreading()
	{
		return $this->proofreading;
	}
	
	function get_numberPages()
	{
		return $this->numberPages;
	}
	
	function get_dtpPagesPerHour()
	{
		return $this->dtpPagesPerHour;
	}
	
	function get_dtpHours()
	{
		return $this->dtpHours;
	}
	
	function get_costPerPage()
	{
		return $this->costPerPage;
	}
	
	function get_dtpCostType()
	{
		return $this->dtpCostType;
	}
	
	function get_dtpSellType()
	{
		return $this->dtpSellType;
	}
	
	function get_numberGraphics()
	{
		return $this->numberGraphics;
	}
	
	function get_graphicsPerHour()
	{
		return $this->graphicsPerHour;
	}
	
	function get_dtpCoordPercent()
	{
		return $this->dtpCoordPercent;
	}
	
	function get_tmWork()
	{
		return $this->tmWork;
	}
	
	function get_fileTreatment()
	{
		return $this->fileTreatment;
	}
	
	function get_numberScaps()
	{
		return $this->numberScaps;
	}
	
	function get_scapsPerHour()
	{
		return $this->scapsPerHour;
	}
	
	function get_qaPagesPerHour()
	{
		return $this->qaPagesPerHour;
	}
	
	function get_qaHours()
	{
		return $this->qaHours;
	}
	
	function get_qaCoordPercent()
	{
		return $this->qaCoordPercent;
	}
	
	function get_pmPercent()
	{
		return $this->pmPercent;
	}
	
	function get_addTask1_cost()
	{
		return $this->addTask1_cost;
	}
	
	function get_addTask1_description()
	{
		return $this->addTask1_description;
	}
	
	function get_addTask2_cost()
	{
		return $this->addTask2_cost;
	}
	
	function get_addTask2_description()
	{
		return $this->addTask2_description;
	}
	
	function get_addTask3_cost()
	{
		return $this->addTask3_cost;
	}
	
	function get_addTask3_description()
	{
		return $this->addTask3_description;
	}
	
	function get_requestedServices()
	{
		return $this->requestedServices;
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
	function addReqServ($key, $value)
	{
		$this->requestedServices[$key] = $value;
		
	}
	
	function addTargetLang($language)
	{
		$index = count($this->targetLanguage);
		$this->targetLanguage[] = $language;
		
		return $index;
	}
		

}


?>