<?php
require_once (__DIR__ . "/../includes/Enum.class.inc");

/**
 * Description of WorkUnit
 *
 * @author Axian Developer
 */

  final class WorkUnitType extends Enum
  {
    public static function enum()
    {
      return self::declareElements(
      __CLASS__,
      array
      (
        'words',
        'days',
        'pages',
        'hours'
      ));
    }
  }
  
