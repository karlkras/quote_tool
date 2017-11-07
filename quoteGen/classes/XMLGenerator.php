<?php

error_reporting(1);

require_once (__DIR__ . '/ProjectManager.php');
require_once (__DIR__ . '/QuoteConstants.php');

/**
 * Description of XMLGenerator
 *
 * @author Axian Developer
 */
class XMLGenerator {

    private $document;
    private $projectManager;
    private $taskNameArray;
    private $rollNameArray;
    private $root;
    private $header;
    private $descriptionTable;
    private $body;
    private $projectSubForm;
    private $sourceLang;
    private $languageTable;
    private $generateCData;

    public function __construct(ProjectManager $projectManager, $taskNameArray, $rolledNameArray, $updateAtTask) {
        $this->projectManager = $projectManager;
        $this->taskNameArray = $taskNameArray;
        $this->rollNameArray = $rolledNameArray;
        $this->generateCData = !$updateAtTask;

        $this->sourceLang = $this->projectManager->getSourceLanguage();
    }

    public function generateXML() {
        ob_clean();
        $this->createDoc();
        $this->generateHeader();
        $this->generateBody();
        $this->appendCData();
        return $this->document;
    }

    private function appendCData() {
        //create the datadump
        $datadump = $this->document->createElement('datadump');
        $this->root->appendChild($datadump);

        $taskservice = $this->document->createElement('taskservice');
        $taskservice->appendChild($this->document->createCDATASection(str_replace("\0", "{[{NULL}]}", serialize($this->projectManager->getTaskService()))));
        $datadump->appendChild($taskservice);

        $projectobj = $this->document->createElement('projectobj');
        $projectobj->appendChild($this->document->createCDATASection(str_replace("\0", "{[{NULL}]}", serialize($this->projectManager->getProjectObj()))));
        $datadump->appendChild($projectobj);

        $projectdata = $this->document->createElement('projectdata');
        $projectdata->appendChild($this->document->createCDATASection(str_replace("\0", "{[{NULL}]}", serialize($this->projectManager->getprojectData()))));
        $datadump->appendChild($projectdata);

        $projectManager = $this->document->createElement('projectManager');
        $projectManager->appendChild($this->document->createCDATASection(str_replace("\0", "{[{NULL}]}", serialize($this->projectManager))));
        $datadump->appendChild($projectManager);
    }

    private function createDoc() {
        $this->document = new DOMDocument();
        $this->document->formatOutput = true;
        //create root element
        $this->root = $this->document->createElement("form1");
        $this->document->appendChild($this->root);
    }

    private function generateHeader() {
        $this->createHeader();
        $this->appendQuoteCompany();
        $this->appendServicesTable();
        $this->appendProjectDescription();
        $this->appendDescriptionTable();
        $this->appendCompany();
        $this->appendContacts();
        $this->appendSrcLangAndProjectID();
        $this->appendTargetLangs();
    }

    private function generateBody() {
        $this->createBody();
        $this->appendBodyHeader();
        $this->createProjectNode();
        $this->appendLanguageTables();
        if (!is_null($this->projectManager->getOtherFrame())) {
            $this->appendOtherTable();
        }
        $this->appendRushFeeTable();
    }

    private function appendRushFeeTable() {
        $baseSellTotal = $this->projectManager->getProjectBaseSellTotal();
        $theDiscount = $this->projectManager->getTaskService()->discount;
        $theRushFee = $this->projectManager->getTaskService()->rushFee;

        $RushTable = $this->document->createElement("RushTable");
        $this->projectSubForm->appendChild($RushTable);

        $subtotalrow = $this->document->createElement("subtotalrow");
        $subtotal = $this->document->createElement("subtotal");
        $subtotal->appendChild($this->document->createTextNode("$" . number_format($baseSellTotal, 2)));
        $subtotalrow->appendChild($subtotal);
        $RushTable->appendChild($subtotalrow);

        $Row1 = $this->document->createElement("Row1");
        $rushFee = $this->document->createElement("rushFee");
        $rushFee->appendChild($this->document->createTextNode("$" . number_format($theRushFee, 2)));
        $Row1->appendChild($rushFee);
        $RushTable->appendChild($Row1);


        $d = abs($theDiscount);
        if ($d > 0) {
            $Row1 = $this->document->createElement("Row2");
            $discount = $this->document->createElement("discount");

            $discount->appendChild($this->document->createTextNode("($" . number_format($d, 2) . ")"));
            $Row1->appendChild($discount);
            $RushTable->appendChild($Row1);
        }
        $ProjectTotalTable = $this->document->createElement('ProjectTotalTable');
        $this->projectSubForm->appendChild($ProjectTotalTable);

        $Row1 = $this->document->createElement("Row1");
        $ProjectTotal = $this->document->createElement("ProjectTotal");
        $baseSellTotal += $theRushFee + $theDiscount;

        // check to see if we've got a minumum...
        $projMin = $this->projectManager->getProjectMinimum();
        if ($projMin != -1) {
            if ($baseSellTotal < $projMin) {
                $baseSellTotal = $projMin;
            }
        }
        $ProjectTotal->appendChild($this->document->createTextNode("$" . number_format(round($baseSellTotal, 2), 2)));
        $Row1->appendChild($ProjectTotal);
        $ProjectTotalTable->appendChild($Row1);
    }

