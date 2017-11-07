<?PHP

define('_HRLY_RATE_', 60);

include_once('words.php');

function LLS_Pricing(&$taskService, $rushFee, $pmSurcharge, &$sellRates, $projectObj) {
    $taskCount = 0;
    $totalPrice = 0;
    $totalWordcount = 0;
    $totalCost = 0;
    $tier = -1;
    $useUSLinguists = $_SESSION['useUSLinguists'];



    //loop through the linguistic tasks and update the pricing
    if (count($taskService->lingTasks) < 2) {
        lls_processLing($taskService->lingTasks, $totalCost, $totalPrice, $totalWordcount, $tier, $useUSLinguists, $taskCount, $sellRates, $projectObj->company->name, $projectObj->pageCount);
    } else {
        foreach ($taskService->lingTasks as $lingTask) {
            if ($lingTask->ltask->type != 'PR' || $_SESSION['proofReading'] != "yes") {
                lls_processLing($lingTask, $totalCost, $totalPrice, $totalWordcount, $tier, $useUSLinguists, $taskCount, $sellRates, $projectObj->company->name, $projectObj->pageCount);
            }
        }
    }

    //loop through the billable tasks
    if (count($taskService->billableTasks) < 2) {
        if ($projectObj->company->name == 'Kaiser Permanente -LLSW') {
            lls_processBillable($taskService->billableTasks->btask, round(_HRLY_RATE_ * 0.85, 2), $totalCost, $totalPrice, $sellRates);
        } else {
            lls_processBillable($taskService->billableTasks->btask, _HRLY_RATE_, $totalCost, $totalPrice, $sellRates);
        }
    } else {
        foreach ($taskService->billableTasks as $billTask) {
            if ($projectObj->company->name == 'Kaiser Permanente -LLSW') {
                lls_processBillable($billTask, round(_HRLY_RATE_ * 0.85, 2), $totalCost, $totalPrice, $sellRates);
            } else {
                lls_processBillable($billTask, _HRLY_RATE_, $totalCost, $totalPrice, $sellRates);
            }
        }
    }



    //loop through the billable tasks and find the PM task and update it's pricing
    $minimum = calculateMinimum($taskService);
    if ($totalPrice < $minimum) {
        $totalPrice = $minimum;
        $_SESSION['hitMinimum'] = true;
    }
    if (count($taskService->billableTasks) < 2) {
        if ($taskService->billableTasks->btask->name == 'Project Management') {
            if (($totalPrice > $minimum) && ($pmSurcharge == true)) {
                $taskService->billableTasks->btask->price = round($totalPrice / 9, 2);
                if ($projectObj->company->name == 'Kaiser Permanente -LLSW') {
                    $taskService->billableTasks->btask->price = round($taskService->billableTasks->btask->price * 0.85, 2);
                }
            } else {
                $taskService->billableTasks->btask->price = 0;
            }
            $totalPrice += $taskService->billableTasks->btask->price;
            $totalCost += round($taskService->billableTasks->btask->workRequired * $taskService->billableTasks->hourlyRate, 2);
        }
    } else {
        foreach ($taskService->billableTasks as $billTask) {
            if ($billTask->btask->name == 'Project Management') {
                if (($totalPrice > $minimum) && ($pmSurcharge == true)) {
                    $billTask->btask->price = round($totalPrice / 9, 2);
                    if ($projectObj->company->name == 'Kaiser Permanente -LLSW') {
                        $billTask->btask->price = round($billTask->btask->price * 0.85, 2);
                    }
                } else {
                    $billTask->btask->price = 0;
                }
                $totalPrice += $billTask->btask->price;
                $totalCost += round($billTask->btask->workRequired * $billTask->hourlyRate, 2);
            }
        }
    }

    if ($rushFee !== 0) {
        if ($rushFee === 0.25 || $rushFee === 'custom25') {
            $rushFeePrice = round($totalPrice * .25, 2);
        } else {
            $rushFeePrice = round($totalPrice * .5, 2);
        }
        $taskService->rushFee = $rushFeePrice;
        $totalPrice += $rushFeePrice;
        //echo "Expedited Turnaround Surcharge: $rushFee<br>";
    }

    $_SESSION['totalCost'] = $totalCost;

    return $totalPrice;
}

