<?php

require_once (__DIR__ . "/PricingMySql.php");
require_once (__DIR__ . "/QuoteLineItem.php");
require_once(__DIR__ . '/../enums/DistributedTypeEnum.php');
require_once(__DIR__ . '/../interfaces/ILinguistQuoteItemContainer.php');

define('_HRLY_RATE_', 60);
define('_FORMAT_HRLY_RATE_', 27);

/**
 * Description of QuotToolUtils
 *
 * @author Axian Developer
 */
class QuoteToolUtils {

    CONST TIERS = [
        'TIER1' => ['Spanish'],
        'TIER2' => ['Chinese', 'French', 'Japanese', 'Korean', 'Russian', 'Vietnamese'],
        'TIER3' => ['Armenian', 'Cambodian', 'German', 'Haitian Creole', 'Italian', 'Polish', 'Portuguese']
    ];

    public static function getRushRateOnTask($taskName, $subSection, $sourceLang, $targetLang, $db_table_name, $defaultRushFeeToken, PricingMySql $pricingDbConnection) {
        $retRate = 1;
        $query = "SELECT rush_rate from " . $db_table_name . " WHERE task_name = '";
        $query .= $taskName . $subSection . str_replace(" ", "_", $sourceLang);
        $query .= "=" . str_replace(" ", "_", $targetLang) . "'";
        $result = $pricingDbConnection->query($query);
        $res1 = $result->fetch_assoc();
        if ($result === false || $res1['rush_rate'] === NULL || $res1['rush_rate'] === 0) {
            $query = "SELECT rate from " . $db_table_name . " WHERE task_name = '" . $taskName;
            $query .= $subSection . "=" . str_replace(" ", "_", $sourceLang) . "=" . str_replace(" ", "_", $targetLang) . "'";
            $result = $pricingDbConnection->query($query);
            if ($defaultRushFeeToken === 'custom25') {
                $retRate = 1.25;
            } else {
                $retRate = 1.5;
            }
            $result->free();
        }
        return $retRate;
    }

    public static function getProjectMinimums($db_table_name, PricingMySql $pricingDbConnection, &$projectMinimmum, &$projectRushMinimum, $applyRushFee) {
        $projectMinimmum = self::getCustomRatePricingItem(["Minimum_project"], 'rate', $db_table_name, $pricingDbConnection);
        if($applyRushFee) {
            $projectRushMinimum = self::getCustomRatePricingItem(["Minimum_project"], 'rush_rate', $db_table_name, $pricingDbConnection);
        }
    }

    public static function getCustomRatePricingItem($columnKeys, $columnName, $db_table_name, PricingMySql $pricingDbConnection) {
        $returnVal = -1;
        foreach ($columnKeys as $key) {
            $theSelect = sprintf("SELECT %s from %s where task_name='%s'", $columnName, $db_table_name, $key);
            $result = $pricingDbConnection->query($theSelect);
            if ($result == false) {
                return -1;
            }
            $returnVal = $result->fetch_assoc();
            $result->free();
            if ($returnVal == null) {
                continue;
            }
            $returnVal = $returnVal[$columnName] / 1000;
            break;
        }
        return is_null($returnVal) ? -1 : $returnVal;
    }

    public static function getPricingSchemeDatabaseIfApplicable($pricing, $projectObj, $myDBConn) {
        $returnTableName = null;
        if ($pricing) {
            $pricingScheme = "";
            if (isset($_SESSION['pricingScheme'])) {
                $pricingScheme = $_SESSION['pricingScheme'];
            } else {
                return $returnTableName;
            }
            
            if ($pricingScheme == "Healthcare List Pricing") {
                $returnTableName = "healthcare_list_pricing";
            } else {
                $client_id = $projectObj->company->id;
                $query = "SELECT * FROM clients WHERE attask_id = " . $client_id;
                $result = $myDBConn->query($query);

                if (!is_bool($result) && $result != null) {
                    $sqlNumRows = 0;
                    $sqlNumRows = $result->num_rows;

                    if ($sqlNumRows > 0) {
                        $res = $result->fetch_assoc();
                        $returnTableName = $res['table_name'];
                    }
                    $result->free();
                }
            }
        }

        return $returnTableName;
    }

