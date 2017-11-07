<?php

/**
 * Interface to 
 * 
 * @author Karl Krasnowsky
 */
interface IXmlPrintable {
    /**
     * reports whether or not this object supports printing.
     */
    public function thisSupportsXmlPrinting();
    /**
     * provides the xml representation of the object to be included in the
     * document.
     */
    public function renderXml();
    
    /**
     * reports on whether or not this object's state is determined to be printed.
     * 
     * @param type $aBooleanValue True if this object should be considered to be
     * printed in to the Xml format, false otherwise.
     */
    public function setShouldPrintXml($aBooleanValue);
    
    public function shouldPrintXml();
    
    /**
     * indicates that this object is required, or not, to be rolled up into
     * a one liner by it's category container parent.
     */
    public function alwaysRollUp();
}
