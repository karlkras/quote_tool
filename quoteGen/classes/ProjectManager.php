<?php

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

require_once(__DIR__ . '/../../attaskconn/LingoAtTaskService.php');
include_once(__DIR__ . '/projectData.php');
include_once(__DIR__ . '/LinguisticQuoteLineItemGenerator.php');
include_once(__DIR__ . '/BillableQuoteLineItemGenerator.php');
require_once(__DIR__ . '/PricingMySql.php');
require_once(__DIR__ . '/TaskCatalogService.php');
include_once(__DIR__ . '/htmlTaskFactory.php');
require_once(__DIR__ . '/ProjectInfo.php');
require_once (__DIR__ . "/CategoryPrinterController.php");
require_once (__DIR__ . "/SummaryController.php");
require_once (__DIR__ . "/LinguistTaskCategoryFrame.php");
require_once (__DIR__ . "/OtherTaskCategoryFrame.php");
require_once (__DIR__ . "/../interfaces/IQuoteItemContainer.php");
require_once (__DIR__ . "/AdditionalFeesAndDiscountController.php");
require_once (__DIR__ . '/ProjectManagementTaskInfo.php');
require_once (__DIR__ . '/ProjectManagerQuoteLineItem.php');
require_once (__DIR__ . '/QuoteItemCategory.php');
require_once (__DIR__ . '/QuoteToolUtils.php');
require_once (__DIR__ . "/../interfaces/IPMInfoSupport.php");
require_once (__DIR__ . '/BillableQuoteLineItemHelper.php');
require_once (__DIR__ . '/RushFeeCalculator.php');
require_once (__DIR__ . '/../../classes/fauxWorkFront/project.php');
require_once (__DIR__ . '/../../classes/fauxWorkFront/taskService.php');

/**
 * Description of ProjectManager
 *
 * @author Axian Developer
 */
class ProjectManager implements JsonSerializable, IPMInfoSupport {

    protected $rateRush = 1;
    protected $projectObj;
    protected $api;
    protected $projectData;
    protected $taskCatalog;
    protected $pricing;
    protected $myDBConn;
    protected $projectInfo;
    protected $taskService;
    protected $categoryPrinterController;
    protected $pmPercent;
    protected $discountAmount = 0;
    protected $rushFees = 0;
    protected $discountType;
    protected $otherServices;
    protected $qaTasks = array();
    protected $projectManagementTaskInfo;
    protected $projectManagerTaskArray = array();
    protected $chargeForProofReading = true;
    protected $ajaxResponseArray = array();
    protected $allTasks;
    protected $nonTratosFormatTask = null;
    protected $pricingDbTable;
    protected $projectMinimum = -1;
    protected $projectRushMinimum = -1;
    protected $minimumApplied = false;
    protected $workFrontTaskArray = array();
    protected $defaultRushFeePercentage = 0.0;
    protected $isBallparker = false;
    protected $callingPage = "";
    protected $applyRushFee = false;
    protected $packageEngineering = false;
    protected $packageAllInternal = false;
    protected $isProjectRushMinimum = false;

    const QA_ITEMS_ARRAY = ["QA Coordination", "QA 1", "Quality Assurance Review"];
    const QA_COORDINATION = 0;
    const QA_1 = 1;
    const QA_REVIEW = 2;

    protected $taskFrameArray = array();

    public function __construct() {
        $this->connect();
        $this->initialize();
        $this->taskCatalog = new TaskCatalogService($this->myDBConn, $this->pricingDbTable);
        $this->categoryPrinterController = new CategoryPrinterController();
        $this->getTaskList();
        $this->categorizeLinguistTaskList();
        $this->createLinguistTaskFrames();
        $this->createProjectManagerCategory();
        $this->initializeQATasks();
        $this->projectInfo = new ProjectInfo();
        $this->createOtherServiceFrame();



        $this->cleanUp();
    }

    public function exportXml($taskNameArray, $rolledNameArray, $updateAtTask) {
        require_once (__DIR__ . '/XMLGenerator.php');

        $this->updateTaskService();

        $generator = new XMLGenerator($this, $taskNameArray, $rolledNameArray, $updateAtTask);
        $doc = $generator->generateXML();

        // now...
        if ($updateAtTask) {
            $this->updateTaskServiceWithProjectMininum();
        }

        return $doc;
    }

    public function getProjectObj() {
        return $this->projectObj;
    }

    public function getProjectData() {
        return $this->projectData;
    }

    public function getTaskService() {
        return $this->taskService;
    }

    public function getOtherFrame() {
        return $this->otherServices;
    }

    public function saveToWorkfront() {
        try {
            $api = new LingoAtTaskService();
            $u = new updateProjectPricing();
            $u->projectObject = $this->projectObj;
            $u->tskService = $this->taskService;

            $api->updateProjectPricing($u);
        } catch (SoapFault $sf) {
            ob_end_clean();
            echo "<br><strong style=\"font-size:1.1em\">We're sorry, there was a problem updating WorkFront:</strong> " . $sf->getMessage() . "<br>";
            echo "<i>" . $sf->detail->ProcessFault->message . "</i><br>";
        } catch (exception $e) {
            ob_end_clean();
            echo "<br><strong>A generic error has occurred: $e->faultstring</strong><br>";
            echo "<hr>Debug Data:<br>";
            echo "Error:<pre>\n";
            var_dump($e);
            echo "</pre>";
            exit;
        }
    }

    public function applyProjectMinimumToWorkFront($total) {
        foreach ($this->taskFrameArray as $theFrame) {
            $lingCategory = $theFrame->getCategories()['Linguistic'];
            $theItems = $lingCategory->getLineItems();
            foreach ($theItems as $task) {
                if (is_a($task, 'LinguistQuoteItemContainer')) {
                    $id = $task->getId();
                    $theWorkFrontTask = $this->workFrontTaskArray["linguistTask"][$id];
                    $theWorkFrontTask->price = $total;
                    break;
                }
            }
            break;
        }
    }

