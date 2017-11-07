<?php

require_once (__DIR__ . "/../interfaces/ILinguistQuoteItem.php");
require_once (__DIR__ . "/CatalogService.php");
require_once (__DIR__ . "/../interfaces/IQuoteItemContainer.php");

require_once (__DIR__ . "/QuoteItemCategory.php");

require_once (__DIR__ . "/DistributedTaskCatalog.php");

require_once (__DIR__ . "/QuoteConstants.php");

require_once (__DIR__ . "/QuoteToolUtils.php");

/**
 * Organizes task objects into logical tree structure for processing.
 * The central processing unit for the QuoteTool task reporitory.
 *
 * @author Karl Krasnowsky
 */
class TaskCatalogService extends CatalogService {

    /**
     * constants.
     */
    const BASE_LINGUIST_BRANCH = "Linguist";
    const BASE_BILLABLE_BRANCH = "Billable Tasks";

    protected $distributedPMTasks = array();
    protected $distributedTaskCatalog;
    protected $distroCatalogInitialized = false;
    protected $nonDistributedCategoryArray = null;
    protected $linguistCategoryArray = array();
    protected $lingustTasksByTargetLangsArray = array();
    protected $allLingustTaskArray = array();
    protected $linguistCategoryPerTargetArray = array();
    protected $sqlConnection;
    protected $dbName;

    public function __construct(PricingMySql $sqlCon, $dbName) {
        $this->sqlConnection = $sqlCon;
        $this->dbName = $dbName;
        $this->distributedTaskCatalog = new DistributedTaskCatalog();
    }

    /**
     * Adds a ICatalogSupport item to the catalog.
     * @param ICatagorySupport $item 
     * @throws Exception If attempt to add a non ICatalogSupport item to the 
     * catalog
     */
    public function addItem(ICatagorySupport $item) {
        if ($item instanceof ILinguistQuoteItem) {
            $this->addLinguistItem($item);
        } elseif ($item instanceof IBillableQuoteLineItem) {
            $this->addBillableItem($item);
        } else {
            throw new Exception("Unknown type attempt to add an item to " . __CLASS__);
        }
    }

    protected function addBillableItem(IBillableQuoteLineItem $item) {
        // need to create the base node...
        if (!array_key_exists(TaskCatalogService::BASE_BILLABLE_BRANCH, $this->taskArray)) {
            $this->taskArray += [TaskCatalogService::BASE_BILLABLE_BRANCH => array()];
        }
        //now put it...
        array_push($this->taskArray[TaskCatalogService::BASE_BILLABLE_BRANCH], $item);
    }

    public function getDistributedBillableTasks() {
        $retArray = array();
        if (array_key_exists(TaskCatalogService::BASE_BILLABLE_BRANCH, $this->taskArray)) {
            foreach ($this->taskArray[TaskCatalogService::BASE_BILLABLE_BRANCH] as $value) {
                if ($value->isDistributed()) {
                    array_push($retArray, $value);
                }
            }
        }
        return $retArray;
    }

    public function getNonDistributedBillableTasks() {
        $retArray = array();
        if (array_key_exists(TaskCatalogService::BASE_BILLABLE_BRANCH, $this->taskArray)) {
            foreach ($this->taskArray[TaskCatalogService::BASE_BILLABLE_BRANCH] as $value) {
                if (!$value->isDistributed()) {
                    array_push($retArray, $value);
                }
            }
        }
        return $retArray;
    }

    public function addDistributedPMTask($targetLang, ProjectManagerQuoteLineItem $pmLine) {
        $this->distributedPMTasks[$targetLang] = $pmLine;
    }

    public function getDistributedPMTasks() {
        return $this->distributedPMTasks;
    }

    public function getFullTaskListPerTarget() {
        $retArray = $this->getLinguistTaskByTargetLangs();
        if (count($retArray) > 0) {
            // now we add the distributed items to each target...
            //$distributedTasks = $this->getDistributedBillableTasks();
            $this->generateDistributedTasks(array_keys($retArray));
            $theDistroTasks = $this->distributedTaskCatalog->getDistributedArray();

            if (count($theDistroTasks) > 0) {
                foreach ($retArray as $targetLang => &$value) {
                    $idLang = QuoteToolUtils::makeLanguageId($targetLang);
                    foreach ($theDistroTasks[$idLang] as $id => $disTask) {
                        array_push($value, $disTask);
                    }
                }
            }
        }
        return $retArray;
    }

