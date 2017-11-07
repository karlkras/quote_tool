<?php
require_once (__DIR__ . '/ProjectManager.php');
/**
 * Description of RushFeeCalculator
 *
 * @author Axian Developer
 */
class RushFeeCalculator {
    
    protected $projectManager;
    protected $projectRushMin = -1;
    
    public function __construct(ProjectManager $pman, $projectRushMinimum) {
        $this->projectManager = $pman;
        $this->projectRushMin = $projectRushMinimum;
    }
    //put your code here
    
    
    public function calculate() {
        $lingItems = $this->projectManager->getTaskFrameArray();
        $lingRushTotal = 0.0;
        $otherRushTotal = 0.0;
        $pmRushTotal = 0.0;
        $lingSellTotal = 0.0;
        
        $this->calculateRushBase($lingItems, $lingRushTotal, $lingSellTotal);
       
        $otherFrame = $this->projectManager->getOtherFrame();
        if (!is_null($otherFrame)) {
            $otherRushTotal = $otherFrame->getItemsRushFees();
        }
        if ($lingRushTotal > 0) {
            $this->calculatePMRush($lingRushTotal, $otherRushTotal, $pmRushTotal);
        }
        $normalTotal = $lingRushTotal + $otherRushTotal + $pmRushTotal;
        
        if($this->projectRushMin > 0) {
            $this->calculateRushMinimum($normalTotal);
        }
        return $normalTotal;
    }
    
    private function calculateRushMinimum(&$normalTotal) {
        $projBaseTotal = $this->projectManager->getProjectBaseSellTotal();
        $testTotal = $this->projectRushMin - $projBaseTotal;
        if($testTotal > $normalTotal) {
            $normalTotal += ($testTotal - $normalTotal);
            $this->projectManager->setIsProjectRushMinimum();
        }
    }
    
    private function calculatePMRush($lingRushTotal, $otherRushTotal, &$pmRushTotal) {
        $projManArray = $this->projectManager->getprojectManagerTaskArray();
        if (! empty($projManArray)) {
            if (!$projManArray[0]->isDistributed()) {
                $pmPercent = $this->projectManager->getPMPercent();
                $pmTaskSellTotal = $this->projectManager->getPMTaskActualSellTotal();
                $projectBaseSellTotal = $this->projectManager->getProjectBaseSellTotal();
                
                $combo = $projectBaseSellTotal + $lingRushTotal + $otherRushTotal;
                $combo -= $pmTaskSellTotal;
                $pmWithRushFeeTotal = $combo / (1 - ($pmPercent / 100)) * ($pmPercent / 100);
                $pmRushTotal = $pmWithRushFeeTotal - $pmTaskSellTotal < 0 ? 0 : $pmWithRushFeeTotal - $pmTaskSellTotal;
            }
        }
    }
    
    private function calculateRushBase($lingItems, &$lingRushTotal, &$lingSellTotal) {
        foreach ($lingItems as $frameName => $frame) {
            $additionalRushAmount = 0;
            $baseRushFeeAmount = $frame->getLinguistRushFeeTotal();
            $sellTotal = $frame->getLinguistSellPriceTotal();
            $pmRushFee = $this->getLanguagePMRushFee($frame, $baseRushFeeAmount);
            if ($baseRushFeeAmount > 0) {
                $comparsionAmount = ($baseRushFeeAmount + $sellTotal + $pmRushFee);
                $totalMinimumWithRushFee = $frame->getMinimumRushFreeTotal();
                if ($totalMinimumWithRushFee > 0 && $totalMinimumWithRushFee > $comparsionAmount) {
                    $additionalRushAmount = $totalMinimumWithRushFee - $comparsionAmount;
                }
                $lingRushTotal += ($baseRushFeeAmount + $pmRushFee + $additionalRushAmount);
            }
            $lingSellTotal += $frame->getLinguistSellPriceTotal();
        }
    }
    
    private function getLanguagePMRushFee($frame, $baseRushFeeAmount) {
        $orgSellTotal = $frame->getLinguistSellPriceTotal(false);

        $distroPM = $this->projectManager->getDistroPM($frame->getId());
        $distroPMAmount = 0;
        if (!is_null($distroPM)) {
            $distroPMAmount = $distroPM->getActualSellPriceTotal();
        } else {
            return $distroPMAmount;
        }
        
        $orgSellTotalLessPm = $orgSellTotal - $distroPMAmount;
        $baseRushWithOrgTotal = $orgSellTotalLessPm + $baseRushFeeAmount;
        $pmPercentAmount = $baseRushWithOrgTotal / (1 - ($this->projectManager->getPMPercent() / 100)) * ($this->projectManager->getPMPercent() / 100);
        if ($pmPercentAmount < ($distroPM->getActualSellPriceTotal()*(1+$this->projectManager->getRushRate()))){
            $pmPercentAmount = $distroPM->getActualSellPriceTotal()*(1+$this->projectManager->getRushRate());
        }

        $pmRushAmount = $pmPercentAmount - $distroPMAmount;

        
        return $pmRushAmount < 0 ? 0 : $pmRushAmount;
    }    
    
}
