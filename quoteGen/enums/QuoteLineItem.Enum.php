<?php

require_once (__DIR__ . "/../includes/Enum.class.inc");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WorkUnit
 *
 * @author Axian Developer
 */

  final class QuoteLineItemEnum extends Enum
  {
    public static function enum()
    {
      return self::declareElements(
      __CLASS__,
    [
        'tr_ce_fuzzy_text',
        'tr_ce_new_text',
        'tr_ce_matchrep_text',
        'tr_ce_words',
        'tr_fuzzy_text',
        'tr_new_text',
        'tr_matchrep_text',
        'tr_words',
        'ce_fuzzy_text',
        'ce_new_text',
        'ce_matchrep_text',
        'OLR',
        'ICR',
        'TR+CE',
        'TR/CE',
        'TR',
        'PR',
        'TM_Work',
        'VO'
        ]);
    }
  }
  