    private function generateDistributedTasks($targetLangs) {
        if (!$this->distroCatalogInitialized) {
            $this->distributedTaskCatalog = new DistributedTaskCatalog();

            $distributedTasks = $this->getDistributedBillableTasks();
            if (count($distributedTasks) > 0) {
                foreach ($targetLangs as $targetLangName) {
                    foreach ($distributedTasks as $disTask) {
                        $new_object = unserialize(serialize($disTask));
                        $workReq = $new_object->getWorkUnitCount() / $new_object->getTargetLangCount();
                        if ($new_object->getWorkUnitType() == WorkUnitType::enum()->hours) {
                            $workReq = ceil($workReq * 4) / 4;
                        }
                        $new_object->setWorkUnitCount($workReq);
                        if (!empty($this->dbName)) {
                            $ref = QuoteConstants::getRatePricingDBLookupRef($new_object->getName());
                            if(!empty($ref)){
                                $lookupArray = [$langSpecific = $ref . "=" . QuoteToolUtils::generateLanguageLookup($this->getSourceLang(),$targetLangName ),
                                $default = $ref];
                                $new_object->setRatePricingDBColumnKeys($lookupArray);
                                QuoteToolUtils::applyCustomSellRate($new_object, $this->dbName, $this->sqlConnection);
                                if($new_object->getDefaultRushFeePercentage() > 0) {
                                    QuoteToolUtils::applyRushFee($new_object, $new_object->getDefaultRushFeePercentage(), true, $this->dbName, $this->sqlConnection);
                                }
                            }
                        }
                        $this->distributedTaskCatalog->addDistributedTask($targetLangName, $new_object);
                    }
                }
            }
            $this->distroCatalogInitialized = true;
        }
    }

    public function getDistributedTaskCatalog() {
        return $this->distributedTaskCatalog;
    }

    protected function addLinguistItem($item) {
        // need to create the base node...
        if (!array_key_exists(TaskCatalogService::BASE_LINGUIST_BRANCH, $this->taskArray)) {
            $this->taskArray += [TaskCatalogService::BASE_LINGUIST_BRANCH => array()];
        }

        if (!array_key_exists($item->getSourceLang(), $this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH])) {
            $this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH] += [$item->getSourceLang() => array()];
        }

        if (!array_key_exists($item->getTargetLang(), $this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$item->getSourceLang()])) {
            $this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$item->getSourceLang()] += [$item->getTargetLang() => array()];
        }