function lls_processLing(&$theTask, &$totalCost, &$totalPrice, &$totalWordcount, &$tier, $useUSLinguists, &$taskCount, &$sellRates, $companyName, $pageCount) {
    //determine which set of pricing we need
    switch ($companyName) {
        case 'Global Compliance -LLSW':
            globalcomplianceBlockPricing($theTask, $totalCost, $totalPrice, $totalWordcount, $tier, $useUSLinguists, $taskCount, $sellRates, $pageCount);
            break;

        case 'State Farm -LLSW':
            statefarmBlockPricing($theTask, $totalCost, $totalPrice, $totalWordcount, $tier, $useUSLinguists, $taskCount, $sellRates);
            break;

        case 'Kaiser Permanente -LLSW':
            kaiserBlockPricing($theTask, $totalCost, $totalPrice, $totalWordcount, $tier, $useUSLinguists, $taskCount, $sellRates);
            break;

        default:
            standardBlockPricing($theTask, $totalCost, $totalPrice, $totalWordcount, $tier, $useUSLinguists, $taskCount, $sellRates, $companyName);
            break;
    }
}

function lls_processBillable(&$theTask, $hourlyRate, &$totalCost, &$totalPrice, &$sellRates) {
    if ($theTask->btask->name != 'Project Management') {
        $price = round($theTask->btask->workRequired * $hourlyRate, 2);
        $theTask->btask->price = $price;
        $totalPrice += $price;
        $sellRates[$theTask->btask->id] = $hourlyRate;
        $totalCost += round($theTask->btask->workRequired * $theTask->hourlyRate, 2);
    }
}