    public static function applyCustomSellRate(QuoteLineItem $theLineItem, $dbTableNane, $myDBConn) {
        if (!is_null($dbTableNane)) {
            $rate = QuoteToolUtils::getCustomRatePricingItem($theLineItem->getRatePricingDBColumnKeys(), "rate", $dbTableNane, $myDBConn);
            $theLineItem->setCustomRatePerUnit($rate);
        }
    }

    public static function applyMinimumSellPrice(IQuoteItem $lingCont, $dbTableName, $myDBConn) {
        if (!is_null($dbTableName)) {
            $price = QuoteToolUtils::getCustomRatePricingItem($lingCont->getMinimumPricingDBColumnKeys(), "rate", $dbTableName, $myDBConn);
            if ($price > -1) {
                $lingCont->setSellPriceMinimum($price);
            }
        }
    }

    public static function determineRushFee($projectData, &$applyCustomRushFees, &$defaultRushFeePercentage) {
        $applyCustomRushFees = $projectData->get_customRushApply();
        $defaultRushFeePercentage = $projectData->get_rushFee();
    }

    public static function applyRushFee(QuoteLineItem $theLineItem, $defaultFeePercentage, $applyCustom, $dbTableNane, $myDBConn) {
        $theLineItem->setDefaultRushFeePercentage($defaultFeePercentage);
        if ($applyCustom && !is_null($dbTableNane)) {
            $rushRate = QuoteToolUtils::getCustomRatePricingItem($theLineItem->getRatePricingDBColumnKeys(), "rush_rate", $dbTableNane, $myDBConn);
            if ($rushRate > 0) {
                $theLineItem->setCustomRushFeePercentage($rushRate);
            }
        }
    }

    /**
     * This replaces spaces and parentheses with underscores. Might add other replacements if run into.
     * 
     * @param type $inString The string to normalize.
     * @return type The normaized string.
     */
    public static function makeLanguageId($inString) {
        // some languages don't have a region... so:
        $test = strpos($inString, "(");
        if (!is_bool($test)) {
            $firstPart = trim(substr($inString, 0, strpos($inString, "(")));
            $firstpart = (str_replace(" ", ":", $firstPart)) . "_";
            $secondpart = str_replace(")", "", (substr($inString, strpos($inString, "(") + 1)));
            $secondpart = (str_replace(" ", ":", $secondpart));
            $fullMonty = $firstpart . $secondpart;
            return $fullMonty;
        } else {
            return str_replace(" ", ":", $inString);
        }
    }

    public static function convertLanguageId($theId) {
        $test = strpos($theId, "_");
        if (!is_bool($test)) {
            $langPairs = explode("_", $theId);
            $firstPart = str_replace(":", " ", $langPairs[0]);
            $secondpart = str_replace(":", " ", $langPairs[1]);
            $fullMonty = $firstPart . " (" . $secondpart . ")";
            return $fullMonty;
        } else {
            return str_replace(":", " ", $theId);
        }
    }

    public static function getCurrencyFormattedValue($unformattedNumber) {

        $fmtr = $a = new \NumberFormatter("en-US", \NumberFormatter::CURRENCY);
        return $fmtr->format($unformattedNumber);
    }

    public static function roundUp($value) {
        $precision = $value < 10 ? 2 : 0;
        $pow = pow(10, $precision);
        return ( ceil($pow * $value) + ceil($pow * $value - ceil($pow * $value)) ) / $pow;
    }

    public static function generateLanguageLookup($sourceLang, $targLang) {
        return str_replace(" ", "_", $sourceLang) . "=" . str_replace(" ", "_", $targLang);
    }

