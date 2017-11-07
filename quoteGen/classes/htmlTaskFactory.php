<?php

include_once (__DIR__ . "/htmlTRCEWordTask.php");
include_once (__DIR__ . "/htmlDistributedTask.php");
include_once (__DIR__ . "/htmlOtherTask.php");
include_once (__DIR__ . "/htmlLinguistTask.php");

/**
 * Description of htmlTaskFactory
 *
 * @author Axian Developer
 */
class htmlTaskFactory {
    const WORD_TYPES = ["tr_ce_new_text", "tr_ce_fuzzy_text", "tr_ce_matchrep_text", "tr_ce_words"];
    public static function getHtmlTask($htmlReference, $targLang = null) {
        $parsedTask = self::parseTask($htmlReference);
        // id no language specified we can assume it's an "other" task;
        if(is_null($targLang)){
            return new htmlOtherTask($parsedTask[1]);
        } else {
            if(count($parsedTask) == 3) {
                if(in_array($parsedTask[2], self::WORD_TYPES)) {
                    return new htmlTRCEWordTask($targLang, $parsedTask[1], $parsedTask[2]);
                } else {
                    return new htmlDistributedTask($targLang, $parsedTask[1]);
                }
            } elseif(count($parsedTask) == 2 ) {
                // this would be your basic linguist task...
                return new htmlLinquistTask($targLang, $parsedTask[1]);
            }
        }
    }
    
    private static function parseTask($taskName) {
        return explode("-", $taskName);
    }
}
