<?php

require_once (__DIR__ . "/IXmlPrintable.php");

/**
 *
 * @author Axian Developer
 */
interface IContainerXmlPrintable extends IXmlPrintable{
    public function setChildrenShouldPrintXml($aBooleanVal);
}