    private function appendOtherTable() {
        $otherFrame = $this->projectManager->getOtherFrame();
        if (!is_null($otherFrame) && $otherFrame->getCategories()->getItemsActualSellPriceTotal() != 0) {
            $total = $otherFrame->getCategories()->getItemsActualSellPriceTotal();
            $IndependentTable = $this->document->createElement("IndependentTable");
            $this->projectSubForm->appendChild($IndependentTable);

            $indyTitle = $this->document->createElement("indyTitle");
            $IndependentTable->appendChild($indyTitle);

            $lineItems = $otherFrame->getCategories()->getLineItems();

            $otherPrintableItemArray = array();
            $pmItem = null;
            foreach ($lineItems as $item) {
                if (!$item->getActualSellPriceTotal() > 0) {
                    continue;
                }
                if (is_a($item, "ProjectManagerQuoteLineItem")) {
                    $pmItem = $item;
                } else {
                    $category = QuoteConstants::getTaskCategory($item->getType());
                    if (!array_key_exists($category, $otherPrintableItemArray)) {
                        $otherPrintableItemArray += [$category => array()];
                    }
                    array_push($otherPrintableItemArray[$category], $item);
                }
            }

            $loopArrayIndex = 0;
            $theKeys = array_keys($otherPrintableItemArray);
            foreach ($otherPrintableItemArray as $category) {
                $key = $theKeys[$loopArrayIndex];
                $catTotal = 0.0;
                foreach ($category as $item) {
                    $rowSuffix = self::determineOtherRowSuffix($key);
                    $catTotal += $item->getActualSellPriceTotal();
                    $name = $this->getTaskName($item->getId(), $item->getType());

                    if ($catTotal > 0) {
                        $this->generateHourTaskRow($IndependentTable, $rowSuffix, $name, $item->getActualSellPriceTotal(), $item->getWorkUnitCount(), $item->getActualSellPricePerUnit());
                    }
                } // and finally:
                $this->generateSubtotal($rowSuffix . "Footer", $IndependentTable, $rowSuffix . "SubTotal", $catTotal);
                $loopArrayIndex++;
            }

            // and now do the project management task if here...
            if (!is_null($pmItem)) {
                $pmTotal = $pmItem->getActualSellPriceTotal();
                if ($pmTotal > 0) {
                    $pmRow = $this->document->createElement('pmRow');
                    $IndependentTable->appendChild($pmRow);
                    $pmCost = $this->document->createElement("pmCost");
                    $pmCost->appendChild($this->document->createTextNode("$" . number_format($pmItem->getActualSellPriceTotal(), 2)));
                    $pmRow->appendChild($pmCost);
                }
            }

            // and now the othertable subtotal.
            $this->generateSubtotal("indyFooter", $IndependentTable, "otherTableSubTotal", $total);
        }
    }

    private function appendLanguageTables() {
        $theFrames = $this->projectManager->getTaskFrameArray();
        $sortFrames = $theFrames;
        ksort($sortFrames);

        foreach ($sortFrames as $lang => $frame) {
            $targetLang = $frame->getName();
            $languageTitle = $this->sourceLang . " to " . $targetLang;
            $langTotal = $frame->getLinguistSellPriceTotal();

            $this->languageTable = $this->document->createElement("LanguageTable");
            $this->projectSubForm->appendChild($this->languageTable);


            //create root / Body / projects / projectSubForm / LanguageTable / langTitle
            $langTitle = $this->document->createElement("langTitle");
            $tableLanguage = $this->document->createElement("tableLanguage");
            $tableLanguage->appendChild($this->document->createTextNode($languageTitle));
            $langTitle->appendChild($tableLanguage);
            $this->languageTable->appendChild($langTitle);

            $langTotalEntry = $this->document->createElement("langTotal");
            $langTotalEntry->appendChild($this->document->createTextNode("$" . number_format($langTotal, 2)));
            $this->languageTable->appendChild($langTotalEntry);

            $this->generateCategories($frame, $languageTitle);

            // now create the langfooter.
            $this->generateSubtotal('langFooter', $this->languageTable, 'langSubTotal', $langTotal);
        }
    }

    private function generateCategories($theFrame, $languageTitle) {
        $packageEngineering = $this->projectManager->getPackageEngineering();
        $packageAllInternal = $this->projectManager->getPackageAllInternal();
        $id = $theFrame->getId();
        if (!$packageEngineering && !$packageAllInternal) {
            $this->generateCategoryRowsNormal($theFrame->getCategories(), $languageTitle, $id);
        } else {
            $this->generateCategoryRowsPackaged($theFrame->getCategories(), $id, $packageAllInternal);
        }
    }