    public function updateTaskServiceWithProjectMininum() {

        $total = $this->getProjectFinalSellTotal();

        if ($this->isProjectMinimumApplied()) {
            $this->resetWorkfrontTaskService();
            $this->applyProjectMinimumToWorkFront($total);
        }
    }

    public function updateTaskService() {
        $this->resetWorkfrontTaskService();

        $langMinimumList = array();
        $languageList = array();

        foreach ($this->taskFrameArray as $lang => $frame) {
            $languageList += [$frame->getId() => array()];
            // first check to see if a minimum language price has been applied
            $langTotal = $frame->getLinguistSellPriceTotal();
            $lingMinimumApplied = false;

            if ($frame->isTotalMinimumApplied()) {
                $langMinimumList += [$frame->getId() => array()];
                $lingMinimumApplied = true;
            }

            // if the minimum we need to find the tr+ce task and set it's price
            // to the minimum and leave the other tasks 0
            if ($lingMinimumApplied) {
                $this->applyLangMinimum($frame, $langTotal);
                continue;
            }

            $theCategories = $frame->getCategories();
            foreach ($theCategories as $catName => $category) {
                $theItems = $category->getLineItems();
                foreach ($theItems as $task) {
                    if (is_a($task, 'BillableQuoteLineItem')) {
                        if ($task->isDistributed()) {
                            continue;
                        }
                    }
                    $id = $task->getId();
                    $totalAmount = $task->getActualSellPriceTotal();
                    if (is_a($task, 'LinguistQuoteLineItem') || is_a($task, 'LinguistQuoteItemContainer')) {
                        $theWorkFrontTask = $this->workFrontTaskArray["linguistTask"][$id];
                        if (!is_null($this->getFormatTaskForNonTratos())) {
                            if (($theWorkFrontTask->type == "TR+CE") || ($theWorkFrontTask->type == "TR")) {
                                $formatDistro = $this->getDistributedTask(QuoteToolUtils::makeLanguageId($lang), $this->getFormatTaskForNonTratos()->getId());
                                $totalAmount += $formatDistro->getActualSellPriceTotal();
                            }
                        }
                        $theWorkFrontTask->price = $totalAmount;
                        if (is_a($task, 'LinguistQuoteLineItem')) {
                            $theWorkFrontTask->workRequred = $task->getWorkUnitCount();
                        }
                    } else {
                        $theWorkFrontTask = $this->workFrontTaskArray["billableTask"][$id];
                        $theWorkFrontTask->price = $totalAmount;
                        $theWorkFrontTask->workRequred = $task->getWorkUnitCount();
                    }
                }
            }
        }

        // now the distributed guys...
        $distroTasks = $this->taskCatalog->getDistributedTaskCatalog()->getDistributedArray();
            
        $fakeId = null;
        if (!is_null($this->getFormatTaskForNonTratos())) {
            $fakeId = $this->getFormatTaskForNonTratos()->getId();
        }
        foreach ($distroTasks as $language => $idArray) {
            
            if (array_key_exists(QuoteToolUtils::makeLanguageId($language), $langMinimumList)) {
                continue;
            }
            
            foreach ($idArray as $id => $task) {
                if (!is_null($fakeId) && $id == $fakeId) {
                    continue;
                }
                $totalAmount = $task->getActualSellPriceTotal();
                $workUnitCount = $task->getWorkUnitCount();
                $theWorkFrontTask = $this->workFrontTaskArray["billableTask"][$id];
                if (!is_null($theWorkFrontTask)) {
                    $theWorkFrontTask->price += $totalAmount;
                    $theWorkFrontTask->workRequired += $workUnitCount;
                }
            }
        }

        // now "other" tasks. Usually where we find the Project Manager object...
        if ($this->haveOtherServices()) {
            $otherFrame = $this->otherServices;
            $theCats = $otherFrame->getCategories();
            $lineItems = $theCats->getLineItems();
            foreach ($lineItems as $task) {
                $id = $task->getId();
                $totalAmount = $task->getActualSellPriceTotal();
                $workUnitCount = $task->getWorkUnitCount();
                $theWorkFrontTask = $this->workFrontTaskArray["billableTask"][$id];
                $theWorkFrontTask->price += $totalAmount;
                $theWorkFrontTask->workRequired += $workUnitCount;
            }
        }

        //finally, if we have a distributed project manager floating around...
        if (!empty($this->projectManagerTaskArray)) {
            $pmDistroList = array();
            foreach ($languageList as $langId => $theArray) {
                if (!array_key_exists($langId, $langMinimumList)) {
                    $distoPM = $this->getDistroPM($langId);
                    if (!is_null($distoPM)) {
                        array_push($pmDistroList, $distoPM);
                    }
                }
            }
            if (!empty($pmDistroList)) {
                $id = $pmDistroList[0]->getId();

                $theProjectManTask = null;
                $strPos = strpos($id, "-");
                $id = substr($id, 0, $strPos);
                $theProjectManTask = $this->workFrontTaskArray["billableTask"][$id];

                $sellTotal = 0.0;
                $unitCount = 0.0;
                foreach ($pmDistroList as $pmTask) {
                    $sellTotal += $pmTask->getActualSellPriceTotal();
                    $unitCount += $pmTask->getWorkUnitCount();
                }
                $theProjectManTask->price = $sellTotal;
                $theProjectManTask->workRequired = $unitCount;
            }
        }


        // get any fees and/or discounts.
        $frame = $this->getAdditionalFeesAndDiscountFrame();
        if (!is_null($frame)) {
            $theItems = $frame->getItems();
            foreach ($theItems as $item) {
                if ($item->getId() == 'discount') {
                    $this->taskService->discount = $item->getAmount();
                }
                if ($item->getId() == 'rushfees') {
                    $this->taskService->rushFee = $item->getAmount();
                }
            }
        }

        //now update the linguist item rates and cost
        $this->updateTaskServiceCostsAndRates();

        $this->projectObj->budget = round($this->getProjectBudget(), 2);

        $_SESSION['taskService'] = serialize($this->taskService);
        $_SESSION['projectObj'] = serialize($this->projectObj);
    }