function standardBlockPricing(&$theTask, &$totalCost, &$totalPrice, &$totalWordcount, &$tier, $useUSLinguists, &$taskCount, &$sellRates, $companyName) {
    if ((($theTask->ltask->type == 'TR+CE') || ($theTask->ltask->type == 'TR')) && (getTotalWords($theTask) > 0)) {


        $totalWordcount = getTotalWords($theTask);
        $totalCost += round($totalWordcount * $theTask->wordRateDetails->trce_new, 2);

        $theLanguage = $theTask->targLang;
        if ($theLanguage == 'English (US)') {
            $theLanguage = $theTask->sourceLang;
        }

        $wordBlocks = ceil($totalWordcount / 25);
        switch ($theLanguage) {
            case 'Spanish (International)':
            case 'Spanish (Latin America)':
            case 'Spanish (Spain)':
            case 'Spanish (US)':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 5.25;
                    $sellRates[$theTask->ltask->id] = 5.25;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 6.5;
                    $sellRates[$theTask->ltask->id] = 6.5;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 9.5;
                    $sellRates[$theTask->ltask->id] = 9.5;
                } else {
                    $price = $wordBlocks * 10.5;
                    $sellRates[$theTask->ltask->id] = 10.5;
                }
                $price = round($price, 2);
                $tier = 1;
                break;

            case 'Chinese (Simplified)':
            case 'Chinese (Traditional)':
            case 'Chinese (Traditional-Hong Kong)':
            case 'French (Belgium)':
            case 'French (Canada)':
            case 'French (France)':
            case 'Japanese':
            case 'Korean':
            case 'Russian':
            case 'Vietnamese':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 8.25;
                    $sellRates[$theTask->ltask->id] = 8.25;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 8.75;
                    $sellRates[$theTask->ltask->id] = 8.75;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 14;
                    $sellRates[$theTask->ltask->id] = 14;
                } else {
                    $price = $wordBlocks * 14.5;
                    $sellRates[$theTask->ltask->id] = 14.5;
                }
                $tier = 2;
                $price = round($price, 2);
                break;

            case 'Armenian':
            case 'Cambodian':
            case 'German':
            case 'German (Austria)':
            case 'Haitian Creole':
            case 'Italian':
            case 'Polish':
            case 'Portuguese (Brazil)':
            case 'Portuguese (Portugal)':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 9.5;
                    $sellRates[$theTask->ltask->id] = 9.5;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 10.5;
                    $sellRates[$theTask->ltask->id] = 10.5;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 14;
                    $sellRates[$theTask->ltask->id] = 14;
                } else {
                    $price = $wordBlocks * 15;
                    $sellRates[$theTask->ltask->id] = 15;
                }
                $tier = 3;
                $price = round($price, 2);
                break;

            default:
                //since certain companies need different schemes, check those
                if ($companyName == 'USAA -LLSW') {
                    if ($totalWordcount > 7500) {
                        $price = $wordBlocks * 13.25;
                        $sellRates[$theTask->ltask->id] = 13.25;
                    } elseif ($totalWordcount > 2500) {
                        $price = $wordBlocks * 13.75;
                        $sellRates[$theTask->ltask->id] = 13.75;
                    } elseif ($totalWordcount > 1000) {
                        $price = $wordBlocks * 19;
                        $sellRates[$theTask->ltask->id] = 19;
                    } else {
                        $price = $wordBlocks * 19.5;
                        $sellRates[$theTask->ltask->id] = 19.5;
                    }
                    $tier = 4;
                    $price = round($price, 2);
                } else {
                    if ($totalWordcount > 7500) {
                        $price = $wordBlocks * 9.5;
                        $sellRates[$theTask->ltask->id] = 9.5;
                    } elseif ($totalWordcount > 2500) {
                        $price = $wordBlocks * 11;
                        $sellRates[$theTask->ltask->id] = 11;
                    } elseif ($totalWordcount > 1000) {
                        $price = $wordBlocks * 16;
                        $sellRates[$theTask->ltask->id] = 16;
                    } else {
                        $price = $wordBlocks * 16.5;
                        $sellRates[$theTask->ltask->id] = 16.5;
                    }
                    $tier = 4;
                    $price = round($price, 2);
                }
                break;
        }

        //determine the DTP price (if any)
        $dtpPrice = 0;
        //$dtpPrice = round($theTask->wordCounts->formattingHours * $theTask->wordRateDetails->hourly,2);
        $dtpPrice = round($theTask->wordCounts->formattingHours * _HRLY_RATE_, 2);

        $dtpData[$theTask->ltask->id]['price'] = $dtpPrice;
        $dtpData[$theTask->ltask->id]['rate'] = _HRLY_RATE_;
        $_SESSION['dtpData'] = serialize($dtpData);

        $theTask->ltask->price = $price + $dtpPrice;
        $totalPrice += $price + $dtpPrice;
        $totalCost += round($theTask->wordCounts->formattingHours * $theTask->wordRateDetails->hourly, 2);
        //echo $theTask->ltask->name, ": ", $theTask->ltask->price,"<br>";
        $taskCount++;
    } else {
        if ($useUSLinguists) {
            $hourlyRate = ($theTask->wordRateDetails->US_based_hourly > 0) ? $theTask->wordRateDetails->US_based_hourly : $theTask->wordRateDetails->hourly;
        } else {

            $hourlyRate = $theTask->wordRateDetails->hourly;
        }
        $costRate = $hourlyRate;
        /* per Dougal, the hourly rate for proofreading should be disconnected from the linguist rate and set to
          a fixed rate of $65/hr.
          $hourlyRate = round($hourlyRate/0.5,2);
         */
        $hourlyRate = 65;

        $price = (($theTask->ltask->workRequired) * $hourlyRate);
        $price = round($price, 2);
        $sellRates[$theTask->ltask->id] = $hourlyRate;
        $totalCost += round($costRate * $theTask->ltask->workRequired, 2);
        $taskCount++;
        $theTask->ltask->price = $price;
        $totalPrice += $price;
    }
}