    private function generateCategoryRowsNormal($theCategories, $languageTitle, $frameID) {
        foreach ($theCategories as $catName => $category) {
            $bundleCatName = $catName == "Linguistic" ? $this->getRollupName("Linguistic Work") : $this->getRollupName($catName);
            $catTotal = 0.0;
            if ($catName == 'Project Management') {
                $catTotal = $this->projectManager->getDistroPMSellTotal($frameID);
            } else {
                $catTotal = $category->getItemsActualSellPriceTotal();
            }
            if ($catTotal == 0) {
                continue;
            }

            $printableItems = $category->getPrintableTasks();
            $rolledItems = $category->getRolledTasks();

            if ($catName == 'Linguistic') {
                $this->generateLinguistCategoryXML($bundleCatName, $languageTitle, $printableItems, $rolledItems, $catTotal);
            } elseif ($catName == 'Quality Assurance') {
                $theItems = $category->getLineItems();
                $perUnit = 0;

                foreach ($theItems as $item) {
                    $perUnit = $item->getActualSellPricePerUnit();
                    if ($perUnit > 0) {
                        break;
                    }
                }
                $qaHeader = $this->document->createElement("qaHeader");
                $this->languageTable->appendChild($qaHeader);


                $this->generateHourTaskRow($this->languageTable, "qa", $bundleCatName, $catTotal, $category->getItemsUnitCount(), $perUnit);

                $this->generateSubtotal('qaFooter', $this->languageTable, 'qaSubTotal', $catTotal);

                // we want to total them all up...
            } elseif ($catName == 'Engineering') {
                // first let's see if there more then one TM Work items in the set...
                $theItems = $category->getLineItems();

                $tmWorkHandled = false;
                $tmWorkUnitCount = 0.0;
                $tmWorkappu = 0.0;
                $tmWorkCost = 0.0;
                $tmWorkTaskName = "";
                $this->checkForMultipleTMWorkItems($theItems, $tmWorkHandled, $tmWorkUnitCount, $tmWorkCost, $tmWorkappu, $tmWorkTaskName);

                $this->generateEngineeringCategoryXML($rolledItems, $tmWorkHandled, $bundleCatName, $printableItems, $tmWorkCost, $tmWorkTaskName, $tmWorkappu, $catTotal, $tmWorkUnitCount);
            } elseif ($catName == "Formatting") {
                $this->generateFormattingCategoryXML($rolledItems, $bundleCatName, $printableItems, $catTotal);
            } elseif ($catName == 'Project Management') {
                $pmHeader = $this->document->createElement("pmHeader");
                $this->languageTable->appendChild($pmHeader);

                $pmRow = $this->document->createElement('pmRow');
                $this->languageTable->appendChild($pmRow);
                $pmCost = $this->document->createElement("pmCost");
                $pmCost->appendChild($this->document->createTextNode("$" . number_format($catTotal, 2)));
                $pmRow->appendChild($pmCost);
            }
        }
    }

    private function generateCategoryRowsPackaged($theCategories, $frameID, $packageAll = false) {
        if (!$packageAll) {
            //then package just engineering
            $this->generatePackagedEngineering($theCategories, $frameID);
        } else {
            $this->generatePackagedAll($theCategories,$frameID);
        }
    }
    
    private function generatePackagedAll($theCategories, $frameID){
		$packageTotal = 0.0;
		$packageTasks = array();
        
        
        //need to do in specific order and sorting doesn't seem to work
        //do linguistic category
        if (array_key_exists('Linguistic', $theCategories)){
            $this->prepareLinguistCategory($theCategories['Linguistic']);
        }
        
        //do engineering category
        if (array_key_exists('Engineering', $theCategories)){
            $this->prepareEngineeringCategory($theCategories['Engineering'], $packageTasks, $packageTotal);            
        }
        
        //do QA category
        if (array_key_exists('Quality Assurance', $theCategories)){
            $category = $theCategories['Quality Assurance'];
            $this->prepareQACategory($category,true,$packageTasks, $packageTotal);
        }
        
        //do formatting category
        if (array_key_exists('Formatting', $theCategories)){
            $category = $theCategories['Formatting'];
            $this->prepareFormattingCategory($category, $packageTasks, $packageTotal);
        }
        
        //do PM category
        if (array_key_exists('Project Management', $theCategories)){
			$category = $theCategories['Project Management'];
			$this->preparePMCategory($category, $frameID);
        }
    }

    private function generatePackagedEngineering($theCategories, $frameID) {
        $packageTotal = 0.0;
        $packageTasks = array();


        //need to do in specific order and sorting doesn't seem to work
        //do linguistic category
        if (array_key_exists('Linguistic', $theCategories)) {
            $category = $theCategories['Linguistic'];
            $this->prepareLinguistCategory($category);
        }

        //do engineering category
        if (array_key_exists('Engineering', $theCategories)) {
            $category = $theCategories['Engineering'];
            $this->prepareEngineeringCategory($category, $packageTasks, $packageTotal);
        }

        //do QA category
        if (array_key_exists('Quality Assurance', $theCategories)) {
            $category = $theCategories['Quality Assurance'];
            $this->prepareQACategory($category, FALSE, $packageTasks, $packageTotal);
        }

        //do formatting category
        if (array_key_exists('Formatting', $theCategories)) {
            $category = $theCategories['Formatting'];
            $this->prepareFormattingCategory($category, $packageTasks, $packageTotal);
        }

        //do PM category
        if (array_key_exists('Project Management', $theCategories)) {
            $category = $theCategories['Project Management'];
            $this->preparePMCategory($category, $frameID);
        }
    }

    private function prepareLinguistCategory($category) {
        $bundleCatName = $this->getRollupName("Linguistic Work");
        $catTotal = 0.0;
        $catTotal = $category->getItemsActualSellPriceTotal();
        if ($catTotal > 0) {
            $printableItems = $category->getPrintableTasks();
            $rolledItems = $category->getRolledTasks();
            $this->generateLinguistCategoryXML($bundleCatName, $languageTitle, $printableItems, $rolledItems, $catTotal);
        }
    }

    private function prepareEngineeringCategory($category, &$packageTasks, &$packageTotal) {
        $bundleCatName = $this->getRollupName('Engineering');
        $catTotal = 0.0;
        $catTotal = $category->getItemsActualSellPriceTotal();
        $packageTotal += $catTotal;
        if ($catTotal > 0) {
            $printableItems = $category->getPrintableTasks();
            $rolledItems = $category->getRolledTasks();
            $theItems = $category->getLineItems();
            $tmWorkHandled = false;
            $tmWorkUnitCount = 0.0;
            $tmWorkappu = 0.0;
            $tmWorkCost = 0.0;
            $tmWorkTaskName = "";
            $this->checkForMultipleTMWorkItems($theItems, $tmWorkHandled, $tmWorkUnitCount, $tmWorkCost, $tmWorkappu, $tmWorkTaskName);

            foreach ($rolledItems as $item) {
                array_push($packageTasks, $item);
            }
            foreach ($printableItems as $item) {
                array_push($packageTasks, $item);
            }
        }
    }