    private function applyLangMinimum($frame, $langTotal) {
        $lingCategory = $frame->getCategories()['Linguistic'];
        $theItems = $lingCategory->getLineItems();
        $theMinimumTask = null;
        foreach ($theItems as $task) {
            if (is_a($task, 'LinguistQuoteItemContainer')) {
                $theMinimumTask = $task;
                break;
            }
        }
        // if there isn't a tr+ce task attempt to apply the revenue
        // to the first linguist task that there is.
        if (is_null($theMinimumTask)) {
            $theMinimumTask = $theItems[0];
        }
        $id = $theMinimumTask->getId();
        $theWorkFrontTask = $this->workFrontTaskArray["linguistTask"][$id];
        $theWorkFrontTask->price = $langTotal;
    }
    
    private function cleanDistributedTasks() {
        $fakeId = null;
        if (!is_null($this->getFormatTaskForNonTratos())) {
            $fakeId = $this->getFormatTaskForNonTratos()->getId();
        }
        $distroTasks = $this->taskCatalog->getDistributedTaskCatalog()->getDistributedArray();
        
        foreach ($distroTasks as $language => $idArray) {    
            if (array_key_exists(QuoteToolUtils::makeLanguageId($language), $langMinimumList)) {
                continue;
            }
            
            foreach ($idArray as $id => $task) {
                if (!is_null($fakeId) && $id == $fakeId) {
                    continue;
                }
                $this->workFrontTaskArray["billableTask"][$id]->workRequired = 0.0;
            }
        }
    }

    public function getProjectBudget() {
        $totalCost = 0.0;
        foreach ($this->taskFrameArray as $lang => $frame) {
            $totalCost += $frame->getLinguistCosts();
        }

        if ($this->haveOtherServices()) {
            $otherFrame = $this->otherServices;
            $theCats = $otherFrame->getCategories();
            $totalCost += $theCats->getItemsBaseCostTotal();
        }

        return $totalCost;
    }

    public function getProjectMinimum() {
        return $this->projectMinimum;
    }

    /**
     * Sell total prior to fees and/or discounts and other stuff.
     */
    public function getProjectBaseSellTotal() {
        $totalSell = 0.0;
        foreach ($this->taskFrameArray as $lang => $frame) {
            $totalSell += $frame->getLinguistSellPriceTotal();
        }

        if ($this->haveOtherServices()) {
            $otherFrame = $this->otherServices;
            $theCats = $otherFrame->getCategories();
            $totalSell += $theCats->getItemsActualSellPriceTotal();
        }

        return $totalSell;
    }

    public function isProjectMinimumApplied() {
        return $this->minimumApplied;
    }

    /**
     * Adds other stuff to the base sell cost such as rush fees, discounts and will
     * determine if project is below a custom minimum cost and will return the 
     * minimum setting value rather then the conputed value.
     * 
     * @return type A long value representing the entire cost (sell price) of the project.
     */
    public function getProjectFinalSellTotal() {
        $totalSell = $this->getProjectBaseSellTotal();

        $feesAndDiscounts = $this->getAdditionalFeesAndDiscountFrame();
        if (!is_null($feesAndDiscounts)) {
            $totalSell += $feesAndDiscounts->getSellTotal();
        }

        if ($this->projectMinimum != -1) {
            if ($totalSell < $this->projectMinimum) {
                $totalSell = $this->projectMinimum;
                $this->minimumApplied = true;
            }
        }
        return $totalSell;
    }

    public function setPMTaskPercent($htmlTaskName, $percent, $targLang = null) {
        $this->setPMPercent($percent);
        $this->ajaxResponseArray = array();
        $taskTypeMeta = htmlTaskFactory::getHtmlTask($htmlTaskName, $targLang);
        $theTask = $this->getTaskFromHtmlMeta($taskTypeMeta);
        if (is_null($theTask)) {
            $theTask = $this->getDistroPM($taskTypeMeta->getTargLang());
        }
        $theTask->setCustomPMPercent($percent);
        if (get_class($taskTypeMeta) != "htmlOtherTask") {
            $this->addResponseItem($taskTypeMeta->getTaskKey(), $theTask->getHtml(null));
            $this->getTotalRow($targLang, $taskTypeMeta->getLookupTargetLang());
        }
    }

    public function categoryPrinter($print, $category, $location) {
        $toPrintOrNotToPrint = filter_var($print, FILTER_VALIDATE_BOOLEAN);
        if ($location == "other_services") {
            if (!is_null($this->otherServices)) {
                $this->otherServices->getCategories()->setShouldPrintXml($toPrintOrNotToPrint);
                //$theCategory->setShouldPrintXml($toPrintOrNotToPrint);
            }
        } else {
            $frameName = QuoteToolUtils::convertLanguageId($location);
            $theFrame = $this->taskFrameArray[$frameName];
            $theCategory = $theFrame->getCategories()[$category];
            $theCategory->setShouldPrintXml($toPrintOrNotToPrint);
        }
    }

