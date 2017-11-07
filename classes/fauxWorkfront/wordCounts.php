<?php
namespace llts\fauxWorkfront;

/**
 * Description of wordCounts
 *
 * @author Axian Developer
 */
class wordCounts {
    public $formattingHours = 0.0; // float
    public $fuzzyWords = 0; // int
    public $matchRepsWords = 0; // int
    public $newWords = 0; // int
    public $wordCount = 0; // int
    
    public function __construct($newWords, $matchRepsWords, $fuzzyWords, $wordCount = 0, $formattingHours = 0) {
        $this->newWords = $newWords;
        $this->fuzzyWords = $fuzzyWords;
        $this->matchRepsWords = $matchRepsWords;
        $this->wordCount = $wordCount;
        $this->formattingHours = $formattingHours;
    }
    
    public function getFormattingHours() {
        return $this->formattingHours;
    }

    public function getFuzzyWords() {
        return $this->fuzzyWords;
    }

    public function getMatchRepsWords() {
        return $this->matchRepsWords;
    }

    public function getNewWords() {
        return $this->newWords;
    }

    public function getWordCount() {
        return $this->wordCount;
    }

    public function setFormattingHours($formattingHours) {
        $this->formattingHours = $formattingHours;
    }

    public function setFuzzyWords($fuzzyWords) {
        $this->fuzzyWords = $fuzzyWords;
    }

    public function setMatchRepsWords($matchRepsWords) {
        $this->matchRepsWords = $matchRepsWords;
    }

    public function setNewWords($newWords) {
        $this->newWords = $newWords;
    }

    public function setWordCount($wordCount) {
        $this->wordCount = $wordCount;
    }

}