    private function prepareQACategory($category, $isPackaged = false, &$packageTasks = 0, &$packageTotal = 0) {
        if ($isPackaged) {
            $catTotal = 0.0;
            $catTotal = $category->getItemsActualSellPriceTotal();
            $packageTotal += $catTotal;
            if ($catTotal > 0) {
                $printableItems = $category->getPrintableTasks();
                $rolledItems = $category->getRolledTasks();
                foreach ($rolledItems as $item) {
                    array_push($packageTasks, $item);
                }
                foreach ($printableItems as $item) {
                    array_push($packageTasks, $item);
                }
            }
        } else {
            $bundleCatName = $this->getRollupName('Quality Assurance');
            $catTotal = 0.0;
            $catTotal = $category->getItemsActualSellPriceTotal();
            if ($catTotal > 0) {
                $printableItems = $category->getPrintableTasks();
                $rolledItems = $category->getRolledTasks();
                $theItems = $category->getLineItems();
                $perUnit = 0;

                foreach ($theItems as $item) {
                    $perUnit = $item->getActualSellPricePerUnit();
                    if ($perUnit > 0) {
                        break;
                    }
                }
                $qaHeader = $this->document->createElement("qaHeader");
                $this->languageTable->appendChild($qaHeader);
                $this->generateHourTaskRow($this->languageTable, "qa", $bundleCatName, $catTotal, $category->getItemsUnitCount(), $perUnit);
                $this->generateSubtotal('qaFooter', $this->languageTable, 'qaSubTotal', $catTotal);
            }
        }
    }

    private function prepareFormattingCategory($category, $packageTasks, $packageTotal) {
        $bundleCatName = $this->getRollupName('Formatting');
        $catTotal = 0.0;
        $catTotal = $category->getItemsActualSellPriceTotal();
        $packageTotal += $catTotal;
        if ($packageTotal > 0) {
            $printableItems = $category->getPrintableTasks();
            $rolledItems = $category->getRolledTasks();
            $dtpHeader = $this->document->createElement("dtpHeader");
            $this->languageTable->appendChild($dtpHeader);

            foreach ($rolledItems as $item) {
                array_push($packageTasks, $item);
            }
            foreach ($printableItems as $item) {
                array_push($packageTasks, $item);
            }

            $rolledTotal = 0.0;
            $rolledUnitCount = 0.0;
            $rolledPricePerUnit = 0.0;
            $this->rollUpItems($packageTasks, $rolledTotal, $rolledUnitCount, $rolledPricePerUnit);
            $this->generateHourTaskRow($this->languageTable, "dtp", $bundleCatName, $rolledTotal, $rolledUnitCount, $rolledPricePerUnit);
            $this->generateSubtotal('dtpFooter', $this->languageTable, 'dtpSubTotal', $packageTotal);
        }
    }

    private function preparePMCategory($category, $frameID) {
        $bundleCatName = $this->getRollupName('Project Management');
        $catTotal = 0.0;
        $catTotal = $this->projectManager->getDistroPMSellTotal($frameID);
        if ($catTotal > 0) {
            $printableItems = $category->getPrintableTasks();
            $rolledItems = $category->getRolledTasks();
            $pmHeader = $this->document->createElement("pmHeader");
            $this->languageTable->appendChild($pmHeader);

            $pmRow = $this->document->createElement('pmRow');
            $this->languageTable->appendChild($pmRow);
            $pmCost = $this->document->createElement("pmCost");
            $pmCost->appendChild($this->document->createTextNode("$" . number_format($catTotal, 2)));
            $pmRow->appendChild($pmCost);
        }
    }

    private function generateLinguistCategoryXML($bundleCatName, $languageTitle, $printableItems, $rolledItems, $catTotal) {
        $transHeader = $this->document->createElement("transHeader");
        $this->languageTable->appendChild($transHeader);

        // now we again add a table language for this section...
        // first, any rolled itmes
        $rolledTotal = 0.0;
        $rolledWords = 0;
        foreach ($rolledItems as $rolledItem) {
            $rolledTotal += $rolledItem->getActualSellPriceTotal();
            if (is_a($rolledItem, 'linguistTRandCETask')) {
                $rolledWords += $rolledItem->getWordCount();
            }
        }
        if ($rolledTotal > 0) {
            $rolledRate = $rolledTotal / $rolledWords;
            $this->generateLinguistBundledRow($bundleCatName, $rolledTotal, $languageTitle, $rolledWords, $rolledRate);
        }

        //$theItems = $category->getLineItems();
        foreach ($printableItems as $task) {
            if ($task->getActualSellPriceTotal() != 0) {
                if (is_a($task, 'LinguistTRandCETask')) {
                    $taskPrefix = $this->getTaskName($task->getId(), $task->getType());
                    $wordItems = $task->getLineItems();

                    if (!$this->handleWordTaskCombined($taskPrefix, $wordItems, $languageTitle)) {
                        foreach ($wordItems as $item) {
                            if ($item->getActualSellPriceTotal() != 0) {
                                $wordTaskName = count($wordItems) == 1 ? $taskPrefix : $this->formatWordTaskName($taskPrefix, $item->getDisplayName());
                                $this->generateWordTaskRow($this->languageTable, "trans", $wordTaskName, $item->getActualSellPriceTotal(), $item->getWorkUnitCount(), $item->getActualSellPricePerUnit(), $languageTitle);
                            }
                        }
                    }
                } else { // an hourly based item...
                    $this->generateHourTaskRow($this->languageTable, "trans", $this->getTaskName($task->getId(), $task->getType()), $task->getActualSellPriceTotal(), $task->getWorkUnitCount(), $task->getActualSellPricePerUnit(),$languageTitle);
                }
            }
        }

        // now append the subtotal...
        $this->generateSubtotal('transFooter', $this->languageTable, 'transSubTotal', $catTotal);
    }