    public function setValueItemOnTask($htmlTaskName, $typeandvalue, $targLang = null) {
        $this->ajaxResponseArray = array();
        $valueItem = explode("_", $typeandvalue);
        $taskTypeMeta = htmlTaskFactory::getHtmlTask($htmlTaskName, $targLang);
        $theTask = $this->getTaskFromHtmlMeta($taskTypeMeta);
        $this->performTaskValueChange($theTask, $valueItem[0], $valueItem[1]);
        if (get_class($taskTypeMeta) != "htmlOtherTask") {
            if (get_class($taskTypeMeta) === "htmlTRCEWordTask") {
                // we need the main task to output the data...
                $lingTRCEParent = $this->getLinguistTask($taskTypeMeta->getLookupTargetLang(), $taskTypeMeta->getTaskId());
                $wordTasks = $lingTRCEParent->getItemHtmlArray();
                foreach ($wordTasks as $name => $theHtml) {
                    $this->addResponseItem($name, $theHtml);
                }
            } else {
                $theTaskData = $theTask->getHtml(is_a($taskTypeMeta, 'htmlDistributedTask') ? $taskTypeMeta->getTargLang() : null);
                $this->addResponseItem($taskTypeMeta->getTaskKey(), $theTaskData);
            }
            $this->getTotalRow($targLang, $taskTypeMeta->getLookupTargetLang());
            $this->getDistributedPM($taskTypeMeta->getLookupTargetLang());
        }
    }

    public function taskPrinter($htmlTaskName, $print, $targLang = null) {
        $taskTypeMeta = htmlTaskFactory::getHtmlTask($htmlTaskName, $targLang);
        $theTask = $this->getTaskFromHtmlMeta($taskTypeMeta);
        if (get_class($taskTypeMeta) === "htmlTRCEWordTask") {
            // we need the main task to output the data...
            $theTask = $lingTRCEParent = $this->getLinguistTask($taskTypeMeta->getLookupTargetLang(), $taskTypeMeta->getTaskId());
        }
        $theTask->setShouldPrintXml($print);
    }

    public function getDBConnection() {
        return $this->myDBConn;
    }

    public function setFormatTaskForNonTratos(BillableQuoteLineItem $item) {
        $this->nonTratosFormatTask = $item;
    }

    public function getFormatTaskForNonTratos() {
        return $this->nonTratosFormatTask;
    }

    private function performTaskValueChange($theTask, $field, $value) {
        switch ($field) {
            case 'margin':
                $theTask->setUserMarginPecentage($value);
                break;
            case 'rate':
                $theTask->setBaseRatePerUnit($value);
                $GLOBALS['ratesEditable'] = true;
                break;
            case 'units':
                $theTask->setWorkUnitCount($value);
                break;
            case 'asppu':
                $theTask->setUserSellPricePerUnit($value);
                break;
        }
    }

    private function getTaskFromHtmlMeta($taskTypeMeta) {
        $retTask = null;
        switch (get_class($taskTypeMeta)) {
            case 'htmlTRCEWordTask':
                $retTask = $this->getTRCEWordTaskItem($taskTypeMeta->getLookupTargetLang(), $taskTypeMeta->getTaskId(), $taskTypeMeta->getWordType());
                break;
            case 'htmlOtherTask':
                $retTask = $this->getOtherBillingTask($taskTypeMeta->getTaskId());
                break;
            case 'htmlLinquistTask':
                $retTask = $this->getLinguistTask($taskTypeMeta->getLookupTargetLang(), $taskTypeMeta->getTaskId());
                break;
            case 'htmlDistributedTask':
                $retTask = $this->getDistributedTask($taskTypeMeta->getTargLang(), $taskTypeMeta->getTaskId());
                break;
        }
        return $retTask;
    }

    private function getDistributedTask($targLang, $id) {
        $distroTasks = $this->taskCatalog->getDistributedTaskCatalog()->getDistributedArray();
        $ret = $distroTasks[$targLang][$id];
        return $ret;
    }

    private function getOtherBillingTask($id) {
        $billableTasks = $this->taskCatalog->getNonDistributedBillableTasks();
        foreach ($billableTasks as $task) {
            if ($task->getId() == (int) $id) {
                return $task;
            }
        }
    }

    private function getTotalRow($languageIdKey, $readableLanguageKey) {
        // now get the total row of the target language...
        $total = $this->getLanguageFrameTotalRow($readableLanguageKey);
        $this->addResponseitem("totalrow-" . $languageIdKey, $total);
    }

    private function getDistributedPM($readableLanguageKey) {
        $thePMs = $this->taskCatalog->getDistributedPMTasks();
        if (!empty($thePMs)) {
            $thePMTask = $thePMs[$readableLanguageKey];
            $theHtml = $thePMTask->getHtml(null);
            $pmKey = "task-" . $thePMTask->getId();
            $this->addResponseitem($pmKey, $theHtml);
        }
    }

    private function addResponseItem($itemName, $itemText) {
        $this->ajaxResponseArray[$itemName] = $itemText;
    }

    public function chargeForProofreading() {
        return $this->chargeForProofReading;
    }

    public function __wakeup() {
        $this->connect();
    }

    public function getDistroPMSellTotal($lang) {
        $ret = 0.0;
        if (!empty($this->projectManagerTaskArray) && $this->projectManagerTaskArray[0]->isDistributed()) {
            foreach ($this->projectManagerTaskArray as $item) {
                $id = $item->getId();
                $pos = strpos($id, $lang);
                if (!is_bool($pos)) {
                    $ret = $item->getActualSellPriceTotal();
                    break;
                }
            }
        }
        return $ret;
    }

    public function getDistroPM($lang) {
        if (!empty($this->projectManagerTaskArray) && $this->projectManagerTaskArray[0]->isDistributed()) {
            foreach ($this->projectManagerTaskArray as $item) {
                $id = $item->getId();
                $pos = strpos($id, $lang);
                if (!is_bool($pos)) {
                    return $item;
                }
            }
        }
        return null;
    }

    protected function getProjectRushFees() {
        $this->isProjectRushMinimum = false;
        $projRushCalc = new RushFeeCalculator($this, $this->projectRushMinimum);
        return $projRushCalc->calculate();
    }

    // this will only be set if the project management task is set to 
    // distributed...
    public function setProjectMagementTaskInfo(ProjectManagementTaskInfo $info) {
        $this->projectManagementTaskInfo = $info;
    }

    private function connect() {
        $this->myDBConn = new PricingMySql();
    }

    public function renderProjectInfo() {
        $this->projectInfo->renderHtml(null);
    }

