<?php

require_once (__DIR__ . "/../interfaces/IHtmlRenderable.php");
require_once (__DIR__ . "/QuoteConstants.php");
require_once (__DIR__ . "/QuoteToolUtils.php");

/**
 * Description of ProjectInfo
 *
 * @author Axian Developer
 */
class ProjectInfo implements IHtmlRenderable {

    protected $projectObj;
    protected $projectData;

    public function __construct() {
        $this->projectObj = unserialize($_SESSION['projectObj']);
        $this->projectData = unserialize($_SESSION['projectData']);
    }

    public function renderHtml($parentId) {
        echo ProjectInfo::HTML_FIELDSET_HEAD;
        $infoItemKeys = array_keys(QuoteConstants::$ProjectInfoData);
        $count = 0;
        foreach ($infoItemKeys as $infoKey) {
            $theItem = QuoteConstants::$ProjectInfoData[$infoKey];
            $count === 0 ? $this->renderFirstItem($infoKey, $theItem) : $this->renderItem($infoKey, $theItem);
            $count++;
        }
        echo ProjectInfo::HTML_FIELDSET_CLOSE;
    }

    public function getHtml($parentId) {
        // don't call this!
    }

    private function renderFirstItem($key, $item) {
        $string = ProjectInfo::HTML_ITEM_FIRST_SPEC;
        $string = $this->fillInOutputItem($string, $key, $item);
        echo $string;
    }

    private function renderItem($key, $item) {
        $string = ProjectInfo::HTML_ITEM_SPEC;
        echo $this->fillInOutputItem($string, $key, $item);
    }

    private function fillInOutputItem($outputSpec, $key, $item) {

        $functionName = $item['functionName'];

        $reflectionMethod = new ReflectionMethod(__CLASS__, $functionName);
        $value = $reflectionMethod->invoke(new ProjectInfo);



        $string = str_replace('{key}', $key, $outputSpec);
        $string = str_replace('{name}', $item['name'], $string);
        $string = str_replace('{value}', $value, $string);

        return $string;
    }

    const HTML_ITEM_FIRST_SPEC = "\t      <tr>
                 <td width=\"25%\" align=\"right\" valign=\"top\">{key}</td>
                 <td width=\"60%\" align=\"left\" valign=\"top\">
                    <input name=\"{name}\" type=\"text\" readonly=\"readonly\" tabindex=\"-1\" style=\"width: 400px;\" value=\"{value}\"/>
                 </td>
              </tr>\n";
    const HTML_ITEM_SPEC = "\t      <tr>
                 <td align=\"right\">{key}</td>
                 <td>
                    <input type=\"text\" name=\"{name}\" value=\"{value}\" style=\"width: 400px;\" readonly=\"readonly\" tabindex=\"-1\" />
                 </td>
              </tr>\n";
    const HTML_FIELDSET_HEAD = "<fieldset style=\"width:50%\"><legend>Quote info</legend>
           <table border=\"1\" align=\"center\" width=\"100%\" bgcolor=\"#FFFFFF\">\n";
    const HTML_FIELDSET_CLOSE = "\t   </table>
       </fieldset>\n";

    public function getDate() {
        return QuoteToolUtils::getDateNow();
    }

    public function getProjectId() {
        if (!is_null($this->projectObj)) {
            return $this->projectObj->id;
        }
        return "";
    }

    public function getProjectName() {
        if (!is_null($this->projectObj)) {
            return $this->projectObj->name;
        }
        return "";
    }

    public function getClientName() {
        if (!is_null($this->projectObj)) {
            return $this->projectObj->company->name;
        }
        return "";
    }

    public function getClientContact() {
        if (!is_null($this->projectObj)) {
            return $this->projectObj->contact->firstName . " " . $this->projectObj->contact->lastName;
        }
        return "";
    }

    public function getSponsor() {
        if (!is_null($this->projectObj) && !is_null($this->projectObj->sponsor)) {
            return $this->projectObj->sponsor->firstName . " " . $this->projectObj->sponsor->lastName;
        }
        return "";
    }

    public function getDeliveryDate() {
        if (!is_null($this->projectData)) {
            return $this->projectData->get_reqDevDate();
        }
        return "";
    }

    public function getRushFee() {
        if (!is_null($this->projectData)) {
            $rushedFee = $this->projectData->get_rushFee();
            $retFee = "";
            switch ($rushedFee) {
                case '0.25':
                    $retFee = "25% of total project cost";
                    break;
                case '0.50':
                    $retFee = "50% of total project cost";
                    break;
                case '1.0':
                    $retFee = "100% of total project cost";
                    break;
                default: $retFee = "none";
            }

            $addedCustomText = "";

            if ($this->projectData->get_customRushApply()) {
                $addedCustomText = ". Custom Rush Fees may apply.";
            }
            return $retFee . $addedCustomText;
        }
        return "";
    }

    public function getDiscount() {
        if (!is_null($this->projectData)) {
            $retVal = "none";
            if (($this->projectData->get_discountValue() != '') && ($this->projectData->get_discountValue() != 0)) {
                switch ($this->projectData->get_discountType()) {
                    case 'percent':
                        $retVal = $this->projectData->get_discountValue() . "% of total project cost";
                        break;
                    case 'fixed':
                        $retVal = "$" . number_format($this->projectData->get_discountValue(), 2);
                        break;
                    default:
                        $retVal = 'none';
                }
            }
            return $retVal;
        }
        return "";
    }

    public function getBillingTerms() {
        if (!is_null($this->projectObj)) {
            return $this->projectObj->company->paymentTerms . " Days";
        }
        return "";
    }

    public function getBillingCycle() {
        if (!is_null($this->projectData)) {
            $retBillingCycle = $this->projectData->get_billingCycle();
            if ($retBillingCycle == 'Progressive') {
                $retBillingCycle += " : " . $this->projectData->get_billingCycleOther();
            }
            return $retBillingCycle;
        }
        return "";
    }

    public function getPricingApplied() {
        return $this->projectData->get_pricingScheme();
    }

}
