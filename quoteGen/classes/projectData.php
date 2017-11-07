<?PHP

class projectData {

    protected $pricingScheme;
    protected $reqDevDate;
    protected $rushFee;
    protected $discountValue;
    protected $discountType;
    protected $billingCycle;
    protected $billingCycleOther;
    protected $transBuyUnits;
    protected $transSellUnits;
    protected $numberOfPages;
    protected $dtpBuyUnits;
    protected $dtpSellUnits;
    protected $initialPMPercent;
    protected $ratesUnlocked;
    protected $pmMinPerLanguage;
    protected $qa_pagesPerHour;
    protected $pricing = false;
    protected $callingPage;
    protected $customRushApply = false;
    protected $chargeForProofreading = false;
    protected $pmPercent;

    
    function get_pmPercent() {
        return $this->pmPercent;
    }
    
    function set_pmPercent($percent) {
        $this->pmPercent = $percent;
    }
    
    function set_pricingScheme($s) {
        $this->pricingScheme = $s;
    }

    function get_pricingScheme() {
        return $this->pricingScheme;
    }
    
    function set_pricing($pricing) {
        $this->pricing = $pricing;
    }
    
    function get_pricing() {
        return $this->pricing;
    }
    
    function set_callingPage($page) {
        $this->callingPage = $page;
    }
    function get_callingPage() {
        return $this->callingPage;
    }
    
    function set_customRushApply($rushApply) {
        $this->customRushApply = $rushApply;
    }

    function get_customRushApply() {
        return $this->customRushApply;
    }
    
    function get_chargeForProofreading() {
        return $this->chargeForProofreading;
    }
    
    function set_chargeForProofreading($charge) {
        $this->chargeForProofreading = $charge;
    }
    
    function set_reqDevDate($d) {
        $this->reqDevDate = $d;
    }

    function get_reqDevDate() {
        return $this->reqDevDate;
    }

    function set_rushFee($r) {
        $this->rushFee = $r;
    }

    function get_rushFee() {
        return $this->rushFee;
    }

    function rushFee($thisProject) {
        $projectTotal = 0;
        foreach ($thisProject as $src => $bySource) {
            if ($src != 'nonDistributed') {
                foreach ($bySource as $byTarget) {
                    foreach ($byTarget['linguistTasks'] as $lingTask) {
                        if ($lingTask->get_sellUnits() == 'words') {
                            $projectTotal += $lingTask->asp('new') + $lingTask->asp('fuzzy') + $lingTask->asp('match');
                        } else {
                            $projectTotal += $lingTask->asp('hourly');
                        }
                    }

                    if (array_key_exists('billableTasks', $byTarget)) {
                        foreach ($byTarget['billableTasks'] as $billTask) {
                            if ($billTask->get_type() != 'Project Manager') {
                                $projectTotal += $billTask->asp();
                            } else {
                                $projectTotal += $billTask->aspByLanguage($thisProject, $billTask->get_targLang());
                            }
                        }
                    }
                }
            } else {
                foreach ($bySource as $nonDistTask) {
                    if ($nonDistTask->get_type() != 'Project Manager') {
                        $projectTotal += $nonDistTask->asp();
                    } else {
                        $projectTotal += $nonDistTask->asp($thisProject);
                    }
                }
            }
        }

        return $projectTotal * ($this->rushFee);
    }

    function set_discountValue($d) {
        $this->discountValue = $d;
    }

    function get_discountValue() {
        return $this->discountValue;
    }

    function set_discountType($t) {
        $this->discountType = $t;
    }

    function get_discountType() {
        return $this->discountType;
    }

    function get_discount($thisProject) {
        if ($this->discountType == 'fixed') {
            return $this->discountValue;
        } else {
            $projectTotal = 0;
            foreach ($thisProject as $src => $bySource) {
                if ($src != 'nonDistributed') {
                    foreach ($bySource as $byTarget) {
                        foreach ($byTarget['linguistTasks'] as $lingTask) {
                            if ($lingTask->get_sellUnits() == 'words') {
                                $projectTotal += $lingTask->asp('new') + $lingTask->asp('fuzzy') + $lingTask->asp('match');
                            } else {
                                $projectTotal += $lingTask->asp('hourly');
                            }
                        }

                        if (array_key_exists('billableTasks', $byTarget)) {
                            foreach ($byTarget['billableTasks'] as $billTask) {
                                if ($billTask->get_type() != 'Project Manager') {
                                    $projectTotal += $billTask->asp();
                                } else {
                                    $projectTotal += $billTask->aspByLanguage($thisProject, $billTask->get_targLang());
                                }
                            }
                        }
                    }
                } else {
                    foreach ($bySource as $nonDistTask) {
                        if ($nonDistTask->get_type() != 'Project Manager') {
                            $projectTotal += $nonDistTask->asp();
                        } else {
                            $projectTotal += $nonDistTask->asp($thisProject);
                        }
                    }
                }
            }

            $projectTotal += $this->rushFee($thisProject);

            return $projectTotal * ($this->discountValue / 100);
        }
    }

    function set_billingCycle($c) {
        $this->billingCycle = $c;
    }

    function get_billingCycle() {
        return $this->billingCycle;
    }

    function set_billingCycleOther($o) {
        $this->billingCycleOther = $o;
    }

    function get_billingCycleOther() {
        return $this->billingCycleOther;
    }

    function set_transBuyUnits($b) {
        $this->transBuyUnits = $b;
    }

    function get_transBuyUnits() {
        return $this->transBuyUnits;
    }

    function set_transSellUnits($s) {
        $this->transSellUnits = $s;
    }

    function get_transSellUnits() {
        return $this->transSellUnits;
    }

    function set_numberOfPages($p) {
        $this->numberOfPages = $p;
    }

    function get_numberOfPages() {
        return $this->numberOfPages;
    }

    function set_dtpBuyUnits($b) {
        $this->dtpBuyUnits = $b;
    }

    function get_dtpBuyUnits() {
        return $this->dtpBuyUnits;
    }

    function set_dtpSellUnits($s) {
        $this->dtpSellUnits = $s;
    }

    function get_dtpSellUnits() {
        return $this->dtpSellUnits;
    }

    function set_initialPMPercent($p) {
        $this->initialPMPercent = $p;
    }

    function get_initialPMPercent() {
        return $this->initialPMPercent;
    }

    function set_ratesUnlocked($r) {
        $this->ratesUnlocked = $r;
    }

    function get_ratesUnlocked() {
        return $this->ratesUnlocked;
    }

    function set_pmMinPerLanguage($b) {
        $this->pmMinPerLanguage = $b;
    }

    function get_pmMinPerLanguage() {
        return $this->pmMinPerLanguage;
    }

    function set_qa_pagesPerHour($p) {
        $this->qa_pagesPerHour = $p;
    }

    function get_qa_pagesPerHour() {
        return $this->qa_pagesPerHour;
    }

}