    public function getPMTaskActualSellTotal() {
        $total = 0.0;
        if (!empty($this->projectManagerTaskArray)) {
            foreach ($this->projectManagerTaskArray as $item) {
                $total += $item->getActualSellPriceTotal();
            }
        }
        return $total;
    }

    public function setIsProjectRushMinimum() {
        $this->isProjectRushMinimum = true;
    }

    public function isProjectRushMinimum() {
        return $this->isProjectRushMinimum;
    }

    public function getprojectManagerTaskArray() {
        return $this->projectManagerTaskArray;
    }

    private function getPMTaskUnitCount() {
        $total = 0.0;
        if (!empty($this->projectManagerTaskArray)) {
            foreach ($this->projectManagerTaskArray as $item) {
                $total += $item->getWorkUnitCount();
            }
        }
        return $total;
    }

    public function renderSummaryPanel() {
        $addFeeAndDiscounts = $this->getAdditionalFeesAndDiscountFrame();
        $addFeeAndDicountItems = array();
        if (!is_null($addFeeAndDiscounts)) {
            $addFeeAndDicountItems = $addFeeAndDiscounts->getItems();
        }
        $summaryController = new SummaryController($this->taskCatalog, $this->taskFrameArray, $addFeeAndDicountItems, $this->projectMinimum, $this);
        $summaryController->renderHtml(null);
    }

    public function addProjectManagerTask(ProjectManagerQuoteLineItem $item) {
        array_push($this->projectManagerTaskArray, $item);
    }

    public function getSourceLanguage() {
        return $this->taskCatalog->getSourceLang();
    }

    public function getTargetLanguages() {
        $getArray = array();
        foreach ($this->taskFrameArray as $lang => $frame) {
            array_push($getArray, $lang);
        }
        $sortArray = $getArray;
        asort($sortArray, SORT_STRING);

        return $sortArray;
    }

    private function haveOtherServices() {
        $test = $this->taskCatalog->getCategorizedNonDistributableBillingTasks();
        if (!empty($test)) {
            return array_key_exists("Other Services", $test);
        }
        return false;
    }

    private function createOtherServiceFrame() {
        if ($this->haveOtherServices()) {
            $obj = $this->taskCatalog->getCategorizedNonDistributableBillingTasks()['Other Services'];
            $this->otherServices = new OtherTaskCategoryFrame($obj);
        }
    }

    public function renderOtherServicesFrame() {
        if (!is_null($this->otherServices)) {
            $this->otherServices->renderHtml(null);
        }
    }

    public function getAdditionalFeesAndDiscountFrame() {
        $addFeeAndDiscountFrame = null;
        // there might not be any items here... but if there are then
        // output them...
        if ($this->projectData->get_rushFee() !== 0) {
            $this->rushFees = $this->getProjectRushFees();
        }
        if ($this->discountAmount > 0 || $this->rushFees > 0) {
            $addFeeAndDiscountFrame = new AdditionalFeesAndDiscountController();
            if ($this->rushFees > 0) {
                $addFeeAndDiscountFrame->addItem(new AdditionalFeeAndDiscountItem("Project Rush Fees", $this->rushFees, "rushfees"));
            }
            if ($this->discountAmount > 0) {
                $addFeeAndDiscountFrame->addItem(new AdditionalFeeAndDiscountItem("Client Applied Discount", $this->determineDiscountAmount(), "discount"));
            }
        }

        return $addFeeAndDiscountFrame;
    }

    public function renderAdditionalFeesAndDiscountFrame() {
        // there might not be any items here... but if there are then
        // output them...
        $addFeeAndDiscountFrame = $this->getAdditionalFeesAndDiscountFrame();
        if (!is_null($addFeeAndDiscountFrame)) {
            $string = $addFeeAndDiscountFrame->renderHtml(null);
        }
    }

    public function getAdditionalFeesAndDiscountFrameHtml() {
        $string = "";
        $addFeeAndDiscountFrame = $this->getAdditionalFeesAndDiscountFrame();
        if (!is_null($addFeeAndDiscountFrame)) {
            $string = $addFeeAndDiscountFrame->getHtml(null);
        }
        return $string;
    }

    private function determineDiscountAmount() {
        $amount = 0.0;
        switch ($this->discountType) {
            case "fixed" :
                $amount = ((float) $this->discountAmount) * -1;
                break;
            case "percent" :
                $projectAmount = $this->getProjectBaseSellTotal();
                $projectAmount += $this->getProjectRushFees();
                $amount = ($projectAmount * (($this->discountAmount / 100)) * -1);
                break;
            default:
                $amount = 0;
        }
        return $amount;
    }

    public function renderBundleEfforts() {
        $this->categoryPrinterController->renderHtml(null);
    }

    public function getTaskFrameArray() {
        return $this->taskFrameArray;
    }

    public function getTaskCatalog() {
        return $this->taskCatalog;
    }

    private function initialize() {
        $this->projectObj = unserialize($_SESSION['projectObj']);
        if (is_a($this->projectObj, 'llts\fauxWorkfront\project')) {
            $this->isBallparker = true;
        }
        $this->projectData = unserialize($_SESSION['projectData']);
        $this->taskService = unserialize($_SESSION['taskService']);
        $_SESSION['rushFee'] = $this->projectData->get_rushFee();
        $_SESSION['customRushApply'] = $this->projectData->get_customRushApply();
        $this->callingPage = $this->projectData->get_callingPage();
        $_SESSION['pricingScheme'] = $this->projectData->get_pricingScheme();

        $applyCustomRushFees = "";

        $rushFee = $this->projectData->get_rushFee();
        if (!is_null($rushFee) && $rushFee != 0) {
            $this->applyRushFee = true;
            QuoteToolUtils::determineRushFee($this->projectData, $applyCustomRushFees, $this->defaultRushFeePercentage);
        }


        $this->chargeForProofReading = $this->projectData->get_chargeForProofreading();
        $this->pricing = $this->projectData->get_pricing();
        $this->pricingScheme = $this->projectData->get_pricingScheme();
        $this->pricingDbTable = QuoteToolUtils::getPricingSchemeDatabaseIfApplicable($this->pricing, $this->projectObj, $this->myDBConn);
        $this->pmPercent = $_POST['pmPercent'];
        $this->discountAmount = $this->projectData->get_discountValue();
        $this->discountType = $this->projectData->get_discountType();
        if (!is_null($this->pricingDbTable)) {
            QuoteToolUtils::getProjectMinimums($this->pricingDbTable, 
                    $this->myDBConn, 
                    $this->projectMinimum, 
                    $this->projectRushMinimum,
                    $this->applyRushFee);
            
        }
        $this->sortTaskServiceTasks();
    }