    private function generateEngineeringCategoryXML($rolledItems, $tmWorkHandled, $bundleCatName, $printableItems, $tmWorkCost, $tmWorkTaskName, $tmWorkappu, $catTotal, $tmWorkUnitCount) {
        $engHeader = $this->document->createElement("engHeader");
        $this->languageTable->appendChild($engHeader);

        // first anything bundled...
        $rolledTotal = 0.0;
        $rolledUnitCount = 0.0;
        $rolledItemCount = 0;
        $rolledPricePerUnit = 0.0;
        foreach ($rolledItems as $rolledItem) {
            if ($rolledItem->getDisplayName() === "TM Work" && $tmWorkHandled) {
                continue;
            }
            $rolledTotal += $rolledItem->getActualSellPriceTotal();
            $rolledUnitCount += $rolledItem->getWorkUnitCount();
            $rolledPricePerUnit += $rolledItem->getActualSellPricePerUnit();
            $rolledItemCount++;
        }
        if ($rolledTotal > 0) {
            $this->generateHourTaskRow($this->languageTable, "eng", $bundleCatName, $rolledTotal, $rolledUnitCount, $rolledPricePerUnit / $rolledItemCount);
        }

        // now the printable items...

        foreach ($printableItems as $task) {
            if ($task->getDisplayName() === "TM Work" && $tmWorkHandled) {
                continue;
            }
            $this->generateHourTaskRow($this->languageTable, "eng", $this->getTaskName($task->getId(), $task->getType()), $task->getActualSellPriceTotal(), $task->getWorkUnitCount(), $task->getActualSellPricePerUnit());
        }

        if ($tmWorkHandled) {
            if ($tmWorkCost > 0) {
                $this->generateHourTaskRow($this->languageTable, "eng", $tmWorkTaskName, $tmWorkCost, $tmWorkUnitCount, $tmWorkappu);
            }
        }

        $this->generateSubtotal('engFooter', $this->languageTable, 'engSubTotal', $catTotal);
    }

    private function generateFormattingCategoryXML($rolledItems, $bundleCatName, $printableItems, $catTotal) {
        $dtpHeader = $this->document->createElement("dtpHeader");
        $this->languageTable->appendChild($dtpHeader);

        $rolledTotal = 0.0;
        $rolledUnitCount = 0.0;
        $rolledPricePerUnit = 0.0;
        $this->rollUpItems($rolledItems, $rolledTotal, $rolledUnitCount, $rolledPricePerUnit);

        if ($rolledTotal > 0) {
            $this->generateHourTaskRow($this->languageTable, "dtp", $bundleCatName, $rolledTotal, $rolledUnitCount, $rolledPricePerUnit);
        }

        foreach ($printableItems as $task) {
            $this->generateHourTaskRow($this->languageTable, "dtp", $this->getTaskName($task->getId(), $task->getType()), $task->getActualSellPriceTotal(), $task->getWorkUnitCount(), $task->getActualSellPricePerUnit());
        }

        $this->generateSubtotal('dtpFooter', $this->languageTable, 'dtpSubTotal', $catTotal);
    }

    private function handleWordTaskCombined($taskName, $workItems, $languageTitle) {
        $handled = false;
        if (count($workItems) > 1) {
            $wordCount = 0;
            $hasSellValue = 0;
            $sellTotal = 0;
            foreach ($workItems as $item) {
                $wordCount += $item->getWorkUnitCount();
                $sellTotal += $item->getActualSellPriceTotal();
                $hasSellValue += $item->getActualSellPriceTotal() == 0 ? 0 : 1;
            }
            if ($hasSellValue == 1) {
                $this->generateCombinedWordTaskRow($this->languageTable, "trans", $taskName, $sellTotal, $wordCount, $languageTitle);

                $handled = true;
            }
        }
        return $handled;
    }

    private function createProjectNode() {
        //create root / Body / projects
        $projects = $this->document->createElement("projects");
        $this->body->appendChild($projects);

        //create root / Body / projects / projectSubForm
        $this->projectSubForm = $this->document->createElement("projectSubform");
        $projects->appendChild($this->projectSubForm);

        //create root / Body / projects / projectSubForm / ProjectName
        $ProjectName = $this->document->createElement("ProjectName");
        $ProjectName->appendChild($this->document->createTextNode($this->projectManager->getProjectObj()->name));
        $this->projectSubForm->appendChild($ProjectName);
    }

    private function createBody() {
        $this->body = $this->document->createElement("Body");
        $this->root->appendChild($this->body);
    }