//        if(!array_key_exists($item->getCategory(),$this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$item->getSourceLang()][$item->getTargetLang()])){
//            $this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$item->getSourceLang()][$item->getTargetLang()] += [$item->getCategory() => array()];
//        }
        //now put it...
        array_push($this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$item->getSourceLang()][$item->getTargetLang()], $item);
    }

    public function getAllLinguistTasks() {
        if (empty($this->allLingustTaskArray)) {
            foreach ($this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH] as $sourceLang => $sourceLangArray) {
                foreach ($sourceLangArray as $targetLang => $targLangArray) {
                    foreach ($targLangArray as $value) {
                        array_push($this->allLingustTaskArray, $value);
                    }
                }
            }
        }
        return $this->allLingustTaskArray;
    }

    public function getSourceLang() {
        // there should be only one source language per project, so...
        $theKeys = array_keys($this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH]);

        return array_values($theKeys)[0];
    }

    public function getAllBuillableTasks() {
        $retArray = array();
        foreach ($this->taskArray[TaskCatalogService::BASE_BILLABLE_BRANCH] as $value) {
            array_push($retArray, $value);
        }
        return $retArray;
    }

    public function getLinguistTaskByTargetLangs() {
        if (empty($this->lingustTasksByTargetLangsArray)) {
            $retArray = array();
            // we assume there's only one source language, so...
            foreach ($this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH] as $sourceLang => $sourceLangArray) {
                break;
            }

            foreach ($this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$sourceLang] as $targetLang => $targetLangArray) {
                $this->lingustTasksByTargetLangsArray += [$targetLang => array()];
                foreach ($targetLangArray as $value) {
                    array_push($this->lingustTasksByTargetLangsArray[$targetLang], $value);
                }
            }
            // now the distibuted pm tasks if applicable...
            if (!empty($this->distributedPMTasks)) {
                foreach ($this->distributedPMTasks as $targetLang => $value) {
                    array_push($this->lingustTasksByTargetLangsArray[$targetLang], $value);
                }
            }
        }
        return $this->lingustTasksByTargetLangsArray;
    }

    /**
     * All non distributed tasks go into the Other Services category.
     * 
     * @return \QuoteItemCategory
     */
    public function getCategorizedNonDistributableBillingTasks() {
        if (is_null($this->nonDistributedCategoryArray)) {
            $billingTasks = $this->getNonDistributedBillableTasks();
            $this->nonDistributedCategoryArray = array();

            foreach ($billingTasks as $quoteItem) {
                if (!array_key_exists($quoteItem->getCategory(), $this->nonDistributedCategoryArray)) {
                    $this->nonDistributedCategoryArray += [$quoteItem->getCategory() => new QuoteItemCategory($quoteItem->getCategory())];
                }
                $this->nonDistributedCategoryArray[$quoteItem->getCategory()]->putLineItem($quoteItem);
            }

            if (array_key_exists("Other Services", $this->nonDistributedCategoryArray)) {
                $this->nonDistributedCategoryArray["Other Services"]->setShouldPrintXml(false);
            }
        }
        return $this->nonDistributedCategoryArray;
    }

    public function categorizeLinguistTasksByTargetLang($targLang) {
        if (!array_key_exists($targLang, $this->linguistCategoryArray)) {
            $taskArry = $this->getFullTaskListPerTarget();
            $this->linguistCategoryArray += [$targLang => array()];

            $tagetLangArray = $taskArry[$targLang];
            foreach ($tagetLangArray as $quoteItem) {
                if (!array_key_exists($quoteItem->getCategory(), $this->linguistCategoryArray[$targLang])) {
                    $this->linguistCategoryArray[$targLang] += [$quoteItem->getCategory() => new QuoteItemCategory($quoteItem->getCategory())];
                }
                $this->linguistCategoryArray[$targLang][$quoteItem->getCategory()]->putLineItem($quoteItem);
            }
            // now walk the list and set printable state to defaults...
            if (array_key_exists("Linguistic", $this->linguistCategoryArray[$targLang])) {
                $this->linguistCategoryArray[$targLang]["Linguistic"]->setShouldPrintXml(false);
            }
            if (array_key_exists("Engineering", $this->linguistCategoryArray[$targLang])) {
                $this->linguistCategoryArray[$targLang]["Engineering"]->setShouldPrintXml(false);
            }
            if (array_key_exists("Formatting", $this->linguistCategoryArray[$targLang])) {
                $this->linguistCategoryArray[$targLang]["Formatting"]->setShouldPrintXml(true);
            }
        }
        return $this->linguistCategoryArray[$targLang];
    }

    public function getCategorizedLinguistTasksPerTarget() {
        if (empty($this->linguistCategoryPerTargetArray)) {
            foreach ($this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$this->getSourceLang()] as $targLangKey => $targArr) {
                $this->linguistCategoryPerTargetArray += [$targLangKey => $this->categorizeLinguistTasksByTargetLang($targLangKey)];
            }
        }
        return $this->linguistCategoryPerTargetArray;
    }

    /**
     * Returns the number of target translation languages for a given
     * source language.
     * 
     * @param type $sourcLanguage A source language name (e.g, 'English (US)') 
     * to check for number of target language translations.
     * @return int Number of target languages for a given source language.
     */
    public function getNumberOfTargetLanguages($sourcLanguage) {
        $ret = 0;
        if(!is_null($sourcLanguage)){
            foreach ($this->taskArray[TaskCatalogService::BASE_LINGUIST_BRANCH][$sourcLanguage] as $targetLang => $targLangArray) {
                $ret++;
            }
        }
        return $ret;
    }

    public function getAllItems() {
        return array_merge($this->getAllLinguistTasks(), $this->getAllBuillableTasks());
    }

}