    function sortTaskServiceTasks() {
        foreach ($this->taskService->lingTasks as $task) {
            if (!array_key_exists('linguistTask', $this->workFrontTaskArray)) {
                $this->workFrontTaskArray += ['linguistTask' => array()];
            }
            if (!array_key_exists($task->ltask->id, $this->workFrontTaskArray['linguistTask'])) {
                $this->workFrontTaskArray['linguistTask'] += [$task->ltask->id => $task->ltask];
            }
        }
        foreach ($this->taskService->billableTasks as $task) {
            if (!array_key_exists('billableTask', $this->workFrontTaskArray)) {
                $this->workFrontTaskArray += ['billableTask' => array()];
            }
            if (!array_key_exists($task->btask->id, $this->workFrontTaskArray['billableTask'])) {
                $this->workFrontTaskArray['billableTask'] += [$task->btask->id => $task->btask];
            }
        }
    }

    private function resetWorkfrontTaskService() {
        foreach ($this->workFrontTaskArray as $type => $id) {
            foreach ($id as $object) {
                $object->price = 0.0;
                if($type == LINGUIST_TASK || $object->name == "Project Management") {
                    $object->workRequired = 0.0;
                }
            }
        }
        
        $this->taskService->discount = 0.0;
        $this->taskService->rushFee = 0.0;
        foreach ($this->taskService->lingTasks as $task) {
            $task->lingCosts->fuzzyCost = 0.0;
            $task->lingCosts->hourlyCost = 0.0;
            $task->lingCosts->matchRepCost = 0.0;
            $task->lingCosts->minimumCost = 0.0;
            $task->lingCosts->newCost = 0.0;
        }
        $this->cleanDistributedTasks();
    }

    public function setPriceOnWorkFrontTask(IQuoteItem $item) {
        if (is_a($item, 'LinguistQuoteLineItem') || is_a($item, 'LinguistQuoteItemContainer')) {
            $this->workFrontTaskArray['linguistTask'][$item->getId()]->ltask->price = $item->getActualSellPriceTotal();
        }
        if (is_a($item, 'BillableQuoteLineItem')) {
            $this->workFrontTaskArray['billableTask'][$item->getId()]->btask->price = $item->getActualSellPriceTotal();
        }
    }

    function isBallParker() {
        return $this->isBallparker;
    }

    function getCallingPage() {
        return $this->callingPage;
    }

    public function setPMPercent($num) {
        $this->pmPercent = $num;
    }
    
    public function getPMPercent() {
        return $this->pmPercent;
    }

    public function getProjectInfo() {
        return $this->projectInfo;
    }

    public function getPricingTable() {
        return $this->pricingDbTable;
    }

    public function renderLinguistFrames() {

        // just for a test... let's just render one...
        //array_values($this->taskFrameArray)[0]->renderHtml();
        $lingItems = $this->getTaskFrameArray();
        ksort($lingItems);
        foreach ($lingItems as $frameName => $theFrame) {
            $theFrame->renderHtml(null);
        }
    }

    private function initializeQATasks() {
        // now go get the qa tasks (if applicable) and populate...
        $qaPages = $this->projectData->get_numberOfPages();
        $qaPagesPerHour = $this->projectData->get_qa_pagesPerHour();

        if (!is_null($qaPages) && $qaPages != -1) {
            $this->setQA($qaPages, $qaPagesPerHour);
        }
    }

    private function getLanguageFrameTotalRow($lang) {
        return $this->taskFrameArray[$lang]->totalRowToHtml();
    }

    private function getTRCEWordTaskItem($targLang, $taskId, $wordItemName) {
        $theItem = $this->getLinguistTask($targLang, $taskId);

        if (is_a($theItem, "LinguistTRandCETask")) {
            return $theItem->getWordItemTask($wordItemName);
        }
    }

    private function getLinguistTask($targLang, $taskId) {
        $idLookup = (int) $taskId;
        $rawList = $this->taskCatalog->getLinguistTaskByTargetLangs();
        $theTaskLangSet = $rawList[$targLang];

        foreach ($theTaskLangSet as $item) {
            $id = $item->getId();
            if ($item->getId() === $idLookup) {
                return $item;
            }
        }
    }