    private function appendBodyHeader() {
        //create root / Body / totalProjectCost
        $totalProjectCost = $this->document->createElement("totalProjectCost");

        $cost = $this->projectManager->getProjectFinalSellTotal();
        $cost = round($cost, 2);
        $cost = QuoteToolUtils::getCurrencyFormattedValue($cost);
        $totalProjectCost->appendChild($this->document->createTextNode($cost));
        $this->body->appendChild($totalProjectCost);
        //end root / Body / totalProjectCost
        //create root / Body / projectTimeline
        $projectTimeline = $this->document->createElement("projectTimeline");
        if ($this->projectManager->getProjectData()->get_reqDevDate() == "") {
            $projectTimeline->appendChild($this->document->createTextNode("TBD"));
        } else {
            $projectTimeline->appendChild($this->document->createTextNode("Requested delivery is " . $this->projectManager->getProjectData()->get_reqDevDate()));
        }

        $this->body->appendChild($projectTimeline);
        //end root / Body / projectTimeline
        //create root / Body / paymentTerms
        $paymentTerms = $this->document->createElement("paymentTerms");
        if ($this->projectManager->getProjectObj()->company->paymentTerms == "")
            $paymentTerms->appendChild($this->document->createTextNode("30 days from invoice date"));
        else
            $paymentTerms->appendChild($this->document->createTextNode($this->projectManager->getProjectObj()->company->paymentTerms . " days from invoice date"));
        $this->body->appendChild($paymentTerms);

        $billingCycle = $this->document->createElement("billingCycle");
        switch ($this->projectManager->getProjectData()->get_billingCycle()) {
            case 'progress': $cycle_str = $this->projectManager->getProjectData()->get_billingCycleOther();
                break;
            case '50-50': $cycle_str = "50% at project start, 50% at project delivery";
                break;
            case 'Project Start': $cycle_str = "At project start";
                break;
            case 'On Delivery': $cycle_str = "At project delivery";
                break;
        }
        $billingCycle->appendChild($this->document->createTextNode($cycle_str));
        $this->body->appendChild($billingCycle);
    }

    private function createHeader() {
        //create root / header
        $this->header = $this->document->createElement("Header");
        $this->root->appendChild($this->header);
    }

    private function appendQuoteCompany() {
        //create root / header / Table1
        $Table1 = $this->document->createElement("Table1");
        $this->header->appendChild($Table1);

        //create root / header / Table1 / Row1
        $Row1 = $this->document->createElement("Row1");
        $Table1->appendChild($Row1);

        //create root / header / Table1 / Row1 / QuoteCompany
        $QuoteCompany = $this->document->createElement("QuoteCompany");
        $QuoteCompany->appendChild($this->document->createTextNode($this->projectManager->getProjectObj()->name));
        $Row1->appendChild($QuoteCompany);
    }

    private function appendServicesTable() {
        $ServicesTable = $this->document->createElement("ServicesTable");
        $this->header->appendChild($ServicesTable);

        //create root / header / ServicesTable / Row1(s)
        foreach ($this->taskNameArray['name'] as $idx => $name) {
            $Row1 = $this->document->createElement("Row1");
            $ReqServ = $this->document->createElement("ReqServ");
            $ReqServ->appendChild($this->document->createTextNode($name));
            $Row1->appendChild($ReqServ);
            $ServicesTable->appendChild($Row1);
        }
        if (!is_null($this->projectManager->getFormatTaskForNonTratos())) {
            $Row1 = $this->document->createElement("Row1");
            $ReqServ = $this->document->createElement("ReqServ");
            $ReqServ->appendChild($this->document->createTextNode("Formatting"));
            $Row1->appendChild($ReqServ);
            $ServicesTable->appendChild($Row1);
        }
    }

    private function appendProjectDescription() {
        $ProjectDescription = $this->document->createElement("ProjectDescription");
        $ProjectDescription->appendChild($this->document->createTextNode(QuoteConstants::PROJECT_DESCRIPTION));
        $this->header->appendChild($ProjectDescription);
    }

    private function appendDescriptionTable() {
        //end root / header / ProjectDescription
        //create root / header / DescriptionTable
        $this->descriptionTable = $this->document->createElement("DescriptionTable");
        $this->header->appendChild($this->descriptionTable);
    }

    private function appendCompany() {
        //create root / header / DescriptionTable / Row1
        $Row1 = $this->document->createElement("Row1");
        $this->descriptionTable->appendChild($Row1);

        //create root / header / DescriptionTable / Row1 / Company
        $Company = $this->document->createElement("Company");
        $Company->appendChild($this->document->createTextNode($this->projectManager->getProjectObj()->company->name));
        $Row1->appendChild($Company);
        //end root / header / DescriptionTable / Row1 / Company
        //create root / header / DescriptionTable / Row1 / InvoiceDate
        $InvoiceDate = $this->document->createElement("InvoiceDate");
        $InvoiceDate->appendChild($this->document->createTextNode(QuoteToolUtils::getDateNow()));
        $Row1->appendChild($InvoiceDate);
    }

    private function appendContacts() {
        $Row2 = $this->document->createElement("Row2");
        $this->descriptionTable->appendChild($Row2);

        //create root / header / DescriptionTable / Row2 / ContactName
        $ContactName = $this->document->createElement("ContactName");
        $cn = $this->projectManager->getProjectObj()->contact->firstName . " "
                . $this->projectManager->getProjectObj()->contact->lastName . "\n";
        $cn .= $this->projectManager->getProjectObj()->contact->phone . "\n" .
                $this->projectManager->getProjectObj()->contact->email;
        $ContactName->appendChild($this->document->createTextNode($cn));
        $Row2->appendChild($ContactName);
        //end root / header / DescriptionTable / Row2 / ContactName
        //create root / header / DescriptionTable / Row2 / lingoContact
        $lingoContact = $this->document->createElement("lingoContact");
        $lc = $this->projectManager->getProjectObj()->sponsor->firstName . " " .
                $this->projectManager->getProjectObj()->sponsor->lastName . "\n";
        $lc .= $this->projectManager->getProjectObj()->sponsor->phone . "\n" .
                $this->projectManager->getProjectObj()->sponsor->email;
        $lingoContact->appendChild($this->document->createTextNode($lc));
        $Row2->appendChild($lingoContact);
    }