function kaiserBlockPricing(&$theTask, &$totalCost, &$totalPrice, &$totalWordcount, &$tier, $useUSLinguists, &$taskCount, &$sellRates) {
//kaiser pricing is 15% off standard block pricing


    if ((($theTask->ltask->type == 'TR+CE') || ($theTask->ltask->type == 'TR')) && (getTotalWords($theTask) > 0)) {


        $totalWordcount = getTotalWords($theTask);
        $totalCost += round($totalWordcount * $theTask->wordRateDetails->trce_new, 2);

        $theLanguage = $theTask->targLang;
        if ($theLanguage == 'English (US)') {
            $theLanguage = $theTask->sourceLang;
        }

        $wordBlocks = ceil($totalWordcount / 25);
        switch ($theLanguage) {
            case 'Spanish (International)':
            case 'Spanish (Latin America)':
            case 'Spanish (Spain)':
            case 'Spanish (US)':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 4.46;
                    $sellRates[$theTask->ltask->id] = 4.46;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 5.53;
                    $sellRates[$theTask->ltask->id] = 5.53;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 8.08;
                    $sellRates[$theTask->ltask->id] = 8.08;
                } else {
                    $price = $wordBlocks * 8.93;
                    $sellRates[$theTask->ltask->id] = 8.93;
                }
                $price = round($price, 2);
                $tier = 1;
                break;

            case 'Chinese (Simplified)':
            case 'Chinese (Traditional)':
            case 'Chinese (Traditional-Hong Kong)':
            case 'French (Belgium)':
            case 'French (Canada)':
            case 'French (France)':
            case 'Japanese':
            case 'Korean':
            case 'Russian':
            case 'Vietnamese':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 7.01;
                    $sellRates[$theTask->ltask->id] = 7.01;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 7.44;
                    $sellRates[$theTask->ltask->id] = 7.44;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 11.9;
                    $sellRates[$theTask->ltask->id] = 11.9;
                } else {
                    $price = $wordBlocks * 12.33;
                    $sellRates[$theTask->ltask->id] = 12.33;
                }
                $tier = 2;
                $price = round($price, 2);
                break;

            case 'Armenian':
            case 'Cambodian':
            case 'German':
            case 'German (Austria)':
            case 'Haitian Creole':
            case 'Italian':
            case 'Polish':
            case 'Portuguese (Brazil)':
            case 'Portuguese (Portugal)':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 8.08;
                    $sellRates[$theTask->ltask->id] = 8.08;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 8.93;
                    $sellRates[$theTask->ltask->id] = 8.93;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 11.9;
                    $sellRates[$theTask->ltask->id] = 11.9;
                } else {
                    $price = $wordBlocks * 12.75;
                    $sellRates[$theTask->ltask->id] = 12.75;
                }
                $tier = 3;
                $price = round($price, 2);
                break;

            default:
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 8.08;
                    $sellRates[$theTask->ltask->id] = 8.08;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 9.35;
                    $sellRates[$theTask->ltask->id] = 9.35;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 13.6;
                    $sellRates[$theTask->ltask->id] = 13.6;
                } else {
                    $price = $wordBlocks * 14.03;
                    $sellRates[$theTask->ltask->id] = 14.03;
                }
                $tier = 4;
                $price = round($price, 2);
                break;
        }

        //determine the DTP price (if any)
        $dtpPrice = 0;
        //$dtpPrice = round($theTask->wordCounts->formattingHours * $theTask->wordRateDetails->hourly,2);
        $dtpRate = round(_HRLY_RATE_ * 0.85, 2);
        $dtpPrice = round($theTask->wordCounts->formattingHours * $dtpRate, 2);

        $dtpData[$theTask->ltask->id]['price'] = $dtpPrice;
        $dtpData[$theTask->ltask->id]['rate'] = $dtpRate;
        $_SESSION['dtpData'] = serialize($dtpData);

        $theTask->ltask->price = $price + $dtpPrice;
        $totalPrice += $price + $dtpPrice;
        $totalCost += round($theTask->wordCounts->formattingHours * $theTask->wordRateDetails->hourly, 2);
        //echo $theTask->ltask->name, ": ", $theTask-l>task->price,"<br>";
        $taskCount++;
    } else {
        if ($useUSLinguists) {
            $hourlyRate = ($theTask->wordRateDetails->US_based_hourly > 0) ? $theTask->wordRateDetails->US_based_hourly : $theTask->wordRateDetails->hourly;
        } else {

            $hourlyRate = $theTask->wordRateDetails->hourly;
        }
        $costRate = $hourlyRate;
        $hourlyRate = round(($hourlyRate / 0.5) * 0.85, 2);

        $price = (($theTask->ltask->workRequired) * $hourlyRate);
        $price = round($price, 2);
        $sellRates[$theTask->ltask->id] = $hourlyRate;
        $totalCost += round($costRate * $theTask->ltask->workRequired, 2);
        $taskCount++;
        $theTask->ltask->price = $price;
        $totalPrice += $price;
    }
}

