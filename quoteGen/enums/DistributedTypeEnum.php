<?php
require_once (__DIR__ . "/../includes/Enum.class.inc");
/**
 * Description of DistributedTypeEnum
 *
 * @author Axian Developer
 */
final class DistributedTypeEnum extends Enum
  {
    public static function enum()
    {
      return self::declareElements(
      __CLASS__,
      array
      (
        'evenly',
        'unevenly',
        'not',
      ));
    }
  }