    private function appendSrcLangAndProjectID() {
        $Row3 = $this->document->createElement("Row3");
        $this->descriptionTable->appendChild($Row3);

        //create root / header / DescriptionTable / Row3 / SrcLang
        $SrcLang = $this->document->createElement("SrcLang");

        $SrcLang->appendChild($this->document->createTextNode($this->sourceLang));
        $Row3->appendChild($SrcLang);
        //end root / header / DescriptionTable / Row3 / SrcLang
        //create root / header / DescriptionTable / Row3 / ProjectNumber
        $ProjectNumber = $this->document->createElement("ProjectNumber");
        $ProjectNumber->appendChild($this->document->createTextNode($this->projectManager->getProjectObj()->id));
        $Row3->appendChild($ProjectNumber);
    }

    private function appendTargetLangs() {
        $Row4 = $this->document->createElement("Row4");
        $this->descriptionTable->appendChild($Row4);
        $targLangs = $this->projectManager->getTargetLanguages();
        //create root / header / DescriptionTable / Row4 / TgtLanguages
        $TgtLanguages = $this->document->createElement("TgtLanguages");

        $langs = "";
        $loopCount = 0;
        foreach ($targLangs as $language) {
            if ($loopCount != 0) {
                $langs .= ", ";
            }
            $langs .= $language;
            $loopCount++;
        }
        //$l = substr($l, 0, strlen($l) - 2);   //remove the trailing return from the string
        $TgtLanguages->appendChild($this->document->createTextNode($langs));
        $Row4->appendChild($TgtLanguages);
    }

    private function getRollupName($catName) {
        return $this->rollNameArray[$catName];
    }

    function getTaskName($taskId, $taskType) {
        $strPos = strpos($taskId, "-");

        if (!is_bool(($strPos))) {
            $taskId = substr($taskId, 0, $strPos);
        }

        $idx = FALSE;
        foreach ($this->taskNameArray['ids'] as $key => $value) {
            if (in_array($taskId, $value)) {
                $idx = $key;
                break;
            }
        }

        if ($idx === FALSE) {
            switch ($taskType) {
                case "TR+CE": $taskName = "Translation and Copyediting";
                    break;
                case "TR": $taskName = "Translation";
                    break;
                case "CE": $taskName = "Copyediting";
                    break;
                case "PR": $taskName = "Proofreading";
                    break;
                case "Formatting Specialist": $taskName = "Formatting";
                    break;
                case "OLR": $taskName = "Online Review";
                    break;
                default: $taskName = $taskType;
            }
        } else {
            $taskName = $this->taskNameArray['name'][$idx];
        }
        return $taskName;
    }

    private function formatWordTaskName($trcePrefix, $WordTaskName) {
        $strPos = strpos($WordTaskName, "-");
        $endString = substr($WordTaskName, $strPos);
        return $trcePrefix . " " . $endString;
    }

    private static function determineOtherRowSuffix($category) {
        $row = "";
        switch ($category) {
            case "Engineering" :
                $row = "eng";
                break;
            case "Formatting" :
                $row = "dtp";
                break;
            case "Quality Assurance" :
                $row = "qa";
                break;
            default:
                $row = "unknown";
        }
        return $row;
    }

    private function checkForMultipleTMWorkItems($theItems, &$tmworkHandled, &$tmWorkUnitCount, &$tmWorkCost, &$tmWorkappu, &$tmWTaskName) {
        $tmworkHandled = false;
        $tmworkArray = array();
        foreach ($theItems as $item) {
            if ($item->getDisplayName() == "TM Work" && $item->getActualSellPriceTotal() > 0) {
                array_push($tmworkArray, $item);
            }
        }
        $tmWorkUnitCount = 0.0;
        $tmWorkappu = 0.0;
        $tmWorkCost = 0.0;
        $tmWTaskName = "";
        if (count($tmworkArray) > 1) {
            $tmworkHandled = true;
            // if there are more then one of these, simply handle it as a printable item...
            $tmWTaskName = $this->getTaskName($tmworkArray[0]->getId(), $tmworkArray[0]->getType());
            $tmWorkappu = $tmworkArray[0]->getActualSellPricePerUnit();
            foreach ($tmworkArray as $tmWork) {
                $tmWorkUnitCount += $tmWork->getWorkUnitCount();
                $tmWorkCost += $tmWork->getActualSellPriceTotal();
            }
            if ($tmWorkCost > 0) {
                $tmworkHandled = true;
            }
        }
    }

    private function rollUpItems($rolledItems, &$rolledTotal, &$rolledUnitCount, &$rolledPricePerUnit) {
        $rolledTotal = 0.0;
        $rolledUnitCount = 0.0;
        $rolledPricePerUnit = 0.0;
        foreach ($rolledItems as $rolledItem) {
            $rolledTotal += $rolledItem->getActualSellPriceTotal();
            $rolledUnitCount += $rolledItem->getWorkUnitCount();
        }
        if ($rolledTotal > 0) {
            $rolledPricePerUnit = round($rolledTotal / $rolledUnitCount,2);
        }
        return;
    }