function statefarmBlockPricing(&$theTask, &$totalCost, &$totalPrice, &$totalWordcount, &$tier, $useUSLinguists, &$taskCount, &$sellRates) {
    if ((($theTask->ltask->type == 'TR+CE') || ($theTask->ltask->type == 'TR')) && (getTotalWords($theTask) > 0)) {


        $totalWordcount = getTotalWords($theTask);
        $totalCost += round($totalWordcount * $theTask->wordRateDetails->trce_new, 2);

        $theLanguage = $theTask->targLang;
        if ($theLanguage == 'English (US)') {
            $theLanguage = $theTask->sourceLang;
        }

        $wordBlocks = ceil($totalWordcount / 25);
        switch ($theLanguage) {
            case 'Spanish (International)':
            case 'Spanish (Latin America)':
            case 'Spanish (Spain)':
            case 'Spanish (US)':
            case 'Chinese (Simplified)':
            case 'Chinese (Traditional)':
            case 'Chinese (Traditional-Hong Kong)':
            case 'French (Belgium)':
            case 'French (Canada)':
            case 'French (France)':
            case 'Japanese':
            case 'Korean':
            case 'Russian':
            case 'Vietnamese':
                $price = $wordBlocks * 7.65;
                $sellRates[$theTask->ltask->id] = 7.65;

                $price = round($price, 2);
                $tier = 1;
                break;


            default:
                $price = $wordBlocks * 10.8;
                $sellRates[$theTask->ltask->id] = 10.8;


                $tier = 2;
                $price = round($price, 2);
                break;
        }

        //determine the DTP price (if any)
        $dtpPrice = 0;
        //$dtpPrice = round($theTask->wordCounts->formattingHours * $theTask->wordRateDetails->hourly,2);
        $dtpPrice = round($theTask->wordCounts->formattingHours * _HRLY_RATE_, 2);

        $dtpData[$theTask->ltask->id]['price'] = $dtpPrice;
        $dtpData[$theTask->ltask->id]['rate'] = _HRLY_RATE_;
        $_SESSION['dtpData'] = serialize($dtpData);

        $theTask->ltask->price = $price + $dtpPrice;
        $totalPrice += $price + $dtpPrice;
        $totalCost += round($theTask->wordCounts->formattingHours * $theTask->wordRateDetails->hourly, 2);
        //echo $theTask->ltask->name, ": ", $theTask->ltask->price,"<br>";
        $taskCount++;
    } else {
        if ($useUSLinguists) {
            $hourlyRate = ($theTask->wordRateDetails->US_based_hourly > 0) ? $theTask->wordRateDetails->US_based_hourly : $theTask->wordRateDetails->hourly;
        } else {

            $hourlyRate = $theTask->wordRateDetails->hourly;
        }
        $costRate = $hourlyRate;
        $hourlyRate = round($hourlyRate / 0.5, 2);

        $price = (($theTask->ltask->workRequired) * $hourlyRate);
        $price = round($price, 2);
        $sellRates[$theTask->ltask->id] = $hourlyRate;
        $totalCost += round($costRate * $theTask->ltask->workRequired, 2);
        $taskCount++;
        $theTask->ltask->price = $price;
        $totalPrice += $price;
    }
}

