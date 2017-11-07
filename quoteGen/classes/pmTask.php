<?PHP

class pmTask extends lingo_billableTask {

    protected $usesCustom; //bool
    protected $hourlyMinimum;

    function cost() {
        return $this->workRequired * $this->hourlyRate;
    }

    function aspByLanguage($thisProject, $language) {
        $pmTotal = 0;

        foreach ($thisProject as $srcLang => $bySource) {

            if ($srcLang != 'nonDistributed') {
                foreach ($bySource as $tgtLang => $byTarget) {

                    if ($tgtLang == $language) {
                        $languageMinimum = checkSellLanguageMinimum($thisProject, $this->sourceLang, $this->targLang);
                        $translationMinimum = checkSellTranslationMinimum($thisProject, $this->sourceLang, $this->targLang);
                        foreach ($byTarget['linguistTasks'] as $lingTask) {
                            //if it is a "language" task, then we need to make sure the language minimum wasn't hit
                            //and if it was then don't add the task ASP to the total
                            if (($lingTask->get_taskonly_name() == 'TR') ||
                                    ($lingTask->get_taskonly_name() == 'CE') ||
                                    ($lingTask->get_taskonly_name() == 'TR+CE') ||
                                    ($lingTask->get_taskonly_name() == 'PR') ||
                                    ($lingTask->get_taskonly_name() == 'OLR')
                            ) {
                                if ($languageMinimum == 0) {
                                    if (($lingTask->get_taskonly_name() == 'TR') ||
                                            ($lingTask->get_taskonly_name() == 'CE') ||
                                            ($lingTask->get_taskonly_name() == 'TR+CE')) {
                                        if ($translationMinimum == 0) {
                                            if ($lingTask->get_sellUnits() == 'words') {
                                                $pmTotal += $lingTask->asp('new') + $lingTask->asp('fuzzy') + $lingTask->asp('match');
                                            } else {
                                                $pmTotal += $lingTask->asp('hourly');
                                            }
                                        }
                                    } else {
                                        if ($lingTask->get_sellUnits() == 'words') {
                                            $pmTotal += $lingTask->asp('new') + $lingTask->asp('fuzzy') + $lingTask->asp('match');
                                        } else {
                                            $pmTotal += $lingTask->asp('hourly');
                                        }
                                    }
                                }
                            } else { //not one of the "language" tasks
                                if ($lingTask->get_sellUnits() == 'words') {
                                    $pmTotal += $lingTask->asp('new') + $lingTask->asp('fuzzy') + $lingTask->asp('match');
                                } else {
                                    $pmTotal += $lingTask->asp('hourly');
                                }
                            }
                        }

                        //if the language minimum was hit, then add the minimum to the total
                        if ($languageMinimum != 0) {
                            $pmTotal += $languageMinimum;
                        } else {
                            if ($translationMinimum != 0) {
                                $pmTotal += $translationMinimum;
                            }
                        }

                        foreach ($byTarget['billableTasks'] as $billTask) {
                            if ($billTask->get_type() != 'Project Manager') {
                                $pmTotal += $billTask->asp();
                            }
                        }
                    }
                }
            }
        }

        $pm_percent = (1 - ($this->get_markupPercent() / 100));

        $markup = ($this->get_markupPercent() / 100);

        $pmASP = (($pmTotal / $pm_percent) * $markup);

        if ($pmASP < $this->hourlyMinimum)
            return $this->hourlyMinimum;
        else
            return $pmASP;
    }

    function asp($thisProject) {
        $pmTotal = get_total_for_pm($thisProject);
        $pmASP = ($pmTotal / (1 - ($this->get_markupPercent() / 100)) * ($this->get_markupPercent() / 100));

        if ($pmASP < $this->hourlyMinimum)
            return $this->hourlyMinimum;
        else
            return $pmASP;
    }

    function gm($thisProject) {
        if ($this->evenlyDistributed == 'nonDistributed') {
            if ($this->asp($thisProject) == 0)
                return 0;
            else
                return (($this->asp($thisProject) - $this->cost()) / $this->asp($thisProject)) * 100;
        }
        else {
            if ($this->aspByLanguage($thisProject, $this->targLang) == 0) {
                return 0;
            } else {
                return (($this->aspByLanguage($thisProject, $this->targLang) - $this->cost()) / $this->aspByLanguage($thisProject, $this->targLang)) * 100;
            }
        }
    }

    function usesCustom() {
        return $this->usesCustom;
    }

    function set_usesCustom($u) {
        $this->usesCustom = $u;
    }

}