    private function generateSubtotal($footerName, $tableNode, $subTotalElementName, $subTotalValue) {
        $footer = $this->document->createElement($footerName);
        $tableNode->appendChild($footer);
        $subTotalElement = $this->document->createElement($subTotalElementName);
        $subTotalElement->appendChild($this->document->createTextNode("$" . number_format($subTotalValue, 2)));
        $footer->appendChild($subTotalElement);
    }

    private function generateLinguistBundledRow($bundleName, $bundleCost, $languageTitle, $words = null, $rate = null) {
        $this->generateTaskRow($this->languageTable, "trans", $bundleName, $bundleCost, $words, $rate, "word", $languageTitle);
    }

    private function generateHourTaskRow($tableNode, $taskNameElementSuffix, $taskName, $taskCost, $unitCount, $perUnitRate, $languageTitle) {
        $this->generateTaskRow($tableNode, $taskNameElementSuffix, $taskName, $taskCost, $unitCount, $perUnitRate, "hour", $languageTitle);
    }

    private function generateWordTaskRow($tableNode, $taskNameElementSuffix, $taskName, $taskCost, $unitCount, $perUnitRate, $languagePair) {
        $theRow = $this->document->createElement($taskNameElementSuffix . "Row");
        $tableNode->appendChild($theRow);

        $tableLanguage = $this->document->createElement("tableLanguage");
        $tableLanguage->appendChild($this->document->createTextNode($languagePair));
        $theRow->appendChild($tableLanguage);

        $theTaskName = $this->document->createElement($taskNameElementSuffix . "TaskName");
        $theTaskName->appendChild($this->document->createTextNode($taskName));
        $theRow->appendChild($theTaskName);

        $theUnits = $this->document->createElement($taskNameElementSuffix . "Units");
        if (!is_null($unitCount)) {
            $theUnits->appendChild($this->document->createTextNode(number_format($unitCount) . " words"));
        }
        $theRow->appendChild($theUnits);

        $theRate = $this->document->createElement($taskNameElementSuffix . "Rate");
        if (!is_null($perUnitRate)) {
            $theRate->appendChild($this->document->createTextNode("$" . number_format($perUnitRate, 2) . "/word"));
        }
        $theRow->appendChild($theRate);

        $theCost = $this->document->createElement($taskNameElementSuffix . "Cost");
        $theCost->appendChild($this->document->createTextNode("$" . number_format($taskCost, 2)));
        $theRow->appendChild($theCost);
    }

    private function generateCombinedWordTaskRow($tableNode, $taskNameElementSuffix, $taskName, $taskCost, $unitCount, $languagePair) {
        $theRow = $this->document->createElement($taskNameElementSuffix . "Row");
        $tableNode->appendChild($theRow);

        $tableLanguage = $this->document->createElement("tableLanguage");
        $tableLanguage->appendChild($this->document->createTextNode($languagePair));
        $theRow->appendChild($tableLanguage);

        $theTaskName = $this->document->createElement($taskNameElementSuffix . "TaskName");
        $theTaskName->appendChild($this->document->createTextNode($taskName));
        $theRow->appendChild($theTaskName);

        $theUnits = $this->document->createElement($taskNameElementSuffix . "Units");
        if (!is_null($unitCount)) {
            $theUnits->appendChild($this->document->createTextNode(number_format($unitCount) . " words"));
        }
        $theRow->appendChild($theUnits);

        $theCost = $this->document->createElement($taskNameElementSuffix . "Cost");
        $theCost->appendChild($this->document->createTextNode("$" . number_format($taskCost, 2)));
        $theRow->appendChild($theCost);
    }

    private function generateTaskRow($tableNode, $taskNameElementSuffix, $taskName, $taskCost, $unitCount = null, $perUnitRate = null, $type = null, $languageTitle = null) {
        $theRow = $this->document->createElement($taskNameElementSuffix . "Row");
        $tableNode->appendChild($theRow);
        if ($taskNameElementSuffix == 'trans') {
            $tableLanguage = $this->document->createElement('tableLanguage');
            $tableLanguage->appendChild($this->document->createTextNode($languageTitle));
            $theRow->appendChild($tableLanguage);
        }
        $theTaskName = $this->document->createElement($taskNameElementSuffix . "TaskName");
        $theTaskName->appendChild($this->document->createTextNode($taskName));
        $theRow->appendChild($theTaskName);

        $theUnits = $this->document->createElement($taskNameElementSuffix . "Units");
        if (!is_null($unitCount)) {
            if ( is_numeric( $unitCount ) && strpos( $unitCount, '.' ) === false){
                $unitCountStr = number_format($unitCount);
            }else{
                $unitCountStr = number_format($unitCount,2);
            }
            $theUnits->appendChild($this->document->createTextNode($unitCountStr . " " . $type . "s"));
        }
        $theRow->appendChild($theUnits);

        $theRate = $this->document->createElement($taskNameElementSuffix . "Rate");
        if (!is_null($perUnitRate)) {
            $theRate->appendChild($this->document->createTextNode("$" . number_format($perUnitRate, 2) . "/" . $type));
        }
        $theRow->appendChild($theRate);

        $theCost = $this->document->createElement($taskNameElementSuffix . "Cost");
        $theCost->appendChild($this->document->createTextNode("$" . number_format($taskCost, 2)));
        $theRow->appendChild($theCost);
    }

}