function globalcomplianceBlockPricing(&$theTask, &$totalCost, &$totalPrice, &$totalWordcount, &$tier, $useUSLinguists, &$taskCount, &$sellRates, $pageCount) {
    if ((($theTask->ltask->type == 'TR+CE') || ($theTask->ltask->type == 'TR')) && (getTotalWords($theTask) > 0)) {


        $totalWordcount = getTotalWords($theTask);
        $totalCost += round($totalWordcount * $theTask->wordRateDetails->trce_new, 2);

        $theLanguage = $theTask->targLang;
        if ($theLanguage == 'English (US)') {
            $theLanguage = $theTask->sourceLang;
        }

        $wordBlocks = ceil($totalWordcount / 25);
        switch ($theLanguage) {
            case 'Spanish (International)':
            case 'Spanish (Latin America)':
            case 'Spanish (Spain)':
            case 'Spanish (US)':
            case 'Chinese (Simplified)':
            case 'Chinese (Traditional)':
            case 'Chinese (Traditional-Hong Kong)':
            case 'French (Belgium)':
            case 'French (Canada)':
            case 'French (France)':
            case 'Japanese':
            case 'Korean':
            case 'Russian':
            case 'Vietnamese':
                $price = $wordBlocks * 7.65;
                $sellRates[$theTask->ltask->id] = 7.65;

                $price = round($price, 2);
                $tier = 1;
                break;


            default:
                $price = $wordBlocks * 10.8;
                $sellRates[$theTask->ltask->id] = 10.8;


                $tier = 2;
                $price = round($price, 2);
                break;
        }

        //determine the DTP price (if any)
        $dtpPrice = 0;

        $dtpPrice = round($pageCount * 10, 2);

        $dtpData[$theTask->ltask->id]['price'] = $dtpPrice;
        $dtpData[$theTask->ltask->id]['rate'] = 10;
        $_SESSION['dtpData'] = serialize($dtpData);

        $theTask->ltask->price = $price + $dtpPrice;
        $totalPrice += $price + $dtpPrice;
        $totalCost += round($theTask->wordCounts->formattingHours * $theTask->wordRateDetails->hourly, 2);
        //echo $theTask->ltask->name, ": ", $theTask->ltask->price,"<br>";
        $taskCount++;
    } else {
        if ($useUSLinguists) {
            $hourlyRate = ($theTask->wordRateDetails->US_based_hourly > 0) ? $theTask->wordRateDetails->US_based_hourly : $theTask->wordRateDetails->hourly;
        } else {

            $hourlyRate = $theTask->wordRateDetails->hourly;
        }
        $costRate = $hourlyRate;
        $hourlyRate = round($hourlyRate / 0.5, 2);

        $price = (($theTask->ltask->workRequired) * $hourlyRate);
        $price = round($price, 2);
        $sellRates[$theTask->ltask->id] = $hourlyRate;
        $totalCost += round($costRate * $theTask->ltask->workRequired, 2);
        $taskCount++;
        $theTask->ltask->price = $price;
        $totalPrice += $price;
    }
}

?>