    public static function getDistributionEnum($strategy) {
        $enum;
        switch ($strategy) {
            case "evenly" :
                $enum = DistributedTypeEnum::enum()->evenly;
                break;
            case "unevenly" :
                $enum = DistributedTypeEnum::enum()->unevenly;
                break;
            case "not distributed" :
                $enum = DistributedTypeEnum::enum()->not;
                break;
            default:
                throw new Exception("Unrecognized distribution strategy: " . $strategy);
        }
        return $enum;
    }

    private static function getLanguageTier($lang) {
        $retTier = "TIER4";
        foreach (self::TIERS as $tierKey => $langArray) {
            if (in_array($lang, $langArray)) {
                $retTier = $tierKey;
                break;
            }
        }
        return $retTier;
    }

    public static function getStandardBlockPricingForTRCE($lang, $totalWordcount, &$standardMinimum, &$rushMinimum) {

        $wordBlocks = ceil($totalWordcount / 25);
        $price = 0.0;
        $tierLang = $lang;
        $pos = strpos($tierLang, " (");
        if (!is_bool($pos)) {
            $tierLang = substr($tierLang, 0, $pos);
        }

        $theTier = self::getLanguageTier($tierLang);

        switch ($theTier) {
            case 'TIER1':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 5.25;
                    break;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 6.5;
                    break;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 9.5;
                    break;
                } else {
                    $price = $wordBlocks * 10.5;
                    break;
                }
                break;
            case 'TIER2':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 8.25;
                    break;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 8.75;
                    break;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 14;
                    break;
                } else {
                    $price = $wordBlocks * 14.5;
                    break;
                }
                break;
            case 'TIER3':
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 9.5;
                    break;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 10.5;
                    break;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 14;
                    break;
                } else {
                    $price = $wordBlocks * 15;
                    break;
                }
                break;
            case 'TIER4':  // anything else that isn't 1, 2, or 3
                if ($totalWordcount > 7500) {
                    $price = $wordBlocks * 9.5;
                    break;
                } elseif ($totalWordcount > 2500) {
                    $price = $wordBlocks * 11;
                    break;
                } elseif ($totalWordcount > 1000) {
                    $price = $wordBlocks * 16;
                    break;
                } else {
                    $price = $wordBlocks * 16.5;
                    break;
                }
                break;
        }
        $price = round($price, 2);

        if ($theTier === "TIER1" || $theTier === "TIER2") {
            $standardMinimum = 99;
            $rushMinimum = 149;
        } elseif ($theTier === "TIER3") {
            $standardMinimum = 109;
            $rushMinimum = 159;
        } else {
            $standardMinimum = 125;
            $rushMinimum = 179;
        }

        return $price;
    }

    public static function getFormatTaskDataFromDoctansTRCE($theTask, &$price, &$rate, &$workRequired) {
        $price = 0;
        $price = round($theTask->wordCounts->formattingHours * _FORMAT_HRLY_RATE_, 2);
        $workRequired = $theTask->wordCounts->formattingHours;
        $rate = _FORMAT_HRLY_RATE_;
    }

    public static function getStandardBlockPricingOnHourly($useUSLinguists, $theTask) {
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

        return $hourlyRate;

//        $price = (($theTask->ltask->workRequired) * $hourlyRate);
//        $price = round($price, 2);
//        $sellRates[$theTask->ltask->id] = $hourlyRate;
//        $totalCost += round($costRate * $theTask->ltask->workRequired, 2);
//        $taskCount++;
//        $theTask->ltask->price = $price;
//        $totalPrice += $price;
    }

    public static function getDateNow() {
        date_default_timezone_set('America/Los_Angeles');
        $dateFormat = "M d, Y";
        return date($dateFormat);
    }

    public static function getFauxWFId($size) {
        $minstr = "1";
        for ($c = 0; $c < $size - 1; $c++) {
            $minstr .= "0";
        }
        $min = (int) $minstr;
        $max = ($min * 10) - 1;
        return rand($min, $max);
    }
    
    public static function toFloat($num){
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }

}