    private function getTaskList() {
        //get the list of tasks from the project
        //place the linguistic tasks into arrays by language
        if (count($this->taskService->lingTasks) == 1 && !is_array($this->taskService->lingTasks)) {
            $languageSource = $this->taskService->lingTasks->sourceLang;
            new LinguisticQuoteLineItemGenerator($this->taskService->lingTasks, $this, $this->projectObj, $this->pricing, $this->myDBConn);
        } else {
            $languageSource = array_values($this->taskService->lingTasks)[0]->sourceLang;
            // need to go through these and reorder to make sure all the TR&CE tasks are first...
            $lingArray = array();

            foreach ($this->taskService->lingTasks as $lingt) {
                if ($lingt->ltask->type == 'TR+CE') {
                    array_push($lingArray, $lingt);
                }
            }
            // and now...
            foreach ($this->taskService->lingTasks as $lingt) {
                if ($lingt->ltask->type != 'TR+CE') {
                    array_push($lingArray, $lingt);
                }
            }

            foreach ($lingArray as $lingt) {
                new LinguisticQuoteLineItemGenerator($lingt, $this, $this->projectObj, $this->pricing, $this->myDBConn);
            }
        }

        // now get the billable items...
        //put the billableTasks into the project array


        if (!is_null($this->taskService->billableTasks)) {
            if (count($this->taskService->billableTasks) == 1 && !is_array($this->taskService->billableTasks)) {
                new BillableQuoteLineItemGenerator($this->taskService->billableTasks, $languageSource, $this, $this->projectObj, $this->pricing, $this->myDBConn);
            } else {
                foreach ($this->taskService->billableTasks as $billTask) {
                    new BillableQuoteLineItemGenerator($billTask, $languageSource, $this, $this->projectObj, $this->pricing, $this->myDBConn);
                }
            }
        }

        $formatTask = $this->getFormatTaskForNonTratos();
        if (!is_null($formatTask)) {
            $targLangCount = $this->taskCatalog->getNumberOfTargetLanguages($this->taskCatalog->getSourceLang());
            $formatTask->setTargetLangCount($targLangCount);
            $this->taskCatalog->addItem($formatTask);
        }
    }

    private function getDistroQATasksByLang() {
        $distrocat = $this->taskCatalog->getDistributedTaskCatalog();

        foreach ($distrocat->getDistributedArray() as $targLang => $items) {
            foreach ($items as $id => $theTask) {
                $name = $theTask->getName();
                foreach (self::QA_ITEMS_ARRAY as $qaName) {
                    if ($name == $qaName) {
                        if (!array_key_exists($targLang, $this->qaTasks)) {
                            $this->qaTasks[$targLang] = array();
                        }
                        $this->qaTasks[$targLang][$name] = $theTask;
                    }
                }
            }
        }
    }

    private function setQA($numOfPages, $pagesPerHour) {
        $numberOfUnits = ($numOfPages / $pagesPerHour);
        $qaCoordination = ceil(($numberOfUnits * 0.10) * 4) / 4;
        $qa1 = ceil($numberOfUnits * 4) / 4;
        $taskNames = self::QA_ITEMS_ARRAY;

        $this->getDistroQATasksByLang();

        if (!empty($this->qaTasks)) {
            foreach ($this->qaTasks as $targLang => $theItems) {
                if (2 == count($this->qaTasks[$targLang])) {
                    $this->qaTasks[$targLang][$taskNames[self::QA_COORDINATION]]->setWorkUnitCount($qaCoordination);
                    $this->qaTasks[$targLang][$taskNames[self::QA_1]]->setWorkUnitCount($qa1);
                } else {
                    $this->qaTasks[$targLang][$taskNames[self::QA_REVIEW]]->setWorkUnitCount($qa1);
                }
            }
        }
    }

    private function categorizeLinguistTaskList() {
        $theList = $this->taskCatalog->getCategorizedLinguistTasksPerTarget();
        foreach ($theList as $targKey => $targCatArray) {
            foreach ($targCatArray as $cat) {
                $this->categoryPrinterController->addCategoryPrintController($cat);
            }
        }
    }

    public function renderUnlockRateButton() {

        echo str_replace("{DISABLED}", isset($_SESSION['ratesAreEditable']) ? "disabled" : "", ProjectManager::RATE_BUTTON);
    }

    public function getProjectSellPriceTotalWithRushFee() {
        $total = 0.0;
        // first do the linguist frame containers...
        foreach ($this->taskFrameArray as $targLang => $frame) {
            $testTotal = $frame->getLinguistSellPriceTotalWithRushFee();
            $total += $testTotal;
        }
        // now the undistributed tasks if there are any.
        $nonDistArray = $this->taskCatalog->getNonDistributedBillableTasks();
        if (!is_null($nonDistArray) && !empty($nonDistArray)) {
            foreach ($nonDistArray as $item) {
                if (!is_a($item, "ProjectManagerQuoteLineItem")) {
                    $testTotal = $item->getActualSellPriceTotalWithRushFee();
                    $total += $testTotal;
                }
            }
        }
        return (float) $total;
    }

    public function getPMProjectSellPriceTotal() {
        $total = 0.0;
        // first do the linguist frame containers...
        foreach ($this->taskFrameArray as $targLang => $frame) {
            $testTotal = $frame->getLinguistSellPriceTotal();
            $total += $testTotal;
        }
        // now the undistributed tasks if there are any.
        $nonDistArray = $this->taskCatalog->getNonDistributedBillableTasks();
        if (!is_null($nonDistArray) && !empty($nonDistArray)) {
            foreach ($nonDistArray as $item) {
                if (!is_a($item, "ProjectManagerQuoteLineItem")) {
                    $testTotal = $item->getActualSellPriceTotal();
                    $total += $testTotal;
                }
            }
        }
        return (float) $total;
    }

    private function createLinguistTaskFrames() {
        $theList = $this->taskCatalog->getCategorizedLinguistTasksPerTarget();
        foreach ($theList as $targKey => $theThing) {
            $pushArray = $theList[$targKey];
            // walk the list and find the the
            $linguistItems = $pushArray['Linguistic']->getLineItems();
            $wordCount = 0;
            $rushFeeMimimum = -1;
            $minimumTotal = -1;

            foreach ($linguistItems as $lineItem) {
                if (is_a($lineItem, 'IHasWords')) {
                    $rushFeeMimimum = $lineItem->getLanguageFrameMinimumRushFeeTotal();
                    $minimumTotal = $lineItem->getLanguageFrameMinimumTotal();
                    $wordCount = $lineItem->getUnitCount();
                    break;
                }
            }
            $this->taskFrameArray[$targKey] = new LinguistTaskCategoryFrame($targKey, $pushArray, $wordCount, $this, $rushFeeMimimum, $minimumTotal);
        }
    }

    private function createProjectManagerCategory() {
        if ($this->projectManagementTaskInfo != null) {
            // we need to build a project manager category and task for each frame...
            foreach ($this->taskFrameArray as $targName => $theFrame) {
                $aPMTask = $this->projectManagementTaskInfo->buildPMTask($theFrame);
                // create a corresponding category entry...
                $category = new QuoteItemCategory($aPMTask->getCategory());
                // but the pm task in it...
                $category->putLineItem($aPMTask);

                $theFrame->insertProjectManagerCategory($category);
                $this->taskCatalog->addDistributedPMTask($targName, $aPMTask);
                $this->addProjectManagerTask($aPMTask);
            }
        }
    }

    public function buildSummaryTableArrayForJson() {
        $retArray = array();
        $addFeeAndDiscounts = $this->getAdditionalFeesAndDiscountFrame();
        $addFeeAndDicountItems = array();
        if (!is_null($addFeeAndDiscounts)) {
            $addFeeAndDicountItems = $addFeeAndDiscounts->getItems();
        }
        $summaryController = new SummaryController($this->taskCatalog, $this->taskFrameArray, $addFeeAndDicountItems, $this->projectMinimum, $this);

        $retArray[$summaryController->getSellPriceSummaryDivId()] = $summaryController->buildSellSummaryTable();
        $retArray[$summaryController->getCostSummaryDivId()] = $summaryController->buildCostSummaryTable();
        $retArray[$summaryController->getGrossMarginSummaryDivId()] = $summaryController->buildGMSummaryTable();
        return $retArray;
    }

    public function buildOtherServicesArrayForJson() {
        if (!is_null($this->otherServices)) {
            $totalRow = $this->otherServices->totalRowToHtml();
            $this->addResponseItem('other_total_row', $totalRow);
            $theTaskArray = $this->otherServices->getItemsHtml();
            foreach ($theTaskArray as $name => $html) {
                $this->addResponseItem($name, $html);
            }
        }
    }

    public function buildAdditionalFeesAndCreditsForJson() {
        $retArray = array();
        $retArray[AdditionalFeesAndDiscountController::ID_NAME] = $this->getAdditionalFeesAndDiscountFrameHtml();
        return $retArray;
    }

    private function cleanUp() {

        //$this->projectInfo->renderHtml();
    }

    public function jsonSerialize() {
        $this->buildOtherServicesArrayForJson();
        $total = array_merge($this->ajaxResponseArray, $this->buildSummaryTableArrayForJson(),
                //$this->buildOtherServicesArrayForJson(), 
                $this->buildAdditionalFeesAndCreditsForJson());
        return[$total];
    }

    public function getPMSellTotal() {
        $pmSelltotal = $this->getPMProjectSellPriceTotal();
        return $pmSelltotal;
    }

    public function setPackageEngineering($status) {
        $this->packageEngineering = $status;
    }

    public function getPackageEngineering() {
        return $this->packageEngineering;
    }

    public function setPackageAllInternal($status) {
        $this->packageAllInternal = $status;
    }

    public function getPackageAllInternal() {
        return $this->packageAllInternal;
    }

    protected function updateTaskServiceCostsAndRates() {
        $workFrontTaskService = $this->getTaskService();
        if (!is_array($workFrontTaskService->lingTasks)) {
            $theTask = $this->getLinguistTask($workFrontTaskService->lingTasks->targLang, $workFrontTaskService->lingTasks->ltask->id);
            if (is_a($theTask, 'LinguistTRandCETask')) {
                $this->updateTaskServiceWithTRCE($theTask, $workFrontTaskService->lingTasks);
            } elseif (is_a($theTask, 'LinguistQuoteLineItem')) {
                $this->updateTaskServiceWithLineItem($theTask, $workFrontTaskService->lingTasks);
            }
        } else {
            foreach ($workFrontTaskService->lingTasks as $wfLingTask) {
                //find matching LinguistQuoteLineItem
                $theTask = $this->getLinguistTask($wfLingTask->targLang, $wfLingTask->ltask->id);
                if (is_a($theTask, 'LinguistTRandCETask')) {
                    $this->updateTaskServiceWithTRCE($theTask, $wfLingTask);
                } elseif (is_a($theTask, 'LinguistQuoteLineItem')) {
                    $this->updateTaskServiceWithLineItem($theTask, $wfLingTask);
                }
            }
        }
    }

    protected function updateTaskServiceWithTRCE($theTask, $wfTask) {
        foreach ($theTask->getLineItems() as $item) {
            $rate = $item->getBaseRatePerUnit();
            $cost = round($item->getBaseCostTotal(), 2);
            $rateKey = $item->getStandardRateKey();
            $wfTask->wordRateDetails->$rateKey = $rate;

            $type = explode('_', $rateKey);
            end($type);
            $lastIndex = key($type);
            switch ($type[$lastIndex]) {
                case 'new': $property = 'newCost';
                    break;
                case 'fuzzy': $property = 'fuzzyCost';
                    break;
                case '100Match': $property = 'matchRepCost';
                    break;
            }
            $wfTask->lingCosts->$property = $cost;
        }
        $wfTask->lingCosts->minimumCost = $theTask->getCostMinimum();
    }

    protected function updateTaskServiceWithLineItem($theTask, &$wfTask) {
        $wfTask->wordRateDetails->hourly = $theTask->getBaseRatePerUnit();
        $wfTask->lingCosts->hourlyCost = round($theTask->getBaseCostTotal(), 2);
        $wfTask->lingCosts->minimumCost = $theTask->getCostMinimum();
    }

    function getRushRate() {
        return $this->defaultRushFeePercentage;
    }

    const CUSTOM_TOTAL_TITLE = "Total price calculated from project minimum.";
    const RATE_BUTTON = "    <div style=\"text-align:center; width:100%\">
                <input type=\"button\" name=\"Unlock\" id=\"UnlockRates\" value=\"Unlock Rate Fields\" {DISABLED}/>
            </div>\n";

}
